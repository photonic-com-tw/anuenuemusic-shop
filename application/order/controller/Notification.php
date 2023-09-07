<?php

namespace app\order\controller;

use app\order\controller\MainController;
use think\Request;
use think\Db;
use think\Session;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class Notification extends MainController
{
	public function __construct() {
        parent::__construct();
    }

	public function send_notification(){
		$payload = $_POST;

		if(!$payload['title'])$this->error('請輸入推波標題');
		if(!$payload['msg'])$this->error('請輸入推波內容');
		
		$open_url = $_POST['open_url'];

		$subscription = Db::connect('main_db')->table('subscription')->select();
		foreach ($subscription as $key => $value) {
			# code...
			$subscription_data = [
				'contentEncoding' 	=> 'aes128gcm',
				'endpoint'			=> $value['endpoint'],
				'expirationTime'	=> $value['expirationTime'],
				'keys'				=> [
										'auth'		=> $value['auth'],
										'p256dh'	=> $value['p256dh']
									]
			];
			$result = $this->do_send($subscription_data, $payload);
			// dump($result);
		}

		$this->success('發送完成');
	}

	private function do_send($subscription_data, $payload){
		$subscription = Subscription::create($subscription_data);

		$auth = array(
		    'VAPID' => array(
		        'subject' 	=> 'https://github.com/Minishlink/web-push-php-example/',
		        'publicKey'	=> 'BHAWEuTvLrhhDk8-3ItMsN65M6FOOYWt0yYy_mIgKm75r5Gxp7Ox1O6D314ef2ZH1TjUwZrGwL6avFHuHqinWaI', // don't forget that your public key also lives in app.js
		        'privateKey'=> 'CDRJNkbBu8O4ZMfP0YEPd35Db4nc6MHK1ZWfthwVbks', // in the real world, this would be in a secret file
		    ),
		);

		$webPush = new WebPush($auth);
		$report = $webPush->sendOneNotification(
		    $subscription,
		    json_encode($payload, JSON_UNESCAPED_UNICODE)
		);

		// handle eventual errors here, and remove the subscription from your server if it is expired
		$endpoint = $report->getRequest()->getUri()->__toString();

		if ($report->isSuccess()) {
		    return "[v] Message sent successfully for subscription {$endpoint}.";
		} else {
		    return "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
		}
	}
}