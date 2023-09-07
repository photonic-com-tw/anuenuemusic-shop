<?php
namespace app\admin\controller;
use app\admin\controller\MainController;
use think\Request;
use think\Db;
use think\Validate;
use think\Session;
//Photonic Class
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Admin extends MainController
{
	const PER_PAGE_ROWS = 5;
	const SIMPLE_MODE_PAGINATE = false;
	private $DBTextConnecter;
	private $resTableName;
	public function __construct()
	{
		parent::__construct();
		$this->DBTextConnecter = DBTextConnecter::withTableName('admin');
		$this->DBadmin_info = DBTextConnecter::withTableName('admin_info');
		$this->DBFileConnecter = DBFileConnecter::withTableName('admin_info');
		$this->resTableName = 'admin';
	}

	public function edit(Request $request)
	{
		//ini_set('session.gc_maxlifetime', '1880');
		
		$admin_per = Session::get('admin')['permission'];
		
		/*if($admin_per != 'all')
			$this->redirect('Login/logout');*/
			
		$admin = Db::table($this->resTableName)
			->where("permission != 'all' ")
			->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE
			)->each(function($item, $key){
				
				if(empty($item['purview']))
					$item['purview'] ='{}';
				
				return $item;
			});
							
		$this->assign('admin', $admin);
		return $this->fetch('account');
	}

	public function edit2(Request $request)
	{
	
		$admin_per = Session::get('admin')['permission'];
		
		if($admin_per != 'all')
			$this->redirect('Login/logout');
			
		$admin = Db::table($this->resTableName)
			->where("permission != 'all' ")
			->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE
			)->each(function($item, $key){
				
				if(empty($item['purview']))
					$item['purview'] ='{}';
				
				return $item;
			});
					
		$first_list = Db::table('backstage_menu')->select();
		$second_list = Db::table('backstage_menu_second')->select();
		$show_edit_list =[];
		
		foreach($first_list as $k => $v){
			$show_edit_list[$v['id']]['id'] = $v['id'];
			$show_edit_list[$v['id']]['name'] = $v['name'];
		}
			
		foreach($second_list as $k => $v){	
			if(empty($show_edit_list[$v['backstage_menu_id']]['sub'])){
				$show_edit_list[$v['backstage_menu_id']]['sub'] = [];
				
			}
			array_push($show_edit_list[$v['backstage_menu_id']]['sub'],$v);
		}
			
		$this->assign('show_edit_list', $show_edit_list);
		$this->assign('admin', $admin);
		return $this->fetch('account2');
	}

	public function point_set()
	{
		$admin = Db::table('points_setting')->find(3)['value'];
		$this->assign('admin', $admin);

		$use_product = Db::table('points_setting')->where("id = '1'")->find();
		$use_product = $use_product['value'] ? $use_product['value'] : "[]";
		$this->assign('use_product', $use_product);

		return $this->fetch('point_set');
	}
		
	public function point_set_update(){
		
		$value1 = Request::instance()->post('value1');
		try{
			Db::table('points_setting')->where("id = '3'")->update(['value'=>$value1]);	
		} catch (\Exception $e){
			$this->dumpException($e);
		}

		// 儲存適用分館
		$use_product = empty($_POST['use_product']) ? [] : $_POST['use_product'];
		Db::table('points_setting')->where("id = '1'")->update(['value'=> json_encode($use_product)]);

		$this->success('修改成功');
	}
		
	public function maxlifetime_set()
	{
		$admin = Db::connect('main_db')->table('excel')->field('value1')->find(5)['value1'];
		$this->assign('admin', (($admin)));
		return $this->fetch('maxlifetime_set');
	}
		
	public function maxlifetime_update(){
			
		$value1 = Request::instance()->post('value1');
		try{
			Db::connect('main_db')->table('excel')->where("id = '5'")->update(['value1'=>$value1]);
		} catch (\Exception $e){
			$this->dumpException($e);
		}

		$this->redirect('Admin/maxlifetime_set');
	}
		
	public function add()
	{
		$insert = Request::instance()->post();
			
		$insert['purview'] = json_encode($insert['purview']??'');
		
		$rule = [
				'name' 	   => 'require',
			'account'  => 'require',
			'password' => 'require',
				'email'	   => 'require'
		];
		$msg = [
			'name.require' => '名稱不得為空',
			'account.require' => '帳號不得為空',
			'password.require' => '密碼不得為空',
			'email.require' => '信箱不得為空'
		];
		$validate = new Validate($rule,$msg);
		$data = [
			'name'  => $insert['name'],
			'account'  => $insert['account'],
			'password' => $insert['password'],
			'email'  => $insert['email'],
		];
		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}
			
		try{
			
			Db::table($this->resTableName)
			->insert([
				'name' =>  $insert['name'],
				'account' =>  $insert['account'],
				'password' =>  md5($insert['password']),
				'originalPassword' =>  $insert['password'],
				'email' =>  $insert['email'],
				'permission' =>  'no',
				'purview' =>  $insert['purview']
			]);
		} catch (\Exception $e){
			$this->dumpException($e);
		}
			
		$this->success('更新成功');
	}

	public function del() {
		$id = Request::instance()->get('id');
		try{
			Db::table($this->resTableName)->delete($id);
		} catch (\Exception $e){
			$this->dumpException($e);
		}
		$this->success('刪除成功');	
	}

	public function update() {
		$new_password = Request::instance()->post('new_password');
		$rep_password = Request::instance()->post('rep_password');
		$old_password = Request::instance()->post('old_password');
		
		$id = Request::instance()->get('id');
		$adminData = Db::table('admin')->find($id);
		if($adminData['password'] != md5($old_password)){
			$this->dumpError('舊密碼錯誤');
		}
		$rule = [
			'new_password' => 'require|confirm:rep_password'
		];
		$msg = [
			'new_password.require' => '新密碼不得為空',
			'new_password.confirm'=>'新密碼和確認密碼必須一致!'
		];
		$validate = new Validate($rule,$msg);
		
		$data = [
			'rep_password' => $rep_password,
			'new_password' => $new_password
		];
		if (!$validate->check($data)) {
			$this->dumpError($validate->getError());
		}
		$updateData = [
			// 'id' => 1,
			'originalPassword' => $new_password,
			'password' => md5($new_password)
		];
		// var_dump($updateData);exit;
		try{
			Db::table($this->resTableName)->where("id = '".$id."'")->update($updateData);
			// $this->DBTextConnecter->setDataArray($updateData);
			// $this->DBTextConnecter->upTextRow();
		} catch (\Exception $e){
			$this->dumpException($e);
		}

		$this->success('更新成功');
	}
	
	public function current_change(){
			
		$id = Request::instance()->post('id');
		
		try{
			Db::table($this->resTableName)->where("permission = 'current'")->update(['permission'=>'no']);
			Db::table($this->resTableName)->where("id = '".$id."'")->update(['permission'=>'current']);
			return json([
				'status' => true,
				'message' => '設定完成',
			], 200);

		}catch (\Exception $e){
			return json([
				'status' => false,
				'message' => $e->getMessage(),
			], 200);
		}
	}

	public function update_purview() {
			
		$id = Request::instance()->post('up_id');
		if($id == 2 && Session::get('admin')['id'] != 1){
			$this->error('不可修改管理員權限');
		}

		$purview = $_POST['update_purview']??'';
		$purview = json_encode($purview);
		
		$updateData = [
			'id' => $id,
			'purview' =>$purview
		];
		try{
			$this->DBTextConnecter->setDataArray($updateData);
			$this->DBTextConnecter->upTextRow();
		} catch (\Exception $e){
			$this->dumpException($e);
		}
		$this->success('更新成功');
	}

	public function emailUpdate()
	{
		$id = Request::instance()->post('id');
		$email = Request::instance()->post('email');
		try{
			$this->DBTextConnecter->setDataArray([
				'id' => $id,
				'email' => $email
			]);
			$this->DBTextConnecter->upTextRow();
		} catch (\Exception $e){
			return json(['status' => false,'message' => $e->getMessage(),], 200);
		}
		return json(['status' => true,'message' => '更新完成'], 200);
	}

	public function admin_info(){
		$admin_info = Db::table('admin_info')->find(1);
		$this->assign('admin_info', $admin_info);
		return $this->fetch('admin_info');
	}
	public function admin_info_update(){
		$newData = Request::instance()->post();

		$picNameList = ['customer_logo', 'system_logo', 'marketing_logo', 'favicon', 'success_logo', 'error_logo'];
		$file = Request::instance()->file();
		// dump($file);exit;

		try{
			for($i = 1; $i <= count($picNameList); $i++) {
				if( $newData['del_' . $picNameList[ $i-1 ]] ){
					$newData[$picNameList[ $i-1 ]] = '';
					unset($file[$picNameList[ $i-1 ]]);
				}
				unset($newData['del_' .$picNameList[ $i-1 ]]);
			}
		} catch (\Exception $e) {
			$this->dumpException($e);
		}
		// dump($file);exit;
		try{
			$this->DBadmin_info->setDataArray($newData);
			$this->DBadmin_info->upTextRow();
			if(count($file)>0 && !empty($file)){
				$this->DBFileConnecter->setFileArray($file);
				$this->DBFileConnecter->setPrivateKeyId($newData['id']);
				$this->DBFileConnecter->upFileRow();
			}
		} catch (\Exception $e) {
			$this->dumpException($e);
		}
		$this->success('更新成功');
	}


	public function system_email(){
		$system_email = Db::table('system_email')->find(1);
        $this->assign('system_email', $system_email);
		return $this->fetch('system_email');
	}
	public function system_email_update(){
		if(!$_POST['column'])
			$this->error("更新失敗");
		
		$data = [
			$_POST['column'] => $_POST['value']
		];
		Db::table('system_email')->where("id=1")->update($data);
		$this->success('更新成功');
	}
}