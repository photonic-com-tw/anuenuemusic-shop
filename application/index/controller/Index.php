<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use app\index\controller\Product;

class Index extends PublicController
{
	public function index() {
		$tag = Db::table('frontend_data_name')->where('show_type = "tag"')->order('id asc')->select();
		$this->assign('tag', $tag);

		$show_time_limit_prod = Db::connect('main_db')->table('excel')->where('id = 9')->find()['value1'];
		$this->assign('show_time_limit_prod', $show_time_limit_prod);

		$index_edm = Db::connect('main_db')->table('excel')->where('id = 11')->find()['value1'];
		$this->assign('index_edm', $index_edm);
		
		$use_sepc_price = Db::connect('main_db')->table('excel')->where('id = 10')->find()['value1'];
		$this->assign('use_sepc_price', $use_sepc_price);

		$product = Db::table('product')
					->field('id, title,pic_icon,
							 index_adv01_pic, index_adv01_link,
							 index_adv02_pic, index_adv02_link,
							 index_adv03_pic, index_adv03_link,
							 index_adv04_pic, index_adv04_link,
							 index_adv05_pic, index_adv05_link,
							 index_adv06_pic, index_adv06_link,
							 index_adv07_pic, index_adv07_link')
					->where('online = 1')->order('order_id')->select();

		foreach ($product as $key => $value) {
			// 處理七格廣告
			for($i='1';$i<='7';$i++){
				$show_price='0'.$i.'_show_price';
				$ayyay_index_adv01_link='index_adv0'.$i.'_link';

				if(!empty($value[$ayyay_index_adv01_link])){
					/*抓取連結id*/
					$aim_id = count(explode('productinfo.html?id=',$value[$ayyay_index_adv01_link])) > 1 ? explode('productinfo.html?id=',$value[$ayyay_index_adv01_link])[1] : '0';

					/*找尋商品*/
					$productinfo = Db::table('productinfo')->field('title, has_price')->where("id ='". $aim_id."' AND online=1")->select();
					
					/*若存在商品 且為 開啟狀態*/
					if($productinfo){
						if($productinfo[0]['has_price']=='0'){
						    $product[$key][$show_price]= '<span>'.$this->lang_menu['請詢價'].'</span>';
							continue;
						}

						$new_price = Product::get_price($aim_id);
						if(!$new_price) continue;

						if($new_price[0]['price']==$new_price[0]['count']){
							$product[$key][$show_price]='<span>'.$this->lang_menu['原價'].dolar.dolar_mark.' '.$new_price[0]['price'].'</span>';
						}else{
						    $product[$key][$show_price]= '<s>'.$this->lang_menu['原價'].dolar.dolar_mark.' '.$new_price[0]['price'].'</s>'."&nbsp;&nbsp;&nbsp;".'<span>'.$this->lang_menu['優惠'].dolar.dolar_mark.' '.$new_price[0]['count'].'</span>';
						}

					}
				}
			}

			// 顯示分館的子分類
			$product[$key]['typeinfo'] = Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id, typeinfo.pic')
					->where('typeinfo.parent_id = ' . $product[$key]['id'] . "
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									OR
									((typeinfo.start < " . time() . ") AND (typeinfo.end > " . time() . "))
								)")
					->order('typeinfo.order_id')->select();
			if(count($product[$key]['typeinfo']) == 0){
				$product[$key]['typeinfo'] = array(
					0 => array(
						'id' => 0,
						'title' => '', /*沒有子館*/
						'pic' => ''
					)
				);
			}
		}

		//var_dump($product);
		$this->assign('product', $product);
		//dump($product);

		$slideshow = Db::table('slideshow')->field('title, pic, link')->order('id')->select();
		$this->assign('slideshow', $slideshow);

		$expiring_product = $this->productInfo('expiring_product');
		$this->assign('expiring_product', $expiring_product);

		$hot_product = $this->productInfo('hot_product');
		$this->assign('hot_product', $hot_product);

		$recommend_product = $this->productInfo('recommend_product');
		$this->assign('recommend_product', $recommend_product);

		$spe_price_product = $this->productInfo('spe_price_product', 'orders asc, table.id desc');
		$this->assign('spe_price_product', $spe_price_product);

		$edmID = Db::table('index_excel')->field('data3')->find(37)['data3'];
		$this->assign('edmID', $edmID);
		
		$timeRange = Db::table('index_excel')->field('data2, data3')->find(32);
		$time = time();
		$this->assign('time_range', 1);

		if($timeRange['data2'] <= $time && ($time <= $timeRange['data3']+86400 || 0 >= $timeRange['data3']+86400)){
			$time_product = $this->productInfo('time_product');
			$this->assign('startTime', (date('m-d H:i',$timeRange['data2'])));
			$this->assign('endTime', (date('m-d H:i',$timeRange['data3'])));
			$this->assign('time_product', $time_product);
			$this->assign('time_block', 1);

			if($timeRange['data3'] > 0){
				$reciprocal = $timeRange['data3']+86400 - $time;
				$reciprocalTime['hr'] = floor($reciprocal/(60*60));
				$reciprocalTime['mn'] = floor(($reciprocal%(60*60))/60);
				$reciprocalTime['sc'] = $reciprocal%60;

				$this->assign('reciprocalTime', $reciprocalTime);
			}else{
				$this->assign('time_range', 0);
			}
		}else{
			$this->assign('time_block', 0);
		}

        if (session('user')){
            $this->assign('haveLogin','true');
        }else{
            $this->assign('haveLogin','false');
        }

		return $this->fetch();
	}

	private function productInfo($tableName, $order = '') {			
		$productInfo = Db::table($tableName)
			->alias('table')
			->field('productinfo.title, productinfo.id,
					productinfo.pic, productinfo.has_price, productinfo.is_registrable, productinfo.examinee_date, productinfo.expire_date')
			->where('productinfo.online = 1 AND table.product_id <> 0')
			->join('productinfo','productinfo.id = table.product_id');
		if($tableName == 'spe_price_product'){
			$productInfo = $productInfo->order($order);
		}else{
			$productInfo = $productInfo->order('table.id');
		}
		$productInfo = $productInfo->select();

		$Product = new Product(Request::instance());
		foreach ($productInfo as &$vo) {

			$vo['pic'] = json_decode($vo['pic'],true)[0];

			// 顯示商品價格
			$vo['show'] = $Product->get_product_price_option($vo);

			// 標記優惠券
			$vo[$vo['id']]['coupon_button'] = Product::find_prod_coupon($vo['id']);

			// 標記活動或優惠
			$vo['act_data'] = Product::find_prod_act($vo['id']);
		}

		return $productInfo;
	}

	public function subscripe(){
		// $subscription = $_POST;
		$subscription = json_decode(file_get_contents('php://input'), true);
		// dump($subscription);

		if (!isset($subscription['endpoint'])) {
		    echo 'Error: not a subscription';
		    return;
		}

		$method = $_SERVER['REQUEST_METHOD'];

		$user_id = Session::get('user') ? Session::get('user.id') : 0;
		$data = [
			'user_id'			=> $user_id,
    		'endpoint'			=> $subscription['endpoint'],
    		'expirationTime'	=> $subscription['expirationTime'],
    		'auth'				=> $subscription['keys']['auth'],
    		'p256dh'			=> $subscription['keys']['p256dh'],
    	];

		switch ($method) {
		    case 'POST' :
		    	$subscription = Db::connect('main_db')->table('subscription')->where('endpoint', $data['endpoint'])->select();
		    	if($subscription){
		    		// update the key and token of subscription corresponding to the endpoint
			    	if($user_id==0) unset($data['user_id']);
			    	Db::connect('main_db')->table('subscription')->where('endpoint', $data['endpoint'])->update($data);
		    	}else{
		        	// create a new subscription entry in your database (endpoint is unique)
		    		Db::connect('main_db')->table('subscription')->insert($data);
		    	}
		        break;
		    case 'DELETE':
		        // delete the subscription corresponding to the endpoint
		        break;
		    default:
		        echo "Error: method not handled";
		        return;
		}
	}
}



