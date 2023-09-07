<?php
namespace app\order\controller;
use app\order\controller\MainController;
use think\Request;
use think\Db;
use think\Validate;
use think\Session;

use app\ajax\controller\VipType;

class Admin extends MainController
{	
	public function point_set(){
		$admin = Db::connect('main_db')->table('excel')->field('value1')->find(2)['value1'];
		$this->assign('admin', $admin);

		return $this->fetch('point_set');
    }
	public function point_set_update(){
		$value1 = Request::instance()->post('value1');
		try{
			Db::connect('main_db')->table('excel')->where("id = '2'")->update(['value1'=>$value1]);
        } catch (\Exception $e){
            $this->dumpException($e);
        }
		$this->redirect('Admin/point_set');
	}


	public function member_discount(){
		$fisrt_buy = Db::connect('main_db')->table("vip_type")->where("id = 0")->find();
		$this->assign('fisrt_buy', $fisrt_buy);

		$vip_type = VipType::get_types();
		$this->assign('vip_type', $vip_type);

		return $this->fetch('member_discount');
	}
	public function add_vip_type(){
		$insert = Request::instance()->post();
		$rule = [
			'type'  	=> 'require',
			'vip_name'	=> 'require',
			'discount' 	=> 'require',
		];
		$msg = [
			'type.require'		=> '請選擇折扣方式',
			'vip_name.require' 	=> '名稱不得為空',
			'discount.require'	=> '優惠值不得為空',
		];
		$validate = new Validate($rule,$msg);
		$data = [
			'type'  	=> $insert['type'],
			'vip_name'  => $insert['vip_name'],
			'discount'	=> $insert['discount'],
		];
		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}
			
		try{
			Db::connect('main_db')->table("vip_type")->insert($insert);
		} catch (\Exception $e){
			$this->dumpException($e);
		}
			
		$this->success('新增成功');
	}
	public function update_vip_type(){
		$id = Request::instance()->get('id');

		$update = Request::instance()->post();
		unset($update['id']);
		$rule = [
			'type'  	=> 'require',
			'vip_name'	=> 'require',
			'discount' 	=> 'require',
		];
		$msg = [
			'type.require'		=> '請選擇折扣方式',
			'vip_name.require' 	=> '名稱不得為空',
			'discount.require'	=> '優惠值不得為空',
		];
		$validate = new Validate($rule,$msg);
		$data = [
			'type'  	=> $update['type'],
			'vip_name'  => $update['vip_name'],
			'discount'	=> $update['discount'],
		];
		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}
			
		try{
			Db::connect('main_db')->table("vip_type")->where("id = '".$id."'")->update($update);
		} catch (\Exception $e){
			$this->dumpException($e);
		}
			
		$this->success('更新成功');
	}
	public function del_vip_type() {
		$id = Request::instance()->get('id');
		if($id=="0")$this->error("不可刪除");
		try{
			Db::connect('main_db')->table("vip_type")->delete($id);
		} catch (\Exception $e){
			$this->dumpException($e);
		}
		$this->success('刪除成功');	
	}
}