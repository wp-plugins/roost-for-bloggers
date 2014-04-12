<?php

class Roost {
    
    public static $roost_version = "2.0.5";
    
    public static function site_url() {
        return get_option( 'siteurl' );   
    }
    
    public static function roost_settings() {
        return get_option('roost_settings');   
    }
    
    public function __construct() {
        $this->add_actions();
        
    }
    
    public static function init() {
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {		
			self::upgrade();
		} else {
			self::install();
		}
	}

	public static function upgrade() {
		global $wpdb;
        $table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		foreach($results as $result) {	
			$app_key = $result->appkey;
			$app_secret = $result->appsecret;
			$username = $result->username;
			$auto_push = $result->autopush;
			$app_usage = $result->appusage;
			$custom_bar_text = $result->custombartext;
		}		
		
		if ( !empty( $app_key ) ) {
			$roost_settings = array(
				"appKey" => $app_key,
				"appSecret" => $app_secret,
				"username" => $username,
				"version" => self::$roost_version,
				"autoPush" => $auto_push
            );
			
			add_option('roost_settings', $roost_settings);		
			
			if ( $app_usage == 1 ) {
				$app_usage = 'TOP';
			} elseif ( $app_usage == 2 ) {
				$app_usage = 'BOTTOM';
			} else {
				$app_usage = 'OFF';
			}
			
			$remote_content = array(
				'roostBarSetting' => $app_usage
			);
			
			if ( strlen( $custom_bar_text ) > 0 ) {
				$remote_content['roostBarText'] = $custom_bar_text;
			}
			
			$remote_data = array(
				'method' => 'PUT',
				'remoteAction' => 'app',
				'appkey' => $app_key,
				'appsecret' => $app_secret,
				'remoteContent' => json_encode( $remote_content )
			);
			Roost_API::roost_remote_request( $remote_data );		
		}

		$structure = "drop table if exists $table";
		$wpdb->query( $structure );
		
		self::install();
	}
	
	public static function install() {
		$roost_settings = self::roost_settings();
        
		if ( empty( $roost_settings ) ) {
			$roost_settings = array(
				"appKey" => '',
				"appSecret" => '',
				"username" => '',
				"version" => self::$roost_version,
				"autoPush" => 0
            );			
			add_option('roost_settings', $roost_settings);
		} else {
            $roost_settings['version'] = self::$roost_version;
        	update_option('roost_settings', $roost_settings);
        }
        self::roost_activated();
	}

    public static function roost_activated() {
        add_option('roost_do_redirect', true);
    }

    public function activate_redirect() {
        if ( get_option('roost_do_redirect', false) ) {
            delete_option('roost_do_redirect');
            if( !isset( $_GET['activate-multi'] ) ){
                wp_redirect( admin_url( 'admin.php?page=roost-for-bloggers/includes/roost-core.php' ) );
                exit;
            }
        }
    }

	public static function uninstall(){
		delete_option('roost_settings');
        delete_post_meta_by_key( 'roostOverride' );
	}

    public function add_actions() {
        add_action( 'transition_post_status', array( $this, 'build_note' ), 10, 3 );
        add_action( 'post_submitbox_misc_actions', array( $this, 'note_override' ) );
        add_action( 'wp_head', array( $this, 'byline' ), 1 );
        add_action( 'save_post', array( $this, 'save_post_meta_roost' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
        
        if ( is_admin() ) {
            add_action('admin_init', array( $this, 'activate_redirect' ) );
            add_action( 'admin_notices', array( $this, 'setup_notice' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu_add' ) );
            add_filter( 'plugin_action_links_roost-for-bloggers/roost.php', array( $this, 'add_action_links' ) );
        }
    }

    public function add_action_links ( $links ) {
        $rlink = array(
            '<a href="' . admin_url( 'admin.php?page=roost-for-bloggers/includes/roost-core.php' ) . '">Go to Plugin</a>',
        );
        return array_merge( $rlink, $links );
    }
    
    public function load_scripts() {
		$roost_settings = self::roost_settings();
		$app_key = $roost_settings['appKey'];
		if ($app_key && !is_admin()) {
			wp_enqueue_script( 'roostjs', ROOST_URL . 'layout/js/roostjs.js', array('jquery'), self::$roost_version, false );
			wp_localize_script( 'roostjs', 'pushNotificationsByRoostMe', array( 'appkey' => $app_key) );
		}
		if(is_admin()){
			wp_enqueue_script( 'roostGoogleFont', ROOST_URL . 'layout/js/roostGoogleFont.js', '', self::$roost_version, false );		
		}
	}

    public function byline() {
        $byline = "<!-- Push notifications for this website enabled by Roost. Support for Safari and Chrome Web Push. (v ". self::$roost_version .") - http://roost.me/ -->";
        echo "\n${byline}\n";
    }

    public static function setup_notice() {
        global $hook_suffix;
        $roost_page = "toplevel_page_roost-for-bloggers/includes/roost-core";
        
        $roost_settings = self::roost_settings();
        $app_key = $roost_settings['appKey'];

		if ( !$app_key && ( $hook_suffix !== $roost_page ) ) {
    ?>
		<div class="updated" id="roostSetupNotice">
            <div id="roostNoticeLogo">
                <img src="<?php echo( ROOST_URL . 'layout/images/roost_logo.png' ) ?>" />
            </div>
            <div id="roostNoticeText">
                <p>
                    Thanks for installing the Roost plugin! You’re almost finished with<br />setup, all you need to do is create an account and login.
                </p>
            </div>
            <div id="roostNoticeTarget">
                <a href="<?php echo( admin_url( 'admin.php?page=roost-for-bloggers/includes/roost-core.php' ) ); ?>" id="roostNoticeCTA" >
                    <span id="roostNoticeCTAHighlight"></span>
                    Finish Setup
                </a>
            </div>
		</div>    
    <?php
        } else if ( !$app_key && ( $hook_suffix === $roost_page ) ) {
            $api_check = Roost_API::api_check();
            if ( is_wp_error( $api_check ) ) {
    ?>
        <div class="error" id="roost-api-error">There was a problem accessing the <strong>Roost API</strong>. You may not be able to log in. Contact Roost support at <a href="mailto:support@roost.me" target="_blank">support@roost.me</a> for more information.</div>
    <?php
            }
        }
    }    

	public function admin_menu_add(){
	    add_menu_page(
        	"Roost Web Push",
	        "Roost Web Push",
	        "manage_options",
    	    __FILE__,
        	array( __CLASS__, 'admin_menu_page' ),
	        ROOST_URL . "layout/images/roost_thumb.png"
	    );
	}    

	public function admin_scripts() {
		wp_enqueue_style( 'rooststyle', ROOST_URL . 'layout/css/rooststyle.css', '', self::$roost_version );
		wp_enqueue_script( 'roostscript', ROOST_URL . 'layout/js/roostscript.js', array('jquery'), self::$roost_version );
	}
	
	public static function save_username($roost_user){
		$roost_settings = self::roost_settings();
		$roost_settings['username'] = $roost_user;
		update_option('roost_settings', $roost_settings);
	}
	
	public static function update_keys($form_keys){
		$roost_settings = self::roost_settings();
		$roost_settings['appKey'] = $form_keys['appKey'];
		$roost_settings['appSecret'] = $form_keys['appSecret'];
		update_option('roost_settings', $roost_settings);
	}
	
	public static function update_settings($form_data){	
		$roost_settings = self::roost_settings();
		$roost_settings['autoPush'] = $form_data['autoPush'];
		update_option('roost_settings', $roost_settings);
	}

    public function save_post_meta_roost( $post_id ) {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return false;
        } elseif (isset($_POST['post_status'])) {
            $no_note = get_post_meta($post_id, 'roostOverride', true);
            if (isset($_POST['roostOverride']) && !$no_note) {
                $override_setting = $_POST['roostOverride'];
                add_post_meta($post_id, 'roostOverride', $override_setting, true);
            } elseif (!isset($_POST['roostOverride']) && $no_note) {
                delete_post_meta($post_id, 'roostOverride');
            }
        }
    }

    public static function filter_string( $string ) {
        $string = str_replace('&#8220;', '&quot;', $string);
        $string = str_replace('&#8221;', '&quot;', $string);
        $string = str_replace('&#8216;', '&#39;', $string);
        $string = str_replace('&#8217;', '&#39;', $string);
        $string = str_replace('&#8211;', '-', $string);
        $string = str_replace('&#8212;', '-', $string);
        $string = str_replace('&#8242;', '&#39;', $string);
        $string = str_replace('&#8230;', '...', $string);
        $string = str_replace('&prime;', '&#39;', $string);
        return html_entity_decode($string, ENT_QUOTES);
    }

    public function build_note( $new_status, $old_status, $post ) {
		if ( $new_status != $old_status && !empty( $post ) ) {
		    $post_type = get_post_type( $post );
		    if ( $post_type === 'post' && $new_status === 'publish' ) {
				$post_id = $post->ID;
				$roost_settings = self::roost_settings();
				$app_key = $roost_settings['appKey'];
				$app_secret = $roost_settings['appSecret'];
				$auto_push = $roost_settings['autoPush'];

				if ( $auto_push == 1 && !empty( $app_key ) ) {	
					if ( ( $new_status === 'publish' ) && ( $old_status === 'future' ) ) {
						$override = get_post_meta($post_id, 'roostOverride', true);
					} else {
						if (isset($_POST['roostOverride'])) {
						    $override = $_POST['roostOverride'];
						}
					}
					if (empty($override)) {
						$alert = get_the_title( $post_id );
						if( $alert === null ) {
						    $alert = "";  
						}
						$url = wp_get_shortlink( $post_id );
						if ( has_post_thumbnail($post_id)) {
						    $raw_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id));
						    $image_url = $raw_image[0];
						} else {
						    $image_url = false;
						}
						Roost_API::send_notification($alert, $url, $image_url, $app_key, $app_secret);
					}
				}
			}
        }
    }

	public function note_override(){
        global $post;
        if ( $post->post_type === 'post' ) {
            if ( 'publish' == $post->post_status ) {
                $check_hidden = true;   
            }
            $roost_settings = self::roost_settings();
            $app_key = $roost_settings['appKey'];
            $auto_push = $roost_settings['autoPush'];
            $pid = get_the_ID();
            $checked = get_post_meta($pid, 'roostOverride', true);
            if( !empty( $app_key ) && $auto_push == 1 ){
                printf('<div class="misc-pub-section misc-pub-section-last" id="roost-override" %s >', ( isset( $check_hidden ) ) ? "style='display:none;'":"" );
                printf('<label><input type="checkbox" value="1" id="roostOverrideCheckbox" name="roostOverride" %s />', ( !empty( $checked ) ) ? "checked":"" );
                echo '<strong>Do NOT</strong> send notification with <strong>Roost</strong></label>';
                echo '</div>';
            }
        }
	}
	
    public static function complete_login( $form_keys ) {
        self::update_keys( $form_keys );
        $status = '<span class="roost-os-bold">Welcome to Roost!</span> The plugin is up and running and visitors to your site using Safari on OS X Mavericks are currently being prompted to subscribe for push notifications. Once you have subscribers you\'ll be able see recent activity, all-time stats, and send manual push notifications. If you have questions or need support, just email us at <a href="mailto:support@roost.me" target="_blank">support@roost.me</a>.';
        return $status;
    }

	public static function admin_menu_page() {
		$roost_settings = self::roost_settings();
		if ( empty( $roost_settings ) ) {
			self::upgrade();
        }        
		$app_key = $roost_settings['appKey'];
		$app_secret = $roost_settings['appSecret'];
		if ( !empty( $app_key ) ) {
			$roost_server_settings = Roost_API::get_server_settings($app_key, $app_secret);	
			$roost_stats = Roost_API::get_stats($app_key, $app_secret);
        }

	    if ( isset( $_POST['roostlogin'] ) ) {
			$roost_user = $_POST['roostuserlogin'];
			$roost_pass = $_POST['roostpasslogin'];
			$logged_in = Roost_API::login($roost_user, $roost_pass);
			if ( $logged_in['success'] === true ) {
				self::save_username( $roost_user );
				if ( count( $logged_in['apps'] ) > 1 ){
					$roost_sites = $logged_in['apps'];
				} else {
					$form_keys = array(
						"appKey" => $logged_in['apps'][0]['key'],
						"appSecret" => $logged_in['apps'][0]['secret']
					);
                    
                    $app_key = $form_keys['appKey'];
                    $app_secret = $form_keys['appSecret'];
                    
                    $roost_server_settings = Roost_API::get_server_settings( $app_key, $app_secret );	
        			$roost_stats = Roost_API::get_stats( $app_key, $app_secret );
                    $status = self::complete_login( $form_keys );
				}
			} else {
				$status = 'Please check your Email or Username and Password.';
			}
		}

	    if ( isset( $_POST['roostconfigselect'] ) ) {
			$selected_site = $_POST['roostsites'];
			$site = explode( "|", $selected_site );
			$site_key = $site[0];
			$site_secret = $site[1];
			$form_keys = array(
				"appKey" => $site_key,
				"appSecret" => $site_secret
			);

            $app_key = $form_keys['appKey'];
            $app_secret = $form_keys['appSecret'];
            
            $roost_server_settings = Roost_API::get_server_settings($app_key, $app_secret);	
        	$roost_stats = Roost_API::get_stats($app_key, $app_secret);
            $status = self::complete_login($form_keys);
		}
        
	    if ( isset( $_POST['clearkey'] ) ) {
		    $form_keys = array(
		        "appKey" => "",
		        "appSecret" => ""
		    );
		    $roost_user = '';
		    self::update_keys($form_keys);
		    self::save_username($roost_user);
		    $status = 'Roost has been disconnected.';
	    }
	    
	    if ( isset( $_POST['savesettings'] ) ) {	
            if ( isset( $_POST['autoPush'] ) ) {
                $form_data = array(
                    "autoPush" => $_POST['autoPush']
                );
                self::update_settings( $form_data );
            } else {
                $form_data = array(
                    "autoPush" => false
                );
                self::update_settings( $form_data );
            }
            
            Roost_API::save_remote_settings( $app_key, $app_secret, $roost_server_settings, $_POST );
			$roost_server_settings = Roost_API::get_server_settings( $app_key, $app_secret );	
			$roost_stats = Roost_API::get_stats( $app_key, $app_secret );
	        $status = 'Settings Saved.';
	    }
		
	    if ( isset( $_POST['manualpush'] ) ) {
	        $manual_text = $_POST['manualtext'];
	        $manual_link = $_POST['manuallink'];
			if ( $manual_text == "" || $manual_link == "" ) {
				$status = 'Your message or link can not be blank.';
            } else {
                $roost_settings = self::roost_settings();
                $app_key = $roost_settings['appKey'];
                $app_secret = $roost_settings['appSecret'];
                if ( strpos( $manual_link, 'http' ) === false ) {
                    $manual_link = 'http://' . $manual_link;
                }
                $msg_status = Roost_API::send_notification( $manual_text, $manual_link, false, $app_key, $app_secret );
                if ( $msg_status['success'] === true ) {
                    $status = 'Message Sent.';
                } else {
                    $status = 'Message failed. Please make sure you have a valid URL.';
                }  
			}
        }
	    require_once( dirname( plugin_dir_path( __FILE__ ) ) . '/layout/admin.php');		
	}

}
