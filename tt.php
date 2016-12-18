<?php
$url = 'https://api.weibo.com/oauth2/access_token';
$url = 'https://www.songogo.com/ecpay/epweike_api.php';

echo(file_get_contents($url));
die();


curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

curl_exec ($ch);

if(curl_errno($ch))
{
    echo 'Curl error: ' . curl_error($ch);
}
curl_close ($ch);

