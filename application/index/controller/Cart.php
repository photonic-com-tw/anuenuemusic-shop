<?php

namespace app\index\controller;

use app\index\controller\Product;

use think\Session;
use think\Controller;
use think\Request;
use think\Db;

use pattern\recursiveCorrdination\cartRC\Proposal;
use pattern\recursiveCorrdination\cartRC\MemberFactory;
use pattern\recursiveCorrdination\discountRC\Proposal as DiscountProposal;
use pattern\recursiveCorrdination\discountRC\MemberFactory as DiscountMemberFactory;
use pattern\simpleFactory\discountFactory\DiscountFactory;
use PHPMailer\PHPMailer\PHPMailer;

use app\index\controller\Paypal;
use app\index\controller\Tspg;
use app\index\controller\Tsbc;

class Cart extends PublicController {

    private $card_pay_set; 
    private $product_shipping_set; 

    public function __construct() {
        parent::__construct(request()->instance());
        
        $this->assign('Paypal_Pay', Paypal_Pay);
        $this->assign('WeChat_Pay', WeChat_Pay);
        $this->assign('dolar', dolar);

        // $this->error("系統升級中，暫時無法購物");
        if(!$this->user){ $this->error($this->lang_menu['請先登入會員'], url('Login/login'));};

        /* 商品可否設定刷卡 */
        $this->card_pay_set = Db::connect('main_db')->table('excel')->where('id = 7')->find()['value1'];
        $this->assign('card_pay_set', $this->card_pay_set);

        /* 商品可否設定運法 */
        $this->product_shipping_set = Db::connect('main_db')->table('excel')->where('id = 15')->find()['value1'];
        $this->assign('product_shipping_set', $this->product_shipping_set);

        header('Set-Cookie: cross-site-cookie=name; SameSite=None; Secure');
        header('Set-Cookie: cookie2=name; SameSite=None; Secure', false);
    }

    public function cart() {
        /* 處理購物車商品 start */
        $Proposal = Proposal::withTeamMembersAndRequire(
            ['GetCartData'],
            ['user_id' => $this->userId]
        );
        $Proposal = MemberFactory::createNextMember($Proposal);
        $cartData = [];
        // dump($Proposal->projectData['data']);
        foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
            $singleData = $this->get_singleData($key); /* 取得商品資料 */
            $singleData['num'] = $value;
            array_push($cartData, $singleData);
        }
        // dump($cartData);
        $this->assign('cartData', $cartData);
        /* 處理購物車商品 end */

        /*取得可加價購商品*/

        /* 處理運費 start */
        $shipping = $this->get_shipping_method($cartData);
        $this->assign('shipping', $shipping);
        /* 處理運費 end */

        /* 處理刷卡 start */
        $card_pay = 1; // 預設可以刷卡
        if($this->card_pay_set==1){
            array_walk($cartData, function($item)use(&$card_pay){
                if($item['card_pay'] == 0){
                    $card_pay = 0;
                }
            });
        }
        $this->assign('card_pay', $card_pay);
        /* 處理刷卡 edn */

        $home = parent::get_user_home(Session::get('user.home'));
        $this->assign('home', $home);

        $consent = Db::table('consent')->where("id=1")->find();
        $this->assign('consent_shopping', $consent['shopping']);
        $this->assign('consent_other', $consent['other']);

        // 讀取報名資料
        $this->assign('exinfo', $_SESSION['exinfo']??'');

        //各種同意書
        $consent = Db::table('consent')->find("1");
        $this->assign('consent', $consent);

        return $this->fetch('cart');
    }

    public function add_price(Request $request){
        /*取得可加價購商品*/
        $addprice_group = $this->get_addprice_products();
        // dump($addprice_group);exit;
        if(count($addprice_group)==0 || !empty(self::$closeFunction['加價購設定'])){
            $this->redirect('/index/cart/cart.html');
        }
        $this->assign('addprice_group', $addprice_group);

        /*
            $typeinfo_id = isset($_GET['id']) ? $_GET['id'] : "";

            $product = Db::table('product')->where('title="類型" and online=1')->find();
            if(!$product){
                $this->redirect('/index/cart/cart.html');
            }
            $typeinfo = Db::table('typeinfo')->where('parent_id="'.$product['id'].'" and title="配件" and branch_id=0 and online=1')->find();
            if($typeinfo){
                $typeinfos = Db::table('typeinfo')->where('parent_id="'.$product['id'].'" and branch_id='.$typeinfo['id'].' and online=1')->select();
            }
            if(!$typeinfos){
                $this->redirect('/index/cart/cart.html');
            }
            $this->assign('typeinfos', $typeinfos);

            // 處理目標分類id
            if($typeinfo_id == "0" || $typeinfo_id == ""){
                $typeinfo_id = $typeinfos[0]['id'];
            }
            $this->assign('typeinfo_id', $typeinfo_id);

            // 找出目標分類的商品
            $cate_serach_main = '"branch_id":"'.$typeinfo_id.'"';
            $productinfo = Db::table('productinfo p')
                            ->field('p.title, p.id, p.pic AS pic1, p.has_price, p.is_registrable,p.examinee_date,p.expire_date, p.content')
                            ->join('productinfo_orders po', 'po.prod_id=p.id', 'left')
                            ->where("p.final_array like '%".$cate_serach_main."%' AND p.online = 1 AND po.branch_id=".$typeinfo_id)
                            ->order('po.order_id asc, p.id desc')
                            ->select();
            // dump($productinfo);exit;

            // 處理商品資料
            $Product = new Product($request);
            $coupon_button = []; $act_button = [];
            foreach ($productinfo as $key => $value) {
                $productinfo[$key]['pic1'] =  json_decode($value['pic1'],true);

                // 顯示商品價格
                $productinfo[$key]['show'] = $Product->get_product_price_option($value);

                // 標記優惠券
                $coupon_button[$key] = $Product->find_prod_coupon($value['id']);

                // 標記活動或折扣
                $act_button[$key]['act_data'] = $Product->find_prod_act($value['id']);
            }
            $this->assign('coupon_button', $coupon_button);
            $this->assign('act_button', $act_button);
            $this->assign('productinfo', $productinfo);
            // dump($productinfo);
        */

        /* 計算總金額 */
        $Proposal = Proposal::withTeamMembersAndRequire(
            ['GetCartData'],
            ['user_id' => $this->userId]
        );
        $Proposal = MemberFactory::createNextMember($Proposal);
        $cartData = [];
        foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
            $singleData = $this->get_singleData($key); /* 取得商品資料 */
            $singleData['num'] = $value;
            array_push($cartData, $singleData);
        }
        $DiscountProposal = DiscountProposal::withTeamMembersAndRequire(
            // ['Total', 'CouponCheck', 'PointCheck','ActCheck'],
            ['Total', 'CouponCheck', 'PointCheck','ActCalculate', 'MemberDiscount'],
            [
                'user_id' => $this->userId,
                'cartData' => $cartData
            ]
        );
        $DiscountProposal = DiscountMemberFactory::createNextMember($DiscountProposal);

        $this->assign('discountData', $DiscountProposal->projectData);

        return $this->fetch();
    }

    public function redirect3Next(){

        $once = Db::connect('main_db')->table('orderform')->where("id='".$_POST['id']."'")->find();
        $date = getdate(time());
        $date = $date['mday'];                      

        //create order number
        $order_number = $this->create_order_number();
        //----

        Db::connect('main_db')->table('orderform')->where('id',$_POST['id'])->update(['show_date' => '1','order_number'=>$order_number]);

        //$once['id']=Db::connect('main_db')->table('orderform')->find('id');
        //echo $order_number;

        $once['email']=Db::connect('main_db')->table('account')->where('id',$once['user_id'])->value('email');

        $OrderData['orderData']=['id' =>$_POST['id'],'total' => $once['total'],'order_number' =>$order_number,'pay_way' => $once['payment'],'email' => $once['email']];
        $this->redirect2Next($OrderData['orderData']);
    }

    public function getdiscount() {
        $Proposal = Proposal::withTeamMembersAndRequire(
            ['GetCartData'],
            ['user_id' => $this->userId]
        );
        $Proposal = MemberFactory::createNextMember($Proposal);

        $points_limit = 0; // 限用點數
        $points_setting = json_decode( Db::table('points_setting')->find(1)['value'] ); // 限用點數的分館

        $need_shipfee = false; /* 預設不須處理運費 */
        $cartData = [];
        foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
            
            $singleData = $this->get_singleData($key); /* 取得商品資料 */
            if($singleData['key_type']=='normal' || substr($singleData['key_type'],0,3)=='kol' || $singleData['key_type']=='add'){ /* 有購買 一般商品 或 網紅商品 或 加價購商品 */
                $need_shipfee = true; /* 需處理運費 */

                /* 檢查商品是否適用點數 */
                $final_array = Db::table('productinfo')->field('final_array')->find($singleData['type_product_id'])['final_array'];
                if($points_setting){
                    foreach ($points_setting as $prev_id) {
                        if(strpos($final_array, '"prev_id":"'.$prev_id.'"') !== false){ // 此館可以使用點數
                            $points_limit += ( $singleData['type_count'] * $value ); // 累計可使用的點數量
                            break; // 換下個商品
                        };
                    }
                }
            }

            $singleData['num'] = $value;
            array_push($cartData, $singleData);
        }

        $DiscountProposal = DiscountProposal::withTeamMembersAndRequire(
            // ['Total', 'CouponCheck', 'PointCheck','ActCheck'],
            ['Total', 'CouponCheck', 'PointCheck','ActCalculate', 'MemberDiscount'],
            [
                'user_id' => $this->userId,
                'cartData' => $cartData
            ]
        );
        $DiscountProposal = DiscountMemberFactory::createNextMember($DiscountProposal);

        if($DiscountProposal->projectData["points"]){
            $points_limit = $points_limit > $DiscountProposal->projectData["points"][0]["point"] ? $DiscountProposal->projectData["points"][0]["point"] : $points_limit;
        }
        $this->assign('points_limit', $points_limit);
        $this->assign('discountData', $DiscountProposal->projectData);
        $this->assign('coupon_c', count($DiscountProposal->projectData['coupon']));
        $this->assign('acts_c', count($DiscountProposal->projectData['acts']['actCart']));
        // dump($DiscountProposal->projectData);

        /* 處理運費 */
        if($_GET['send_way']=='undefined') return "<p style='line-height: 1.4285em; font-family: microsoft jhenghei; font-size: 14px;'>".$this->lang_menu['請選擇運送方式，或是此筆訂單的商品有限制個別運送方式，請分開結帳']."</p>";
        $shipping = $this->get_shipping_method($cartData, $_GET['send_way']);
        if( !$shipping ) return "<p style='line-height: 1.4285em; font-family: microsoft jhenghei; font-size: 14px;'>".$this->lang_menu['無此運送方式']."</p>";

        if($need_shipfee){
            $shipping[0]['price'] = $DiscountProposal->projectData['Total'] < $shipping[0]['free_rule'] ? $shipping[0]['price'] : 0;
        }else{
            $shipping[0]['price'] = 0;
        }
        $this->assign('shipping', $shipping[0]);
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
        if( $DiscountProposal->projectData['Total'] > 20000 && in_array($shipping[0]['name'], ['全家取貨', '7-11取貨', '萊爾富取貨']) ){ //綠界物流金額上限
            return "<p style='line-height: 1.4285em; font-family: microsoft jhenghei; font-size: 14px;'>".$this->lang_menu['超商取貨無法運送金額超過20,000元之商品']."</p>";
        }else{
            return $this->fetch('discount');
        }
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
    }

    public function selectPlace(){

        $Order_Data = Request::instance()->get('Order_Data');
        Session::set('Order_Data',$Order_Data);
        // dump(Session::get('Order_Data'));exit;
        $user_id = $this->userId ? $this->userId : 0;
        $temp_order_data = [
            'user_id'   => $user_id,
            'order_data'=> $Order_Data,
            'cart_data' => Session::get('cart'),
            'randomkey' => time().$this->randomkeys(10),
            'time'      => date("Y-m-d H:i:s"),
        ];
        $temp_order_data_id = Db::table('temp_order_data')->insertGetId($temp_order_data);

        $Order_Data = json_decode($Order_Data);

        // 電子地圖
        define('HOME_URL', 'http://www.sample.com.tw/logistics_dev');
        include(ROOT_PATH.'extend/thirdparty/Ecpay.Logistic.Integration.php');

        try {
            $AL = new \EcpayLogistics();

            //檢查是否需超商付款
            $IsCollection = $Order_Data->pay_way == "貨到付款" ? \EcpayIsCollection::YES : \EcpayIsCollection::NO;

            //檢查是否需超商付款
            $Device = parent::isMobileCheck() ? \EcpayDevice::MOBILE : \EcpayDevice::PC;

            //檢查哪種超商取貨
            $send_way = Db::table('shipping_fee')->where('id='.$Order_Data->send_way)->find();
            if(!$send_way)$this->error($this->lang_menu['無此運送方式']);

            if($send_way['name'] =="全家取貨"){
                $LogisticsSubType = \EcpayLogisticsSubType::FAMILY;
            }elseif ($send_way['name'] =="7-11取貨") {
                $LogisticsSubType = \EcpayLogisticsSubType::UNIMART;
            }elseif ($send_way['name'] =="萊爾富取貨") {
                $LogisticsSubType = \EcpayLogisticsSubType::HILIFE;
            }else{
                $this->error($this->lang_menu['無此運送方式']); /*無此超商取件方式*/
            }

            $AL->Send = array(
                'MerchantID' => MerchantID,
                'MerchantTradeNo' => 'no' . date('YmdHis'),
                'LogisticsSubType' => $LogisticsSubType, //FAMI、UNIMART、HILIFE
                'IsCollection' => $IsCollection,
                'ServerReplyURL' => 'https://'.$_SERVER['HTTP_HOST'].'/index/cart/buy',
                'ExtraData' => $temp_order_data['randomkey'],
                'Device' => $Device
            );
            // CvsMap(Button名稱, Form target)
            $html = $AL->CvsMap($this->lang_menu['電子地圖(統一)']);
            echo $html;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function buy() {
        // dump($_POST);
        if(isset($_POST['ExtraData'])){
            $temp_order_data_id = Db::table('temp_order_data')->where('randomkey="'.$_POST['ExtraData'].'" AND over=0')->find();
            if(!$temp_order_data_id)$this->error($this->lang_menu['資訊不足']); /*請填寫完整訂單內容*/

            $cart = json_decode( $temp_order_data_id['cart_data'] );
            Session::set('cart', $temp_order_data_id['cart_data']);
            $this->userId = $temp_order_data_id['user_id'];
            $user = Db::connect(config('main_db'))->table('account')->find($this->userId);
            Session::set('user', $user);
            $this->user = $user;
            $OrderData = (array)json_decode($temp_order_data_id['order_data']);
            $OrderData['selectPlace'] = Request::instance()->post();
            
            Db::table('temp_order_data')->where('randomkey="'.$_POST['ExtraData'].'"')->update(['over'=>1]);
        }else{
            $OrderData = Request::instance()->post();
            $OrderData['selectPlace'] = [];
            $cart = json_decode(Session::get('cart'),true);
        }
        // dump($OrderData);exit;
        // dump($cart);exit;

        $points_limit = 0;
        $points_setting = json_decode( Db::table('points_setting')->find(1)['value'] );

        foreach($cart as $k => $v){
            $key_list = explode("_", $k);
            $key_id = $key_list[0];
                if( count($key_list)>1 ){
                    $key_type = $key_list[1];
                }else{
                    $key_type = 'normal';
                }

            // 依商品類型檢查庫存
            switch($key_type){
                case 'normal': // 一般商品 或 某網紅的商品 或 加價購商品
                case substr($key_type , 0, 3)=='kol':
                case 'add':
                    // 找好所有購買商品資料
                    $key_list = explode('_', $k);
                    $prod_data = Db::table('productinfo_type pt')
                                        ->join('productinfo p','pt.product_id = p.id')
                                        ->field('   pt.num, 
                                                    pt.num as rest_num,
                                                    pt.title,
                                                    pt.online as pt_online, 
                                                    pt.count as pt_count, 
                                                    p.title as pro_name, 
                                                    p.card_pay, 
                                                    p.is_registrable,
                                                    p.expire_date, 
                                                    p.online as p_online, 
                                                    p.final_array')->find($key_id);

                    // 檢查商品是否上架
                    $Product = new Product(Request::instance());
                    $respData = $Product->check_product_and_infotype_close($prod_data['final_array']);
                    if( $prod_data['p_online'] ==2 || $respData->close){
                        $this->error($this->lang_menu['此筆訂單包含已下架的商品，'].$this->lang_menu['請刪除後再建立訂單']);
                    }

                    // 檢查品項是否被假刪除
                    if( $prod_data['pt_online'] == 0){
                        $this->error($this->lang_menu['此筆訂單包含不正確的品項，'].$this->lang_menu['請刪除後再建立訂單']);
                    }

                    // 檢查商品是否可刷卡
                    if( $this->card_pay_set==1 && ($OrderData['pay_way'] == $this->lang_menu['綠界刷卡(一次付清)'] || $OrderData['pay_way'] == $this->lang_menu['綠界刷卡(分期付款)']) && $prod_data['card_pay'] == 0){
                        $this->error($this->lang_menu['此筆訂單包含不可刷卡之商品，請勿使用刷卡功能']);
                    }

                    // 檢查商品是還可報名
                    if( $this->control_register== 1 && $prod_data['is_registrable'] == '1' && $prod_data['expire_date'] < time() && $prod_data['expire_date'] != -28800){
                        $this->error($this->lang_menu['此筆訂單包含已報名截止的商品，'].$this->lang_menu['請刪除後再建立訂單']);
                    }

                    /* 檢查商品是否適用點數 */
                    if($points_setting){
                        foreach ($points_setting as $prev_id) {
                            if(strpos($prod_data['final_array'], '"prev_id":"'.$prev_id.'"') !== false){ // 此館可以使用點數
                                $points_limit += ( $prod_data['pt_count'] * $v ); // 累計可使用的點數量
                                break; // 換下個商品
                            };
                        }
                    }

                    $store_num[$key_id] = $prod_data;
                    break;

                case 'coupon': // 優惠券商品
                    $_POST['id'] = $key_id;
                    $_POST['num'] = $v;
                    $_POST['cmd'] = 'assign';
                    $Product = new Product(Request::instance());
                    $respData = $Product->checkCouponNum();
                    if(!$respData['status']){
                        $coupon= Db::table('coupon')->where("id='". $key_id."'")->find();
                        $this->error( $respData['message'].'('.$coupon['title'].')' );
                    }
                    break;

                default:
                    break;
            }
        }

        if(strpos($OrderData['discount'], 'points_') !== false){
            if($OrderData['point']>$points_limit){
                $this->error($this->lang_menu['超出可使用點數上限']);
            }
        }

        if(empty($this->Front_name['庫存警示'])){ 
            // 依購物車減數量
            foreach($cart as $k => $v){
                $key_list = explode("_", $k);
                    if( count($key_list)>1 ){
                        $key_type = $key_list[1];
                    }else{
                        $key_type = 'normal';
                    }
                $prod_id = $key_list[0];

                if($key_type!='coupon'){ /*非優惠券商品 要檢查庫存*/
                    $store_num[$prod_id]['num'] = (int)$store_num[$prod_id]['num'] - (int)$v;
                    if($store_num[$prod_id]['num'] < 0){ // 減到小於0表示購買的一般+網紅商品+加價購商品數量超過庫存量
                        $this->error(
                            $this->lang_menu['商品']."：".$store_num[$prod_id]["pro_name"]."，<br>".
                            $this->lang_menu['規格']."：".$store_num[$prod_id]["title"]."，<br>".
                            $this->lang_menu['數量剩下']."：".$store_num[$prod_id]["rest_num"]."，".
                            $this->lang_menu['請再重新填選數量']
                        );
                    }
                }
            }
        }

        $OrderData['orderData'] = $this->createOrder($OrderData);

        if(isset($OrderData['exinfo'])){
            $this->exinfo($OrderData);//整理考生資料 fatfat   
        }   

        $OrderData['orderData']['pay_way'] = $OrderData['pay_way'];
        $OrderData['orderData']['email'] = $OrderData['transport_email'];
        $this->redirect2Next($OrderData['orderData']);
    }

    public function dumpCart(){
            // echo "<pre>";
            var_dump(Session::get('cart'));
            // var_dump(Session::get('user'));
    }

    public function randomkeys($length){
        $pattern = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = '';

        for($i=0;$i<$length;$i++){
            $key .= $pattern{rand(0,61)};
        }
        return $key;
    }

    private function checkNumber() {
        $Proposal = Proposal::withTeamMembersAndRequire(
            ['GetCartData'],
            ['user_id' => $this->userId]
        );
        $Proposal = MemberFactory::createNextMember($Proposal);

        $cartData = [];
        $checkNum = [];
        foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
            $singleData = Db::table('productinfo_type')
                            ->alias('type')
                            ->field('type.id AS type_id,
                                     type.title AS type_title,
                                     type.num AS productNum,
                                     product.id AS productLocation,
                                     type.price AS type_price,
                                     info.title AS info_title,
                                     type.count AS type_count')
                            ->join('productinfo info', 'info.id = type.product_id')
                            ->join('typeinfo', 'typeinfo.id = info.parent_id')
                            ->join('product', 'typeinfo.parent_id = product.id')
                            ->find($key);

            if(empty($singleData)){
                $singleData = Db::table('productinfo_type')
                            ->alias('type')
                            ->field('type.id AS type_id,
                                     type.num AS productNum,
                                     product.id AS productLocation,
                                     type.price AS type_price,
                                     type.count AS type_count,
                                     type.product_id AS type_product_id')
                            ->join('productinfo info', 'info.id = type.product_id')
                            ->join('product', 'info.prev_id = product.id')
                            ->find($key);     
            }
                            
            $singleData['num'] = $value;
            if($singleData['num'] > $singleData['productNum']) {
                array_push($checkNum, $singleData['info_title'].'-'.$singleData['type_title'].'只剩下'.$singleData['productNum'].'個');
            }

            $singleData['countPrice'] = ceil(($singleData['type_count']/10) * $singleData['type_price']);
            array_push($cartData, $singleData);
        }
    }

    public function createOrder($OrderData, $buy_method='online') {
        //get cart data
            $Proposal = Proposal::withTeamMembersAndRequire(
                ['GetCartData'],
                ['user_id' => $this->userId]
            );
            $Proposal = MemberFactory::createNextMember($Proposal);

            $cartData = [];
            foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
                $singleData = $this->get_singleData($key, $buy_method); /* 取得商品資料 */
                $singleData['num'] = $value;
                array_push($cartData, $singleData);
            }
        //----

        //get discount data
            $DiscountProposal = DiscountProposal::withTeamMembersAndRequire(
                ['Total', 'CouponCheck', 'PointCheck' ,'ActCalculate', 'MemberDiscount'],
                [
                    'user_id' => $this->userId,
                    'cartData' => $cartData
                ]
            );
            $DiscountProposal = DiscountMemberFactory::createNextMember($DiscountProposal);
        //----

        //create products data
            $need_shipfee = false;  // 是否需運費
            $products = [];
            $buy_coupon = [];       // 購買優惠券(成單後派發優惠券用)
            $freeDiscountSum = 0;   // 立馬省總優惠金額
            foreach ($cartData as $value) {
                if($value['key_type'] != 'coupon'){ // 購買非優惠券商品
                    $need_shipfee = true; // 需計算運費
                }

                $url2 = $value['info_pic1'];

                $product = [
                    'name' => $value['info_title'] . '-' . $value['type_title'],
                    'price' => $value['countPrice'],
                    'num' => $value['num'],
                    'total' => $value['countPrice'] * $value['num'],
                    'url2' => $_SERVER['HTTP_HOST'].'/public/static/index/'.$url2,
                    'type_id' => $value['type_id'],
                    'key_type' => $value['key_type'], // 商品種類(ex:一般商品、優惠券商品...)
                ];

                // 根據商品商品種類產生對應資料
                switch($value['key_type']){
                    case 'normal': // 一般商品 或 某網紅的商品 或 加價購商品
                    case substr($value['key_type'] , 0, 3)=='kol':
                    case 'add':
                        // 因為立馬省改動過單價，這邊要記錄回來真實的單價
                        $productinfo_type = [];
                        $key_list = explode("_", $value['type_id']);
                        $type_id = $key_list[0];
                        $productinfo_type = Db::table('productinfo_type')->find($type_id);
                        if($productinfo_type && $value['key_type']!='add'){
                            $origin_price = $productinfo_type['count'];
                            $product['price'] = $origin_price;
                            $product['total'] = $origin_price * $value['num'];
                        }

                        // 計算立馬省總優惠金額
                        $actProductInList = Db::table('act_product')
                                    ->field('act.*')
                                    ->join('act', 'act.id = act_product.act_id')
                                    ->join('productinfo_type', 'act_product.prod_id = productinfo_type.product_id')
                                    ->where('productinfo_type.id = "'.$type_id.'"')
                                    ->where('act.act_type = 2')
                                    ->where("act.online = 1 AND ( act.end <= 0 OR (act.start < " . time() . " AND act.end > " . time() . "))")
                                    ->select();
                        if(count($actProductInList) > 0 && $value['key_type']!='add'){ /*有套用利馬省 且 非加價購商品*/
                            $freeDiscountSum += $value["num"] * $actProductInList[0]['discount1']; 
                        }


                        $up = Db::table('productinfo')->find($value['type_product_id']);
                        $Position =  Db::table('productinfo_type pt')->field('pos.name')->join('position pos','pt.position = pos.id')->find($value['type_id'])['name'];
                        $product['Author'] = $up['Author'];
                        $product['house'] = $up['house'];
                        $product['position'] = $Position;
                        $product['url'] = $_SERVER['HTTP_HOST'].'/admin/productinfo/edit/id/'.$value['type_product_id'].'.html';
                        $product['info_id'] = $value['type_product_id'];
                        $product['is_registrable'] = $value['is_registrable'];
                        $product['deal_position'] = $buy_method=='online' ? "0" : "1"; // 線上購物需核銷庫存
                        break;

                    case 'coupon': // 優惠券商品
                        $product['Author'] = '';
                        $product['house'] = '';
                        $product['position'] = '無';
                        $key_id = explode('_', $value['type_product_id'])[0];
                        $product['url'] = $_SERVER['HTTP_HOST'].'/admin/coupon/show/id/'.$key_id.'.html';
                        $product['info_id'] = "0";
                        $product['is_registrable'] = "0";
                        $product['deal_position'] = "1"; // 不需銷庫存
                        $buy_coupon[$key_id] = $value['num'];
                        break;  
                }

                array_push($products, $product);
            }
            // dump($products);exit;
            if( count($products)==0 ){
                $this->error($this->lang_menu['資訊不足']); /*購物車內無商品，不可建立訂單*/
            }
        //----

        // 處理運費
            $shipping = $this->get_shipping_method($cartData, $OrderData['send_way']);
            if( $OrderData['send_way']==$this->lang_menu['到店取貨'] || !$need_shipfee){ /*商品銷售、到店取貨、不需運費 使用*/
                array_push($products, [
                    'name' => $this->lang_menu['運費'],
                    'price' => 0,
                    'num' => 1,
                    'total' => 0
                ]);
                $shipping[0]['price'] = 0;

            }else{ // 有需運送的商品，且配送方式非到店取貨
                if( !$shipping )$this->error($this->lang_menu['無此運送方式']);
                $OrderData['send_way'] = $shipping[0]['name'];

                if($DiscountProposal->projectData['Total'] > $shipping[0]['free_rule']) { // 如果購物金額超過免運條件
                    $shipping[0]['price'] = 0; // 設定運費為0元
                }

                array_push($products, [
                    'name' => $shipping[0]['name'].$this->lang_menu['運費'],
                    'price' => $shipping[0]['price'],
                    'num' => 1,
                    'total' => $shipping[0]['price']
                ]);     
            }
        //----

        //create add_point data
            if($OrderData['pay_way']!=$this->lang_menu['綠界刷卡(分期付款)']){
                if($OrderData['discount'] == 'none_discount' && empty($this->Front_name['點數設定'])){
                    $point_rate = (float)Db::table('points_setting')->find(3)['value'];
                    $add_point = floor(($DiscountProposal->projectData['Total'] - $DiscountProposal->projectData['acts']['sumNoneGetPoint']) / $point_rate);
                }else{
                    $add_point = 0;
                }
            }else{
                $add_point = 0;
            }
        //----
        
        //產生優惠說明 & 計算購物總金額
            $discountData = explode("_", $OrderData['discount']);
            $discountData[1] = $discountData[0] == 'points' ? $OrderData['point'] : $discountData[1];

            if ($discountData[0] == 'acts'){ // 活動優惠
                $discountFinal = [];
                foreach($DiscountProposal->projectData['acts']['actCart'] as $acKey => $acValue){

                    $actDiscount = $acValue['calculated']['discount'];

                    if (($acValue['type']==1)|($acValue['type']==3))
                        $discountFinal[] = [
                                            'type'=>$this->lang_menu['活動'],
                                            'name'=>$acValue['name'],
                                            'count'=>$this->lang_menu['打'].$actDiscount.$this->lang_menu['折'],
                                            'product'=>$acValue['prod']];
                    else
                        $discountFinal[] = [
                                            'type'=>$this->lang_menu['活動'],
                                            'name'=>$acValue['name'],
                                            'count'=>$this->lang_menu['扣'].$actDiscount.$this->lang_menu['元'],
                                            'product'=>$acValue['prod']
                                        ];
                }

                $discountFinalData['discount'] = json_encode($discountFinal, JSON_UNESCAPED_UNICODE);
                $discountFinalData['total'] = $DiscountProposal->projectData['acts']['sum'];
            
            }else if($discountData[0] == "firstbuy"){ // 會員首購優惠
                $discountFinalData['discount'] = urldecode(json_encode([
                    [
                        'type' => urlencode($this->lang_menu['會員首購優惠']),
                        'name' => urlencode($DiscountProposal->projectData['firstBuyDiscount']['vip_name']),
                        'count' => urlencode($this->lang_menu['扣'] . $DiscountProposal->projectData['firstBuyDiscount']['discount'] . '元'),
                    ]
                ]));
                $discountFinalData['total'] = $DiscountProposal->projectData['Total'] - $DiscountProposal->projectData['firstBuyDiscount']['discount'];

            }else if($discountData[0] == "vipdiscount"){ // VIP會員優惠
                $discountFinalData['discount'] = urldecode(json_encode([
                    [
                        'type' => urlencode($this->lang_menu['VIP會員優惠']),
                        'name' => urlencode($DiscountProposal->projectData['vipDiscount']['vip_name']),
                        'count' => urlencode($this->lang_menu['扣'] . $DiscountProposal->projectData['vipDiscount']['discount'] . '元'),
                    ]
                ]));
                $discountFinalData['total'] = $DiscountProposal->projectData['Total'] - $DiscountProposal->projectData['vipDiscount']['discount'];

            }else{ // 其他優惠(ex:紅利、優惠券、直接輸入型優惠券、無優惠)
                $discountObject = DiscountFactory::createDiscount(
                    $discountData,
                    $DiscountProposal->projectData['Total']
                );
                // var_dump($discountObject);
                $discountFinalData = $discountObject->getDiscountAndTotal($OrderData);
                $discountFinalData['total'] = $discountFinalData['total'] < 0 ? 0 : $discountFinalData['total'] ;
            }
        //----

        //---- count free(auto) discount
        $freeDiscount = $buy_method=='online' ? $freeDiscountSum : 0;
        // dump($freeDiscount);
        //----------------

        //create order number
            $order_number = $this->create_order_number();
        //----

        //ps for uniform_numbers and company_title
            $ps = '';
            if($OrderData['uniform_numbers'] != ''){
                $ps .= $this->lang_menu['統一編號'].'：' . $OrderData['uniform_numbers'];
            }

            if($OrderData['company_title'] != ''){
                $ps .= $this->lang_menu['公司抬頭'].'：' . $OrderData['company_title'];
            }
        //----

        $discountFinalData['total'] = $discountFinalData['total'] < 0 ? 0 : $discountFinalData['total'] ;
        $discountFinalData['total'] += $shipping[0]['price'];
        // dump($discountFinalData);exit;

        if (isset($this->user)){
            $userId = $this->userId;
            $role   = 'member';
        }else{
            $userId = 0;
            $role   = 'guest';
        }

        //新增資料
        $time = time();
        $Eff = Db::connect('main_db')->table('excel')->field('value1')->find(2)['value1'];//Sql給予期限
        $Eff_data = Db::connect('main_db')->table('excel')->field('value2')->find(2)['value2'];//Sql給予期限  12-31
        $Y = (date("Y",$time)+$Eff).'-'.$Eff_data.' 23:59:59';//取得年份

        $receipts_state = isset($OrderData['receipts_state']) ? $OrderData['receipts_state'] : '0';
        $transport_state = isset($OrderData['transport_state']) ? $OrderData['transport_state'] : '0';
        $order_status = isset($OrderData['status']) ? $OrderData['status'] : 'New';
        $insertData = [
            'user_id' => $userId,
            'order_number' => $order_number,
            'create_time' => $time,
            'over_time' => $Y,
            'payment' => $OrderData['pay_way'],
            'transport' => $OrderData['send_way'],
            'transport_location' => $OrderData['addrC'],
            'transport_location_name' => $OrderData['transport_location_name'],
            'transport_location_phone' => $OrderData['transport_location_phone'],
            'transport_location_tele' => $OrderData['transport_location_tele'],
            'transport_location_textarea' => $OrderData['transport_location_textarea'],
            'transport_email' => $OrderData['transport_email'],
            'product' => json_encode($products, JSON_UNESCAPED_UNICODE),
            'add_point' => $add_point,
            'shipping' => $shipping[0]['price'],
            'total' => $discountFinalData['total'],
            'discount' => $discountFinalData['discount'],
            'freediscount' => $freeDiscount,
            'receipts_state' => $receipts_state,
            'transport_state' => $transport_state,
            'uniform_numbers' => $OrderData['uniform_numbers'],
            'company_title' => $OrderData['company_title'],
            'ps' => $ps,
            'status' => $order_status
        ];
        //----

        //物流資訊
        if( count($OrderData['selectPlace']) > 0){
            $insertData['MerchantTradeNo'] = $OrderData['selectPlace']['MerchantTradeNo'];
            $insertData['LogisticsSubType'] = $OrderData['selectPlace']['LogisticsSubType'];
            $insertData['CVSStoreID'] = $OrderData['selectPlace']['CVSStoreID'];
            $insertData['CVSStoreName'] = $OrderData['selectPlace']['CVSStoreName'];
            $insertData['CVSAddress'] = $OrderData['selectPlace']['CVSAddress'];
            $insertData['CVSTelephone'] = $OrderData['selectPlace']['CVSTelephone'];
            $insertData['CVSOutSide'] = $OrderData['selectPlace']['CVSOutSide'];
            // $insertData['ExtraData'] = $OrderData['selectPlace']['ExtraData'];
        }
        //----

        //資料庫新增訂單
        // dump($insertData);exit;
        $id = Db::connect('main_db')->table('orderform')->insertGetId($insertData);

        //建立直接輸入型優惠券使用紀錄
        if($discountData[0]=='directcoupon'){
            $discount = json_decode($insertData['discount'])[0];
            $discount->count = (Int)mb_substr($discount->count, 2, -1);

            Db::table('coupon_direct_record')->insertGetId([
                'user_id'   => $userId,
                'coupon_id' => $discount->coupon_id,
                'order_id'  => $id,
                'datetime'  => $insertData['create_time'],
                'total'     => $insertData['total'],
                'discount'  => $discount->count,
            ]);
        }

        //更新購買優惠券
        if($buy_coupon){
            foreach ($buy_coupon as $key => $value) {
                Db::table('coupon_pool')->where('owner is null and login_time is null and use_time is null and coupon_id ='.$key)->limit($value)->update(['owner'=> $userId]);
            }
        }

        // 清空購物車
        $this->clearCart();
        Session::delete('cart');

        return [
            'id' => $id,
            'total' => $discountFinalData['total'],
            'order_number' => $order_number,
            'role' => $role
        ];
    }



    private function clearCart() {

        $Proposal = Proposal::withTeamMembersAndRequire(
            ['GetCartData'],
            ['user_id' => $this->userId]
        );
        $Proposal = MemberFactory::createNextMember($Proposal);

        /*只扣線上可購買數量*/
        foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
            Db::table('productinfo_type')->where('id', $key)->setDec('num', $value);
        }

        Db::table('cart')->where('user_id', $this->userId)->delete();
    }

    private function redirect2Next($OrderData) {

        $o=Db::connect('main_db')->table('orderform')->where('id', $OrderData['id'])->select();

        $result=(array)json_decode($o[0]['product'], true);
        $res_goods='';
        foreach ($result as $key => $value) {
            $res_goods .=$value['name'];
            if(!empty($result[$key+1])){
                $res_goods .= '、';
            }
        }

        if($OrderData['pay_way']!=$this->lang_menu['綠界刷卡(一次付清)'] && $OrderData['pay_way']!=$this->lang_menu['綠界刷卡(分期付款)']){
            $globalMailData = parent::getMailData();

            $new_order_letter = $this->lang_menu['訂單成功消費者信'];
            $new_order_letter = str_replace("{globalMailData_mailFromName}", $globalMailData['mailFromName'], $new_order_letter);
            $new_order_letter = str_replace("{Service_Tel}", Service_Tel, $new_order_letter);
            $mailBody = "
                <html>
                    <head></head>
                    <body>
                        <div>
                            ".$new_order_letter."
                            ".$this->lang_menu['訂單編號']."：".$o[0]['order_number']."<br>
                            ".$this->lang_menu['訂單時間']."：".date('Y/m/d H:i',$o[0]['create_time'])."<br>
                            ".$this->lang_menu['訂購商品']."：".$res_goods."<br>
                            ".$this->lang_menu['訂單金額']."：".$OrderData['total']."<br>
                            ".$this->lang_menu['購買人']."：".$this->user['name']."<br>
                            ".$this->lang_menu['收件人']."：".$o[0]['transport_location_name']."<br>
                            ".$this->lang_menu['出貨地址']."：".$o[0]['transport_location']."<br>
                            E-mail：".$OrderData['email']."<br>
                            ".$this->lang_menu['手機號碼']."：".$o[0]['transport_location_phone']."<br>
                            ".$this->lang_menu['聯絡電話']."：".$o[0]['transport_location_tele']."<br>
                            ".$this->lang_menu['付款方式']."：".$o[0]['payment']."<br>
                            ".$this->lang_menu['備註']."：".$o[0]['transport_location_textarea']."<br>
                        </div>
                        <div>
                        ". $globalMailData['system_email']['order_complete'] ."
                        </div>
                    </body>
                </html>
            ";
            $mail_return = parent::Mail_Send($mailBody,'client',$OrderData['email'],$this->lang_menu['訂單成功資訊']);
            $mailBody = "
                <html>
                    <head></head>
                    <body>
                        <div>
                            新訂單提醒<br>
                            訂單位置：".$globalMailData['mailFromName']."
                            訂單編號：".$o[0]['order_number']."<br>
                            訂單時間：".date('Y/m/d H:i',$o[0]['create_time'])."<br>
                            訂購商品：".$res_goods."<br>
                            訂單金額：".$OrderData['total']."<br>
                            購買人：".$this->user['name']."<br>
                            收件人：".$o[0]['transport_location_name']."<br>
                            出貨地址：".$o[0]['transport_location']."<br>
                            E-mail：".$OrderData['email']."<br>
                            手機號碼：".$o[0]['transport_location_phone']."<br>
                            聯絡電話：".$o[0]['transport_location_tele']."<br>
                            付款方式：".$o[0]['payment']."<br>
                            備註：".$o[0]['transport_location_textarea']."<br>
                        </div>
                        <div style='color:red;'>
                            ≡ 此信件為系統自動發送，請勿直接回覆 ≡
                        </div>
                    </body>
                </html>
            ";
            $mail_return = parent::Mail_Send($mailBody,'admin','',"新訂單提醒");
        }


        if($this->ecpay_card==1){
            if($OrderData['pay_way']==$this->lang_menu['綠界刷卡(一次付清)'] || $OrderData['pay_way']==$this->lang_menu['綠界刷卡(分期付款)']){
                $this->ecpy_card_pay($OrderData);
            }
            else if($OrderData['pay_way']==$this->lang_menu['Paypal']){
                $this->paypal_pay($OrderData);
            }
            else if($OrderData['pay_way']==$this->lang_menu['支付寶'] || $OrderData['pay_way']==$this->lang_menu['微信支付']){
                $this->tsbc_pay($OrderData);
            }
        }else if($this->tspg_card==1){
            $this->tspg_card_pay($OrderData);
        }

        $this->redirect(url('orderform/orderform_success', [
            'id' => $OrderData['order_number']
        ]));
    }

    private function ecpy_card_pay($OrderData){
        /* 綠界 */
        include(ROOT_PATH.'extend/thirdparty/ECPay.Payment.Integration.php');

        try {
            $obj = new \ECPay_AllInOne();

            //服務參數
            $obj->ServiceURL  = ServiceURL;                                        //服務位置
            $obj->HashKey     = HashKey;                                           //Hashkey，請自行帶入ECPay提供的HashKey
            $obj->HashIV      = HashIV;                                            //HashIV，請自行帶入ECPay提供的HashIV
            $obj->MerchantID  = MerchantID;                                        //MerchantID，請自行帶入ECPay提供的MerchantID
            $obj->EncryptType = '1';                                                //CheckMacValue加密類型，請固定填入1，使用SHA256加密


            $globalMailData = parent::getMailData();
            //基本參數(請依系統規劃自行調整)

            $MerchantTradeNo = $OrderData['order_number'];
            $obj->Send['ReturnURL'] = url('Ecreturn/returnurl', [
                'id' => $OrderData['id']
            ],'' , true);

            $obj->Send['MerchantTradeNo']   = $MerchantTradeNo;                          //訂單編號
            $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       //交易時間
            $obj->Send['TotalAmount']       = (int)$OrderData['total'];                  //交易金額
            $obj->Send['TradeDesc']         = $globalMailData['mailFromName'].$this->lang_menu['訂單'];   //交易描述
            $obj->Send['ChoosePayment']     = \ECPay_PaymentMethod::Credit ;              //付款方式:Credit
            $backurl ="";

            $backurl = "/index/orderform/orderform_success/id/".$OrderData['order_number'];


            $obj->Send['ClientBackURL']     = url($backurl, [], 'html', true);    //付款完成通知回傳的網址
            $obj->Send['NeedExtraPaidInfo'] = 'Y';
            //var_dump($obj->Send['ReturnURL']);die();

            //訂單的商品資料
            array_push($obj->Send['Items'], [
                'Name' => $globalMailData['mailFromName'].$this->lang_menu['訂單'],
                'Price' => (int)$OrderData['total'],
                'Currency' => "元",
                'Quantity' => (int) "1",
                'URL' => "dedwed"
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if($OrderData['pay_way'] == $this->lang_menu['綠界刷卡(一次付清)']) {
            $obj->CheckOut();
        }

        if($OrderData['pay_way'] == $this->lang_menu['綠界刷卡(分期付款)']) {
            $obj->SendExtend['CreditInstallment'] = CreditInstallment;         //分期期數，預設0(不分期)，信用卡分期可用參數為:3,6,12,18,24
            $obj->SendExtend['InstallmentAmount'] = (int)$OrderData['total'] ;  //使用刷卡分期的付款金額，預設0(不分期)
            $obj->SendExtend['Redeem'] = false ;                                //是否使用紅利折抵，預設false
            $obj->SendExtend['UnionPay'] = false;                               //是否為聯營卡，預設false;
            $obj->CheckOut();
        }
    }
    private function paypal_pay($OrderData){
        if($OrderData['pay_way']==$this->lang_menu['Paypal']){
            $backurl = "https://".$_SERVER['SERVER_NAME']."/index/paypal/callback";

            $globalMailData = parent::getMailData();
            
            $Paypal = new Paypal();
            try{
                $res = $Paypal->pay($OrderData['order_number'], $OrderData['total'], $globalMailData['mailFromName'], $backurl, $OrderData['id']);
                exit;
            }catch (\Exception $e){
                $this->error($this->lang_menu['交易有誤，'].$this->lang_menu['請稍後再試'], url('orderform/orderform_success', ['id' => $OrderData['order_number']]) );
            }
        }
    }
    private function tsbc_pay($OrderData){
        if($OrderData['pay_way']==$this->lang_menu['支付寶'] || $OrderData['pay_way']==$this->lang_menu['微信支付']) {
            $backurl = "https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform_success/id/".$OrderData['order_number'];

            $globalMailData = parent::getMailData();
            
            $Tsbc = new Tsbc();
            $res = $Tsbc->auth($OrderData, $globalMailData['mailFromName'], $backurl);
            if($res){
                $this->success($this->lang_menu['已生成付款QRcode，再請您掃碼付款'], url('orderform/orderform_success', ['id' => $OrderData['order_number']]) );
            }else{
                $this->error($this->lang_menu['生成付款QRcode有誤，'].$this->lang_menu['請稍後再試'], url('orderform/orderform_success', ['id' => $OrderData['order_number']]) );
            }
        }
    }
    private function tspg_card_pay($OrderData){
        if($OrderData['pay_way']==$this->lang_menu['台新刷卡(一次付清)']) {

            $backurl = "https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform_success/id/".$OrderData['order_number'];

            $globalMailData = parent::getMailData();
            
            $Tspg = new Tspg();
            $res = $Tspg->auth($OrderData['order_number'], $OrderData['total'], $globalMailData['mailFromName'], $backurl, $OrderData['id']);
            // dump($res);exit;
            if($res->params->ret_code == '00'){
                $this->redirect($res->params->hpp_url);
                // $this->redirect($res->params->post_back_url);
            }else{
                $this->error($this->lang_menu['交易有誤，'].$this->lang_menu['請稍後再試'], url('orderform/orderform_success', ['id' => $OrderData['order_number']]) );
            }
        }
    }

    /*AJAX*/
    public function cartCtrl() {
        // return json([
        //         'status' => false,
        //         'message' => '系統升級中，暫時無法購物',
        //         'num' => 0,
        //     ], 200);
        
        $cart = json_decode(Session::get('cart'),true);
        // dump($cart);exit;
        $cmd = $_POST['cmd'];
        $cart_key = $_POST['product_id'];
        $num = $_POST['num'];

        $key_list = explode("_", $cart_key);
        $key_id = $key_list[0];
        if( count($key_list)>1 ){
            $key_type = $key_list[1];
        }else{
            $key_type = 'normal';
            $cart_key = $key_id.'_normal';
        }

        if(substr($key_type , 0, 3) == 'kol' ){ // 有掛某網紅
            $productinfo_type = Db::table('productinfo_type')->find($key_id); // 找出該品項
            $kol_id = substr($key_type , 3); // 取出網紅
            // 找出該商品的與該網紅關使用中的最新紀錄
            $kol_productinfo = Db::table('kol_productinfo')->where('productinfo_id ='.$productinfo_type['product_id'].' AND is_using=1 AND kol_id='.$kol_id)->order('id desc')->select();

            if( !empty($kol_productinfo) ){ // 檢查商品是該網紅代銷中
                // 該網紅代銷中，接續檢查網紅是否已到開賣日
                $kol = Db::table('kol')->find($kol_id); // 找出該網紅
                if( strtotime($kol['start_date']) > time() ){ // 檢查網紅起賣日是否大於現在
                    // 未開賣
                    $cart_key = $key_id.'_normal';
                }
            }else{
                // 非該網紅代銷中
                $cart_key = $key_id.'_normal';
            }
        }

        // 依加入購物車的品項區分檢查方式
        switch($key_type){
            case 'normal': // 一般商品 或 某網紅的商品 或 加價購商品
            case substr($key_type , 0, 3)=='kol':
            case 'add':
                /*按種類整理購物車商品，統計數量*/
                $store_num = []; // 有加入購物車的商品的庫存量
                $cart_num = []; // 購物車累積數量
                $addprice_num = []; // 加購商品累積數量
                foreach($cart as $cart_k => $cart_v){ /*對每個購物車內的商品處理*/
                    $cart_key_list = explode("_", $cart_k);
                    $cart_key_id = $cart_key_list[0];
                    if( count($cart_key_list)>1 ){
                        $cart_key_type = $cart_key_list[1];
                    }else{
                        $cart_key_type = 'normal';
                    }

                    if( $cart_key_type!= 'coupon' ){ // 統計 非優惠券商品的全部商品 庫存、購物車量

                        $store_num[$cart_key_id] = Db::table('productinfo_type pt')->join('productinfo p','pt.product_id = p.id')
                                                    ->field('pt.num, pt.num as rest_num,pt.title ,pt.online ,p.title as pro_name, p.card_pay')->find($cart_key_id);
                        // 品項已被假刪除
                        if( $store_num[$cart_key_id]['online']==0){
                            return json([
                                'status' => false,
                                'message' => $this->lang_menu['內容有誤'], /*不存在此品項*/
                                'num' => Request::instance()->post('num'),
                            ], 200);
                        }

                        if(!isset($cart_num[$cart_key_id])){
                            $cart_num[$cart_key_id] = $cart_v;
                        }else{
                            $cart_num[$cart_key_id] += $cart_v;
                        }

                        if(substr($cart_key_type , 0, 3)=='add'){ /*統計 加價購商品的購物車輛*/
                            if(!isset($addprice_num[$cart_key_id])){
                                $addprice_num[$cart_key_id] = $cart_v;
                            }else{
                                $addprice_num[$cart_key_id] += $cart_v;
                            }
                        }
                    }
                }
                if( empty($this->Front_name['庫存警示']) ){ /*檢查此商品的庫存*/
                    $num_limit = false;
                    if(empty($store_num[$key_id])){ // 購物車內無此商品
                        $storeNum = Db::table('productinfo_type')->find($key_id)['num'];
                        if($storeNum < $num){ // 庫存若小於 本次加入的量
                            $num_limit = true;
                        }
                    }else if($cmd == 'increase'){ // 用加的方法
                        if($store_num[$key_id]['num'] < $cart_num[$key_id] + $num){ // 庫存若小於 購物車累積量 + 本次加入的量
                            $num_limit = true;
                        }
                    }else if( $cmd == 'assign'){ // 用指派數量的方法
                        if($store_num[$key_id]['num'] < $cart_num[$key_id] - $cart[$cart_key] + $num){ // 庫存若小於 購物車累積的量 - 某品項的量 + 某品項指派的量
                            $num_limit = true;
                        }
                    }
                    
                    if($num_limit){
                        return json([
                            'status' => false,
                            'message' => $this->lang_menu['庫存不足'],
                            'num' => Request::instance()->post('num'),
                        ], 200);
                        exit;
                    }
                }

                if($key_type=='add'){ /*檢查加價購商品數量上限*/
                    $addable_addprice = $this->get_addprice_products();

                    $num_limit = false;
                    $addable_addprice_num = $addable_addprice['type'.$key_id]['adp_p_num']; /*找出加入購物車的品項的可加購量*/
                    if(empty($addprice_num[$key_id])){ // 購物車內無此商品
                        if( $addable_addprice_num < $num){ // 可加購量小於 本次加入的量
                            $num_limit = true;
                        }
                    }else if($cmd == 'increase'){ // 用加的方法
                        if($addable_addprice_num < $addprice_num[$key_id] + $num){ // 可加購量小於 購物車累積量 + 本次加入的量
                            $num_limit = true;
                        }
                    }else if( $cmd == 'assign'){ // 用指派數量的方法
                        if($addable_addprice_num < $addprice_num[$key_id] - $cart[$cart_key] + $num){ // 可加購量小於 購物車累積的量 - 某品項的量 + 某品項指派的量
                            $num_limit = true;
                        }
                    }
                    if($num_limit){
                        return json([
                            'status' => false,
                            'message' => $this->lang_menu['超出加價購數量上限'],
                            'num' => Request::instance()->post('num'),
                        ], 200);
                    }
                }
                break;

            case 'coupon': // 優惠券商品
                $_POST['id'] = $key_id;
                $Product = new Product(Request::instance());
                $respData = $Product->checkCouponNum();

                if(!$respData['status']){
                    return json([
                                'status' => false,
                                'message' => $respData['message'],
                                'num' => Request::instance()->post('num'),
                            ], 200);
                    exit;
                }
                break;

            default:
                break;
        }

        // 操作(加減)購物車
        try{
            $Proposal = Proposal::withTeamMembersAndRequire(
                [
                    'GetCartData', 'Assign', 'Increase', 'Delete', 'Decrease'
                ],
                [
                    'cmd' => $cmd,
                    'user_id' => $this->userId,
                    'id' => $cart_key,
                    'num' => $num
                ]
            );
            
            $Proposal = MemberFactory::createNextMember($Proposal);
            Session::set('cart',$Proposal->projectData['data']);

            return json([
                'status' => true,
                'message' => sizeof(json_decode($Proposal->projectData['data'], true)),
                'num' => $num,
            ], 200);
        }catch (\Exception $e){
            return json([
                'status' => false,
                'message' => $e->getMessage(),
                'num' => Request::instance()->post('num'),
            ], 200);
        }
    }

    /*取得某品項在購物車內的數量*/
    public function cartCtrl_num() {      
        $cart = json_decode(Session::get('cart'),true);
        // dump($cart);exit;

        $cart_key = $_POST['product_id'];
 
        $key_list = explode("_", $cart_key);
        $key_id = $key_list[0];
        if( count($key_list)>1 ){
            $key_type = $key_list[1];
        }else{
            $key_type = 'normal';
            $cart_key = $key_id.'_normal';
        }

        if(substr($key_type , 0, 3) == 'kol' ){ // 有掛某網紅
            $productinfo_type = Db::table('productinfo_type')->find($key_id); // 找出該品項
            $kol_id = substr($key_type , 3); // 取出網紅
            // 找出該商品的與該網紅關使用中的最新紀錄
            $kol_productinfo = Db::table('kol_productinfo')->where('productinfo_id ='.$productinfo_type['product_id'].' AND is_using=1 AND kol_id='.$kol_id)->order('id desc')->select();

            if( !empty($kol_productinfo) ){ // 檢查商品是該網紅代銷中
                // 該網紅代銷中，接續檢查網紅是否已到開賣日
                $kol = Db::table('kol')->find($kol_id); // 找出該網紅
                if( strtotime($kol['start_date']) > time() ){ // 檢查網紅起賣日是否大於現在
                    // 未開賣
                    $cart_key = $key_id.'_normal';
                }
            }else{
                // 非該網紅代銷中
                $cart_key = $key_id.'_normal';
            }
        }

        // 依加入購物車的品項區分檢查方式
        foreach($cart as $cart_k => $cart_v){ /*對每個購物車內的商品處理*/
            if($cart_k == $cart_key){
                return json([
                    'status' => true,
                    'message' => $cart_v,
                ], 200);
            }
        }
                
        return json([
            'status' => true,
            'message' => 0,
        ], 200);
    }

    /* 取得報名資料填寫頁面 */
    public function examinee(){
        $city = Db::table('city')->select();
        $this->assign('city', $city);

        $home = parent::get_user_home(Session::get('user.home'));
        $this->assign('home', $home);

        $consent = Db::table('consent')->find();
        $this->assign('consent', $consent['examinee']);
        
        return $this->fetch('examinee');
    }

    /* 取得報名資料修改頁面 */
    public function examinee_edit(){
        
        $city = Db::table('city')->select();
        $this->assign('city', $city);

        $home = parent::get_user_home(Session::get('user.home'));
        $this->assign('home', $home);

        $consent = Db::table('consent')->find();
        $this->assign('consent', $consent['examinee']);
        
        return $this->fetch('examinee_edit');
    }

    /* 取得學校名稱 */
    public function school_ajax(){
        $_POST['a'] = str_replace("台","臺",$_POST['a']);

        switch ($_POST['t'] ) {
          case '幼稚園':
            $show = Db::table('school_kinder')->field('name')->where(" area like '%".$_POST['a']."%'")->order('CONVERT(`name` using big5)')->select();
            break;
          case '國小':
            $show = Db::table('school_e')->field('name')->where(" area like '%".$_POST['a']."%'")->order('CONVERT(`name` using big5)')->select();
            break;
          case '國中':
            $show = Db::table('school_j_h')->field('name')->where(" area like '%".$_POST['a']."%'")->order('CONVERT(`name` using big5)')->select();
            break;
          case '高中':
            $show = Db::table('school_h')->field('name')->where(" area like '%".$_POST['a']."%'")->order('CONVERT(`name` using big5)')->select();
            break;
        }

        foreach($show as $k => $v){
            echo '<option value="'.$v['name'].'">'.$v['name'].'</option>';
        }
    }

    /* 紀錄已填寫的報名資料 */
    public function examinee_session(){
        // dump($_SESSION['exinfo']);exit;
        if($_POST['html'] == 'undefined')$_POST['html']='';
        $_SESSION['exinfo'][$_POST['key']] = $_POST['html'];
    }

    /* 考生資料存入資料庫 */
    public function exinfo($OrderData) {
        // dump($OrderData);
        // exit;
        $data = [];
        foreach($OrderData['exinfo'] as $k => $v){
            $pd = [];
            $key_list = explode("_", $k);
            $key_id = $key_list[0];
            if( count($key_list)>1 ){
                $key_type = $key_list[1];
            }else{
                $key_type = 'normal';
            }
            
            foreach($OrderData['exinfo'][$k] as $num => $v2){
                $pd['type_id'] = $key_id;
                $pd['order_id'] = $OrderData['orderData']['id'];
                $pd['user_id'] = $OrderData['id'];
                foreach($OrderData['exinfo'][$k][$num] as $key => $value){
                    // dump($key);exit;
                    switch ($key) {
                      case 'examinee_school':
                        $pd['examinee_school'] = $value;
                        break;
                      case 'examinee_class':
                        $pd['examinee_class'] = $value;
                        break;
                      case 'examinee_name':
                        $pd['examinee_name'] = $value;
                        break;
                      case 'examinee_id':
                        $pd['examinee_id'] = $value;
                        break;
                      case 'examinee_birthday':
                        $pd['examinee_birthday'] = $value;
                        break;
                      case 'parents_name':
                        $pd['parents_name'] = $value;
                        break;
                      case 'parents_phone':
                        $pd['parents_phone'] = $value;
                        break;
                      case 'parents_mail':
                        $pd['parents_mail'] = $value;
                        break;  
                      case 'parents_tel':
                        $pd['parents_tel'] = $value;
                        break;  
                      case 'parents_add':
                        $pd['parents_add'] = $value;
                        break;  
                      case 'type_product_id':
                        $pd['prod_id'] = $value;
                        break;
                    }
                }
                array_push($data,$pd);
                unset($pd);
            }
        }
        // dump($data);exit;
        Db::table('examinee_info')->insertAll($data);

        // 清空報名紀錄
        $_SESSION['exinfo'] = "";
    }

    public static function get_singleData($key, $buy_method='online'){

        $key_list = explode("_", $key);
        $key_id = $key_list[0];
        if( count($key_list)>1 ){
            $key_type = $key_list[1];
        }else{
            $key_type = 'normal';
        }

        switch($key_type){
            case 'normal': // 一般商品 或 某網紅的商品 或 加價購商品
            case substr($key_type , 0, 3)=='kol':
            case 'add':
                $singleData = Db::table('productinfo_type')
                            ->alias('type')
                            ->field('
                                     type.id AS type_id,
                                     type.price AS type_price,
                                     type.count AS type_count,
                                     type.title AS type_title,
                                     type.product_id AS type_product_id,

                                     info.title AS info_title,
                                     info.pic AS info_pic1,
                                     info.card_pay AS card_pay,
                                     info.is_registrable,
                                     info.shipping_type,
                                     info.shipping_fee_tag
                                    ')
                            ->join('productinfo info', 'info.id = type.product_id')
                            ->find($key_id);
                
                $singleData['info_pic1'] = json_decode($singleData['info_pic1'],true)[0];

                if($key_type=='add'){ /*加價購修改價格*/
                    $addprice = self::get_addprice_products();
                    // dump($addprice);
                    if($addprice && empty(self::$closeFunction['加價購設定']) ){
                        $singleData['countPrice'] = $addprice['type'.$key_id] ? $addprice['type'.$key_id]['adp_dis'] : $singleData['type_count'];
                         $singleData['type_count'] = $addprice['type'.$key_id] ? $addprice['type'.$key_id]['adp_dis'] : $singleData['type_count'];
                    }else{
                        $singleData['countPrice'] = $singleData['type_count'];
                    }
                }else if($buy_method=='online'){ // 線上購物 要套用立馬省
                    $freediscount = Db::table('act_product')
                                    ->join('act', 'act.id = act_product.act_id')
                                    ->where('act.act_type = 2')
                                    ->where('act_product.prod_id = '.$singleData['type_product_id'])
                                    ->where("act.online = 1 AND ( act.end <= 0 OR (act.start < " . time() . " AND act.end > " . time() . "))")
                                    ->select();
                    $singleData['countPrice'] = !empty($freediscount) ?  $singleData['type_count'] - $freediscount[0]['discount1'] : $singleData['type_count'];
                
                }else{
                    $singleData['countPrice'] = $singleData['type_count'];
                }

                // 修改type_id，讓購物車可以記錄到
                $singleData['type_id'] = $singleData['type_id'].'_'.$key_type;
                break;

            case 'coupon': // 優惠券商品
                $coupon = Db::table('coupon_pool')
                        ->whereNull('coupon_pool.owner')
                        ->field('
                                    coupon_pool.id AS coupon_pool_id,
                                    coupon.price AS price,
                                    coupon.title AS title,
                                    coupon.pic AS pic,
                                    coupon.id AS coupon_id
                                ')
                        ->where('(
                                    coupon_pool.login_time < ' . (time() - 21600) . ' OR ' .'
                                    coupon_pool.login_time IS NULL
                                )')
                        ->where('coupon.id = ' . $key_id)
                        ->join('coupon', 'coupon_pool.coupon_id = coupon.id')
                        ->find();
                $singleData = [
                    'type_title'        => '',
                    'type_price'        => $coupon['price'],
                    'type_count'        => $coupon['price'],
                    'countPrice'        => $coupon['price'],
                    'info_title'        => $coupon['title'],
                    'info_pic1'         => $coupon['pic'],
                    'type_id'           => $coupon['coupon_id'].'_'.$key_type,
                    'type_product_id'   => $coupon['coupon_id'].'_'.$key_type,
                    // 'productId'          => $coupon['coupon_id'],
                    'card_pay'          => 1, // 預設都可刷卡
                    'is_registrable'    => 0, // 預設不用填報名資料
                    'shipping_type'     => "",// 預設何種運法都可以
                    'shipping_fee_tag'  => "0",// 預設運費標籤為0元
                ];
                break;

            default:
                break;
        }

        $singleData['key_type'] = $key_type;
        return $singleData;
    }

    public function get_shipping_method($cartData, $shipping_id=false){
        // 取得運送方法
            //--- 加入法: 可運運法為所有運法的聯集
            // $shipping_type = ""; // 運送方式
            // if($this->product_shipping_set==1){ /* 有啟用商品關聯運法 */
            //     foreach ($cartData as $key => $value) {
            //        if($value['shipping_type']==""){ // 無勾選運法
            //             $shipping_type = '';
            //             break;
            //         }else{
            //             $shipping_type .= $value['shipping_type'].',';
            //         }
            //     }
            // }
            // if($shipping_type ==''){ /* 無限制運法 */
            //     $shipping_where = '1=1';
            // }else{
            //     $shipping_where = explode(',', $shipping_type);
            //     $shipping_where = array_filter($shipping_where, function($v){ return $v !=null; });
            //     $shipping_where = "id in (".join(',', $shipping_where).")";
            // }

            //--- 刪去法: 可運運法為所有運法的交集
            if($this->product_shipping_set==1){ /* 有啟用商品關聯運法 */
                foreach ($cartData as $key => $value) {
                    if($value['shipping_type']==""){ // 無勾選運法
                        if(!isset($shipping_type)){ // 還未設定過可運運法
                            $shipping_type=[];
                            $shipping_all = Db::table('shipping_fee')->where('online=1')->order('order_id asc, id desc')->select();
                            array_walk($shipping_all, function($item)use(&$shipping_type){
                                array_push($shipping_type, $item['id']);
                            });
                        }

                    }else{// 有勾選運法
                        if(!isset($shipping_type)){ // 還未設定過可運運法
                            $shipping_type = explode(',', $value['shipping_type']);
                        }else{
                            $shipping_type = array_intersect ($shipping_type, explode(',', $value['shipping_type']));
                        }
                    }
                }
                $shipping_where = isset($shipping_type) ? array_filter($shipping_type, function($v){ return $v !=null; }) : [];
                $shipping_where = $shipping_where ? "id in (".join(',', $shipping_where).")" : 'id=-1';
            }else{
                $shipping_where = '1=1';
            }

            if($shipping_id){
                $shipping_fee = Db::table('shipping_fee')->where($shipping_where.' AND online=1 AND id ="'.$shipping_id.'"')->order('order_id asc, id desc')->select();
            }else{
                $shipping_fee = Db::table('shipping_fee')->where($shipping_where.' AND online=1')->order('order_id asc, id desc')->select();
            }

        /*調整運費*/
        if(empty(self::$closeFunction['運費標籤管理'])){ /*如果有使用運費標籤管理功能*/
            $calculated_shipping_fee = 0;
            foreach ($cartData as $key => $value) {
                if($value['key_type']=='add'){ continue; } /*加價購商品不計算運費*/
                $shipping_fee_tag = Db::table('shipping_fee_tag')->where('id="'.$value['shipping_fee_tag'].'"')->find();
                if($shipping_fee_tag){ /*有找到套用的運費標籤*/
                    $calculated_shipping_fee += ( $shipping_fee_tag['price'] * $value['num'] ); /*累計運費*/
                }
            }

            /*修改所有運法的金額為計算出來的運費*/
            foreach ($shipping_fee as $key => $value) {
                $shipping_fee[$key]['price'] = $calculated_shipping_fee;
            }
        }

        return $shipping_fee;
    }

    static public function get_addprice_products(){
        $cart = json_decode(Session::get('cart'),true);
        // dump($cart);exit;

        $type_ids = []; /*紀錄總共買了那些品項，用於找出可加價購的商品*/
        foreach ($cart   as $key => $num) {
            $key_list = explode("_", $key);
            if( count($key_list)>1 ){
                $key_type = $key_list[1];
            }else{
                $key_type = 'normal';
            }
            $type_id_ori = $key_list[0];

            if($key_type=='normal' || substr($key_type,0,3)=='kol'){ /* 一般商品 或 網紅商品 可列入找尋加價購*/
                foreach (range(1,$num) as $v) {
                    array_push($type_ids, $type_id_ori);
                }
            }
        }
        // dump($type_ids);
        $addprice_group = $type_ids ? Product::get_addprice_products($type_ids)['addprice_group'] : [];

        return $addprice_group;
    }

    public function create_order_number(){
        $count = Db::connect(config('main_db'))->table('orderform')->where('order_number like "'.config('subDeparment').'O'.date('Ymd').'%"')->order('id desc')->find();
        $count = $count ? intval(substr($count['order_number'],-3)) + 1 : 1;
        if($count < 10){
            $count = '00' . $count;
        }else if($count < 100){
            $count = '0' . $count;
        }

        return  $order_number = config('subDeparment') . 'O' . date('Ymd') . $this->randomkeys(5) . $count;
    }
}