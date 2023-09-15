<?php

namespace pattern;

use think\Db;

class PointRecords
{   
    private $db_connect;
    private $user_id;

    public function __construct($user_id=0) {
        $this->db_connect   = Db::connect('main_db'); /*商品網站用此連線*/
        // $this->db_connect   = Db::connect(); /*訂單網站用此連線*/
        $this->user_id      = $user_id;
    }
    /*跟換物件user_id*/
    public function change_user_id($user_id){
        $this->user_id      = $user_id;
    }

    /*新增點數紀錄*/
    public function add_records($insertData){
        $insertData['user_id'] = $this->user_id;
        $insertData['msg_time'] = date('Y-m-d H:i:s');
        $records = $this->db_connect->table('points_record')->insertGetId($insertData);
        $this->db_connect->table('account')->where('id', $this->user_id)->setInc('point', $insertData['points']);
        return $records;
    }

    /*取得點數紀錄*/
    public function get_user_records(){
        $records = $this->db_connect->table('points_record')->where('user_id','=',$this->user_id)->order('id desc')->select();
        return $records;
    }

    /*取得當前點數*/
    public function get_current_poiints(){
        $account = $this->db_connect->table('account')->where('id','=',$this->user_id)->find();
        return $account['point'];
    }
    
    /*取得過期點數*/
    public function get_expiring_poiints($type="expiring"){
        $points_belong_time = strtotime( $this->expire_point_belongs_time($type) );
        $expiring_poiints = $this->db_connect->table('points_record')
                                    ->where('user_id','=', $this->user_id)
                                    ->where('belongs_time', '<', $points_belong_time)
                                    ->order('id desc')->sum('points');
        $current_used_points = $this->db_connect->table('points_record')
                                    ->where('user_id','=', $this->user_id)
                                    ->where('belongs_time', '>', $points_belong_time)
                                    ->where('points', '<', 0)
                                    ->order('id desc')->sum('points');

        $expiring_poiints += $current_used_points;

        $expiring_poiints = $expiring_poiints < 0 ? 0 : $expiring_poiints;
        return $expiring_poiints;
    }
    public function expire_point_belongs_time($type="expiring"){
        $excel = $this->db_connect->table('excel')->where('id','=','2')->find();
        $expire_period = $excel['value1']; /*年 ex:1 */
        $expire_date   = $excel['value2']; /*結算日 ex:12-31 */

        $time_str = date('Y').'-'.$expire_date. ' -'.$expire_period.'Year +1Day';

        if($type=="expired") { /*找已過期*/ 
            if( strtotime(date('Y-m-d')) <= strtotime( date('Y').'-'.$expire_date) ){ /*如果當前日期比設定過期日的月日小或一樣*/
                $time_str.=' -1Year'; /*需額外扣除一年*/
            }
        }
        
        return $time_str;
    }
    public function expiring_count_date(){
        $excel = $this->db_connect->table('excel')->where('id','=','2')->find();
        $expire_date   = $excel['value2']; /*結算日 ex:12-31 */
        $count_date = date('Y').'-'.$expire_date;
        return $count_date;
    }

    /*點數記錄依user_id群組起來，用於取得所有有點數的會員，*/
    public function get_member_has_points(){
        $records = $this->db_connect->table('points_record')->group('user_id')->select();
        return $records;
    }
    /*設定點數成過期*/
    public function set_point_expire(){
        $expired_poiints = $this->get_expiring_poiints($type="expired");

        if($expired_poiints>0){
            $expired_poiints = $expired_poiints * (-1);
            $time_str = strtotime( $this->expire_point_belongs_timeexpire_point_belongs_timeexpire_point_belongs_time($type="expired").' -1Day');
            $count_date = date('Y-m-d', $time_str);
            $this->add_records([
                'msg'           => $count_date.' '.LANG_MENU['點數到期'],
                'points'        => $expired_poiints,
                'belongs_time'  => time()
            ]);
        }

        return ['user_id'=>$this->user_id, 'expired_poiints'=>$expired_poiints];
    }
}
