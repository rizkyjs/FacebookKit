<?php namespace Riedayme\FacebookKit;

class FacebookFeedTimeLine
{

	public $cookie;	
	public $access_token;	

	public function Auth($data,$type = false) 
	{


		if ($type == 'cookie') {
			
			$process = FacebookAuth::AuthUsingCookie($data);

			$this->cookie = $process['cookie'];
			$this->access_token = $process['access_token'];			
		}else{
			die("auth type not selected");
		}
	}	

	public function GetTimeLineByToken($limit = 1)
	{

		$url = "https://graph.facebook.com/me/home?fields=id&limit={$limit}&access_token={$this->access_token}";

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');

		$access = FacebookHelper::curl($url,false,$headers);

		$response = json_decode($access['body'],true);

		$extract = array();
		foreach ($response['data'] as $post) {

			$extractid = explode('_', $post['id']);
			$userid = $extractid[0];
			$postid = $extractid[1];

			$extract[] = [
				'userid' => $userid,
				'postid' => $postid
			];
		}

		return $extract;
	}

}