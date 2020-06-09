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

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Cookie Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputLimit($data = false) {

		if ($data) return $data;

		echo "Masukan Limit Feed (angka): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) {
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return (!$input) ? die('Limit Feed Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputReact($data = false) {

		if ($data) return $data;

		echo "Masukan Reaksi yang dikirim : [LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, UNREACT]".PHP_EOL;

		$input = trim(fgets(STDIN));

		$react = ['LIKE', 'LOVE', 'CARE', 'HAHA', 'WOW', 'SAD', 'ANGRY', 'UNREACT'];

		if (!in_array($input,$react)) {
			die("Reaksi Pilihan tidak valid".PHP_EOL);
		}

		return (!$input) ? die('Reaction Masih Kosong'.PHP_EOL) : $input;
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

			if ($process == 'URL_NOTFOUND') {
				echo "[!Gagal!] URL REact pada post {$datapost['postid']} tidak ditemukan.".PHP_EOL;	
			}elseif ($process == 'UNREACT') {
				echo "[!Gagal!] React Post {$process['url']}, Kemungkinan post sudah diberi react.".PHP_EOL;	
			}else{
				echo "Sukses React Post {$process['url']} <-------------".PHP_EOL;
				self::SaveLog($this->username,$datapost['postid']);
			}
		}else{
			echo "[!Gagal!] React Post {$process['url']}, Kesalahan pada kode.".PHP_EOL;
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
		// if (count($datafeed) != count($ReadLog) - 1) {
		// 	echo "Update Log Feed <-------------".PHP_EOL;
		// 	self::SaveLog($this->username,implode(PHP_EOL, $freshlog),false);
		// }

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

		$data['cookie'] = InputHelper::GetInputCookie('sb=IddBXhX0VL6oJlj52JyiTuvJ; datr=IddBXutHOFSYJe25zc1vGxsv; c_user=100016865703374; xs=29%3AOl2lBtCHN_uMRA%3A2%3A1591565467%3A17482%3A10881; spin=r.1002218250_b.trunk_t.1591656797_s.1_v.2_; _fbp=fb.1.1591658868127.1122750508; m_pixel_ratio=1; act=1591661888252%2F0; fr=0SE9GIw95RNs7jw6d.AWVm8KZ6szZ-cj59NxUQvLqq-PE.BeQdYM.OF.F7e.0.0.Be3y0R.AWUbwZtR; presence=EDvF3EtimeF1591684372EuserFA21B16865703374A2EstateFDsb2F1591615022594EatF1591616159687Et3F_5b_5dEutc3F1591616159692G591684372009CEchF_7bCC; wd=1600x347');
		$data['limit'] = InputHelper::GetInputLimit();
		$data['react'] = InputHelper::GetInputReact();		

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