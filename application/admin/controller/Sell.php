<?php
namespace app\admin\controller;
use app\admin\controller\MainController;
use think\Request;
use think\Db;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

use think\Session;
use app\index\controller\Cart;

class Sell extends MainController
{
	private $DBTextConnecter;
	private $DBFileConnecter;
	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
		
		
		if($searchKey != ''){
			
			
			$ISBN = Db::table('productinfo')->where('ISBN',$searchKey)->find();
			$productinfo_type  = Db::table('productinfo_type')->where('product_id',$ISBN['id'])->order('count')->select();
			
			$select_position =[];
			//dump($productinfo_type);
			foreach($productinfo_type as $k => $v){
				$productinfo_type[$k]['portion'] =[];
				$position_portion  = Db::table('position_portion pp')->
									field("pp.*, pp.id as pp_id ,pos.*")->
									where("productinfo_type = '".$v['id']."'")->
									join('position pos','pp.position_id = pos.id')->
									select();
				
				foreach($position_portion as $pk => $pv){
					$productinfo_type[$k]['portion'][$pk] = array('id'=>$pv['pp_id'],'name'=>$pv['name'],'number'=>$pv['position_number'],'num'=>$pv['num']);
					//$sell_array[$v['id']] = $productinfo_type[$k]['portion'][$pk];
					
					$select_position[$pv['position_id']] = $pv['name'];
				}
				
				//dump($position_portion);
				
			}
			//dump($sell_array);
			
			//dump($productinfo_type);exit;
			
			
				$this->assign('productinfo_type', $productinfo_type);
			
			
		}
			$this->assign('id', $ISBN['id']??'');
			$this->assign('select_position', $select_position??'');
				$this->assign('repeat', $ISBN['r_repeat']??'');
			$this->assign('productinfo_type', $productinfo_type??'');
		//exit;
		return $this->fetch('sell');
	}

	public function search(){
		
		$stock = Db::table('position_portion')
				->where("product_id = '".$_POST['product_id']."' and position_id = '".$_POST['position_id']."'  and position_number = '".$_POST['position_number']."'")
				->find();
				//{"productinfo_type":958,"position_portion":4,"num":1}
		return array(['productinfo_type'=>$stock['productinfo_type'],'position_portion'=>$stock['id']]);
	}

	public function create() {
	}

	public function update() {
		// 先清空購物車
		Session::set('cart',[]);
		$sell_array = json_decode($_POST["sell_array"],true);
		$searchKey = $_POST["searchKey"];
		$model = $_POST["model"];
		// dump($sell_array);

		$total=[];
		foreach($sell_array as $k => $v){//依productinfo_type將數量總和在一起
			
			$pt_id =$v['pt_id'];
			if(empty($total[$pt_id])){
				$total[$pt_id]= $v;	
			}else{
				if( empty( $total[$pt_id]['num'] ) )  $total[$pt_id]['num']=0;
				$total[$pt_id]['num'] += $v['num'];
			}
		}		
		// dump($total);
		// dump($model);
		// exit;

		if($model == '0'){//現場銷售

			$Cart = new Cart(Request::instance());
			foreach($total as $k => $v){
				// 商品加入購物車
				$_POST['cmd'] = 'increase';
				$_POST['product_id'] = $k;
				$_POST['num'] = $v['num'];
				$ressult = $Cart->cartCtrl();
				$ressult = json_decode($ressult->getContent());
				if(!$ressult->status){
					$this->error($ressult->message);
				}
			}
			// 建立現場銷售訂單
			$OrderData = [
				  "pay_way"	=> "貨到付款",
				  "send_way"	=> "到店取貨",
				  "transport_location_name"	=> "現場購物",
				  "transport_email"	=> "",
				  "transport_location_phone"	=> "",
				  "transport_location_tele"	=> "",
				  "addrC"	=> "現場購物",
				  "id"	=> "",
				  "discount"	=> "none_discount",
				  "point"	=> "",
				  "uniform_numbers"	=> "",
				  "company_title"	=> "",
				  "transport_location_textarea"	=> "",
				  "tygwtk"	=> "",
				  "selectPlace"	=> [],
				  "receipts_state" => 1,
				  "transport_state" =>1,
				  "status"	=> "Complete"
			];
			$Cart->createOrder($OrderData, 'offline'); // 建立訂單(線下購買方式)，過程中扣除線上可購買數量

		}else{//線上銷售

			foreach($total as $k => $v){//比對庫存
				$stock = Db::table('productinfo_type')->field('num')->where("id = '".$k."'")->find();
				$productinfo = Db::table('productinfo_type')->where("id = '".$v['product_id']."'")->find();
				
				if($productinfo['r_repeat']==0){ // 一碼一品項
					$type_total=Db::table('position_portion')->where("product_id = '".$v['product_id']."' and productinfo_type = '".$v['pt_id']."' and position_id = '".$v['position']."'")->count();

				}else{ // 一碼多品項
					$type_total=Db::table('position_portion')->where("product_id = '".$v['product_id']."' and productinfo_type = '".$v['pt_id']."' and position_id = '".$v['position']."'")->find()['num'];	
				}
				
				if(($type_total - $v['num']) < $stock['num']){
					$this->error('並未有網路訂單或超出訂單數量，請勿出貨',url('sell/index').'?searchKey='.$searchKey);
				}
			
			}
		}

		//扣除庫存
		foreach($sell_array as $k => $v){	
			Db::table('position_portion')->where('id',$v['pp_id'])->dec('num',$v['num'])->update(); // 扣除被撿走或的編碼數量

			Db::table('position_portion')->where('num',0)->delete(); // 編碼剩餘數為0則刪除紀錄
			Db::table('position_portion')->where('product_id',0)->delete(); // 編碼無對應商品則刪除紀錄
		}
		
		
		$this->success('更新成功',url('sell/index').'?searchKey='.$searchKey);
		//dump($total);
	}

	public function cellCtrl() {
	}

	public function edit() {
				
		$type = $_POST['type'];
		$id = $_POST['id'];
		
		
		switch ($type) {
		case "add":
			$data['name'] = $_POST['name'];
			$data['number'] = $_POST['number'];
			if(Db::table($this->tableName)->insert($data)){
				echo 'success';
			}else{
				echo '新增失敗';
			}
			break;
		case "delete":
			if(Db::table($this->tableName)->where('id',$id)->delete()){
				echo 'success';
			}else{
				echo '失敗';
			}
					break;
		case "update":
			$data['number'] = $_POST['number'];
			if(Db::table($this->tableName)->where('id',$id)->update($data)){
				echo 'success';
			}else{
				echo '新增失敗';
			}
			break;
			
		}
		
	}
	

	public function search_bar_mul(){
		$bar = $_POST['bar'];
		$sell_array = $_POST['sell_array'];
		$sell_array = json_decode($sell_array,true);
		$goods = Db::table('productinfo p')->field('p.title as name,
													pp.id as pp_id,
													pt.id as pt_id, 
													pt.product_id,
													pt.position,
													pt.price,
													pt.count,
													pp.num,
													pt.title,
													p.r_repeat,
													pos.name as p_name,
													pos.number as  p_number,
													pos.max as  p_max,
													pp.position_number
												')
				->where("p.ISBN = '".$bar."'")
				->join('productinfo_type pt','p.id = pt.product_id','left')
				->join('position_portion pp','pp.productinfo_type = pt.id')
				->join('position pos','pp.position_id = pos.id')
				->order('pt.order_id asc, pt.id asc, pp.position_number asc')
				->select();
		// dump($goods);

		if(empty($goods)){
			return 'no';
			exit;
		}

		//dump($goods);
		$re = '
			<h5>商品名稱：'.$goods[0]['name'].'</h5>
			<table>
				<tr>
					<th style="width:30%">市價</th>
					<th style="width:30%">品項</th>
					<th style="width:15%">售價</th>
					<th style="width:100px">位置編碼</th>
				</tr>';
		
		foreach($goods as $k => $v){//數量不夠得跳過
			$pass = 0;
			if($v['num'] == 0){continue;}

			foreach($sell_array as $sk => $sv){
				if($sv['pp_id'] == $v['pp_id']){
					if($v['num'] < $sv['num']+1){
						$pass = 1;
						break;
					}
				}
			}
			if($pass == 1)
				continue;

			$p_code = $v['p_max'] == "1" ? $v['p_name'].$v['position_number'] : $v['p_name'].str_pad($v['position_number'], strlen($v['p_number']), "0", STR_PAD_LEFT);
			$re .= '<tr onclick="search_bar('.$v['pp_id'].','.$v['product_id'].')" >';
			$re .= '<td>'.$v['price'].'</td>';
			$re .= '<td>'.$v['title'].'</td>';
			$re .= '<td>'.$v['count'].'</td>';
			$re .= '<td>'.$p_code.'</td>';
			$re .= '</tr>';
		}
		$re .= '</table>';
		
		echo $re;
		/*
		$goods = array_values($goods);
		return json_encode($goods);*/
	}

	public function search_bar(){
		
		$pp_id = $_POST['pp_id'];
		$product_id = $_POST['product_id'];
		$sell_array = $_POST['sell_array'];
		$sell_array = json_decode($sell_array,true);
		$goods = Db::table('productinfo p')
			->field('
				p.title,
				pt.product_id,
				pt.position,
				pt.price,pt.count,pt.num,
				pt.title as dis,
				pt.id as pt_id, 
				pp.id as pp_id, 
				pos.name as p_name,
				pos.number as  p_number,
				pos.max as  p_max,
				pp.position_number')
			->where("pp.id = '".$pp_id."' and p.id='".$product_id."'")
			->join('productinfo_type pt','p.id = pt.product_id','left')
			->join('position_portion pp','pp.productinfo_type = pt.id')
			->join('position pos','pp.position_id = pos.id')
			->order('pt.order_id asc, pt.id asc, pp.position_number asc')
			->select();
		
		if(!empty($goods)){
			$goods[0]['num'] = 1;
			$goods[0]['title'] = $goods[0]['title'];
			$goods[0]['p_code'] = $goods[0]['p_max'] == "1" ? $goods[0]['p_name'].$goods[0]['position_number'] : $goods[0]['p_name'].str_pad($goods[0]['position_number'], strlen($goods[0]['p_number']), "0", STR_PAD_LEFT);
		}else{
			return 'no';
			exit;
		}

		
		$ck = 0;
		$pos = 0;
		foreach($sell_array as $k => $v){
			if($v['pp_id'] == $goods[0]['pp_id']){
				$sell_array[$k]['num']++;
				$ck = 1;
			}
			$pos = $k;
		}
		
		if($ck == 0){
			$sell_array[++$pos] = $goods[0];
		}

		$sell_array = array_values($sell_array);
		return json_encode($sell_array);
	}	
}