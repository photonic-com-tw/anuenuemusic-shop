<?php
namespace app\ajax\controller;

use think\Controller;
use think\Request;
use think\Db;

//Photonic Class
use pattern\MemberInstance;

class Member extends Controller
{
	public function __construct() {
		header("Access-Control-Allow-Origin: *");
        parent::__construct();
		$this->tableName = 'account';

		// $secret = Request::instance()->post('secret');
    }
    public function accountDB(){
    	return Db::connect('main_db')->table($this->tableName);
    }
	
	/*添加會員(單個)*/
	public function addMember(){
		$id = 0;
		$data = Request::instance()->post();

		$newData = [
			// 'id' => $data['id'],
			'phone' => $data['account'],
			'password' => $data['user_pw'],
			'name' => $data['user_name'],
			'email' => $data['email'],
			'birthday' => $data['birth_day'],
			'line_id' => $data['line_id'],
			'F_S_NH_Address' => $data['zipcode'].$data['city'].$data['district'].$data['road'],
			'tele' => $data['telephone'],
		];
		$newData['status'] = 1;
		
		/*新增會員*/
		$MemberInstance = new MemberInstance(0);
		$returnData = $MemberInstance->insert_user_data($newData);
		// if($returnData['code']==0){ $this->error($returnData['msg']); }
		
		return json($returnData);
	}

	/*修改會員資料(密碼外)*/
	public function updateMember(){
		// $id = Request::instance()->post('id');
		// if(!$id){ $this->error('請提供編輯對象'); }

		// $updateData = Request::instance()->post();

		// /*修改會員*/
		// $MemberInstance = new MemberInstance($id);
		// $returnData = $MemberInstance->update_user_data($updateData);
		// if($returnData['code']==0){ $this->error($returnData['msg']); }

		// $this->success('修改成功');
	}
}