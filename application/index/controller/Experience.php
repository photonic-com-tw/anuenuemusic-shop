<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Experience extends PublicController
{
	public function experience()
	{
		$experience = Db::table('experience')
						->field('id, title, pic, content, url')
						->where('online = 1')
						->order('orders asc, time desc')
						->paginate(10);
		$this->assign('experience',$experience);
		return $this->fetch('experience');
	}
}
