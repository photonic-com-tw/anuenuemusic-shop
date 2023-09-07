<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Distribution extends PublicController
{
	public function distribution()
	{

		$getId = $_GET['id']?$_GET['id']:0;
		$dataList = Db::table('stronghold')->field('id, title')->order('order_id asc, id desc')->select();
		$this->assign('distrmenu', $dataList);

		$strongHolds = Db::table('typeinfo_str')->field('pic, title, content, url, sub_pics')
			->where('parent_id ='.$getId.' and online = 1')
			->order('orders','asc')->order('id', 'desc')
			->paginate(12,false,['query'=>request()->param()])
			->each(function($item, $key){
				$item['sub_pics'] =  json_decode($item['sub_pics']);
				return $item;
			});



		$this->assign('strongholds', $strongHolds);

		// return json($strongHolds);
		return $this->fetch('distribution');
	}
}
