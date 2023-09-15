<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Validate;
use DBtool\DBTextConnecter;

class Member extends PublicController
{

	public function __construct()
    {
    	$Request = Request::instance();
		parent::__construct($Request);
		$this->assign('mebermenu_active', 'member');

		$consent = Db::table('consent')->where("id=1")->find();
		$this->assign('consent_other', $consent['other']);

		$referer_url = Request::instance()->server('HTTP_REFERER');
		// dump($referer_url);exit();
		if( preg_match('/'.$_SERVER["HTTP_HOST"].'/i', $referer_url) || !$referer_url){
			if(!$this->user){ $this->error(LANG_MENU['請先登入會員'], url('Login/login'));};
		}else{
			if(!$this->user){ $this->redirect(url('Login/login'));};
		}
	}

	public static function get_member_data(){
		$userD = Db::connect(config('main_db'))->table('account')
			->field("account.*, 
					vip.id as vip_id, vip.vip_name")
			->join('vip_type vip', 'account.vip_type = vip.id', 'left')
			->where('account.id', Session::get('user.id'))
			->find();
		return $userD;
	}

	public function member(){
		$userD = $this->get_member_data();

		$userD['F_I_CNo']='';
		$userD['F_I_TNo']='';
		$userD['F_S_NH_Zip']='';
		$userD['F_S_NH_Address']='';
		$userD['F_I_CNo_Name']='';
		$userD['F_I_TNo_Name']='';

		if($TryStrpos=strpos($userD['home'],"<fat>")){
			$ex = explode("<fat>",$userD['home']);
			$userD['F_I_CNo']=$ex[0];
			$userD['F_I_TNo']=$ex[1];
			$userD['F_S_NH_Zip']=$ex[2];
			$userD['F_S_NH_Address']=$ex[3];

			if(!empty($userD['F_I_CNo']))
				$userD['F_I_CNo_Name'] = Db::table('city')->where(" AutoNo = '".$userD['F_I_CNo']."'")->field('Name')->find()['Name'];

			if(!empty($userD['F_I_TNo']))
				$userD['F_I_TNo_Name'] = Db::table('town')->where(" AutoNo = '".$userD['F_I_TNo']."'")->field('Name')->find()['Name'];
		}else{
			$userD['F_S_NH_Address'] = $userD['home'];
		}
		$this->assign('userD', $userD);

		$city = Db::table('city')->select();
		$this->assign('city', $city);

		return $this->fetch('member');
	}


	public function chpwd()
	{
		$id = Request::instance()->post('id');
		$passwordA = Request::instance()->post('passwordA');
		$passwordB = Request::instance()->post('passwordB');

		// if( !preg_match('/([0-9]+)/' ,$passwordA) || !preg_match('/([a-zA-Z]+)/' ,$passwordA) ){
		// 	$this->error(LANG_MENU['密碼需包含英文及數字']);
		// }
		if( preg_match('/[^A-Za-z0-9 ]/' ,$passwordA) || strlen($passwordA)<5 || strlen($passwordA)>14 ){
			$this->error(LANG_MENU['密碼需包含英文及數字']);
		}
		if($passwordA != $passwordB){
			$this->error(LANG_MENU['密碼不一致']);
		}

		$data = [
			'id' => $id,
			'pwd' => md5($passwordA)
		];

		try{
			Db::connect(config('main_db'))->table('account')->update($data);
        } catch (\Exception $e){
            $this->error(LANG_MENU['發生錯誤']);
		}
		$userD = Db::connect(config('main_db'))->table('account')->find($this->user['id']);

		Session::set('user', $userD);

        $this->success(LANG_MENU['操作成功']); /*更新成功*/
	}

	public function chdata()
	{
		$data = Request::instance()->post();

		$rule = [
			'name'  => 'require',
			'phone' => 'require|number',
			'F_S_NH_Address' => 'require',
			// 'F_I_CNo' => 'require',
			// 'F_I_TNo' => 'require',
			// 'F_S_NH_Zip' => 'require'
		];
		$msg = [
			'name.require' => LANG_MENU['名稱不得為空'],
			'phone.require' => LANG_MENU['手機不得為空'],
			'phone.number' => LANG_MENU['手機只能是數字'],
			'F_S_NH_Address.require' => LANG_MENU['地址不得為空'],
			// 'F_I_CNo.require' => LANG_MENU['請選擇縣市'],
			// 'F_I_TNo.require' => LANG_MENU['請選擇區'],
			// 'F_S_NH_Zip.require' => LANG_MENU['請確認郵遞區號有填寫'],
		];
		$validate = new Validate($rule,$msg);
		$validate->rule('regex', '/^.[A-Za-z0-9]+$/');

		if ($validate->check($data)) {
			$F_I_CNo = isset($data['F_I_CNo']) ? $data['F_I_CNo'] : "";
			$F_I_TNo = isset($data['F_I_TNo']) ? $data['F_I_TNo'] : "";
			$F_S_NH_Zip = isset($data['F_S_NH_Zip']) ? $data['F_S_NH_Zip'] : "";
			if($F_I_CNo != '' && $F_I_TNo !='' && $F_S_NH_Zip !=''){
				$data['home'] = $F_I_CNo.'<fat>'.$F_I_TNo.'<fat>'.$F_S_NH_Zip.'<fat>'.$data['F_S_NH_Address'];
			}else{
				$data['home'] = $data['F_S_NH_Address'];
			}

			unset($data['F_I_CNo']);
			unset($data['F_I_TNo']);
			unset($data['F_S_NH_Zip']);
			unset($data['F_S_NH_Address']);

			$data['birthday'] = strtotime($data['birthday']);
			try{
				Db::connect(config('main_db'))->table('account')->update($data);			
	        } catch (\Exception $e){
	            $this->error(LANG_MENU['內容有誤']);
			}
			$userD = Db::connect(config('main_db'))->table(	'account')->find($this->user['id']);

			Session::set('user', $userD);
	        $this->success(LANG_MENU['操作成功']);
	    }else{
	    	$this->error($validate->getError());
	    }
	}

	/* 我的註冊商品 */
	public function reg_product(){
		$this->assign('mebermenu_active', 'reg_product');
		$userD = Db::table('excel e')
				->field('e.*, er.product_type_code')
				->join('excel_reply er', 'er.product_code = e.product_code', 'left')
				->where("e.account_number ='".$this->user['id']."'")
				->order('e.id desc')
				->group('e.id desc')
				->paginate(10);

		$page = $userD->render();
		$this->assign('page', $page);
		$this->assign('userD',$userD);
		//var_dump($orderform);die();

		return $this->fetch('reg_product');
	}

	/* 我的收藏商品 */
	public function product_store()
	{
		$this->assign('mebermenu_active', 'product_store');
		$products = Db::table('product_store ps')
					->field('p.*')
					->join('productinfo p', 'p.id=ps.product_id', 'left')
					->where("ps.user_id ='".$this->user['id']."'")
					->where("p.online !=2")	/*不是關閉的*/
					->order('ps.id desc')
					->paginate(20)
					->each(function($item, $key){
						$item['pic'] =  json_decode($item['pic'],true)[0];
						return $item;
					});

		$page = $products->render();
		$this->assign('page', $page);
		$this->assign('products',$products);

		return $this->fetch();
	}

	/* 註冊商品 */
	public function reg_product_sign()
	{
		return $this->fetch();
	}
}

