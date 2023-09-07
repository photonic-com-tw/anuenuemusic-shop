<?php
namespace pattern;

use think\Db;
use app\index\controller\PublicController;

class WanpayInstance
{   
    static private $wanpay_shop_no = "";
    static private $wanpay_key = "";
    public $post_url ="";
    public $post_data ="";

    public function __construct($post_url) {       
        self::$wanpay_shop_no = '21008024';
        self::$wanpay_key = 'QGbZvggxNdGgMUnp';
        $this->post_url = $post_url;
    }

    /*根據post_data產生簽名值*/
    public function set_sign_post_data($post_data){
    	unset($post_data['sign']);
    	ksort($post_data);
    	
    	$sign = [];
        foreach ($post_data as $key => $value) {
            if($value!=""){
                array_push($sign, $key."=".$value);
            }
        }
        $sign = strtoupper( md5( implode('&', $sign).'&key='.self::$wanpay_key ) );
        $post_data['sign'] = $sign;

        $this->post_data = $post_data;
        return $post_data;
    }

    /*發送請求*/
    public function send_requset(){
    	$result = PublicController::send_requset($this->post_url, $this->post_data);
        dump($result);exit;
        return $result;
    }
}
