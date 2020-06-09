<?php  
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookChecker;
use Riedayme\FacebookKit\FacebookFeedTimeLine;
use Riedayme\FacebookKit\FacebookPostReaction;

Class InputHelper
{
	public function GetInputCookie($data = false) {

		if ($data) return $data;

		echo "Masukan Cookie : ".PHP_EOL;

		return trim(fgets(STDIN));
	}

	public function GetInputLimit($data = false) {

		if ($data) return $data;

		echo "Masukan Limit Feed : ".PHP_EOL;

		return trim(fgets(STDIN));
	}

	public function GetInputReact($data = false) {

		if ($data) return $data;

		echo "Masukan Reaksi yang dikirim : [LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, UNREACT]".PHP_EOL;

		return trim(fgets(STDIN));
	}	
}

Class FacebookAutoReactTimeLine
{

	public $cookie;
	public $username;
	public $limit;
	public $react;

	public function Auth($data) 
	{

		echo "Masuk Akun <-------------".PHP_EOL;
		echo "Check Login <-------------".PHP_EOL;

		$auth = new FacebookAuth();
		$results =$auth->AuthUsingCookie($data['cookie']);

		$this->username = $results['username'];		
		$this->cookie = $results['cookie'];
		$this->limit = $data['limit'];
		$this->react = $data['react'];		
	}

	public function GetFeed()
	{

		echo "Membaca Feed Timeline <-------------".PHP_EOL;

		$Feed = new FacebookFeedTimeLine();
		$Feed->Auth($this->cookie,'cookie');

		$results =$Feed->GetTimeLineByToken($this->limit);

		echo "Berhasil Mendapatkan Feed <-------------".PHP_EOL;

		return self::SyncPost($results);
	}

	public function LikePost($datapost)
	{
		echo "Proses React Post {$datapost['userid']}||{$datapost['postid']} <-------------".PHP_EOL;

		$react = new FacebookPostReaction();
		$react->Auth($this->cookie,'cookie');
		$process = $react->ReactPostByScraping([
			'userid' => $datapost['userid'], 
			'postid' => $datapost['postid'], 
			'type' => $this->react
		]);

		if ($process != false) {
			echo "Sukses React Post {$process['url']} <-------------".PHP_EOL;
			self::SaveLog($this->username,$datapost['postid']);
		}else{
			echo "Gagal".PHP_EOL;
		}
	}

	public function SyncPost($datafeed)
	{

		echo "Sync Feed <-------------".PHP_EOL;

		$ReadLog = self::ReadLog($this->username);

		$results = array();
		$freshlog = array();
		foreach ($datafeed as $feed) {
			if (is_array($ReadLog) AND in_array($feed['postid'], $ReadLog)) {
				echo "Skip {$feed['postid']}, feed sudah di proses. ".PHP_EOL;
				$freshlog[] = $feed['postid'];
				continue;
			}

			$results[] = $feed;
		}

		/* Update Log Data Fresh Story */
		if (count($datafeed) != count($ReadLog) - 1) {
			echo "Update Log Feed <-------------".PHP_EOL;
			self::SaveLog($this->username,implode(PHP_EOL, $freshlog),false);
		}

		return $results;
	}

	public function ReadLog($identity)
	{		

		$logfilename = "feedtimeline-data-{$identity}";
		$log_url = array();
		if (file_exists($logfilename)) 
		{
			$log_url = file_get_contents($logfilename);
			$log_url  = explode(PHP_EOL, $log_url);
		}

		return $log_url;
	}

	public function SaveLog($identity,$datastory,$append = true)
	{
		if ($append) {
			return file_put_contents("feedtimeline-data-{$identity}", $datastory.PHP_EOL, FILE_APPEND);
		}else{			
			return file_put_contents("feedtimeline-data-{$identity}", $datastory.PHP_EOL);
		}
	}
}

Class Worker
{
	public function Run()
	{

		$data['cookie'] = InputHelper::GetInputCookie('sb=WLsWXlngssLwlMsnSJms_8J4; datr=WLsWXi3Pbi7dQBDxRrgxTzkY; c_user=100012436637902; xs=35%3AJr1KNFvJ0wJTXg%3A2%3A1578548087%3A5019%3A11172; spin=r.1002220780_b.trunk_t.1591682292_s.1_v.2_; presence=EDvF3EtimeF1591682306EuserFA21B12436637902A2EstateFDutF1591682306119CEchF_7bCC; fr=1d4Hm4xepZmwZqQmm.AWXRgjoGZb24LSh3FejDjPf-re4.BeFrtY.7d.F7f.0.0.Be3yUC.AWUkdWQ8; wd=1440x353');
		$data['limit'] = InputHelper::GetInputLimit(10);
		$data['react'] = InputHelper::GetInputReact('LIKE');		

		$delay_default = 10;
		$delay = 10;
		$delayfeed_default = 10;
		$delayfeed = 10;

		/* Call Class */
		$Working = new FacebookAutoReactTimeLine();
		$Working->Auth($data);

		$nofeed = 0;
		$likepost = 0;
		while (true) {

			/* when nofeed 5 reset sleep value to default */
			if ($nofeed >= 5) {
				$delayfeed = $delayfeed_default;
				$nofeed = 0;
			}

			$FeedList = $Working->GetFeed();

			if (empty($FeedList)) {
				echo "Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
				sleep($delayfeed);

				$delayfeed = $delayfeed*rand(2,3);
				$nofeed++;

				continue;
			}

			foreach ($FeedList as $key => $post) {

				/* when likepost 5 reset sleep value to default */
				if ($likepost >= 5) {
					$delay = $delay_default;
					$likepost = 0;
				}	

				$Working->LikePost($post);

				echo "Delay {$delay} <--------------".PHP_EOL;
				sleep($delay);

				$delay = $delay+5;
				$likepost++;
			}

		}		

	}
}

Worker::Run();
// use at you own risk