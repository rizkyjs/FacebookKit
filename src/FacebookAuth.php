<?php namespace Riedayme\FacebookKit;

class FacebookAuth
{

	public static function AuthUsingCookie($cookie)
	{

		$userid = FacebookCookie::GetUIDCookie($cookie);
		if (empty($userid)) die("cookie tidak valid");

		$accesstoken = FacebookAccessToken::GetTouchToken($cookie);
		$userinfo = FacebookResourceUser::GetUserInfoByToken($accesstoken);

		return [
		'userid' => $userid,
		'username' => $userinfo['username'], 
		'photo' => $userinfo['photo'],
		'cookie' => $cookie,
		];

	}	

	public function AuthLoginByMobile($username,$password)
	{



		$url = 'https://mbasic.facebook.com/login.php';

		$postdata = "email={$username}&pass={$password}";

		$useragent = FacebookUserAgent::Linux();

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