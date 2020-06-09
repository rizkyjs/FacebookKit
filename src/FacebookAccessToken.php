<?php namespace Riedayme\FacebookKit;

class FacebookAccessToken
{

	public function GetTouchToken($cookie)
	{

		$url = 'https://m.facebook.com/composer/ocelot/async_loader/?publisher=feed';

		$headers = array();
		$headers[] = 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
		$headers[] = 'Cookie: '.$cookie;		

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$accesstoken = FacebookHelper::GetStringBetween($response,'accessToken\":\"','\",\"useLocalFilePreview\"');

		return $accesstoken;
	}

}