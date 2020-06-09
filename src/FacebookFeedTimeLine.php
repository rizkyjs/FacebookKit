<?php namespace Riedayme\FacebookKit;

class FacebookFeedTimeLine
{

	public static function GetTimeLineByToken($token)
	{

		$url = "https://graph.facebook.com/me?fields=name,picture&access_token={$token}";

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Linux();

		$access = FacebookHelper::curl($url,false,$headers);

		$response = json_decode($access['body'],true);

		return [
		'id' => $response['id'],
		'username' => $response['name'],
		'photo' => $response['picture']['data']['url']
		];
	}

}