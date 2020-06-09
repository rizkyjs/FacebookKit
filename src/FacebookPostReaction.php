<?php namespace Riedayme\FacebookKit;

class FacebookPostReaction
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

	public function ReactPostByScraping($data)
	{

		$url = self::GetReactionURL($data);
		if (!$url) die("Tidak ditemukan url react");

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['header'];

		if (strpos($response, 'HTTP/2 200')) {
			$status = true;
		}else{
			$status = false;
		}
		return [
			'status' => $status,
			'id' => $data['postid'],
			'url' => "https://www.facebook.com/{$data['userid']}/posts/{$data['postid']}"
		];
	}

	public function GetReactionURL($data)
	{


		$url = "https://mbasic.facebook.com/reactions/picker/?is_permalink=1&ft_id={$data['postid']}";
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$XpathReactlionlist = $xpath->query('//li/table/tbody/tr/td/a/@href');

		if($XpathReactlionlist->length > 0) 
		{
			$reaction_data = array();
			foreach ($XpathReactlionlist as $node) 
			{
				$url = FacebookHelper::InnerHTML($node);
				$url = "https://mbasic.facebook.com".$url;

				if (!strpos($url, '/story.php')) {

					$type = self::GetReactionType($url);

					$reaction_data[$type] = html_entity_decode(trim($url));
				}
			}

			return (!empty($reaction_data[$data['type']])) ? $reaction_data[$data['type']] : $reaction_data['UNREACT'];
		}

		return false;		
	}

	public function GetReactionType($url)
	{

		$type = false;
		if (strpos($url, 'reaction_type=1&')) {
			$type = 'LIKE';
		}elseif (strpos($url, 'reaction_type=2&')) {
			$type = 'LOVE';
		}elseif (strpos($url, 'reaction_type=16&')) {
			$type = 'CARE';
		}elseif (strpos($url, 'reaction_type=4&')) {
			$type = 'HAHA';
		}elseif (strpos($url, 'reaction_type=3&')) {
			$type = 'WOW';
		}elseif (strpos($url, 'reaction_type=7&')) {
			$type = 'SAD';
		}elseif (strpos($url, 'reaction_type=8&')) {
			$type = 'ANGRY';
		}elseif (strpos($url, 'reaction_type=0&')) {
			$type = 'UNREACT';
		}

		return $type;
	}

}