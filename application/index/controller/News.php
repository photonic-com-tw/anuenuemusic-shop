<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class News extends PublicController
{
	public function news()
	{
		$news = Db::table('news')->where('online = 1')->order('orders asc, time desc')
			->paginate(10)
			->each(function($item, $key){
				$item['time'] =   date("Y-m-d", strtotime($item['time']) );
				return $item;
			});

		$this->assign('news',$news);
		return $this->fetch('news');
	}

	public function news_c()
	{
		$id = Request::instance()->get('id');

        $news = Db::table('news')->where('id', $id)->select();
        $news[0]['time'] = date("Y-m-d", strtotime($news[0]['time']) );
		$this->assign('news', $news[0]);

		$pageup = Db::table('news')->field("id")->where("id < '".$_GET['id']."' and online = 1")->order("id desc")->limit("0,1")->select();		
		$pagedown = Db::table('news')->field("id")->where("id > '".$_GET['id']."' and online = 1")->order("id asc")->limit("0,1")->select();			
		$this->assign('pageup',$pageup);		
		$this->assign('pagedown',$pagedown);

		return $this->fetch('news_c');
	}

}

