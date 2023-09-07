<?php
namespace pattern;

use think\Db;
use think\Validate;

class MemberInstance
{   
    private $user_id;
    static private $tableName = 'account';
    static private $orderTableName = 'orderform';
    static public $account_column = 'email'; /*帳號欄位*/

    public function __construct($user_id=0) {   
        $this->user_id = $user_id;
    }
    /*取得會員資料庫連線*/
    static public function main_db(){
    	return Db::connect('main_db');
    }
    /*更換當前會員id*/
    public function change_user_id($user_id){
        $this->user_id = $user_id;
    }

    /*取得會員資料*/
    public function get_user_data($addr_change="combine", $cond=[]){
    	$userData = self::main_db()->table(self::$tableName.' a')
			    			->field("a.*, 
									 vip.id as vip_id, vip.vip_name")
							->join('vip_type vip', 'a.vip_type = vip.id', 'left');
    	if($cond){ /*有傳入篩選條件*/
    		$userData = $userData->where($cond); /*依篩選條件搜尋*/
    	}else{
			$userData = $userData->where('a.id', $this->user_id); /*依物件user_id搜尋*/
    	}

		$userData = $userData->find();

		if( $userData ){ /*額外為顯示處理資料*/
			/*生日轉換為日期*/
			$userData['birthday'] = $userData['birthday'] ? date('Y-m-d', $userData['birthday']) : "";
			
			/*處理地址*/
			if($addr_change=="combine"){ /*合併顯示地址*/
		    	try{
					if($userData['home']){
						$addcods = explode('<fat>',$userData['home']);
						
						$city = Db::connect(config('A_sub'))->table('city')->where("AutoNo = ".$addcods[0])->select();
						$town = Db::connect(config('A_sub'))->table('town')->where("AutoNo = ".$addcods[1])->select();
						$post = $addcods[2];
						$otheradd = $addcods[3];
						
						$userData['home'] = $post.' '.$city[0]['Name'].$town[0]['Name'].$otheradd;
					}
				}catch (\Exception $e){
					$addcods = explode('<fat>',$userData['home']);
					$userData['home'] = count($addcods)>1 ? ' ' : $userData['home'];
				}
			}
			else if($addr_change=="split"){ /*獨立成縣市、區、郵遞區號*/
				$userData['F_I_CNo']='';
				$userData['F_I_TNo']='';
				$userData['F_S_NH_Zip']='';
				$userData['F_S_NH_Address']='';
				$userData['F_I_CNo_Name']='';
				$userData['F_I_TNo_Name']='';

				if($TryStrpos=strpos($userData['home'],"<fat>")){
					$ex = explode("<fat>",$userData['home']);
					$userData['F_I_CNo']=$ex[0];
					$userData['F_I_TNo']=$ex[1];
					$userData['F_S_NH_Zip']=$ex[2];
					$userData['F_S_NH_Address']=$ex[3];

					if(!empty($userData['F_I_CNo']))
						$userData['F_I_CNo_Name'] = Db::table('city')->where(" AutoNo = '".$userData['F_I_CNo']."'")->field('Name')->find()['Name'];

					if(!empty($userData['F_I_TNo']))
						$userData['F_I_TNo_Name'] = Db::table('town')->where(" AutoNo = '".$userData['F_I_TNo']."'")->field('Name')->find()['Name'];
				}else{
					$userData['F_S_NH_Address'] = $userData['home'];
				}
			}
		}

    	return $userData;
    }
    /*修改會員資料*/
    public function update_user_data($updateData, $cond=[], $change_format=true){
    	$lang_menu = get_lang_menu();

    	$returnData = ['code'=>0, 'msg'=>"", 'data'=>[]];
    	if( empty($updateData) ){ $returnData['msg'] = $lang_menu['資訊不足']; } /*請提供修改資料*/

    	if( isset($updateData[self::$account_column]) ){ /*要修改帳號*/
			/*檢查是否有與「其他」帳號重複*/
			$adminData = $this->get_user_data($addr_change="ori", [
									'a.'.self::$account_column=>array('eq', $updateData[self::$account_column]), 
									'a.id'=>array('neq', $this->user_id)
								]);
			if($adminData){
				$returnData['msg'] = $lang_menu['內容有誤']; /*帳號已經存在*/
				return $returnData;
			}
		}

		if($change_format){ /*調整帳號資料*/
			$returnData = self::arrange_data_to_db_format($updateData);
			if($returnData['code']==0){ /*調整資料時有錯誤訊息*/
				return $returnData;
			}else{
				$updateData = $returnData['data'];
			}
		}
		unset($updateData['id']);

		/*篩選要修改的對象*/
		$userData = self::main_db()->table(self::$tableName.' a');
    	if($cond){ /*有傳入篩選條件*/
    		$userData = $userData->where($cond); /*依篩選條件搜尋*/
    	}else{
			$userData = $userData->where('a.id', $this->user_id); /*依物件user_id搜尋*/
    	}
    	/*修改資料*/
    	$result = $userData->update($updateData);

    	if($result){
    		$returnData['code'] = 1;
    		$returnData['msg'] = $lang_menu['操作成功']; /*修改成功*/
    		$returnData['data'] = $this->get_user_data($addr_change="combine", $cond);
    	}else{
    		$returnData['msg'] == $lang_menu['操作成功']; /*無資料需要修改*/
    	}

    	return $returnData;
    }
    /*新增會員資料*/
    public function insert_user_data($newData, $change_format=true){
    	$lang_menu = get_lang_menu();
    	$returnData = ['code'=>0, 'msg'=>"", 'data'=>[]];

    	/* 判斷帳號唯一性 */
	    	$where = [];
	    	if(isset($newData['id'])){ /*有設定id*/
	    		if($newData['id']!=""){
	    			$where['a.id'] = array('eq', $newData['id']);
	    		}
	    	}
	    	if(isset($newData[self::$account_column])){ /*有設定帳號*/
	    		if($newData[self::$account_column]!=""){
	    			$where['a.'.self::$account_column] = array('eq', $newData[self::$account_column]);
	    		}
	    	}
	    	if(isset($newData['gmail'])){ /*有設定goolge帳號*/
	    		if($newData['gmail']!=""){
	    			$where['a.gmail'] = array('eq', $newData['gmail']);
	    		}
	    	}
	    	if(isset($newData['line_id'])){ /*有設定line_id*/
	    		if($newData['line_id']!=""){
	    			$where['a.line_id'] = array('eq', $newData['line_id']);
	    		}
	    	}
	    	if(isset($newData['FB_id'])){ /*有設定FB_id*/
	    		if($newData['FB_id']!=""){
	    			$where['a.FB_id'] = array('eq', $newData['FB_id']);
	    		}
	    	}
	    	if($where){
				$accounts = $this->get_user_data($addr_change="ori", $where);

				if($accounts){
					$returnData['msg'] = $lang_menu['內容有誤']; /*帳號已經存在*/
					return $returnData;
				}
	    	}

    	if($change_format){ /*調整帳號資料*/
	    	$returnData = self::arrange_data_to_db_format($newData);
			if($returnData['code']==0){ /*調整資料時有錯誤訊息*/
				return $returnData;
			}else{
				$newData = $returnData['data'];
			}
    	}

    	/*額外新會員資料*/
    	$newData['number'] = config('subDeparment') . 'US' . date('Ymd') . self::getMemberNumber();
    	$newData['createtime'] = time();

    	$newData['upline_user'] = isset($newData['upline_user']) ? $newData['upline_user'] : 0;
		if($newData['upline_user']){
			$recommend_user = Db::connect(config('main_db'))->table('account')->field('id,name,number,recommend_content')->where('number', $newData['upline_user'])->find();
			$newData['upline_user'] = $recommend_user ? $recommend_user['id'] : 0;
		}else{
			$newData['upline_user'] = 0;
		}

    	if($returnData['msg']==""){
    		// dump($newData);exit;
    		self::main_db()->table(self::$tableName)->insert($newData);
    		$returnData['data'] =  $this->get_user_data($addr_change="combine", $where);
    		$returnData['code'] = 1;
    	}

    	return $returnData;
    }

    /*依條件計算當前會員的訂單資料*/
    public function get_user_order_data($cond=[]){
        $orderform = self::main_db()->table(self::$orderTableName)->where('user_id', $this->user_id);

        if( isset($cond['status']) ){ /*篩選訂單狀態*/
        	if( $cond['status']=='All' ){ /*全部的成立訂單*/
        		$orderform = $orderform->where('status not in ("Cancel", "Return")');
        	}
        	else if( $cond['status']=='All_no' ){ /*全部的失效訂單*/
        		$orderform = $orderform->where('status in ("Cancel", "Return")');
        	}
        	else{
        		$orderform = $orderform->where('status', $cond['status']);
        	}
        }

        /*判斷計算方式*/
        $method = isset($cond['method']) ? $cond['method'] : 'sum'; /*預設加總金額*/
        if( $method=='select' ){
        	$calculated_order_price = $orderform->order('id desc')->select();
        }
        else if( $method=='count' ){
        	$calculated_order_price = $orderform->count();
        }else{
        	$calculated_order_price = $orderform->sum('total');
        }

        return $calculated_order_price;
    }

    /*替當前會員建立抽抽樂紀錄*/
    public function set_lucky_draw($price=0, $pay_record_id=0, $order_id=0){
    	$times = 0;

    	/*檢查登入(使用者資料)*/
    	$user_data = $this->get_user_data();
		if(!$user_data){ return $times; }

		/*檢查會員等級*/
		$vip_type_id = $user_data['vip_type'];
		$vip_type_id = $vip_type_id==0 && $vip_type_id=="" ? "-1" : $vip_type_id;
		$vip_type = self::main_db()->table('vip_type')->find($vip_type_id);
		if(!$vip_type){ /*沒設定為VIP會員(不限等級) 或 設定之等級不存在*/
			return $times;
		}else if($vip_type['id']=='0'){
			return $times;
		}

		/*計算消費金額可刮次數*/
		$limit_price = Db::table('consumption_draw_limit')->find(1)['price'];
		$times = floor( $price / $limit_price );
		if($times < 1) return $times;

		/*逐次建立刮刮樂資料*/
		foreach( range(1, $times) as $once ){
			/*從1到所有啟用獎品之數量加總中 隨機取得一亂數*/
			$ratios = Db::table('consumption_draw')->where('online', 1)->sum('ratio');
			$rand_num = rand(1, $ratios);

			/*根據取得的亂數確認中獎項目*/
			$draw_id = 0;
			$gift_pic = "";
			$gift_name = "";
			$calculated_num = 0;
			$consumption_draws = Db::table('consumption_draw')->where('online', 1)->order('ratio asc, id desc')->select();
			foreach ($consumption_draws as $consumption_draw) {
				$calculated_num += $consumption_draw['ratio'];
				if( $calculated_num >= $rand_num){
					$draw_id = $consumption_draw["id"];
					$gift_pic = $consumption_draw["pic"];
					$gift_name = $consumption_draw["name"];
					break;
				}
			}

			$data = [
				'user_id' => $this->user_id,
				'pay_record_id' => $pay_record_id,
				'order_id' => $order_id,
				'draw_id' => $draw_id,
				'gift_pic' => $gift_pic,
				'gift_name' => $gift_name,
				'createdate' => time(),
				'show' => 0,
			];
			Db::table('consumption_draw_record')->insert($data);
		}

		return $times;
    }

    /*依搜尋條件取得會員列表資料*/
	static public function search_member($status, $request){
		// 取得搜尋變數
		$tag = $request->get('tag') ?? '1';
		$search_result['tag'] = $tag;

		/*會員搜尋資料*/
		$memberKey = $request->get('memberKey') ?? '';
		$search_result['memberKey'] = $memberKey;

		$memberFromType = $request->get('memberFromType') ?? '';
		$search_result['memberFromType'] = $memberFromType;

		$nameKey = $request->get('nameKey') ?? '';
		$search_result['nameKey'] = $nameKey;
		
		$vipType = $request->get('vipType') ?? '';
		$search_result['vipType'] = $vipType;

		$date_st = $request->get('date_st') ?? '';
		$search_result['date_st'] = $date_st;
		
		$date_en = $request->get('date_en') ?? '';
		$search_result['date_en'] = $date_en;
		
		/*註冊商品搜尋資料*/
		$searchKey1 = $request->get('searchKey1') ?? '';
		$search_result['searchKey1'] = $searchKey1;

		$buy_date_st1 = $request->get('buy_date_st1') ?? '';
		$search_result['buy_date_st1'] = $buy_date_st1;
		
		$buy_date_en1 = $request->get('buy_date_en1') ?? '';
		$search_result['buy_date_en1'] = $buy_date_en1;

		$reg_date_st = $request->get('reg_date_st') ?? '';
		$search_result['reg_date_st'] = $reg_date_st;
		
		$reg_date_en = $request->get('reg_date_en') ?? '';
		$search_result['reg_date_en'] = $reg_date_en;

		/*購買商品搜尋資料*/
		$searchKey2 = $request->get('searchKey2') ?? '';
		$search_result['searchKey2'] = $searchKey2;
		
		$buy_date_st2 = $request->get('buy_date_st2') ?? '';
		$search_result['buy_date_st2'] = $buy_date_st2;
	
		$buy_date_en2 = $request->get('buy_date_en2') ?? '';
		$search_result['buy_date_en2'] = $buy_date_en2;

		
		// 處理sql篩選語句		
		$account_number_list = '';
		$number_list = '';
		$excel_wh = '';
		$account_number = '';
		$his_number = '';
		$excel_number = 0;
		$order_number = 0;

		$number_list .= "( a.name like '%" . $memberKey . "%' or 
						   a.number like '%" . $memberKey . "%' or 
						   a.email like '%" . $memberKey . "%' or 
						   a.phone like '%" . $memberKey . "%' )";

		if($date_st != ''){
			if($number_list != '') $number_list .= " and ";
			$number_list .= "(a.createtime BETWEEN '".strtotime($date_st)."' and '".(strtotime($date_en)+84600)."')";
		}

		if($vipType != ''){
			if($vipType=="-1"){ // 搜尋無等級會員
				$number_list .= " and a.vip_type=0 ";
			}else{ // 搜尋其他等級會員
				$number_list .= " and a.vip_type=".$vipType;
			}
		}

		if($memberFromType != ''){
			$number_list .= " and a.number like '".$memberFromType."%'";
		}

		$rowData = self::main_db()->table(self::$tableName.' a')
				->field("a.*,
						vip_type.id as vip_id, vip_type.vip_name")
				->join('vip_type', 'a.vip_type = vip_type.id', 'left')
				->where($number_list." and a.status like '%".$status."%'")
				->order('id desc')
				->select();
		$search_result['rowData'] = $rowData;

		$do_number = 0;
		$h_number = []; // 已註冊/已購買的會員
		$n_number = []; // 未註冊/未購買的會員
		if($nameKey == "1"){ // 註冊商品搜尋
			foreach ($rowData as $key => $value) {
				$reg_where = "";

				$reg_where = "account_number = '".$value['id'] ."'";
				if($searchKey1 != ''){
					if($reg_where != '')
							$reg_where .= " and ";
						
					$reg_where .= "product_name like '%".$searchKey1."%'";
				}
				
				
				if($buy_date_st1 != ''){
					if($reg_where != '')
							$reg_where .= " and ";
					$reg_where .= "(buytime BETWEEN '".$buy_date_st1."' and '".$buy_date_en1."' ) ";
				}			
				
				if($reg_date_st != ''){
					if($reg_where != '')
							$reg_where .= " and ";	
					$reg_where .= "(regtime BETWEEN '".$reg_date_st."' and '".$reg_date_en."') ";
				}			

				//dump($reg_where);
				$ck = Db::connect(config('A_sub'))->table('excel')->where($reg_where)->group('account_number')->field('account_number')->select();
				$rowData[$key]['reg'] = '0';
				if($ck){
					$rowData[$key]['reg'] = '1';
					$do_number++;
					array_push($h_number,$rowData[$key]);
				}else{
					array_push($n_number,$rowData[$key]);
				}
			}
		}else if($nameKey == "2"){ // 購買商品搜尋
			foreach ($rowData as $key => $value) {
				$his_wh = '';

				if($searchKey2 != ''){
					$his_wh = "product like '%" . $searchKey2 . "%'  and";
				}
				if($buy_date_st2 != ''){
					$his_wh .= " create_time > '".strtotime($buy_date_st2)."' and create_time <'".(strtotime($buy_date_en2)+86400)."' and ";			
				}

				//dump($his_wh);
				$ck = self::main_db()->table(self::$orderTableName)->field('user_id')
						->where($his_wh."  user_id = '".$value['id']."'")
						->group('user_id')
						->select();
				$rowData[$key]['buy'] = '0';
				
				if($ck){
					$rowData[$key]['buy'] = '1';
					$do_number++;
					array_push($h_number,$rowData[$key]);
				}else{
					array_push($n_number,$rowData[$key]);
				}
			}
		}
		$search_result['do_number'] = $do_number;
		$search_result['h_number'] = $h_number;
		$search_result['n_number'] = $n_number;

		return $search_result;
	}
	/*依搜尋條件回傳屬於的user_id sql篩選語法*/
	static public function user_id_sql($cond=[]){
		$user_id_sql = "";
		if(empty($cond)){ return $user_id_sql; }

		$users = self::main_db()->table(self::$tableName.' a');
		
		if(isset($cond['searchKey'])){ /*關鍵字篩選(姓名、會員編號)*/
			if($cond['searchKey']){
				$users = $users->where(function ($query) use ($cond) {
				            $query->where('name', 'like', "%".$cond['searchKey']."%")
				                  ->whereOr('number', 'like', "%".$cond['searchKey']."%");
				        });
			}
		}

		$users = $users->select();

        $user_ids = [];
        foreach ($users as $value) {
        	array_push($user_ids, $value['id']);
        }
        if($user_ids){
        	$user_id_sql = 'user_id in ('. implode(",", $user_ids) .')';
        }

        return $user_id_sql;
	}
	
	/*取得新會員編號*/
	static public function getMemberNumber(){
		$count = self::main_db()->table(self::$tableName)->where('number like "'.config('subDeparment').'US'.date('Ymd').'%"')->order('id desc')->find();
		$count = $count ? intval(substr($count['number'],-3)) + 1 : 1;
		if($count < 10){
			$count = '00' . $count;
		}else if($count < 100){
			$count = '0' . $count;
		}

		return $count;
	}
	/*調整資料格式以符合資料庫*/
	static public function arrange_data_to_db_format($data){
		$lang_menu = get_lang_menu();
		$returnData = ['code'=>0, 'msg'=>"", 'data'=>[]];

		/*設定檢查*/
			/*必填欄位*/
				$rule = [];
				$msg = [];
				if( isset($data['email']) ){
					$rule['email'] = 'require|email';
					$msg['email.email'] = $lang_menu['email格式錯誤'];
					$msg['email.require'] = $lang_menu['email不得為空'];
				}
				if( isset($data['name']) ){
					$rule['name'] = 'require';
					$msg['name.require'] = $lang_menu['名稱不得為空'];
				}
				if( isset($data['phone']) ){
					$rule['phone'] = 'require|number';
					$msg['phone.require'] = $lang_menu['手機不得為空'];
					$msg['phone.number'] = $lang_menu['手機只能是數字'];
				}
				if( isset($data['F_S_NH_Address']) ){
					$rule['F_S_NH_Address'] = 'require';
					$msg['F_S_NH_Address.require'] = $lang_menu['地址不得為空'];
				}
				// if( isset($data['F_I_CNo']) ){
				// 	$rule['F_I_CNo'] = 'require';
				// 	$msg['F_I_CNo.require'] = $lang_menu['請選擇縣市'];
				// }
				// if( isset($data['F_I_TNo']) ){
				// 	$rule['F_I_TNo'] = 'require';
				// 	$msg['F_I_TNo.require'] = $lang_menu['請選擇區'];
				// }
				// if( isset($data['F_S_NH_Zip']) ){
				// 	$rule['F_S_NH_Zip'] = 'require';
				// 	$msg['F_S_NH_Zip.require'] = $lang_menu['請確認郵遞區號有填寫'];
				// }
				/*進行驗證*/
				$validate = new Validate($rule,$msg);
				$validate->rule('regex', '/^.[A-Za-z0-9]+$/');
				if(!$validate->check($data)){
		    		$returnData['msg'] = $validate->getError();
		    		return $returnData;
				}

			/*檢查密碼*/
				if( isset($data['password']) || isset($data['pwd']) ){
					$password = isset($data['password']) ? $data['password'] : "";
					$password = isset($data['pwd']) ? $data['pwd'] : $password;
					// if( !preg_match('/([0-9]+)/' ,$password) || !preg_match('/([a-zA-Z]+)/' ,$password)){
					// 	$returnData['msg'] = $lang_menu['密碼需包含英文及數字'];
					// 	return $returnData;
					// }
					if( preg_match('/[^A-Za-z0-9 ]/' ,$password) || strlen($password)<5 || strlen($password)>14 ){
						$returnData['msg'] = $lang_menu['密碼需包含英文及數字'];
			    		return $returnData;
					}
					if( isset($data['passwordB']) ){
						if($password != $data['passwordB']){
							$returnData['msg'] = $lang_menu['密碼不一致'];
				    		return $returnData;
						}
					}
					$data['pwd'] = $password;
				}

		/*資料修改格式*/
			/*密碼*/
				if(isset($data['pwd'])){
					$data['pwd'] = md5($data['pwd']);
				}

			/*生日*/
		    	if(isset($data['birthday'])){
		    		if($data['birthday']){
		    			$data['birthday'] = strtotime($data['birthday']);
		    			if(!$data['birthday']){
							$returnData['msg'] = $lang_menu['生日格式請輸入YYYY/MM/DD'];
							return $returnData;
						}
		    		}
	    		}

	    	/*地址*/
		    	$home = "";
		    	if( isset($data['F_I_CNo']) && isset($data['F_I_TNo']) && isset($data['F_S_NH_Zip']) ){
		    		if( $data['F_I_CNo'] != '' && $data['F_I_TNo'] !='' && $data['F_S_NH_Zip'] !=''){
						$home = $data['F_I_CNo'].'<fat>'.$data['F_I_TNo'].'<fat>'.$data['F_S_NH_Zip'].'<fat>';
					}
				}
				if( isset($data['F_S_NH_Address']) ){
					$home .= $data['F_S_NH_Address'];
				}
				if($home){
					$data['home'] = $home;
				}
				unset($data['F_I_CNo']);
				unset($data['F_I_TNo']);
				unset($data['F_S_NH_Zip']);
				unset($data['F_S_NH_Address']);

				unset($data['password']);
				unset($data['passwordB']);
				unset($data['term']);

		$returnData['code'] = 1;
		$returnData['data'] = $data;
    	return $returnData;
	}
}
