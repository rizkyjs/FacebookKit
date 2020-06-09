<?php namespace Riedayme\FacebookKit;

class FacebookAuth
{

	public function AuthUsingCookie($cookie)
	{

		$userid = FacebookCookie::GetUIDCookie($cookie);
		if (empty($userid)) die("cookie tidak valid");

		$access_token = FacebookAccessToken::GetTouchToken($cookie);
		$userinfo = FacebookResourceUser::GetUserInfoByToken($access_token);

		return [
		'userid' => $userid,
		'username' => $userinfo['username'], 
		'photo' => $userinfo['photo'],
		'cookie' => $cookie,
		'access_token' => $access_token
		];

	}	

	public function AuthLoginByMobile($username,$password)
	{



		$url = 'https://mbasic.facebook.com/login.php';

		$postdata = "email={$username}&pass={$password}";

		$useragent = FacebookUserAgent::Get('Windows');

		$login = FacebookHelper::curl($url,$postdata,false,false,$useragent);

		$response = $login['header'];

		$cookie = FacebookCookie::ReadCookie($response);

		echo "<pre>";
		echo $response;
		echo "</pre>";

		echo $login['body'];

		if (strpos($response, 'checkpoint')) {
			die("Akun terkena checkpoint");
		}elseif (!strpos($cookie, 'c_user=')) {
			die("Username atau password salah");
		}else{

			if (strpos($cookie, 'c_user=')) {

				echo $cookie;
				exit;

				return [
				'username' => $username,
				'cookie' => $cookie
				];
			}else{				
				die("error tidak diketahui");
			}

		}			

	}

}