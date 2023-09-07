<?php

namespace app\admin\controller;

use app\admin\controller\MainController;

use think\Request;

use think\Db;

class System extends MainController
{
	public function index(){	
		$system_intro = Db::table('system_intro')->where('system_id = 1')->select();
		$this->assign('system_intro', $system_intro[0]);
		return $this->fetch('index');
	}

	public function update(){	
		$data = $_POST;
		Db::table('system_intro')->where('system_id = 1')->data($data)->update();
		$this->success('更新成功');
	}
}