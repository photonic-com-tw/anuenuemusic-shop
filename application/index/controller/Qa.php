<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
class Qa extends PublicController
{
	public function qa()
	{
		$page = Request::instance()->get('page') ?? '1';
		$this->assign('page', $page);

		$cate = Request::instance()->get('cate') ?? "";
		$this->assign('cate',$cate);

		$searchText = Request::instance()->get('searchText') ?? "";
		$this->assign('searchText',$searchText);

		$qa = Db::table('qa')->field('q, a')->where('online = 1');
		if($cate) $qa = $qa->where('category', $cate);
		if($searchText) $qa = $qa->where(function ($query) use ($searchText) {
		    $query->where('q', 'like', "%".$searchText."%")
				->whereOr('a', 'like', "%".$searchText."%");
		});

		$qa = $qa->order('order_id')->paginate(
			10,
			false,
			[
			'query' => ['cate' => $cate,
						'searchText' => $searchText]
			]
		);
		$this->assign('qa',$qa);

		$qa_cate = Db::table('qa')->field('id ,category')->where('online = 1')->group('category')->order('category asc')->select();
		$this->assign('qa_cate',$qa_cate);


		return $this->fetch('qa');
	}
}
