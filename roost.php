<?php
/*
Plugin Name: Roost Web Push
Plugin URI: http://www.roost.me/
Description: Drive traffic to your website with Safari Mavericks push notifications and Roost.
Version: 2.0
Author: Roost.me
Author URI: http://www.roost.me/
License: GPL2
*/

/*  Copyright 2013  Roost.me Team  (email : support@noticesoftware.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	
	$siteurl = get_option('siteurl');
	define('ROOST_URL', plugin_dir_url(__FILE__));
	global $roostVersion;
	$roostVersion = "2.0";

	register_activation_hook(__FILE__, 'roostInit');
	register_uninstall_hook(__FILE__, 'roostUninstall');

	add_action('admin_enqueue_scripts', 'roostAdminSS');
	add_action('admin_menu', 'roostAdminMenu');
	add_action('wp_enqueue_scripts', 'roostLoadScripts');		
	add_action('publish_post', 'roostMe');
	add_action('post_submitbox_misc_actions', 'roostOverride');

	add_shortcode('RoostBar', 'roostBar');
	add_shortcode('Roost', 'roostBtn');
	add_shortcode('RoostMobile', 'roostmBtn');

	function roostInit() {
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {		
			roostUpgrade();
		} else {
			roostInstall();
		}
	}
	
	function roostUpgrade() {
		global $wpdb;
		global $roostVersion;
		$table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		foreach($results as $result) {	
			$appKey = $result->appkey;
			$appSecret = $result->appsecret;
			$username = $result->username;
			$autoPush = $result->autopush;
			$appUsage = $result->appusage;
			$customBarText = $result->custombartext;
		}		
		
		if (!empty($appKey)) {
			$roostSettings = array(
				"appKey" => $appKey,
				"appSecret" => $appSecret,
				"username" => $username,
				"version" => $roostVersion,
				"autoPush" => $autoPush
            );
			
			add_option('roost_settings', $roostSettings);		
			
			if ($appUsage == 1) {
				$appUsage = 'TOP';
			} elseif ($appUsage == 2) {
				$appUsage = 'BOTTOM';
			} else {
				$appUsage = 'OFF';
			}
			
			$remoteContent = array(
				'roostBarSetting' => $appUsage
			);
			
			if (strlen($customBarText) > 0) {
				$remoteContent['roostBarText'] = $customBarText;
			}
			
			$remoteData = array(
				'method' => 'PUT',
				'remoteAction' => 'app',
				'appkey' => $appKey,
				'appsecret' => $appSecret,
				'remoteContent' => json_encode($remoteContent)
			);
			roostRemoteRequest($remoteData);		
		}

		$structure = "drop table if exists $table";
		$wpdb->query($structure);
		
		roostInstall();
	}
	
	function roostInstall() {
		$roostSettings = get_option('roost_settings');
		global $roostVersion;

		if (empty($roostSettings)) {
			$roostSettings = array(
				"appKey" => '',
				"appSecret" => '',
				"username" => '',
				"version" => $roostVersion,
				"autoPush" => 1
            );			
			add_option('roost_settings', $roostSettings);
		}
	}
	
	function roostUninstall(){
		delete_option('roost_settings');
	}
	
	function roostAdminMenu(){
	    add_menu_page(
        	"Roost.me",
	        "Roost.me",
	        "manage_options",
    	    __FILE__,
        	"roostAdminMenuList",
	        ROOST_URL . "layout/images/roost_thumb.png"
	    );
	}
	function roostAdminSS() {
		wp_enqueue_style( 'rooststyle', ROOST_URL . 'layout/css/rooststyle.css' );
		wp_enqueue_script( 'roostscript', ROOST_URL . 'layout/js/roostscript.js', array('jquery') );
	}
	
	function roostRemoteRequest($remoteData) {
		$authCreds = '';
		if (!empty($remoteData['appkey'])) {
			$authCreds = 'Basic ' . base64_encode( $remoteData['appkey'] .':'.$remoteData['appsecret'] );
		}
		$remoteURL = 'https://get.roost.me/api/' . $remoteData['remoteAction'];

		$headers = array(
	        'Authorization'  => $authCreds,
	        'Accept'       => 'application/json',
	        'Content-Type'   => 'application/json',
	        'Content-Length' => strlen( $remoteData['remoteContent'] )
	    );
	    
	    $remotePayload = array(
	        'method'    => $remoteData['method'],
	        'headers'   => $headers,
	        'body'      => $remoteData['remoteContent']
	    );
	    $response = wp_remote_request($remoteURL, $remotePayload);
	    return $response;
	}
		
	function roostLogin($roostUser, $roostPass){
		$remoteContent = array(
			'username' => $roostUser,
			'password' => $roostPass
		);
		$remoteData = array(
			'method' => 'POST',
			'remoteAction' => 'accounts/details',
			'appkey' => $roostUser,
			'appsecret' => $roostPass,
			'remoteContent' => json_encode($remoteContent)
		);
		return roostRemoteRequest($remoteData);		
	}
	
	function roostSaveUsername($roostUser){
		$roostSettings = get_option('roost_settings');
		$roostSettings['username'] = $roostUser;
		update_option('roost_settings', $roostSettings);
	}
	
	function roostUpdateKeys($formKeys){
		$roostSettings = get_option('roost_settings');
		$roostSettings['appKey'] = $formKeys['appKey'];
		$roostSettings['appSecret'] = $formKeys['appSecret'];
		update_option('roost_settings', $roostSettings);
	}
	
	function roostUpdateSettings($formData){	
	   	$roostSettings = get_option('roost_settings');
		$roostSettings['autoPush'] = $formData['autoPush'];
		update_option('roost_settings', $roostSettings);
	}

function roostFilterString($string) {
	     $target = str_replace('&#8220;', '&quot;', $string);
	     $target = str_replace('&#8221;', '&quot;', $string);
	     $target = str_replace('&#8216;', '&#39;', $string);
	     $target = str_replace('&#8217;', '&#39;', $string);
	     $target = str_replace('&#8211;', '-', $string);
	     return html_entity_decode($string, ENT_QUOTES);
	}

	function roostMe($post_ID){
		$roostSettings = get_option('roost_settings');
		$appKey = $roostSettings['appKey'];
		$appSecret = $roostSettings['appSecret'];
		$autoPush = $roostSettings['autoPush'];
		if (isset($_POST['roostOverride'])) {
			$roostOverride = $_POST['roostOverride'];
		}
		if ($autoPush == 1 && strlen($appKey) > 0 && empty($roostOverride)) {
			if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
			    $siteurl = get_option('siteurl');
		    	$alert = roostFilterString(get_the_title($post_ID));
			    $url = $siteurl . "/?p=" . $post_ID;
                if ( has_post_thumbnail($post_ID)) {
                    $rawImage = wp_get_attachment_image_src(get_post_thumbnail_id($post_ID));
                    $imageURL = $rawImage[0];
                } else {
                    $imageURL = false;
                }
                //roostSendNotification($alert, $url, $imageURL, $appKey, $appSecret);
			}
		}
	}
	
	function roostOverride(){
		$roostSettings = get_option('roost_settings');
		$appKey = $roostSettings['appKey'];
        $autoPush = $roostSettings['autoPush'];
		$roost_page_status = get_post_status(get_the_ID());
		if((strlen($appKey) > 1) && ($autoPush == 1) && ($roost_page_status == 'auto-draft') || ($roost_page_status == 'draft')){
		    echo '<div class="misc-pub-section misc-pub-section-last" id="roost-override">'
		         . '<label><input type="checkbox" value="1" id="roostOverrideCheckbox" name="roostOverride" /> <strong>Do NOT</strong> send notification with <strong>Roost</strong></label>'
		    .'</div>'
		    ."<script>jQuery('.save-timestamp').on('click', function() {setTimeout(function(){var text = jQuery('#timestamp').text(); text = text.slice(0, text.indexOf(' ')); if(text === 'Schedule') { jQuery('#roost-override').hide(); jQuery('#roostOverrideCheckbox').prop('checked', true);} else { jQuery('#roost-override').show(); jQuery('#roostOverrideCheckbox').prop('checked', false);}}, 250);});</script>";
		}
	}
	
	function roostSendNotification($alert, $url, $imageURL, $appKey, $appSecret) {
		$remoteContent = array(
			'alert' => $alert	
		);
		if ($url){
			$remoteContent['url'] = $url;
		}
        if ($imageURL) {
            $remoteContent['imageURL'] = $imageURL;   
        }
		$remoteData = array(
			'method' => 'POST',
			'remoteAction' => 'push',
			'appkey' => $appKey,
			'appsecret' => $appSecret,
			'remoteContent' => json_encode($remoteContent)
		);
		return roostRemoteRequest($remoteData);
	}

	function roostBar($atts, $content = null) {
		$roostSettings = get_option('roost_settings');
		$appKey = $roostSettings['appKey'];
		if (!empty($appKey) && strlen($appKey) > 0) {
			$roostBar = "<div class='roost-bar'></div>";
		} else {
            return;   
        }
		return $roostBar;
	}

	function roostBtn($atts, $content = null) {
		$roostSettings = get_option('roost_settings');
		$appKey = $roostSettings['appKey'];
		if (!empty($appKey) && strlen($appKey) > 0) {
			$roostButton = "<div class='roost-button'></div>";
		} else {
            return;   
        }
		return $roostButton;
	}
	
	function roostmBtn($atts, $content = null) {
		$roostSettings = get_option('roost_settings');
		$appKey = $roostSettings['appKey'];
		if (!empty($appKey) && strlen($appKey) > 0) {
			$roostButton = "<div class='roost-button-mobile'></div>";
		} else {
            return;
        }
		return $roostButton;
	}
	
    function getRoostServerSettings($appKey, $appSecret) {
        $remoteData = array(
            'method' => 'POST',
            'remoteAction' => 'app',
            'appkey' => $appKey,
            'appsecret' => $appSecret,
            'remoteContent' => ''
        );
		return json_decode(wp_remote_retrieve_body(roostRemoteRequest($remoteData)), true);	
    }

    function getRoostStats($appKey, $appSecret) {
        $remoteData = array (
            'method' => 'POST',
            'remoteAction' => 'stats/app',
            'appkey' => $appKey,
            'appsecret' => $appSecret,
            'remoteContent' => ''
        );
        return json_decode(wp_remote_retrieve_body(roostRemoteRequest($remoteData)), true);
    }
    
	function roostAdminMenuList() {
		$roostSettings = get_option('roost_settings');
		if (empty($roostSettings)) {
			roostUpgrade();
        }        
		$appKey = $roostSettings['appKey'];
		$appSecret = $roostSettings['appSecret'];
		if (!empty($appKey)) {
			$roostServerSettings = getRoostServerSettings($appKey, $appSecret);	
			$roostStats = getRoostStats($appKey, $appSecret);
		}

	    if (isset($_POST['roostlogin'])) {
			$roostUser = $_POST['roostuserlogin'];
			$roostPass = $_POST['roostpasslogin'];
			$logginIntoRoost = json_decode(wp_remote_retrieve_body(roostLogin($roostUser, $roostPass)), true);
			if ($logginIntoRoost['success'] === true) {
				roostSaveUsername($roostUser);
				if (count($logginIntoRoost['apps']) > 1){
					$roostSites = $logginIntoRoost['apps'];
				} else {
					$formKeys = array(
						"appKey" => $logginIntoRoost['apps'][0]['key'],
						"appSecret" => $logginIntoRoost['apps'][0]['secret']
					);
					roostUpdateKeys($formKeys);				
					$status = '<span class="roost-os-bold">Welcome to Roost!</span> The plugin is up and running and visitors to your site using Safari on OSX Mavericks are currently being prompted to subscribe for push notifications. Once you have subscribers you\'ll be able see recent activity, all-time stats about your subscribers, and send manual push notifications to your subscribers.';
				}
			} else {			
				$status = 'Please check your Email or Username and Password.';
			}	
		}
		
	    if (isset($_POST['roostconfigselect'])) {
			$roostSelectedSite = $_POST['roostsites'];
			$roostSite = explode("|", $roostSelectedSite);
			$roostSiteKey = $roostSite[0];
			$roostSiteSecret = $roostSite[1];
			$formKeys = array(
				"appKey" => $roostSiteKey,
				"appSecret" => $roostSiteSecret
			);
			roostUpdateKeys($formKeys);
					$status = '<span class="roost-os-bold">Welcome to Roost!</span> The plugin is up and running and visitors to your site using Safari on OSX Mavericks are currently being prompted to subscribe for push notifications. Once you have subscribers you\'ll be able see recent activity, all-time stats about your subscribers, and send manual push notifications to your subscribers.';
		}
	    
	    if (isset($_POST['clearkey'])) {
		    $formKeys = array(
		        "appKey" => "",
		        "appSecret" => ""
		    );
		    $roostUser = '';
		    roostUpdateKeys($formKeys);
		    roostSaveUsername($roostUser);
		    $status = 'Roost has been disconnected.';
	    }
	    
	    if (isset($_POST['savesettings'])) {
	        $formData = array(
	            "autoPush" => $_POST['autoPush']
	        );
	        roostUpdateSettings($formData);
	
	        if (isset($_POST['mobilePush'])) {
				if ($roostServerSettings['roostBarSetting'] != "TOP" || $roostServerSettings['roostBarSetting'] != "BOTTOM") {
					$remoteContent['roostBarSetting'] = "TOP";
				}
			} else {
				if ($roostServerSettings['roostBarSetting'] != "OFF") {
					$remoteContent['roostBarSetting'] = "OFF";
				}
			}
			
			if (isset($_POST['autoUpdate'])) {
				if ($roostServerSettings['autoUpdate'] != true) {
					$remoteContent['autoUpdate'] = true;
				}
			} else {
				if ($roostServerSettings['autoUpdate'] != false) {
					$remoteContent['autoUpdate'] = false;
				}
			}
            
		    if(!empty($remoteContent)) {
		        $remoteData = array(
		        	'method' => 'PUT',
		        	'remoteAction' => 'app',
		        	'appkey' => $appKey,
		        	'appsecret' => $appSecret,
		        	'remoteContent' => json_encode($remoteContent)
		        );
		        roostRemoteRequest($remoteData);
		    }
	       	
			$roostServerSettings = getRoostServerSettings($appKey, $appSecret);	
			$roostStats = getRoostStats($appKey, $appSecret);
            
	        $status = 'Settings Saved.';
	    }
		
	    if (isset($_POST['manualpush'])) {
	        $manualText = $_POST['manualtext'];
	        $manualLink = $_POST['manuallink'];
			if ($manualText == "" || $manualLink == "") {
				$status = 'Your message or link can not be blank.';
            } else {
                $roostSettings = get_option('roost_settings');
                $appKey = $roostSettings['appKey'];
                $appSecret = $roostSettings['appSecret'];
                if (strpos($manualLink, 'http') === false) {
                    $manualLink = 'http://' . $manualLink;
                }
                $msgStatus = json_decode(wp_remote_retrieve_body(roostSendNotification($manualText, $manualLink, false, $appKey, $appSecret)), true);
                if ($msgStatus['success'] === true) {
                    $status = 'Message Sent.';
                } else {
                    $status = 'Message failed. Please make sure you have a vaild URL.';
                }  
			}
	    }

	    require_once('layout/admin.php');		
	}
				
	function roostLoadScripts() {
		$roostSettings = get_option('roost_settings');
		$appKey = $roostSettings['appKey'];
		if ($appKey && !is_admin()) {
			wp_enqueue_script( 'roostjs', ROOST_URL . 'layout/js/roostjs.js', array('jquery'), false, true );
			wp_localize_script( 'roostjs', 'roostjsParams', array( 'appkey' => $appKey) );
		}
		if(is_admin()){
			wp_enqueue_script( 'roostGoogleFont', ROOST_URL . 'layout/js/roostGoogleFont.js', '', false, false );		
		}
	}
?>