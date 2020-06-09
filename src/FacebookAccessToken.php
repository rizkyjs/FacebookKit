<?php namespace Riedayme\FacebookKit;

class FacebookAccessToken
{

	public function GetTouchToken($cookie)
	{

		$url = 'https://m.facebook.com/composer/ocelot/async_loader/?publisher=feed';

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Cookie: '.$cookie;		

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$accesstoken = FacebookHelper::GetStringBetween($response,'accessToken\":\"','\",\"useLocalFilePreview\"');

		return $accesstoken;
	}

}