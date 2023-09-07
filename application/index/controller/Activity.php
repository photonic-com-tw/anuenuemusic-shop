<?php



namespace app\index\controller;

use think\Controller;

use think\Db;

use think\Request;



class Activity extends PublicController

{

	public function activity()

	{

		$activity = Db::table('activity')->field('id, title, pic, content')->where('online = 1')->order('orders asc, time desc')->paginate(10);

		$this->assign('activity',$activity);

		return $this->fetch('activity');

	}



	public function activity_c()

	{

		$id = Request::instance()->get('id');

        $activity = Db::table('activity')->field('id, title, pic, content')->where('id', $id)->select();

		$this->assign('activity', $activity[0]);

		$pageup = Db::table('activity')->field("id")->where("id < '".$_GET['id']."' and online = 1")->order("id desc")->limit("0,1")->select();

		$pagedown = Db::table('activity')->field("id")->where("id > '".$_GET['id']."' and online = 1")->order("id asc")->limit("0,1")->select();

		$this->assign('pageup',$pageup);

		$this->assign('pagedown',$pagedown);

		return $this->fetch('activity_c');

	}

}

