<?php

class Roost {

    public function __construct() {
        $this->add_actions();
    }
    
    public static function init() {
		self::install();
	}	

    public static $roost_version = '2.1.1';
        
    public static function site_url() {
        return get_option( 'siteurl' );
    }
    
    public static function registration_url() {
        $tld = 'https://get.roost.me/signup?returnURL=';
        $admin_path = admin_url('admin.php?page=roost-web-push');
        $url = $tld . urlencode( $admin_path . '&source=wpplugin' );
        return $url;
    }

    public static function login_url( $sso ) {
        $tld = 'https://get.roost.me/login?returnURL=';
        $admin_path = admin_url('admin.php?page=roost-web-push');
        $url = $tld . urlencode( $admin_path );
        $url = $url . '&oauth=' . $sso;
        return $url;
    }
    
    public static function roost_settings() {
        return get_option('roost_settings');   
    }
    
	public static function install() {
		$roost_settings = self::roost_settings();
        
		if ( empty( $roost_settings ) ) {
			$roost_settings = array(
				'appKey' => '',
				'appSecret' => '',
				'version' => self::$roost_version,
				'autoPush' => 0,
                'bbPress' => 1,
            );			
			add_option('roost_settings', $roost_settings);
		}
        if( self::$roost_version !== $roost_settings['version'] ) {
            self::update( $roost_settings );
        }
        self::roost_activated();
	}

    public static function update( $roost_settings ) {
        if( "2.0.5" === $roost_settings['version'] ) {
            $roost_settings['bbPress'] = 1;
        }
        $roost_settings['version'] = self::$roost_version;
        update_option('roost_settings', $roost_settings);
    }
    
    public static function roost_activated() {
        add_option('roost_do_redirect', true);
    }

    public function activate_redirect() {
        if ( get_option('roost_do_redirect', false) ) {
            delete_option('roost_do_redirect');
            if( !isset( $_GET['activate-multi'] ) ){
                wp_redirect( admin_url( 'admin.php?page=roost-web-push' ) );
                exit;
            }
        }
    }

	public static function uninstall(){
        delete_option('roost_settings');
        delete_post_meta_by_key( 'roostOverride' );
        delete_post_meta_by_key( 'roostForce' );
        delete_post_meta_by_key( 'roost_bbp_subscription' );
	}

    public function add_actions() {
        add_action( 'transition_post_status', array( $this, 'build_note' ), 10, 3 );
        add_action( 'post_submitbox_misc_actions', array( $this, 'note_override' ) );
        add_action( 'wp_head', array( $this, 'byline' ), 1 );
        add_action( 'save_post', array( $this, 'save_post_meta_roost' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
        add_filter( 'clean_url', array( $this, 'add_async' ), 2, 1 );
        add_action( 'wp_ajax_graph_reload', array( $this, 'graph_reload' ) );
        add_action( 'wp_ajax_nopriv_graph_reload', array( $this, 'graph_reload' ) );
        
        if ( is_admin() ) {
            add_filter( 'plugin_action_links_roost-for-bloggers/roost.php', array( $this, 'add_action_links' ) );
            add_action( 'admin_init', array( $this, 'activate_redirect' ) );
            add_action( 'admin_notices', array( $this, 'setup_notice' ) );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu_add' ) );
        }
    }

    public function add_action_links ( $links ) {
        $rlink = array(
            '<a href="' . admin_url( 'admin.php?page=roost-web-push' ) . '">Go to Plugin</a>',
        );
        return array_merge( $rlink, $links );
    }
    
    public function load_scripts() {
		$roost_settings = self::roost_settings();
		$app_key = $roost_settings['appKey'];
		if ($app_key && !is_admin()) {
            wp_enqueue_script( 'roostjs', '//get.roost.me/js/roost.js#async', '', self::$roost_version, false );
            wp_enqueue_script( 'roostvars', ROOST_URL . 'layout/js/roost_vars.js#async', array( 'roostjs' ), self::$roost_version, false );
            wp_localize_script( 'roostvars', 'pushNotificationsByRoostMe', array( 'appkey' => $app_key) );
		}
	}

    public function add_async( $url ) {
        if ( false === strpos( $url, '#async' ) ) {
            return $url;
        } else if ( is_admin() ) {
            return str_replace( '#async', '', $url );
        } else {
            return str_replace( '#async', '', $url )."' async data-roost='true";
        }
    }

    public function byline() {
        $byline = "<!-- Push notifications for this website enabled by Roost. Support for Safari, Firefox, and Chrome Browser Push. (v ". self::$roost_version .") - http://roost.me/ -->";
        echo "\n${byline}\n";
    }

    public static function setup_notice() {
        global $hook_suffix;
        $roost_page = 'toplevel_page_roost-web-push';
        
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
                <a href="<?php echo( admin_url( 'admin.php?page=roost-web-push' ) ); ?>" id="roostNoticeCTA" >
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
        	'Roost Web Push',
	        'Roost Web Push',
	        'manage_options',
            'roost-web-push',
        	array( __CLASS__, 'admin_menu_page' ),
	        ROOST_URL . 'layout/images/roost_thumb.png'
	    );
	}    

	public static function admin_scripts() {
        wp_enqueue_style( 'rooststyle', ROOST_URL . 'layout/css/rooststyle.css', '', self::$roost_version );
        wp_enqueue_script( 'roostGoogleFont', ROOST_URL . 'layout/js/roostGoogleFont.js', '', self::$roost_version, false );
        $roost_settings = self::roost_settings();
        $app_key = $roost_settings['appKey'];
        if ( !empty( $app_key ) ) {
            wp_enqueue_style( 'morrisstyle', 'http://cdn.oesmith.co.uk/morris-0.4.3.min.css', '', self::$roost_version );
            wp_enqueue_script( 'morrisscript', 'http://cdn.oesmith.co.uk/morris-0.4.3.min.js', array('jquery', 'raphael'), self::$roost_version );
            wp_enqueue_script( 'raphael', '//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js', array('jquery'), self::$roost_version );        
            wp_enqueue_script( 'roostscript', ROOST_URL . 'layout/js/roostscript.js', array('jquery'), self::$roost_version, true );
        }
    }
	
	public static function update_keys( $form_keys ){
		$roost_settings = self::roost_settings();
		$roost_settings['appKey'] = $form_keys['appKey'];
		$roost_settings['appSecret'] = $form_keys['appSecret'];
		update_option('roost_settings', $roost_settings);
	}
    
	public static function update_settings($form_data){	
		$roost_settings = self::roost_settings();
		$roost_settings['autoPush'] = $form_data['autoPush'];
		$roost_settings['bbPress'] = $form_data['bbPress'];
		update_option('roost_settings', $roost_settings);
	}

    public function save_post_meta_roost( $post_id ) {
        if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || empty( $_POST['hiddenRooster'] ) ) {
            return false;
        } else {
            $no_note = get_post_meta( $post_id, 'roostOverride', true );
            $send_note = get_post_meta( $post_id, 'roostForce', true );
            if ( isset( $_POST['roostOverride'] ) && !$no_note ) {
                $override_setting = $_POST['roostOverride'];
                add_post_meta($post_id, 'roostOverride', $override_setting, true);
            } elseif ( !isset( $_POST['roostOverride'] ) && $no_note ) {
                delete_post_meta( $post_id, 'roostOverride' );
            }
            if ( isset( $_POST['roostForce'] ) && !$send_note ) {
                $override_setting = $_POST['roostForce'];
                add_post_meta( $post_id, 'roostForce', $override_setting, true );
            } elseif ( !isset( $_POST['roostForce'] ) && $send_note ) {
                delete_post_meta( $post_id, 'roostForce' );
            }
        }
    }

    public static function filter_string( $string ) {
        $string = str_replace( '&#8220;', '&quot;', $string );
        $string = str_replace( '&#8221;', '&quot;', $string );
        $string = str_replace( '&#8216;', '&#39;', $string );
        $string = str_replace( '&#8217;', '&#39;', $string );
        $string = str_replace( '&#8211;', '-', $string );
        $string = str_replace( '&#8212;', '-', $string );
        $string = str_replace( '&#8242;', '&#39;', $string );
        $string = str_replace( '&#8230;', '...', $string );
        $string = str_replace( '&prime;', '&#39;', $string );
        return html_entity_decode( $string, ENT_QUOTES );
    }

    public function build_note( $new_status, $old_status, $post ) {
		if ( $new_status != $old_status && !empty( $post ) ) {
		    $post_type = get_post_type( $post );
		    if ( 'post' === $post_type && 'publish' === $new_status ) {
				$post_id = $post->ID;
				$roost_settings = self::roost_settings();
				$app_key = $roost_settings['appKey'];
				$app_secret = $roost_settings['appSecret'];
				$auto_push = $roost_settings['autoPush'];
                
				if ( !empty( $app_key ) ) {	
					if ( ( 'publish' === $new_status && 'future' === $old_status ) || empty( $_POST['hiddenRooster'] ) ) {
						$override = get_post_meta( $post_id, 'roostOverride', true );
                        $send_note = get_post_meta( $post_id, 'roostForce', true );
					} else {
                        if ( isset( $_POST['roostOverride'] ) ) {
                            $override = $_POST['roostOverride'];
                        }
                        if( isset( $_POST['roostForce'] ) ) {
                            $send_note = $_POST['roostForce'];
                        }
					}
                }
                if ( ( 1 == $auto_push || !empty( $send_note ) ) && !empty( $app_key ) ) {
					if ( empty( $override ) ) {
						$alert = get_the_title( $post_id );
						$url = wp_get_shortlink( $post_id );
						if ( has_post_thumbnail($post_id)) {
						    $raw_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id));
						    $image_url = $raw_image[0];
						} else {
						    $image_url = null;
						}
						Roost_API::send_notification($alert, $url, $image_url, $app_key, $app_secret, null );
					}
				}
			}
        }
    }

	public function note_override(){
        global $post;
        if ( 'post' === $post->post_type ) {
            if ( 'publish' == $post->post_status ) {
                $check_hidden = true;   
            }          
            $roost_settings = self::roost_settings();
            $app_key = $roost_settings['appKey'];
            $auto_push = $roost_settings['autoPush'];
            if ( !empty( $app_key ) ) {
                printf('<div class="misc-pub-section misc-pub-section-last" id="roost-post-checkboxes" %s >', ( isset( $check_hidden ) ) ? "style='display:none;'":"" );
                $pid = get_the_ID();
                if( 1 == $auto_push ) {
                    $checked = get_post_meta($pid, 'roostOverride', true);
                    printf('<label><input type="checkbox" value="1" id="roost-override-checkbox" name="roostOverride" %s />', ( !empty( $checked ) ) ? "checked":"" );
                    echo '<strong>Do NOT</strong> send notification with <strong>Roost</strong></label>';
                } else {
                    $checked = get_post_meta($pid, 'roostForce', true);
                    printf('<label><input type="checkbox" value="1" id="roost-forced-checkbox" name="roostForce" %s />', ( !empty( $checked ) ) ? "checked":"" );
                    echo '<strong>Send</strong> notification with <strong>Roost</strong></label>';
                }
                echo '<input type="hidden" name="hiddenRooster" value="true" />';
                echo '</div>';
            }
        }
	}
	
    public static function complete_login( $logged_in, $site ) {
        if ( !empty( $logged_in ) ) {
            if ( true === $logged_in['success'] ) {
                if ( count( $logged_in['apps'] ) > 1 ){
                    $roost_sites = $logged_in['apps'];
                    return $roost_sites;
                } else {
                    $form_keys = array(
                        'appKey' => $logged_in['apps'][0]['key'],
                        'appSecret' => $logged_in['apps'][0]['secret'],
                    );
                }
            }
        } elseif ( !empty( $site ) ) {
            $site_key = $site[0];
            $site_secret = $site[1];
            $form_keys = array(
                'appKey' => $site_key,
                'appSecret' => $site_secret,
            );
        }
    
        $response = array();

        if ( !empty( $form_keys ) ) {
            self::update_keys( $form_keys );
            $response['status'] = '<span class="roost-os-bold">Welcome to Roost!</span> The plugin is up and running and visitors to your site using Safari on OS X Mavericks are currently being prompted to subscribe for push notifications. Once you have subscribers you\'ll be able see recent activity, all-time stats, and send manual push notifications. If you have questions or need support, just email us at <a href="mailto:support@roost.me" target="_blank">support@roost.me</a>.';
            $response['server_settings'] = Roost_API::get_server_settings( $form_keys['appKey'], $form_keys['appSecret'] );	
            $response['stats'] = Roost_API::get_stats( $form_keys['appKey'], $form_keys['appSecret'] );
            self::admin_scripts();
        } else {
            $response['status'] = 'Please check your Email or Username and Password.';
            $response['stats'] = null;
            $response['server_settings'] = null;
        }
        return $response;
    }

    public function graph_reload() {
        $roost_settings = self::roost_settings();
        $app_key = $roost_settings['appKey'];
        $app_secret = $roost_settings['appSecret'];
        $type = $_POST['type'];
        $range = $_POST['range'];
        $value = $_POST['value'];
        $time_offset = $_POST['offset'];
        $roost_graph_data = Roost_API::get_graph_data( $app_key, $app_secret, $type, $range, $value, $time_offset );
        $roost_graph_data = json_encode( $roost_graph_data );
        echo $roost_graph_data;
        die();                
    }
    
	public static function admin_menu_page() {
        $roost_settings = self::roost_settings();

        if ( empty( $roost_settings ) ) {
            self::install();
        } else {
            $app_key = $roost_settings['appKey'];
            $app_secret = $roost_settings['appSecret'];
        }
        
        if ( !empty( $app_key ) && empty( $roost_server_settings ) ) {
            $roost_server_settings = Roost_API::get_server_settings( $app_key, $app_secret );	
            $roost_stats = Roost_API::get_stats( $app_key, $app_secret );
        }

        if ( empty( $app_key ) && isset( $_GET['roost_token'] ) ) {
            $roost_token = $_GET['roost_token'];
            $roost_token = urldecode($roost_token);
            $logged_in = Roost_API::login( null, null, $roost_token );
            $response = self::complete_login( $logged_in, null );
            $status = $response['status'];
            $roost_server_settings = $response['server_settings'];	
            $roost_stats = $response['stats'];
        }
        
	    if ( isset( $_POST['roostlogin'] ) ) {
            $roost_user = $_POST['roostuserlogin'];
            $roost_pass = $_POST['roostpasslogin'];
            $logged_in = Roost_API::login( $roost_user, $roost_pass, null );
            $response = self::complete_login( $logged_in, null );
            if( empty( $response['status'] ) ) {
                $roost_sites = $response;
            } else {
                $status = $response['status'];
                $roost_server_settings = $response['server_settings'];	
                $roost_stats = $response['stats'];
            }
		}

	    if ( isset( $_POST['roostconfigselect'] ) ) {
            $selected_site = $_POST['roostsites'];
            $site = explode( '|', $selected_site );
            $response = self::complete_login( null, $site );
            $status = $response['status'];
            $roost_server_settings = $response['server_settings'];	
            $roost_stats = $response['stats'];
		}
        
	    if ( isset( $_POST['clearkey'] ) ) {
            $form_keys = array(
                'appKey' => '',
                'appSecret' => '',
            );
            self::update_keys( $form_keys );
            wp_dequeue_script( 'roostscript' );
            $status = 'Roost has been disconnected.';
	    }

	    if ( isset( $_POST['savesettings'] ) ) {	
            $autoPush = false;
            $bbPress = false;
            if ( isset( $_POST['autoPush'] ) ) {
                $autoPush = true;
            }
            if ( isset( $_POST['bbPress'] ) ) {
                $bbPress = true;
            }
            
            $form_data = array(
                'autoPush' => $autoPush,
                'bbPress' => $bbPress,
            );
            self::update_settings( $form_data );

            Roost_API::save_remote_settings( $app_key, $app_secret, $roost_server_settings, $_POST );
            $roost_server_settings = Roost_API::get_server_settings( $app_key, $app_secret );	
            $roost_stats = Roost_API::get_stats( $app_key, $app_secret );
	        $status = 'Settings Saved.';
	    }
		
	    if ( isset( $_POST['manualpush'] ) ) {
	        $manual_text = $_POST['manualtext'];
	        $manual_link = $_POST['manuallink'];
            if ( '' == $manual_text || '' == $manual_link ) {
                $status = 'Your message or link can not be blank.';
            } else {
                $roost_settings = self::roost_settings();
                $app_key = $roost_settings['appKey'];
                $app_secret = $roost_settings['appSecret'];
                if ( false === strpos( $manual_link, 'http' ) ) {
                    $manual_link = 'http://' . $manual_link;
                }
                $msg_status = Roost_API::send_notification( $manual_text, $manual_link, null, $app_key, $app_secret, null );
                if ( true === $msg_status['success'] ) {
                    $status = 'Message Sent.';
                } else {
                    $status = 'Message failed. Please make sure you have a valid URL.';
                }  
			}
        }
	    require_once( dirname( plugin_dir_path( __FILE__ ) ) . '/layout/admin.php');		
	}
}
