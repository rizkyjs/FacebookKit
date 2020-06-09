<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://m.facebook.com/composer/ocelot/async_loader/?publisher=feed');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36';
$headers[] = 'Sec-Fetch-Site: none';
$headers[] = 'Sec-Fetch-Mode: navigate';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Sec-Fetch-Dest: document';
$headers[] = 'Cookie: sb=WLsWXlngssLwlMsnSJms_8J4; datr=WLsWXi3Pbi7dQBDxRrgxTzkY; c_user=100012436637902; xs=35%3AJr1KNFvJ0wJTXg%3A2%3A1578548087%3A5019%3A11172; spin=r.1002220780_b.trunk_t.1591682292_s.1_v.2_; presence=EDvF3EtimeF1591682306EuserFA21B12436637902A2EstateFDutF1591682306119CEchF_7bCC; fr=1d4Hm4xepZmwZqQmm.AWXRgjoGZb24LSh3FejDjPf-re4.BeFrtY.7d.F7f.0.0.Be3yUC.AWUkdWQ8; wd=1440x353';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;