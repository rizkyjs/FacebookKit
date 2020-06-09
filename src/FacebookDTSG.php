<?php namespace Riedayme\FacebookKit;

/**
 * Handling Cookie
 */
Class FacebookDTSG
{

	public static function GetFromProfile($cookie) 
	{

		$url = 'https://mbasic.facebook.com/profile.php';

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Cookie: '.$cookie;

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$fb_dtsg = FacebookHelper::GetStringBetween($response,'<input type="hidden" name="fb_dtsg" value="','" autocomplete="off" />');

		return $fb_dtsg;
	}
}
