<?php

namespace pattern\simpleFactory\orderFactory;

use think\Db;
use think\Controller;
use think\Request;

// use app\admin\controller\MainController; // 訂單後台用
use app\index\controller\PublicController as MainController; // 商品後台用
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

    // protected $order_db = "";           // 訂單後台用
    protected $order_db = "main_db";    // 商品後台用

    public function __construct($id, $tableName, $coupon_tableName) {
        $this->id = $id;
        $this->tableName = $tableName;
        $this->coupon_tableName = $coupon_tableName;


        // 訂單後台用
        // if($this->id){
        //     $orderform =Db::connect($this->order_db)->table($this->tableName)->where('id', $this->id)->find();
        //     $this->config_db = substr($orderform['order_number'],0,1).'_sub';
        // }
        // 商品後台用
        $this->config_db = "";
    }

    abstract public function changeStatus2Next();

    public function setReportNumber($reportNumber) {

        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('report', $reportNumber);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function setReportState() {

        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('report_check_time', time());

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function setReceiptsState($state) {

        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('receipts_state', $state);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function setTransportState($state) {
        try{
            $o = Db::connect($this->order_db)->table($this->tableName)->where('id', $this->id)->find();

            // 派發優惠券
            $this->change_coupon('send');

            // 增加點數
            if($o['add_point']>0){
                $PointRecords = new PointRecords($o['user_id']);
                $records = $PointRecords->add_records([
                    'msg'           => '完成訂單：'.$o['order_number'].'，贈送點數',
                    'points'        => $o['add_point'],
                    'belongs_time'  => time()
                ]);
            }

            // 修改訂單狀態
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
				->update(['transport_state' => $state,'status'=>'Complete']);

        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


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

			$oo=Db::connect($this->order_db)->table('account')->where('id', $o['user_id'])->find();

            $MainController = new MainController(Request::instance());
            $globalMailData = $MainController->getMailData($this->config_db);

			if($oo){
				$email=$oo['email'];
			}else{
				$email=$o['transport_email'];
			}

			$mail = new PHPMailer();
			$mail->IsSMTP();

			$mail->Host = $globalMailData['mailHost'];
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
            $mail->Username = $globalMailData['mailUsername'];
            $mail->Password = $globalMailData["mailPassword"];
            $mail->Subject = $globalMailData["mailSubject"];
            $mail->From = $globalMailData['mailFrom'];
            $mail->FromName = $globalMailData["mailFromName"];

			/*
			$email = Db::connect($this->order_db)->table('admin')->field('email')->find(1);
			$mail->AddAddress($email['email']);
			*/

			$mail->AddAddress($email);
			$mail->IsHTML(true);

			if($oo){
                $mail->Body = "
                    <html>
                        <head></head>

                        <body>
                            <div>
                                    很抱歉，由於您的訂單因故取消，造成您的不便，敬請見諒。<br>
                                    訂單編號：".$o['order_number']."<br>
                                    訂單時間：".date('Y/m/d H:i',$o['create_time'])."<br>
                                    訂購商品：".$res_goods."<br>
                                    訂單金額：".$o['total']."<br>
                                    購買人".$oo['name']."<br>
                                    收件人：".$o['transport_location_name']."<br>
                                    出貨地址：".$o['transport_location']."<br>
                                    E-mail：".$oo['email']."<br>
                                    手機：".$o['transport_location_phone']."<br>
                                    聯絡電話：".$o['transport_location_tele']."<br>
                                    付款方式：".$o['payment']."<br>
                                    備註：".$o['transport_location_textarea']."<br>
                            </div>

                            <div>
                                <br>
                                ". $globalMailData['system_email']['order_cancel'] ."
                            </div>
                        </body>
                    </html>

                ";
            }else{      

                $mail->Body = "
                    <html>
                        <head></head>
                        <body>
                            <div>
                                    很抱歉，由於您的訂單因故取消，造成您的不便，敬請見諒。<br>
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

                            <div>
                                <br>
                                ". $globalMailData['system_email']['order_cancel'] ."
                            </div>
                        </body>
                    </html>
                ";

            }

			$mail->Send();
        }catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function delete() {
        try{
            Db::connect($this->order_db)->table($this->tableName)->delete($this->id);
            return true;
        } catch (\Exception $e){
            throw new \RuntimeException($e->getMessage());
        }
    }


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
                        case 'send':
                            $change_time = time();
                            $cuopon_where = " coupon_id=".$copuon_id." and owner=".$this_order['user_id']." and use_time is null and login_time is null";
                            break;
                        
                        case 'take_back':
                            $change_time = null;
                            $cuopon_where = " coupon_id=".$copuon_id." and owner=".$this_order['user_id']." and use_time is null";
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
                            ->update([
                                'login_time' => $change_time
                            ]);
                    }
                }
            }
        }
        $db_coupon_pool->commit();
    }
}

