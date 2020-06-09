<?php

echo base64_encode('100006572221234_2801147960114251');
exit;
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://mbasic.facebook.com/ufi/reaction/?ft_ent_identifier=701163620455822&reaction_type=2&is_permalink=0&basic_origin_uri=https%3A%2F%2Fmbasic.facebook.com%2FRiedayme%3F__tn__%3DC-R&_ft_&av=100016865703374&ext=1591919866&hash=AeR6oADQKZGLygBq');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Authority: mbasic.facebook.com';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
$headers[] = 'Sec-Fetch-Site: same-origin';
$headers[] = 'Sec-Fetch-Mode: navigate';
$headers[] = 'Referer: https://mbasic.facebook.com/reactions/picker/?ft_id=701163620455822&origin_uri=https%3A%2F%2Fmbasic.facebook.com%2FRiedayme%3F__tn__%3DC-R&av=100016865703374&refid=17&__tn__=%2AW-R';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
$headers[] = 'Cookie: sb=IddBXhX0VL6oJlj52JyiTuvJ; datr=IddBXutHOFSYJe25zc1vGxsv; c_user=100016865703374; xs=29%3AOl2lBtCHN_uMRA%3A2%3A1591565467%3A17482%3A10881; m_pixel_ratio=1; spin=r.1002218250_b.trunk_t.1591656797_s.1_v.2_; _fbp=fb.1.1591658868127.1122750508; wd=1600x761; fr=0SE9GIw95RNs7jw6d.AWViHuTdRwwKU-VPRqkFWi75fcM.BeQdYM.OF.F7e.0.0.Be3s6i.AWWb-7GZ; presence=EDvF3EtimeF1591660488EuserFA21B16865703374A2EstateFDsb2F1591615022594EatF1591616159687Et3F_5b_5dEutc3F1591616159692G591660488534CEchF_7bCC; act=1591660601293%2F6';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;
