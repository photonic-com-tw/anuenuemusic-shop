<?php
	namespace app\ajax\controller;

	use think\Controller;
	use think\Request;
	use think\Db;

	use DBtool\DBTextConnecter;

	class VipType extends Controller
	{	

		public function __construct() {
	        parent::__construct();	
			$this->accountDB = DBTextConnecter::withTableName('account', 'main_db');		
	    }

	    // 取得vip等級
	    public static function get_types(){
	    	return Db::connect('main_db')->table("vip_type")->where("id != 0")->order('rule', 'asc')->order('id', 'asc')->select();
	    }

	    // 更新VIP等級
		public function update_vip_type($user_id, $vip_type)
		{
			// 修改account的vip_type
			$newData = [
				'id' => $user_id,
				'vip_type' => $vip_type
			];
			$this->accountDB->setDataArray($newData);
			$this->accountDB->upTextRow();

			$vip_relation = Db::connect('main_db')->table('vip_type_relation')->where('user_id', $user_id)->order('id desc')->select();
			if($vip_relation){ // 如果有紀錄
				if($vip_relation[0]['vip_type_id']==$vip_type){ // 最新一筆紀錄與此次修改相同
					return; // 略過新增紀錄
				}
			}

			// 新增紀錄
			$recordData = [
				'user_id' => $user_id,
				'vip_type_id' => $vip_type,
				'datetime' => date('Y-m-d H:i:s')
			];
			Db::connect('main_db')->table('vip_type_relation')->insert($recordData);
		}

		// http://full-admin.sprlight.net/index/vip_type/auto_set_type
		// 自動設定等級
		public function auto_set_type()
		{
			$orderform = Db::connect('main_db')->table('orderform')
						->where("user_id != 0")
						// ->where("receipts_state", 1)
						// ->where("transport_state", 1)
						->where("status", "Complete")
						->select();
			// dump($orderform);

			$user = [];
			foreach ($orderform as $key => $value) {
				if( !isset($user[$value['user_id']]) ) $user[$value['user_id']] = 0;
				$user[$value['user_id']] += $value['total'];
			}
			// dump($user);

			foreach ($user as $key => $value) {
				// 取得目標等級
				$new_type = $this->check_vip_type($value);
				if(!$new_type) continue;

				// // 取得舊等級
				// $user_data = Db::connect('main_db')->table('account')->find($key);
				// if(!$user_data) continue;
				// $old_type = Db::connect('main_db')->table('vip_type')->find($user_data['vip_type']);
				
				// if($old_type){ // 如果有舊等級
				// 	if($old_type['id'] == $new_type['id']){ // 新舊等級一樣
				// 		continue; // 略過更新
				// 	}else if($old_type['rule'] > $new_type['rule']){ // 舊等級條件 大於 新等級條件
				// 		continue; // 略過更新
				// 	}else if($old_type['rule'] == $new_type['rule'] && $old_type['id'] > $new_type['id']){ // 新舊等級條件一樣 且 舊id 大於 新id
				// 		continue; // 略過更新
				// 	}
				// }

				// 處理等級更新
				print_r("會員:\t".$key."\t等級id：\t".$new_type['id']);
				$this->update_vip_type($key, $new_type['id']);
			}
		}
		
		// 依累積金額取得對應VIP等級id
		public function check_vip_type($total)
		{
			// dump($total);
			$vip_type = $this->get_types();
			if(!$vip_type) return [];
			$last_index = count($vip_type) - 1;

			$target_vip_type = []; // 預設沒有等級
			foreach ($vip_type as $key => $value) {
				if($total>=(int)$value['rule']){ // 如果消費金額比條件金額大
					if($key==$last_index){ // 此階層已是最後一階層
						$target_vip_type = $value; // 當前等級就是所屬等級
						break;
					}else{
						continue; // 到下一等級比對
					}
				
				}else if($key!=0){ // 如果消費金額比條件金額小 且 不是第一個階層
					$target_vip_type = $vip_type[$key-1]; // 當前等級的前一等就是所屬等級
					break;
				}else{
					break;
				}
			}

			// dump($target_vip_type);
			return $target_vip_type;
		}
	}

?>			






























