<?php


namespace app\admin\controller;


use app\admin\controller\MainController;
use think\Request;
use think\Db;


use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;


class Tag extends MainController {

    private $DBTextConnecter;
    const ShowType = 'tag';

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('frontend_data_name');
    }


    public function tag() {
    	$use_sepc_price = Db::connect('main_db')->table('excel')->where('id = 10')->find()['value1'];
		$this->assign('use_sepc_price', $use_sepc_price);

		$item = Db::table('frontend_data_name')->where('show_type = "'.self::ShowType.'"')->order('id asc')->select();
		$this->assign('item', $item);
		
		return $this->fetch('tag');
    }

    public function update_tag() {

		$newData = Request::instance()->post();
		// dump($newData);exit;

		foreach ($newData['item'] as $key => $value) {
			$update = [
				'name' => $value
			];

			Db::table('frontend_data_name')->where('id = '.$key)->update($update);
		}

		$this->success('更新成功');
    }
}