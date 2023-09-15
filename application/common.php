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
function param_filter($variable){
    try {
        $not_json = json_decode($variable) === NULL;
    } catch (\Exception $e) {
        $not_json = true;
    }
    if(is_array($variable)){
        foreach ($variable as $key => $value) {
            $variable[$key] = param_filter($value);
        }
    }else{
        if($not_json){ 
            if($variable == strip_tags($variable)){
                $variable = preg_replace('/[`;,\'"]/i', '', $variable);
            }
        }
    }
    return $variable;
}
function get_lang_menu($column='menu'){
    $lang_menu = [];
    $langId = config('subDeparment');
    if($langId=='D'){ $langId = 4; }
    else if($langId=='C'){ $langId = 3; }
    else if($langId=='B'){ $langId = 2; }
    else if($langId=='A'){ $langId = 1; }
    else{ $langId = 1; }
    
    $filename = $_SERVER['DOCUMENT_ROOT'].'/lang/'.$langId.'/'.$column.'.json';
    if(!file_exists($filename)){
        dump('語言版檔案不存在:'.$filename);exit;
    }
    $content = file_get_contents($filename);
    if($content){
        $content = str_replace("{Footer_Title}", Footer_Title, $content);
        $content = str_replace("{Service_Tel}", Service_Tel, $content);
        $content = str_replace("{Service_Tel_A}", Service_Tel_A, $content);
        $content = str_replace("{Service_Email}", Service_Email, $content);
        $lang_menu = $content ? json_decode($content, true) : [];
    }else{
        $lang_menu = [];
    }
    // dump($lang_menu);exit;
    return $lang_menu;
}
function RECEIPTS_STATE($text){
    $RECEIPTS_STATE = [
        LANG_MENU['未收款'], 
        LANG_MENU['已收款'], 
    ];
    $text = $RECEIPTS_STATE[$text];
    return $text;
}
function TRANSPORT_STATE($text)
{
    $TRANSPORT_STATE = [
        LANG_MENU['未出貨'], 
        LANG_MENU['已出貨'], 
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

