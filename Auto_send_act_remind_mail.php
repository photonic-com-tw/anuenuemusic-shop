<?php

$url = "https://anuenuemusic.sprlight.net/index/examination/send_act_remind_mail"; /*活動提醒信，每小時執行一次*/
// Cpanel Cron Jobs: /usr/local/bin/php /home/sprlight/spec.sprlight.net/Auto_send_act_remind_mail.php

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



