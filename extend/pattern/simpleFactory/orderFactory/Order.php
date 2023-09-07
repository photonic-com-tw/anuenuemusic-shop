<?php

namespace pattern\simpleFactory\orderFactory;

use think\Db;
use think\Controller;
use think\Request;

use app\index\controller\PublicController;
use PHPMailer\PHPMailer\PHPMailer;
use pattern\PointRecords;
 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: Abstract Class that all orderClass's parent
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/

abstract class Order extends Controller{
    protected $id;
    protected $config_db;
    protected $tableName;
    protected $coupon_tableName;
    protected $already2Next = false;

    protected $order_db = "main_db";

    public function __construct($id, $tableName, $coupon_tableName) {
        $this->id = $id;
        $this->tableName = $tableName;
        $this->coupon_tableName = $coupon_tableName;

        if($this->id){
            $orderform =Db::connect($this->order_db)->table($this->tableName)->where('id', $this->id)->find();
            $this->config_db = substr($orderform['order_number'],0,1).'_sub';
        }
    }

    abstract public function changeStatus2Next();

    // 修改匯款碼
    public function setReportNumber($reportNumber) {
        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('report', $reportNumber);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 修改回報匯款狀態
    public function setReportState() {
        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('report_check_time', time());

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 更新收款狀態
    public function setReceiptsState($state) {
        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('receipts_state', $state);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 更新出貨狀態
    public function setTransportState($state) {
        $lang_menu = get_lang_menu();
        try{
            $o = Db::connect($this->order_db)->table($this->tableName)->where('id', $this->id)->find();

            if($state==1){ // 是改成出貨
                // 派發優惠券
                $this->change_coupon('send');

                // 增加點數
                if($o['add_point']>0){
                    $PointRecords = new PointRecords($o['user_id']);
                    $records = $PointRecords->add_records([
                        'msg'           => $lang_menu['完成訂單'].'：'.$o['order_number'].$lang_menu['，贈送點數'],
                        'points'        => $o['add_point'],
                        'belongs_time'  => time()
                    ]);
                }
            }

            // 修改訂單狀態
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->update(['transport_state' => $state,'status'=>'Complete']);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 更新備註
    public function setPS($ps) {
        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('ps', $ps);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 退貨
    public function changeStatus2Return($reason) {
        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->update([
                    'status' => 'Return',
                    'return_ps' => $reason
                ]);

            // 收回優惠券
            $this->change_coupon('take_back');

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 取消訂單
    public function changeStatus2Cancel($reason) {
        $lang_menu = get_lang_menu();
        try{
            // 收回優惠券
            $this->change_coupon('take_back');

            // 修改訂單狀態
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->update([
                    'status' => 'Cancel',
                    'cancel_ps' => $reason,
                    'cancel_date'  => date('Y-m-d H:i:s',time()),
                ]);

            $o=Db::connect($this->order_db)->table($this->tableName)->where('id', $this->id)->find();

            $result=(array)json_decode($o['product'], true);
            $res_goods='';

            foreach ($result as $key => $value) {
                $res_goods .=$value['name'];
                if(!empty($result[$key+1])){
                    $res_goods .= '、';
                }

            }

            /*取得購買者資料*/
            $account=Db::connect($this->order_db)->table('account')->where('id', $o['user_id'])->find();
            /*取得寄信通用資料*/
            $globalMailData = PublicController::getMailData($this->config_db);

            $cancel_order_letter = $this->lang_menu['訂單取消消費者信'];
            /*通知消費者*/
            if($account){ /*會員購物*/
                $client_email = $account['email'];
                $Body = "
                    <html>
                        <head></head>
                        <body>
                            <div>
                                ".$cancel_order_letter."<br>
                                ".$this->lang_menu['訂單編號']."：".$o['order_number']."<br>
                                ".$this->lang_menu['訂單時間']."：".date('Y/m/d H:i',$o['create_time'])."<br>
                                ".$this->lang_menu['訂購商品']."：".$res_goods."<br>
                                ".$this->lang_menu['訂單金額']."：".$o['total']."<br>
                                ".$this->lang_menu['購買人']."：".$account['name']."<br>
                                ".$this->lang_menu['收件人']."：".$o['transport_location_name']."<br>
                                ".$this->lang_menu['出貨地址']."：".$o['transport_location']."<br>
                                E-mail：".$account['email']."<br>
                                ".$this->lang_menu['手機號碼']."：".$o['transport_location_phone']."<br>
                                ".$this->lang_menu['聯絡電話']."：".$o['transport_location_tele']."<br>
                                ".$this->lang_menu['付款方式']."：".$o['payment']."<br>
                                ".$this->lang_menu['備註']."：".$o['transport_location_textarea']."<br>
                            </div>
                            <div>
                                <br>
                                ". $globalMailData['system_email']['order_cancel'] ."
                            </div>
                        </body>
                    </html>
                ";
            }else{ /*非會員購物*/
                $client_email = $o['transport_email'];  
                $Body = "
                    <html>
                        <head></head>
                        <body>
                            <div>
                                ".$cancel_order_letter."<br>
                                ".$this->lang_menu['訂單編號']."：".$o['order_number']."<br>
                                ".$this->lang_menu['訂單時間']."：".date('Y/m/d H:i',$o['create_time'])."<br>
                                ".$this->lang_menu['訂購商品']."：".$res_goods."<br>
                                ".$this->lang_menu['訂單金額']."：".$o['total']."<br>
                                ".$this->lang_menu['購買人']."：".$o['transport_location_name']."<br>
                                ".$this->lang_menu['收件人']."：".$o['transport_location_name']."<br>
                                ".$this->lang_menu['出貨地址']."：".$o['transport_location']."<br>
                                E-mail：".$o['transport_email']."<br>
                                ".$this->lang_menu['手機號碼']."：".$o['transport_location_phone']."<br>
                                ".$this->lang_menu['聯絡電話']."：".$o['transport_location_tele']."<br>
                                ".$this->lang_menu['付款方式']."：".$o['payment']."<br>
                                ".$this->lang_menu['備註']."：".$o['transport_location_textarea']."<br>
                            </div>
                            <div>
                                <br>
                                ". $globalMailData['system_email']['order_cancel'] ."
                            </div>
                        </body>
                    </html>
                ";
            }
            PublicController::Mail_Send($Body, $type='client', $client_email, $subject='取消訂單', $this->config_db);

            /*通知管理者*/
            if($account){ /*會員購物*/
                $Body_admin = "
                    <html>
                        <head></head>
                        <body>
                            <div>
                                    親愛的管理者 您好：<br>
                                    有一筆訂單被取消了，內容如下：<br>
                                    訂單編號：".$o['order_number']."<br>
                                    訂單時間：".date('Y/m/d H:i',$o['create_time'])."<br>
                                    訂購商品：".$res_goods."<br>
                                    訂單金額：".$o['total']."<br>
                                    購買人".$account['name']."<br>
                                    收件人：".$o['transport_location_name']."<br>
                                    出貨地址：".$o['transport_location']."<br>
                                    E-mail：".$account['email']."<br>
                                    手機號碼：".$o['transport_location_phone']."<br>
                                    聯絡電話：".$o['transport_location_tele']."<br>
                                    付款方式：".$o['payment']."<br>
                                    備註：".$o['transport_location_textarea']."<br>
                            </div>
                        </body>
                    </html>
                ";
            }else{ /*非會員購物*/
                $Body_admin = "
                    <html>
                        <head></head>
                        <body>
                            <div>
                                    親愛的管理者 您好：<br>
                                    有一筆訂單被取消了，內容如下：<br>
                                    訂單編號：".$o['order_number']."<br>
                                    訂單時間：".date('Y/m/d H:i',$o['create_time'])."<br>
                                    訂購商品：".$res_goods."<br>
                                    訂單金額：".$o['total']."<br>
                                    購買人".$o['transport_location_name']."<br>
                                    收件人：".$o['transport_location_name']."<br>
                                    出貨地址：".$o['transport_location']."<br>
                                    E-mail：".$o['transport_email']."<br>
                                    手機：".$o['transport_location_phone']."<br>
                                    聯絡電話：".$o['transport_location_tele']."<br>
                                    付款方式：".$o['payment']."<br>
                                    備註：".$o['transport_location_textarea']."<br>
                            </div>
                        </body>
                    </html>
                ";
            }
            PublicController::Mail_Send($Body_admin, $type='admin', $email='', $subject='取消訂單', $this->config_db);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 刪除訂單
    public function delete() {
        try{
            Db::connect($this->order_db)->table($this->tableName)->delete($this->id);
            return true;
        } catch (\Exception $e){
            throw new \RuntimeException($e->getMessage());
        }
    }

    // 依傳入方法派送/收回優惠券
    public function change_coupon($mothod){
        $this_order = Db::connect($this->order_db)->table($this->tableName)->where('id', $this->id)->find();

        $product = json_decode($this_order['product']);
        $db_coupon_pool = Db::connect($this->config_db);
        $db_coupon_pool->startTrans();
        foreach ($product as $key => $value) {
            if(isset($value->key_type)){
                if($value->key_type =='coupon' ){

                    $copuon_id = explode('_', $value->type_id)[0];
                    switch ($mothod) {
                        case 'send': /*贈送*/
                            $change_data = ['login_time' => time()]; /*設定領取時間*/
                            $cuopon_where = " coupon_id=".$copuon_id." and owner=".$this_order['user_id']." and use_time is null and login_time is null";
                            break;
                        
                        case 'take_back': /*收回*/
                            $change_data = ['login_time' => null, 'owner' => null];  /*清空領取時間、領取人id*/
                            $cuopon_where = " coupon_id=".$copuon_id." and owner=".$this_order['user_id']." and use_time is null and login_time is null";
                            break;

                        default:
                            $db_coupon_pool->rollback();
                            throw new \RuntimeException('優惠券不足，無法執行');
                            break;
                    }

                    $coupon_pool_ids = Db::connect($this->config_db)->table($this->coupon_tableName)
                        ->field('id')
                        ->where($cuopon_where)
                        ->limit($value->num)
                        ->select();

                    if(count($coupon_pool_ids) < $value->num){
                        $db_coupon_pool->rollback();
                        throw new \RuntimeException('優惠券不足，無法執行');
                    }

                    $in_id = [];
                    foreach ($coupon_pool_ids as $key => $value) {
                        array_push($in_id, $value['id']);
                    }
                    if($in_id){
                        $in_id = '('.join(',', $in_id).')';
                        $db_coupon_pool->table($this->coupon_tableName)
                            ->where('id in '.$in_id)
                            ->update($change_data);
                    }
                }
            }
        }
        $db_coupon_pool->commit();
    }
}

