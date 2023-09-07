<?php

$url = "https://anuenuemusic.sprlight.net/ajax/vip_type/auto_set_type"; /*自動更新會員等級，每日執行一次*/
// Cpanel Cron Jobs: /usr/local/bin/php /home/sprlight/full.sprlight.net/Auto_memberType.php

$curl = curl_init();  
curl_setopt($curl, CURLOPT_URL, $url);  
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
if (! empty($data)) {  
    curl_setopt($curl, CURLOPT_POST, 1);  
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
}  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);  
$output = curl_exec($curl);  
curl_close($curl);  
echo $output;  
