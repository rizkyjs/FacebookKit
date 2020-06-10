<?php  
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookCookie;
use Riedayme\FacebookKit\FacebookChecker;
use Riedayme\FacebookKit\FacebookFeedTimeLine;
use Riedayme\FacebookKit\FacebookPostReaction;

Class InputHelper
{
	public function GetInputCookie($data = false) {

		if ($data) return $data;

		$CheckPreviousCookie = FacebookAutoReactTimeLine::CheckPreviousCookie();

		if ($CheckPreviousCookie) {
			echo "Anda Memiliki Cookie yang tersimpan pilih angkanya dan gunakan kembali : ".PHP_EOL;
			foreach ($CheckPreviousCookie as $key => $cookie) {
				echo "[{$key}]".$cookie['username'].PHP_EOL;
			}
			echo "[x] Masukan cookie baru".PHP_EOL;

			echo "Pilihan Anda : ".PHP_EOL;

			$input = strtolower(trim(fgets(STDIN)));			
		}

		if ($input != 'x') {

			if (strval($input) !== strval(intval($input))) {
				die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
			}

			return $input;
		}

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

		$input = strtoupper(trim(fgets(STDIN)));

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

		$userid = FacebookCookie::GetUIDCookie($data['cookie']);

		if (!$userid) {
			$results = self::ReadPreviousCookie($data['cookie']);
		}else{			

			$auth = new FacebookAuth();
			$results =$auth->AuthUsingCookie($data['cookie']);

			self::SaveCookie($results);
		}

		$this->username = $results['username'];		
		$this->cookie = $results['cookie'];
		$this->limit = $data['limit'];
		$this->react = $data['react'];

	}

	public function SaveCookie($data){

		$filename = 'log-cookie.json';

		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,true);
			$dataexist = false;
			foreach ($read as $key => $logdata) {
				if ($logdata['userid'] == $data['userid']) {
					$inputdata[] = $data;
					$dataexist = true;
				}else{
					$inputdata[] = $logdata;
				}
			}

			if (!$dataexist) {
				$inputdata[] = $data;
			}
		}else{
			$inputdata[] = $data;
		}

		return file_put_contents($filename, json_encode($inputdata,JSON_PRETTY_PRINT));
	}

	public function CheckPreviousCookie()
	{

		$filename = 'log-cookie.json';
		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) {
				$inputdata[] = $logdata;
			}

			return $inputdata;
		}else{
			return false;
		}
	}

	public function ReadPreviousCookie($data)
	{

		$filename = 'log-cookie.json';
		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) {
				if ($key == $data) {
					$inputdata = $logdata;
					break;
				}
			}

			return $inputdata;
		}else{
			die("file tidak ditemukan");
		}
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

		$datapost['url'] = "https://www.facebook.com/{$datapost['postid']}";
		
		$react = new FacebookPostReaction();
		$react->Auth($this->cookie,'cookie');
		$process = $react->ReactPostByScraping([
			'userid' => $datapost['userid'], 
			'postid' => $datapost['postid'], 
			'type' => $this->react
			]);

		if ($process != false) {

			if ($process == 'URL_NOTFOUND') {
				echo "[!Gagal!] URL React pada post {$datapost['url']} tidak ditemukan.".PHP_EOL;	
			}elseif ($process == 'UNREACT') {
				echo "[!Gagal!] React Post {$datapost['url']}, Kemungkinan post sudah diberi react.".PHP_EOL;	
				self::SaveLog($datapost['postid']);	
			}else{
				echo "Sukses React Post {$process['url']} <-------------".PHP_EOL;
				self::SaveLog($datapost['postid']);
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

		$data['cookie'] = InputHelper::GetInputCookie();
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