<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
use think\Db;
function get_lang_menu(){
    $lang_menu = [];
    $langId = config('subDeparment');
    if($langId=='D'){ $langId = 4; }
    else if($langId=='C'){ $langId = 3; }
    else if($langId=='B'){ $langId = 2; }
    else if($langId=='A'){ $langId = 1; }
    else{ $langId = 1; }
    $lang = Db::connect('main_db')->table('lang')->where('lang_id ='.$langId)->find();
    // dump($lang);exit;
    if($lang){
        $lang_menu = $lang['menu'] ? json_decode($lang['menu'], true) : [];
    }
    // dump($lang_menu);exit;
    return $lang_menu;
}
function RECEIPTS_STATE($text)
{
    $lang_menu = get_lang_menu();

    $RECEIPTS_STATE = [
        $lang_menu['未收款'], 
        $lang_menu['已收款'], 
    ];
    $text = $RECEIPTS_STATE[$text];
    return $text;
}
function TRANSPORT_STATE($text)
{
    $lang_menu = get_lang_menu();

    $TRANSPORT_STATE = [
        $lang_menu['未出貨'], 
        $lang_menu['已出貨'], 
    ];
    $text = $TRANSPORT_STATE[$text];
    return $text;
}
function fat_index_text($text, $length)
{
    if (mb_strlen($text, 'utf8') > $length) {
        return mb_substr($text, 0, $length, 'utf8') . '…';
    }
    return $text;
}
function fat_return_checked($text, $aim)
{
    //2020/01/01
    if ($text == $aim) {
        return 'checked';
    }
}

function compare_return($text, $aim, $re)
{
    //2020/02/10
    if ($text == $aim) {
        return $re;
    } else {
        return '';
    }
}

function regidata_readonly($text)
{
    //2020/09/09
    if ($text != '') {
        return 'readonly';
    } else {
        return '';
    }
}

/*回傳指定長度亂數*/
function geraHash($qtd)
{
    $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
    $QuantidadeCaracteres = strlen($Caracteres);
    $QuantidadeCaracteres--;
    $Hash = null;
    for ($x = 1; $x <= $qtd; $x++) {
        $Posicao = rand(0, $QuantidadeCaracteres);
        $Hash .= substr($Caracteres, $Posicao, 1);
    }
    return $Hash;
}
/*上傳base64檔案*/
function uploadFile($path, $file, $fileName = "")
{
    /*取得檔案附檔類型*/
    $getType = explode(";", $file)[0];
    $getType = explode("/", $getType)[1];

    /*設定檔案名稱*/
    if (!$fileName) {
        /*無給定*/
        $t = time();
        $gethash = geraHash(8);
        $fileName = $t . $gethash . '.' . $getType;
    } else {
        /*有給定*/
        $fileName = $fileName . '.' . $getType;
    }

    /*上傳絕對目錄路徑*/
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $fileName;
    // dump($filePath);exit();

    /*處理檔案、上傳主機*/
    $fileData = substr($file, strpos($file, ",") + 1);
    $decodedData = base64_decode($fileData);
    file_put_contents($filePath, $decodedData);

    /*回傳檔案相對路徑*/
    return $path . '/' . $fileName.'?'.rand(0, 10000000000);
}

/*修改輸入文字已符合sql篩選(主要用於json格式的文字比對)*/
function replace_str_to_do_sql_search($str){
    $str = str_replace("/", '\\\\\\\\/', $str);
    return $str;
}

/*合併訂單後台 函式 開始*/
function returnRed($text, $aim)
{
    //2020/01/01
    if ($text == $aim) {
        return 'red';
    }
}
function change_url($text)
{
    $id = explode("?id=", $text);
    if(count($id)>1){ /*前台商品網址*/
        $url = explode("/", $text);
        return $url[0] . '/admin/examination/examinee_list/id/' . $id[1] . '.html';
    }

    $id = explode("/id/", $text);
    if(count($id)>1){ /*後台商品網址*/
        $url = explode("/", $text);
        return $url[0] . '/admin/examination/examinee_list/id/' . $id[1];
    }

    return "";
}

function randomkeys($length){
    $pattern = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = '';

    for($i=0;$i<$length;$i++){
        $key .= $pattern{rand(0,61)};
    }
    return $key;
}
/*合併訂單後台 函式 結束*/

//自定義共用變數
define('MAIN_WEB', 'https://anuenuemusic.com');    //主形象網站
define('MAIN_WEB_LAN', 'https://anuenuemusic.com');//主形象網站-語言版

//自定義共用變數
/*商品固定欄位，設定-1為隱藏*/
define('property1', '副標：'); //文字
define('property2', '-1'); //文字
define('property3', '-1'); //文字

/*FB 粉專*/
define('FB_PAGE_URL', 'https://www.facebook.com/Crm傳訊光科技股份有限公司-358077690789/');

/*edm網址*/
define('EDM_URL', 'shop-edm.sprlight.net');

/*LINE*/
define('client_secret', '64e8dd0b44749dbe070f2d91800db4f4');
define('client_id', '1578554831');
define('line_url', 'https://anuenuemusic.sprlight.net/index/Linglogin/callBack');
define('line_url_open', 'https://anuenuemusic.sprlight.net/index/Linglogin/open');
/*LINE LIFF APP ID*/
define('LIFF_ID', '');

/*Google*/
define('Google_appId', '50854229691-qkgj0a6ru782f2n5b5jsfm0ilfpe885t.apps.googleusercontent.com');

/*FB*/
define('FB_appID', '358077690789');

/*公司資訊*/
define('Footer_Title', '鋐宇樂器有限公司');
define('Service_Tel', '(02)2536-4488');
define('Service_Tel_A', '0225364488');
define('Service_Email', 'service@photonic.com.tw');

/*幣別*/
define('dolar', 'USD');
define('dolar_mark', '$');

/*mail server*/
define('Mail_Host', '127.0.0.1');
define('Mail_Username', 'admin@sprlight.net');
define('Mail_Password', 'spr$()n9e#!');

/*google 機器人驗證*/
define('GOOGLE_RECAPTCHA_SITEKEY', ''); /*網站金鑰*/
define('GOOGLE_RECAPTCHA_SECKEY', '');  /*密鑰*/

define('Paypal_Pay', '0'); /*Paypal 1.用 0.不用*/
define('WeChat_Pay', '0'); /*微信 1.用 0.不用*/

// 測試用綠界金流
// define('ServiceURL', 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5');
// define('HashKey', '5294y06JbISpM5x9');
// define('HashIV', 'v77hoKGq4kWxNNIS');
// define('MerchantID', '2000132');
// define('CreditInstallment', "3,6,12"); //分期期數
// 正式用綠界金流
define('ServiceURL', 'https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5');
define('HashKey', 'sBXqZkYge8mEVQwI');
define('HashIV', 'qPKqJAqZJ25T71rN');
define('MerchantID', '3316094');
define('CreditInstallment', "3,6,12"); //分期期數

// 台新金流
define('Tspg_mid', '000812770028244');
define('Tspg_s_mid', '');
define('Tspg_tid', 'T0000000');

/*合併訂單後台 自定義共用變數 開始*/
// 紅利點數 0.不使用  1.使用
define('POINT_DISCOUNT', '1');
// 推播功能 0.不使用 1.使用
define('NOTIFICATION', '0');

//綠界物流 測試用
define('Logistic_HashKey', '5294y06JbISpM5x9');
define('Logistic_HashIV', 'v77hoKGq4kWxNNIS');
//綠界物流 正式用(預設串接B2C，若改C2C需修改 index\controller\Cart.php, ajax\controller\Ecpaylogistic.php)
// define('Logistic_HashKey', '');
// define('Logistic_HashIV', '');

// 綠界物流 寄件人資料(出貨方)
define('SenderName', '鋐宇樂器有限公司');
define('SenderPhone', '0225364488');
define('SenderCellPhone', '0900000000');
define('SenderZipCode', '10467');
define('SenderAddress', '臺北市中山區松江路188巷1號1樓');
/*合併訂單後台 自定義共用變數 結束*/
