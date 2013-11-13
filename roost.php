<?php
/*
Plugin Name: Roost For Bloggers
Plugin URI: http://www.roost.me/
Description: Wordpress plugin for Roost.me Web-Push. Automate Push Notifications with new posts or send manually from the dashboard.
Version: 1.1
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
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	add_filter('the_content', 'roostmyhtml');
	add_action('publish_post', 'roostme');
	register_activation_hook(__FILE__, 'roost_install');
	register_uninstall_hook(__FILE__, 'roost_uninstall');
	
	function roost_install(){
	    global $wpdb;
	    $table = $wpdb->prefix . "roostsettings";
	    $sql = "CREATE TABLE " . $table . " (
	        id INT NOT NULL AUTO_INCREMENT,
	        appkey TEXT NOT NULL,
	        appsecret TEXT NOT NULL,
	        autopush INT NOT NULL,
	        appusage INT NOT NULL,
	        custommsg TEXT NOT NULL,
	        custombartext TEXT NOT NULL,
	        usecustomtext INT NOT NULL,
	        customtext TEXT NOT NULL,
	        textposition INT NOT NULL,
	        username TEXT NOT NULL,
	        PRIMARY KEY (id)
	    );";
	    $wpdb->query($sql);
	    $sql = "SELECT * FROM " . $table . " where 1";
	    $results = $wpdb->get_results($sql);
	    if (count($results) == 0) {
	        $wpdb->query("INSERT INTO $table(appkey, appsecret, autopush, appusage, custommsg, custombartext, usecustomtext, customtext, textposition, username)
	        VALUES('', '', 1, 0, '', '', '0', '', '', '' )");
	    }
	}
	
	function roost_uninstall(){
	    global $wpdb;
	    $table = $wpdb->prefix . "roostsettings";
	    $structure = "drop table if exists $table";
	    $wpdb->query($structure);
	}
	
	function roost_admin_menu(){
	    add_menu_page(
        	"Roost.me",
	        "Roost.me",
	        "manage_options",
    	    __FILE__,
        	"roost_admin_menu_list",
	        ROOST_URL . "layout/images/roost_thumb_unselected.png"
	    );
	}
	function roost_admin_ss() {
		wp_enqueue_style( 'rooststyle', ROOST_URL . 'layout/css/rooststyle.css' );
		wp_enqueue_script( 'roostscript', ROOST_URL . 'layout/js/roostscript.js', array('jquery') );
	}
	
	add_action( 'admin_enqueue_scripts', 'roost_admin_ss' );
	
	add_action('admin_menu', 'roost_admin_menu');
	
	function roost_remoteRequest($remoteData) {
		$authCreds = '';
		if(!empty($remoteData['appkey'])) {
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
	        'method'    => 'POST',
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
			'remoteAction' => 'accounts/details',
			'appkey' => $roostUser,
			'appsecret' => $roostPass,
			'remoteContent' => json_encode($remoteContent)
		);
		return roost_remoteRequest($remoteData);		
	}
	
	function roost_saveUsername($roostUser){
	    global $wpdb;
	    $table = $wpdb->prefix . "roostsettings";
	    $sql = "Update " . $table . " set username='" . $roostUser . "'";
	    $wpdb->query($sql);
	}
	
	function roost_updateKeys($formKeys){
	    global $wpdb;
	    $table = $wpdb->prefix . "roostsettings";
	    $sql = "Update " . $table . " set appkey='" . $formKeys['Appkey'] . "'";
	    $wpdb->query($sql);
	    $sql = "Update " . $table . " set appsecret='" . $formKeys['Appsecret'] . "'";
	    $wpdb->query($sql);
	}
	
	function roost_updateSettings($formData){	
	    global $wpdb;
	    $table = $wpdb->prefix . "roostsettings";
	    $sql = "Update " . $table . " set appusage='" . $formData['appusage'] . "'";
	    $wpdb->query($sql);
		$sql = "Update " . $table . " set custombartext='" . $formData['custombartext'] . "'";
		$wpdb->query($sql);
	    $sql = "Update " . $table . " set custommsg='" . $formData['custommsg'] . "'";
	    $wpdb->query($sql);
		$sql = "Update " . $table . " set autopush='" . $formData['autopush'] . "'";
		$wpdb->query($sql);
		$sql = "Update " . $table . " set usecustomtext='" . $formData['usecustomtext'] . "'";
		$wpdb->query($sql);
		$sql = "Update " . $table . " set textposition='" . $formData['textposition'] . "'";
		$wpdb->query($sql);
		$sql = "Update " . $table . " set customtext='" . $formData['customtext'] . "'";
		$wpdb->query($sql);
	}
	
	function roostmyhtml($content){
	    global $wpdb;
	    $table = $wpdb->prefix . "roostsettings";
	    $sql = "SELECT * FROM " . $table . " where 1";
	    $results = $wpdb->get_results($sql);
	    $appkey = "";
	    $appsecret = "";
	    $appusage = "";
	    $custombartext= "";
	    $custommsg = "";
	    if (count($results) > 0) {
	        foreach ($results as $result) {
	            $appkey = $result->appkey;
	            $appsecret = $result->appsecret;
	            $appusage = $result->appusage;
	            $custombartext = $result->custombartext;
	            $custommsg = $result->custommsg;
	        }
	    }
	
	    if ($appusage != 0) {
	        if ($appusage == 1 && isset($appkey) && $appkey != null && strlen($appkey) > 0) {
					$customhtml = '<script>';
					$customhtml = $customhtml . "(function(){var bar = document.createElement('div');bar.className='roost-bar';bar.setAttribute('data-message', '".$custombartext."');document.getElementsByTagName('body')[0].insertBefore(bar,document.getElementsByTagName('body')[0].firstChild);})();";
					$customhtml = $customhtml . "</script>";
			}
	        if ($appusage == 2 && isset($appkey) && $appkey != null && strlen($appkey) > 0) {
					$customhtml = '<script>';
					$customhtml = $customhtml . "(function(){var bar = document.createElement('div');bar.className='roost-bar';bar.setAttribute('data-bottom', 'true');bar.setAttribute('data-message','". $custombartext."');document.getElementsByTagName('body')[0].insertBefore(bar,document.getElementsByTagName('body')[0].firstChild);})();";
					$customhtml = $customhtml . "</script>";
			}
	        if ($appusage == 3) {
	            $customhtml = $custommsg;
	        }
	        $content = $customhtml . $content;
	    }
		if(!is_page() && !is_single()){
			remove_filter('the_content', 'roostmyhtml');
	    }
	    return $content;
	}
	
	function roostme($post_ID){		
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		$appkey = "";
		$appsecret = "";
		$appusage = "";
		$custombartext = "";
		$autopush = "";
		$usecustomtext = "";
		$textposition = "";
		$customtext = "";
		if (count($results) > 0) {
		    foreach ($results as $result) {
		        $appkey = $result->appkey;
		        $appsecret = $result->appsecret;
		        $appusage = $result->appusage;
		        $custombartext = $result->custombartext;
		        $autopush = $result->autopush;
		        $usecustomtext = $result->usecustomtext;
		        $textposition = $result->textposition;
		        $customtext = $result->customtext;
		    }
		}
	
		if ($autopush == 1 && strlen($appkey) > 0) {
			if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
			    $siteurl = get_option('siteurl');
			    $mypost = get_post($post_ID);
				if ($usecustomtext == 1) {
				    if ($textposition == 1 ) {
				    	$alert = $customtext . ' ' . get_the_title($post_ID);
				    } else {
				    	$alert = get_the_title($post_ID) . ' ' . $customtext;	    	
				    }
			    } else {
			    	$alert = get_the_title($post_ID);
			    }
			    $url = $siteurl . "/?p=" . $post_ID;
			    roost_sendNotification($alert, $url, $appkey, $appsecret);
			}	    
		}
	}
	
	function roost_buildMsg($manualtext, $manuallink) {
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		$appkey = "";
		$appsecret = "";
		if (count($results) > 0) {
		    foreach ($results as $result) {
		        $appkey = $result->appkey;
		        $appsecret = $result->appsecret;
		    }
		}
		roost_sendNotification($manualtext, $manuallink, $appkey, $appsecret);
	}
	
	function roost_sendNotification($alert, $url, $appkey, $appsecret) {
		if(!$url){
			$remoteContent = array(
				'alert' => $alert	
			);
		} else {
			$remoteContent = array(
				'alert' => $alert,	
				'url' => $url
			);
		}
		$remoteData = array(
			'remoteAction' => 'push',
			'appkey' => $appkey,
			'appsecret' => $appsecret,
			'remoteContent' => json_encode($remoteContent)
		);
		roost_remoteRequest($remoteData);
	}
	
	function roostbtn($atts, $content = null) {
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		foreach ($results as $result) {
			$appkey = $result->appkey;
			$appusage = $result->appusage;
		}
		$segments = $atts['segments'];
		if (isset($appkey) && $appkey != null && strlen($appkey) > 0) {
			$roostButton = "<div class='roost-button' data-segments='" . $segments . "'></div>";
		}
		return $roostButton;
	}
	add_shortcode('Roost', 'roostbtn');
	
	function roostmbtn($atts, $content = null) {
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		foreach ($results as $result) {
			$appkey = $result->appkey;
			$appusage = $result->appusage;
		}
		$segments = $atts['segments'];		
		if (isset($appkey) && $appkey != null && strlen($appkey) > 0) {
			$roostButton = "<div class='roost-button-mobile' data-segments='" . $segments . "'></div>";
		}
		return $roostButton;
	}
	add_shortcode('RoostMobile', 'roostmbtn');
	
	function roost_admin_menu_list() {
	    if (isset($_POST['roostlogin'])) {
			$roostUser = $_POST['roostuserlogin'];
			$roostPass = $_POST['roostpasslogin'];
			$logginIntoRoost = json_decode(wp_remote_retrieve_body(roostLogin($roostUser, $roostPass)), true);
			if($logginIntoRoost['success'] === true) {
				roost_saveUsername($roostUser);
				if(count($logginIntoRoost['apps']) > 1){
					//Work in the handling for multiple configs
					$roostSites = $logginIntoRoost['apps'];
				} else {
					$formKeys = array(
						"Appkey" => $logginIntoRoost['apps'][0]['key'],
						"Appsecret" => $logginIntoRoost['apps'][0]['secret']
					);
					roost_updateKeys($formKeys);				
					$status = 'Logged in to Roost. You\'re Good to Go!';
				}
			} else {			
				$status = $logginIntoRoost['error'] . ' Please check your Username and Password.';
			}	
		}
		
	    if (isset($_POST['roostconfigselect'])) {
			$roostSelectedSite = $_POST['roostsites'];
			$roostSite = explode("|", $roostSelectedSite);
			$roostSiteKey = $roostSite[0];
			$roostSiteSecret = $roostSite[1];
			$formKeys = array(
				"Appkey" => $roostSiteKey,
				"Appsecret" => $roostSiteSecret
			);
			roost_updateKeys($formKeys);
			$status = 'Logged in to Roost. You\'re Good to Go!';
		}
	    
	    if (isset($_POST['clearkey'])) {
		    $formKeys = array(
		        "Appkey" => "",
		        "Appsecret" => ""
		    );
		    $roostUser = '';
		    roost_updateKeys($formKeys);
		    roost_saveUsername($roostUser);
		    $status = 'Roost has been disconnected.';
	    }
	    
	    if (isset($_POST['savesettings'])) {
	        $formData = array(
	            "appusage" => mysql_real_escape_string($_POST['appusage']),
	            "custombartext" => mysql_real_escape_string($_POST['custombartext']),
	            "custommsg" => $_POST['custommsg'],
	            "autopush" => mysql_real_escape_string($_POST['autopush']),
	            "usecustomtext" => mysql_real_escape_string($_POST['usecustomtext']),
	            "textposition" => mysql_real_escape_string($_POST['textposition']),
	            "customtext" => mysql_real_escape_string($_POST['customtext'])
	        );
	        roost_updateSettings($formData);
	        $status = 'Settings Saved.';
	    }
	
	    if (isset($_POST['manualpush'])) {
	        $manualtext = $_POST['manualtext'];
	        $manuallink = $_POST['manuallink'];
			if($manualtext == "" || $manuallink == "") {
				$status = 'Your Message or Link Can Not Be Blank.';
			} elseif (filter_var($manuallink, FILTER_VALIDATE_URL) === false){ 
				$status = 'Please Enter a Valid URL. - (Must contain "http://" and your ".com" or respective domain.';
			} else {
				roost_buildMsg($manualtext, $manuallink);
				$status = 'Message Sent.';	
			}
	    }
	    
	    if (isset($_POST['manualpush2'])) {
	        $manualtext = $_POST['manualtext2'];
			if($manualtext == "") {
				$status = 'Your Message Can Not Be Blank.';
			} else {
		        roost_buildMsg($manualtext, "");        
		        $status = 'Message Sent.';
	    	}
	    }
	    require_once('layout/admin.php');		
	}
				
	function roost_load_scripts() {
		global $wpdb;
		$table = $wpdb->prefix . "roostsettings";
		$sql = "SELECT * FROM " . $table . " where 1";
		$results = $wpdb->get_results($sql);
		if (count($results) > 0) {
		    foreach ($results as $result) {
		        $appkey = $result->appkey;
		    }
		}
		if($appkey && !is_admin()) {
			wp_enqueue_script( 'roostjs', ROOST_URL . 'layout/js/roostjs.js', array('jquery'), false, true );
			wp_localize_script( 'roostjs', 'roostjsParams', array( 'appkey' => $appkey) );
		}
	}
	add_action('wp_enqueue_scripts', 'roost_load_scripts');
	
?>