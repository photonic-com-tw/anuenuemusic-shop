<?php
namespace app\admin\controller;
use app\admin\controller\MainController;
use think\Request;
use think\Db;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;


class Layertree extends MainController
{
	private $DBproduct;
	public function __construct() {
		parent::__construct();
		$this->DBproduct = DBTextConnecter::withTableName('product');
	}

	public function tree() {
		
		return $this->fetch('tree');
	}

	public function next_layer_product($parent_id, $branch_id){
		$next_product = Db::table('typeinfo')->where('parent_id ="'.$parent_id.'" and branch_id ="'.$branch_id.'"')->order('order_id asc, id asc')->select();
		if (count($next_product) > 0) {
			foreach ($next_product as $key => $value) {
				//處理下一階層
				$next_product[$key]['product_num'] = $this->count_products($parent_id, $value['id'], -1);;
				$next_product[$key]['content'] = $this->next_layer_product($parent_id,  $value['id']);
			}
		}
		return $next_product;
	}

	public function count_products($prev_id, $branch_id, $parent_id){
		if($parent_id = -1){
			$product_num = Db::table('productinfo')->where(' final_array like \'%"prev_id":"'.$prev_id.'","branch_id":"'.$branch_id.'"%\'')->count();
		}else{
			$product_num = Db::table('productinfo')->where(' final_array like \'%"prev_id":"'.$prev_id.'","branch_id":"'.$branch_id.'","parent_id":"'.$parent_id.'"%\'')->count();
		}

		return $product_num;
	}


	public function get_product_tree(){
		$product = Db::table('product')->field('id, title, order_id, online')->order('order_id asc, id asc')->select();
		foreach ($product as $key => $value) {
			//第0層 館
			$product[$key]['product_num'] = $this->count_products($value['id'], 0, 0);
			$product[$key]['content'] = $this->next_layer_product($value['id'], 0);
		}

		// dump($product);
		return json($product, 200);
	}
}