<?php


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://mbasic.facebook.com/ufi/reaction/?ft_ent_identifier=701163620455822&reaction_type=3&is_permalink=1&basic_origin_uri=https%3A%2F%2Fmbasic.facebook.com%2FRiedayme%2Fposts%2F701163620455822&_ft_&av=100016865703374&ext=1591932697&hash=AeSQHDz_X6X5ELl9');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
// $headers[] = 'Authority: mbasic.facebook.com';
// $headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36';
// $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
// $headers[] = 'Sec-Fetch-Site: same-origin';
// $headers[] = 'Sec-Fetch-Mode: navigate';
// $headers[] = 'Sec-Fetch-User: ?1';
// $headers[] = 'Sec-Fetch-Dest: document';
// $headers[] = 'Referer: https://mbasic.facebook.com/reactions/picker/?is_permalink=1^&ft_id=701163620455822^&origin_uri=https^%^3A^%^2F^%^2Fmbasic.facebook.com^%^2FRiedayme^%^2Fposts^%^2F701163620455822^&av=100016865703374';
// $headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
$headers[] = 'Cookie: sb=Q5FYXuL7Zf-hHWoDcYY-UiBh; datr=Q5FYXhRV37LHftrlureny0a1; c_user=100016865703374; xs=31%3AOt4FcPq7AR7waQ%3A2%3A1586409168%3A17482%3A10881; _fbp=fb.1.1591218870915.1067997829; spin=r.1002215632_b.trunk_t.1591576483_s.1_v.2_; fr=1f0FKotkon9e6deNQ.AWXyysP4W87ln9BVLa2bVCGuw1w.BeWJFD.Xk.F7d.0.0.Be3t2N.AWWgWlb2; presence=EDvF3EtimeF1591664017EuserFA21B16865703374A2EstateFDutF1591664017635CEchF_7bCC; act=1591664045144%2F6; wd=1440x351';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
	echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;