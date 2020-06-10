<?php namespace Riedayme\FacebookKit;

class FacebookGroupList
{

	public $cookie;	

	public function Auth($data,$type = false) 
	{


		if ($type == 'cookie') {
			
			$process = FacebookAuth::AuthUsingCookie($data);

			$this->cookie = $process['cookie'];
		}else{
			die("auth type not selected");
		}
	}

	public function GetGroupListByScraping()
	{


		$url = "https://mbasic.facebook.com/groups/?seemore&refid=27";
		
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::Get('Windows');
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$GroupList = $xpath->query('/html/body/div/div/div[2]/div/table');

		$extract = array();
		if($GroupList->length > 0) 
		{
			foreach ($GroupList as $node) 
			{
				$GroupLink = $xpath->query('//td[@class="u"]/a', $node);

				if($GroupLink->length > 0) 
				{
					foreach ($GroupLink as $link) {

						$id = $link->getAttribute('href');
						$id = FacebookHelper::GetStringBetween($id,'/groups/','?refid=27');
						$name = $link->nodeValue;
						$url = "https://facebook.com/groups/{$id}";

						$extract[] = [
						'id' => $id,
						'name' => $name,
						'url' => $url
						];
					}
				}
			}
		}

		if (count($extract) > 0) return $extract;

		return false;		
	}
}