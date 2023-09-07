<?php

$url = "https://anuenuemusic.sprlight.net/ajax/points/check_expire"; /*檢查點數到期，每日執行一次*/
// Cpanel Cron Jobs: /usr/local/bin/php /home/sprlight/full.sprlight.net/Auto_check_point.php

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
