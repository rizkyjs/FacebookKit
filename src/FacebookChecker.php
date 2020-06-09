<?php namespace Riedayme\FacebookKit;

class FacebookChecker
{

	public function CheckLiveCookie($cookie)
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

}