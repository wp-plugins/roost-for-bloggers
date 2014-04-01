<?php

class Roost_API {

	public static function roost_remote_request($remote_data) {
		$authCreds = '';
		if (!empty($remote_data['appkey'])) {
			$authCreds = 'Basic ' . base64_encode( $remote_data['appkey'] .':'.$remote_data['appsecret'] );
		}
		$remote_url = 'https://get.roost.me/api/' . $remote_data['remoteAction'];

		$headers = array(
	        'Authorization'  => $authCreds,
	        'Accept'       => 'application/json',
	        'Content-Type'   => 'application/json',
	        'Content-Length' => strlen( $remote_data['remoteContent'] )
	    );
	    
	    $remote_payload = array(
	        'method'    => $remote_data['method'],
	        'headers'   => $headers,
	        'body'      => $remote_data['remoteContent']
	    );
	    $response = wp_remote_request($remote_url, $remote_payload);
	    return $response;
	}

    public static function decode_data($remote_data) {
        $xfer = self::roost_remote_request($remote_data);
        $nxfer = wp_remote_retrieve_body($xfer);
        $lxfer = json_decode($nxfer, true); 
        return $lxfer;
    }
    
    public static function login($roost_user, $roost_pass){
		$remote_content = array(
			'username' => $roost_user,
			'password' => $roost_pass
		);
		$remote_data = array(
			'method' => 'POST',
			'remoteAction' => 'accounts/details',
			'appkey' => $roost_user,
			'appsecret' => $roost_pass,
			'remoteContent' => json_encode($remote_content)
		);
        $response = self::decode_data($remote_data);
        return $response;       
	}

    public static function get_server_settings($appKey, $appSecret) {
        $remote_data = array(
            'method' => 'POST',
            'remoteAction' => 'app',
            'appkey' => $appKey,
            'appsecret' => $appSecret,
            'remoteContent' => ''
        );
        $response = self::decode_data($remote_data);
		return $response;	
    }

    public static function get_stats($app_key, $app_secret) {
        $remote_data = array (
            'method' => 'POST',
            'remoteAction' => 'stats/app',
            'appkey' => $app_key,
            'appsecret' => $app_secret,
            'remoteContent' => ''
        );
        $response = self::decode_data($remote_data);
        return $response;
    }

    public static function save_remote_settings($app_key, $app_secret, $roost_server_settings, $POST) {
        if (isset($POST['mobilePush'])) {
            if ($roost_server_settings['roostBarSetting'] != "TOP" || $roost_server_settings['roostBarSetting'] != "BOTTOM") {
                $remote_content['roostBarSetting'] = "TOP";
            }
        } else {
            if ($roost_server_settings['roostBarSetting'] != "OFF") {
                $remote_content['roostBarSetting'] = "OFF";
            }
        }

        if (isset($POST['autoUpdate'])) {
            if ($roost_server_settings['autoUpdate'] != true) {
                $remote_content['autoUpdate'] = true;
            }
        } else {
            if ($roost_server_settings['autoUpdate'] != false) {
                $remote_content['autoUpdate'] = false;
            }
        }

        if(!empty($remote_content)) {
            $remote_data = array(
                'method' => 'PUT',
                'remoteAction' => 'app',
                'appkey' => $app_key,
                'appsecret' => $app_secret,
                'remoteContent' => json_encode($remote_content)
            );
            self::roost_remote_request($remote_data);
        }
    }
    
    public static function send_notification($alert, $url, $image_url, $app_key, $app_secret) {
        $alert = Roost::filter_string($alert);
        $remote_content = array(
			'alert' => $alert	
		);
		if ($url){
			$remote_content['url'] = $url;
		}
        if ($image_url) {
            $remote_content['imageURL'] = $image_url;   
        }
		$remote_data = array(
			'method' => 'POST',
			'remoteAction' => 'push',
			'appkey' => $app_key,
			'appsecret' => $app_secret,
			'remoteContent' => json_encode($remote_content)
		);
        $response = self::decode_data($remote_data);
        return $response;
	}

}
