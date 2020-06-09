<?php

$postid = '701353603770157';
$fb_dtsg = 'AQEPSZHteB1e:AQGc82YBIEnD';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://touch.facebook.com/ufi/reaction/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "reaction_type=2&ft_ent_identifier={$postid}&fb_dtsg={$fb_dtsg}");
// curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Authority: touch.facebook.com';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
$headers[] = 'X-Response-Format: JSONStream';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'Accept: */*';
$headers[] = 'Origin: https://touch.facebook.com';
$headers[] = 'Sec-Fetch-Site: same-origin';
$headers[] = 'Sec-Fetch-Mode: cors';
$headers[] = 'Referer: https://touch.facebook.com/Riedayme?ref=bookmarks';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
$headers[] = 'Cookie: sb=IddBXhX0VL6oJlj52JyiTuvJ; datr=IddBXutHOFSYJe25zc1vGxsv; c_user=100016865703374; xs=29%3AOl2lBtCHN_uMRA%3A2%3A1591565467%3A17482%3A10881; spin=r.1002218250_b.trunk_t.1591656797_s.1_v.2_; _fbp=fb.1.1591658868127.1122750508; m_pixel_ratio=1; fr=0SE9GIw95RNs7jw6d.AWV3GI8zqzs1ThRNBzQILLpzdf4.BeQdYM.OF.F7e.0.0.Be33P8.AWVyzIvM; presence=EDvF3EtimeF1591704457EuserFA21B16865703374A2EstateFDsb2F1591615022594EatF1591616159687Et3F_5b_5dEutc3F1591616159692G591704344884CEchF_7bCC; act=1591704604568%2F11; x-referer=eyJyIjoiL1JpZWRheW1lP3JlZj1ib29rbWFya3MiLCJoIjoiL1JpZWRheW1lP3JlZj1ib29rbWFya3MiLCJzIjoidG91Y2gifQ%3D%3D; wd=1600x761';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
	echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;