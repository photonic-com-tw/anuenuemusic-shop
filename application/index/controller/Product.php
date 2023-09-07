<?php
namespace app\index\controller;

use app\index\controller\Cart;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use pattern\recursiveCorrdination\cartRC\Proposal;
use pattern\recursiveCorrdination\cartRC\MemberFactory;
use pattern\recursiveCorrdination\discountRC\Proposal as DiscountProposal;
use pattern\recursiveCorrdination\discountRC\MemberFactory as DiscountMemberFactory;
use pattern\simpleFactory\discountFactory\DiscountFactory;
use DBtool\DBFileConnecter;

use app\admin\controller\Addprice;

class Product extends PublicController {

	const PER_PAGE_ROWS = 30;
	const SIMPLE_MODE_PAGINATE = false;

	public function __construct()
    {
    	$Request = Request::instance();
		parent::__construct($Request);

    	$this->assign('Service_Tel', Service_Tel);
    	$this->assign('Service_Tel_A', Service_Tel_A);
	}

	private function getRandADV($rid) {

	    $randAdv = Db::table('product')
            ->field('	id,
						title,
						inner_adv01_pic,
						inner_adv01_link,
						content,
						pic,
						webtype_keywords,
						webtype_description') //多加webtype_keywords,webtype_description
            ->where('online = 1 AND id <> '.$rid)
            ->order('RAND()')
            ->limit(2)
            ->select();
		return $randAdv;
	}

	public function product() {
		// 找出本層分館
		$id = Request::instance()->get('id');
		if($id){
			$product = Db::table('product')
			->field('id,
					title,
					inner_adv01_pic,
					inner_adv01_link,
					inner_adv02_pic,
					inner_adv02_link,
					webtype_keywords,
					online,
					webtype_description') //多加webtype_keywords,webtype_description
			->find($id);
			if($product['online'] =='2')
				$this->error($this->lang_menu['無此頁面'], 'product/typeinfo'); /*此館已關閉*/
		}else{
			$product = Db::table('product')
			->field('id,
					title,
					inner_adv01_pic,
					inner_adv01_link,
					inner_adv02_pic,
					inner_adv02_link,
					webtype_keywords,
					online,
					webtype_description') //多加webtype_keywords,webtype_description
			->where('online=1')
			->find();
			$id = $product['id'];
		}
		$this->assign('product',$product);

		$this->assign('layer_type', 'product');
		$this->assign('main_layer_id', $product['id']);
		
		// 製作麵包屑
		$title_array = $this->get_title_array(['prev_id'=>$product['id'], 'branch_id'=>0]);
		session('title_array', $title_array); /*記憶分類麵包屑*/
		$this->assign('title_array', $title_array);

		$main_layer = [
			'title' => $product['title'],
			'webtype_keywords' => $product['webtype_keywords'],
			'webtype_description' => $product['webtype_description'],
		];
		$this->assign('main_layer', $main_layer);
		
		// 從本層分館找出商品
		$cate_serach_main = '"prev_id":"'.$id.'","branch_id":"0","parent_id":"0"';
		$productinfo = Db::table('productinfo p')
					->field('p.*, p.title, p.id, p.pic AS pic1, p.has_price, p.is_registrable')
					->join('productinfo_orders po', 'po.prod_id=p.id', 'left')
					->where("p.final_array like '%".$cate_serach_main."%' AND p.online = 1 AND (po.prev_id=".$id.' AND po.branch_id=0)')
					->order('po.order_id asc, p.id desc')
					->paginate(
						self::PER_PAGE_ROWS,
						self::SIMPLE_MODE_PAGINATE,
						[
						'query' => ['id' => $id]
						]
					)->each(function($item, $key)use(&$coupon_button, &$act_button){
						$item['pic1'] =  json_decode($item['pic1'],true)[0];

						// 顯示商品價格
						$item['show'] = $this->get_product_price_option($item);

						// 標記優惠券
						$coupon_button[$key] = $this->find_prod_coupon($item['id']);

						// 標記活動或折扣
						$act_button[$key]['act_data'] = $this->find_prod_act($item['id']);

						return $item;
					});
		$this->assign('coupon_button', $coupon_button);
		$this->assign('act_button', $act_button);
		$this->assign('productinfo', $productinfo);
		// dump($productinfo);

		return $this->fetch('product');
	}

	public function typeinfo() {
		$id = Request::instance()->get('id');
		$tag = Request::instance()->get('tag');
		if($tag == '0'){
			$this->redirect('experience/experience');
		}
		
		// 找出本層分類
		$typeinfo = $this->get_typeinfo($id);
		$this->assign('layer_type', 'typeinfo');
		$this->assign('main_layer_id', $typeinfo['id']);

		// 找出此層分類下掛於哪個分館
		$product = Db::table('product')
						->field('id,
								title,
								inner_adv01_pic,
								inner_adv01_link,
								inner_adv02_pic,
								inner_adv02_link,
								online')
						->find($typeinfo['parent_id']);
		$this->assign('product', $product);
		//dump($product);
		
		if($product['online'] == '2'){
			$this->error($this->lang_menu['無此頁面']); /*此館已關閉*/
		}

		// 製作麵包屑
		$title_array = $this->get_title_array(['prev_id'=>$product['id'], 'branch_id'=>$typeinfo['id']]);
		// dump($title_array);exit;
		session('title_array', $title_array); /*記憶分類麵包屑*/
		$this->assign('title_array', $title_array);

		$main_layer = [
			'title' => $typeinfo['title'],
			'webtype_keywords' => $typeinfo['webtype_keywords'],
			'webtype_description' => $typeinfo['webtype_description'],
		];
		$this->assign('main_layer', $main_layer);

		// 從本層分類找出商品
		$cate_serach_main = '"branch_id":"'.$typeinfo['id'].'"';
		$productinfo = Db::table('productinfo p')
						->field('p.*, p.title, p.id, p.pic AS pic1, p.has_price, p.is_registrable')
						->join('productinfo_orders po', 'po.prod_id=p.id', 'left')
						->where("p.final_array like '%".$cate_serach_main."%' AND p.online = 1 AND po.branch_id=".$typeinfo['id'])
						->order('po.order_id asc, p.id desc')
						->paginate(
							self::PER_PAGE_ROWS,
							self::SIMPLE_MODE_PAGINATE,
							[
							'query' => ['id' => $id]
							]
						)->each(function($item, $key)use(&$coupon_button, &$act_button){
							$item['pic1'] =  json_decode($item['pic1'],true)[0];

							// 顯示商品價格
							$item['show'] = $this->get_product_price_option($item);

							// 標記優惠券
							$coupon_button[$key] = $this->find_prod_coupon($item['id']);

							// 標記活動或折扣
							$act_button[$key]['act_data'] = $this->find_prod_act($item['id']);

							return $item;
						});
		$this->assign('coupon_button', $coupon_button);
		$this->assign('act_button', $act_button);
		$this->assign('productinfo', $productinfo);

		return $this->fetch('product');
	}

	public function activity() {
		$id = Request::instance()->get('id');
		$act = DB::table('act')->where('id',$id)->find();
		$this->assign('act', $act);

		$productinfo = Db::table('act')->alias('a')
						->join('act_product ap','a.id = ap.act_id' )
						->join('productinfo pi','ap.prod_id = pi.id')
						->field('pi.title, pi.id, pi.pic as pic1, pi.has_price, pi.id, a.act_type, pi.is_registrable,pi.examinee_date,pi.expire_date')
						// ->where('parent_id = ' . $typeinfo['id'] . " AND online = 1")
						->where('a.id',$id)
						->where("a.online = 1
								AND (
								a.end <= 0
								OR
								( a.start < " . time() . " AND  a.end > " . time() . ")
								)")
						->order('pi.order_id, pi.id desc')
						->paginate(
							self::PER_PAGE_ROWS,
							self::SIMPLE_MODE_PAGINATE,
							[
							// 'query' => ['a.id' => $id]
							]
						)->each(function($item, $key)use(&$coupon_button, &$act_button){
							$item['pic1'] =  json_decode($item['pic1'],true)[0];

							// 顯示價格
							$item['show'] = $this->get_product_price_option($item);

							// 標記優惠券
							$coupon_button[$key] = $this->find_prod_coupon($item['id']);

							// 標記活動或折扣
							$act_button[$key]['act_data'] = $this->find_prod_act($item['id']);

							return $item;
						});
		// echo $sql = Db::table('act')->getLastSql();
		$this->assign('coupon_button', $coupon_button);
		$this->assign('act_button', $act_button);
		$this->assign('productinfo', $productinfo);

		// $productRand = $this->getRandADV($typeinfo['parent_id']);
		// $this->assign('productRand', $productRand);
		
		if (session('user')){
			$this->assign('haveLogin','true');
		}else{
			$this->assign('haveLogin','false');
		}
		return $this->fetch('activity');
	}

	public function coupon() {
		// 有設定prod_id的話就只顯示是用此商品的優惠券
		$prod_id_where = isset($_GET['prod_id']) ? " AND ( (coupon.area_id=".$_GET['prod_id']." AND coupon.area=1) OR coupon.area=0)" : '';

		$productinfo = Db::table('coupon')
						->field('coupon.title AS title,
								coupon.id AS id,
								coupon.pic AS pic,
								coupon.end AS end,
								coupon.discount AS discount,
								coupon.price AS price')
						->where("coupon.online = 1
								AND coupon.start < " . time() . "
								AND coupon.end > " . time() . "
								AND coupon.type = 0".$prod_id_where)
						->order('id desc')
						->select();
		
		$productinfo = array_filter($productinfo, function ($value){
			return (Db::table('coupon_pool')
				->whereNull('owner')
				->where("login_time < " . (time() - 21600) . ' OR login_time IS NULL')
				->where("coupon_id = " . $value['id'])
				->count()
			);
		});
		$this->assign('productinfo', $productinfo);
		return $this->fetch('coupon');
	}
	public function couponinfo() {
		$this->assign('FB_PAGE_URL', FB_PAGE_URL);
		$this->assign('FB_appID', FB_appID);

		$id = Request::instance()->get('id');
		$productinfo = Db::table('coupon')
		->where("online = 1
				AND start < " . time() . "
				AND end > " . time() . "
				AND type = 0")
		->find($id);
		$this->assign('productinfo', $productinfo);
		// dump($productinfo);
		return $this->fetch('couponinfo');
	}

	public function productinfo() {
		$id = Request::instance()->get('id');
		if(!$id){
			$this->error($this->lang_menu['無此頁面']); /*商品不存在*/
		}

		/*商品相關設定參數*/
		$this->assign('FB_PAGE_URL', FB_PAGE_URL);
		$this->assign('FB_appID', FB_appID);
		$this->assign('property1', property1);
		$this->assign('property2', property2);
		$this->assign('property3', property3);
		$upload_film = Db::connect('main_db')->table('excel')->where('id = 6')->find()['value1'];
		$this->assign('upload_film', $upload_film);
		$card_pay = Db::connect('main_db')->table('excel')->where('id = 7')->find()['value1'];
		$this->assign('card_pay', $card_pay);
		$insert_edm = Db::connect('main_db')->table('excel')->where('id = 8')->find()['value1'];
		$this->assign('insert_edm', $insert_edm);
		$use_sepc_price = Db::connect('main_db')->table('excel')->where('id = 10')->find()['value1'];
		$this->assign('use_sepc_price', $use_sepc_price);
		$social_share = Db::connect('main_db')->table('excel')->where('id = 13')->find()['value1'];
		$this->assign('social_share', $social_share);

		/*商品資料*/
		$productinfo = Db::table('productinfo')
							->field('id,product_id,title,ISBN,Author,house,house_date, keywords,out_ID,prodesc,
									 pic, final_array,
									 pushitem,online,
									 content, has_price,
									 text1, text1_online,
									 text2, text2_online,
									 text3, text3_online,
									 text4, text4_online,
									 text5, text5_online,
									 is_registrable, 
									 card_pay')
							->find($id);
		if( empty($productinfo) ){
			$this->error($this->lang_menu['無此頁面']); /*商品不存在*/
		}
		if($productinfo['online'] == '2'){
			$this->error($this->lang_menu['無此頁面']); /*商品不存在*/
		}	

		$kol_id = Request::instance()->get('kol');
		if(empty(self::$closeFunction['網紅列表'])){
			if($kol_id){
				$kol_productinfo = Db::table('kol_productinfo')->where('productinfo_id',$id)->where('kol_id',$kol_id)->where('is_using',1)->select();
				$kol_name = Db::table('kol')->find($kol_id)['kol_name'];
				$this->assign('kol_name', $kol_name);
				if(!$kol_productinfo){$this->redirect('/index/product/productinfo.html?id='.$id);}
			}
		}else{
			if($kol_id){$this->redirect('/index/product/productinfo.html?id='.$id);}
		}

		$productinfo['pic'] = json_decode($productinfo['pic'],true);
		$productinfo['text1'] = str_replace("&lt;","<",$productinfo['text1']);
		$productinfo['text1'] = str_replace("&gt;",">",$productinfo['text1']);
		//dump($productinfo);
		$this->assign('productinfo', $productinfo);

		/*取得商品品項*/
		$price = $this->get_price($productinfo['id']);
		//dump($price);
		$this->assign('price', $price);

		/*取得人氣紀錄*/
		$love = $this->get_love_record($productinfo['id']);
		// dump($love);
		$this->assign('love', $love);
		/*取得收藏紀錄*/
		$store = $this->get_store_record($productinfo['id']);
		// dump($store);
		$this->assign('store', $store);


		// 處理麵包屑紀錄
		$title_array = session('title_array') ? session('title_array') : []; /*取得分類麵包屑*/
		// dump($title_array);
		$layer_type = Request::instance()->get('layer_type');	/*取得當前開啟商品的分類(product/typeinfo)*/
		$layer_id = Request::instance()->get('layer_id');		/*取得當前開啟商品的分類的id*/
		if($layer_type && $layer_id){
			if(	(count($title_array)==1 && $layer_type=='typeinfo') ||			/*只記錄到分館，且商品是在分類中開啟*/
				(count($title_array)>1 && end($title_array)['id']!=$layer_id)	/*記錄到分類，但商品開啟的分類與記錄的不同*/
			){ 
				$add_layer = $this->get_typeinfo($layer_id);
				array_push($title_array, ['id'=> $add_layer['id'], 'title'=>$add_layer['title'], 'type'=>'typeinfo']);
			}
		}

		// 檢查 分館/分類是否關閉 及 麵包屑紀錄是否有效
		$position = $this->check_product_and_infotype_close($productinfo['final_array'], $title_array);
		if($position->close){$this->error($this->lang_menu['無此頁面'],'index/index');} /*此館/分類已關閉*/

		/* 依照檢查結果回傳麵包屑紀錄 */
		if(count($position->title_array)==0){ /*檢查麵包屑無效*/
			$title_array = $this->get_title_array($position->rand_layer); /*依商品階層隨機指派麵包屑*/
		}else{
			$title_array = $position->title_array;
		}
		// dump($title_array);exit;
		$this->assign('title_array', $title_array);

		$current_product_layer_id = $title_array[0]['id']; /*當前瀏覽的分館id*/

		// 目前分館
		$product = Db::table('product')
					->field('id, title, inner_adv01_pic, inner_adv01_link,online')
					->find($current_product_layer_id);
		$this->assign('product', $product);
		
		// 其他推薦分館
		$productRand = $this->getRandADV($current_product_layer_id);
		$this->assign('productRand', $productRand);


		/*推薦商品*/
		$pushItem = [];
		$pushitem_product_ids = explode(',', $productinfo['pushitem']);
		$product_id_where = [];
		foreach ($pushitem_product_ids as $k => $v) {
			array_push($product_id_where, $v);
		}
		// dump($product_id_where);
		if(count($product_id_where)>0){
			// $product_id_where = implode(',', $product_id_where);
			$pushItem = DB::table('productinfo')->where('product_id', 'in', $product_id_where)->select();
			foreach ($pushItem as $k => $v) {
				$pushItem[$k]['pic'] = json_decode($v['pic'],true)[0];
				$pushItem[$k]['price'] = $this->get_product_price_option($v);
			}
		}
		// dump($pushItem); exit;
		$this->assign('pushItem', $pushItem);

		/*活動/折扣標示*/
        $act = $this->find_prod_act($id);
		$this->assign('act', $act);

		/*優惠券標示*/
		$coupon_button = $this->find_prod_coupon($id);
		$this->assign('coupon_button', $coupon_button);

		# 特殊標記(tag)
		parent::product_species($id);


        if (session('user')){
            $this->assign('haveLogin','true');
        }else{
            $this->assign('haveLogin','false');
        }

        /*顯示加價購商品*/
        $type_ids = [];
        foreach ($price as $key => $value) {
        	array_push($type_ids, $value['id']);
        }
        $addprice_resault = $this->get_addprice_products($type_ids);
        $this->assign('addprice_type_group', $addprice_resault['addprice_type_group']);

		return $this->fetch('productinfo');
	}
	
	/*依傳入階層檢查 商品是否顯示 及 傳入的麵包屑紀錄是否有效*/
	public function check_product_and_infotype_close($final_array, $title_array=[]){
		$close = false; /*商品是否不顯示*/
		$in_title_array = false; /*麵包屑是否存在於勾選的階層中*/
		
		$final_array = json_decode( $final_array );
		foreach ($final_array as $k => $v) {
			if(count($title_array)>0 && $in_title_array==false){ /*有提供麵包屑進行比對 且 還未發現存在於勾選的階層中*/
				$last_layer = end($title_array);

				if($last_layer['type']=="typeinfo" && $last_layer['id']==$v->branch_id){
					$in_title_array = true;

				}else if($last_layer['type']=="product" && $last_layer['id']==$v->prev_id && $v->branch_id==0){
					$in_title_array = true;
				}
			}

			$branch_id = $v->branch_id;
			while($branch_id !=0){ // 由下掛的分類id逐層往上找
				$typeinfo = Db::table('typeinfo')
					->field('id, title, parent_id, branch_id, online')
					->find($branch_id);

				if($typeinfo['online']==2){ // 分類關閉了
					$close = true;
					break;
				}else{ // 分類開啟
					$close = false;
					$branch_id = $typeinfo['branch_id'];
				}
			}

			$product = Db::table('product')
					->field('id, title, inner_adv01_pic, inner_adv01_link, online')
					->find($v->prev_id);
			if($product['online'] == 2){ // 分館關閉了
				$close = true;
			}
		}

		$return_data = [
			'close' => $close, /*不顯示狀態*/
			'title_array' => $in_title_array ? $title_array : [], /*麵包屑紀錄*/
			// 'rand_layer' => $final_array[ rand(0, count($final_array)-1) ], /*隨機的一個階層*/
			'rand_layer' => $final_array[0], /*給定勾選的地一個階層*/
		];
		// dump($in_title_array);exit;
		return (object)$return_data;
	}

	/* 製作麵包屑 */
	public function get_title_array($layer){
		// $layer範例：['prev_id'=>'', 'branch_id'=>''];
		$layer = (array)$layer;
		// dump($layer);
		if(!isset($layer['branch_id'])){ $this->error($this->lang_menu['無此頁面']); }else{ $branch_id=$layer['branch_id']; } /*找不到分類*/
		if(!isset($layer['prev_id'])){ $this->error($this->lang_menu['無此頁面']); }else{ $prev_id=$layer['prev_id']; } /*找不到分館*/

		/*處理分類*/
		$title_array = [];
		while($branch_id != 0){
			$type_info = $this->get_typeinfo($branch_id);
			array_unshift($title_array, ['id'=> $type_info['id'], 'title' => $type_info['title'], 'type'=>'typeinfo']);
			$branch_id =  $type_info['branch_id'];
		}

		/*處理分館*/
		$product = Db::table('product')->find($prev_id);
		array_unshift($title_array, ['id'=> $product['id'], 'title' => $product['title'], 'type'=>'product']);
		// dump($title_array);exit;

		return $title_array;
	}

	/* 取得分類 */
	public function get_typeinfo($id){
		$typeinfo = Db::table('typeinfo')->field('title, id, parent_id,branch_id,webtype_keywords,webtype_description,online')
							->where(" (
										typeinfo.end <= 0
										OR
										(typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
										)")
							->find($id);
		if(!$typeinfo)$this->error($this->lang_menu['無此頁面']); /*此分類不存在*/
		if($typeinfo['online']==2)$this->error($this->lang_menu['無此頁面']); /*此分類已關閉*/
		return $typeinfo;
	}

	public function search() {
		$searchKey = isset($_POST['keyword']) ? $_POST['keyword'] : '';
		$this->assign('keyword', $searchKey);

		/*處理搜尋tag的sql語法*/
		$searchTag = isset($_POST['tag']) ? $_POST['tag'] : '';
		$tag_prod = [];
		$tag_prod_where = [];
		if ($searchTag == "hot_product"){ /*人氣商品*/
			$tag_prod = DB::table($searchTag)->order('id asc')->select();
		}elseif ($searchTag == "recommend_product") { /*店長推薦*/
			$tag_prod = DB::table($searchTag)->order('id asc')->select();
		}elseif ($searchTag == "expiring_product") { /*即期良品*/
			$tag_prod = DB::table($searchTag)->order('id asc')->select();
		}elseif ($searchTag == "spe_price_product") { /*特價商品*/
			$tag_prod = DB::table($searchTag)->order('orders asc')->select();
		}
		if($tag_prod){
			foreach ($tag_prod as $key => $value) {
				array_push($tag_prod_where, $value['product_id']);
			}
		}
		$tag_prod_where = $tag_prod_where ? " AND id in (".implode(",", $tag_prod_where).")" : "";

		/*處理layer的sql語法*/
		$layer = isset($_POST['layer']) ? $_POST['layer'] : [];
		$layer_sql = " AND ";
		if( is_array($layer) ){
			foreach ($layer as $prev_id => $branch_ids) {
				$layer_sql .= "( ";
				if( is_array($branch_ids) ){
					foreach ($branch_ids as $branch_id) {
						$layer_sql .= 'p.final_array like \'%{"prev_id":"'.$prev_id.'","branch_id":"'.$branch_id.'","parent_id":"'.$branch_id.'"}%\' OR ';
					}
				}
				$layer_sql .= "false ) AND ";
			}
		}
		$layer_sql .= 'true';
		// dump($layer_sql); exit;

		$rowData = Db::table('productinfo p')
					->field('p.title AS title,
							 p.id AS id,
							 p.pic AS pic1,
							 p.has_price AS has_price,
							 p.is_registrable,p.examinee_date,p.expire_date,
							 p.Author,
							 p.content')
					->where("p.online = 1
							 AND UPPER(p.title) LIKE UPPER('%".$searchKey."%')".$tag_prod_where.$layer_sql)
					->order('id desc')
					->paginate(
						self::PER_PAGE_ROWS,
						self::SIMPLE_MODE_PAGINATE,
						[
							'query' => ['keyword' => $searchKey]
						]
					)->each(function($item, $key)use(&$coupon_button, &$act_button){
						$item['pic1'] =  json_decode($item['pic1'],true)[0];

						//  顯示商品價格
						$item['show'] = $this->get_product_price_option($item);

						// 標記優惠券
						$coupon_button[$key] = $this->find_prod_coupon($item['id']);

						// 標記活動或折扣
						$act_button[$key]['act_data'] = $this->find_prod_act($item['id']);

						return $item;
					});
		$this->assign('coupon_button', $coupon_button);
		$this->assign('act_button', $act_button);
		$this->assign('productinfo', $rowData);

		$productRand = $this->getRandADV(0);
		$this->assign('productRand', $productRand);

		return $this->fetch('search');
	}


    public function getFreeCoupon() {
    	$id = $_POST['id'];
    	$num = (Int)$_POST['num'];
    	$_POST['cmd'] = 'assign';
        
        $respData = $this->checkCouponNum();
		if($respData['status']){
			$coupon_pool_ids = Db::table('coupon_pool')
					->field('coupon_pool.id')
	            	->join('coupon', 'coupon_pool.coupon_id = coupon.id', 'left')
	            	->where('coupon.price', 0)
	            	->where('coupon.type', 0)
	            	->whereNull('coupon_pool.owner')
	            	->where('coupon_pool.coupon_id', $id)
	            	->limit($num)
	            	->select();

	        $in_id = [];
	        foreach ($coupon_pool_ids as $key => $value) {
	        	array_push($in_id, $value['id']);
	        }
	        if($in_id){
		        $in_id = '('.join(',', $in_id).')';
	            Db::table('coupon_pool')
	            	->where('id in '.$in_id)
	            	->update([
	            		'owner' => $this->user['id'],
	            		'login_time' => time()
	    			]);
	        }else{
	        	$respData['message'] = $this->lang_menu['內容有誤']; /*優惠券不存在*/
	        }
        }
        return $respData;
    }
    public function checkCouponNum(){
    	$id = $_POST['id'];
    	$num = $_POST['num'];
    	$cmd = $_POST['cmd'];

    	$coupon = Db::table('coupon_pool')
            ->whereNull('coupon_pool.owner')
            ->field('
						coupon_pool.id AS id,
						coupon.price AS price,
						coupon.title AS title,
						coupon.limit_num AS limit_num
					')
            ->where('(
						coupon_pool.login_time < ' . (time() - 21600) . ' OR ' .'
						coupon_pool.login_time IS NULL
                	)')
            ->where('coupon.id = ' . $id)
            ->join('coupon', 'coupon_pool.coupon_id = coupon.id')
            ->find();

        if(!$coupon){
        	return $respData = [
                'status'  => false,
                'message' => $this->lang_menu['內容有誤'] /*無此優惠券*/
            ];
        }
        $limit_num = $coupon['limit_num']; // 領取數量上限
        //die();

        $coupon_count = Db::table('coupon_pool')
            ->where("owner='".$this->user['id']."' and coupon_id ='". $id."'")
            ->count();

        $coupon_count = $coupon_count + $num; // 已領取的量+量

	    if($cmd=='increase'){ // 用加法，多檢查以加入購物車的量
    		$cartData = (Array)json_decode(Session::get('cart'));
    		$incart_num = isset($cartData[$id.'_coupon']) ? $cartData[$id.'_coupon'] : 0;
    		$coupon_count = $coupon_count + $incart_num;
	    }

	    if( !Session::get('user') ){
			$respData = [
                'status'  => false,
                'message' => $this->lang_menu['請先登入會員']
            ];
		}elseif(!$coupon){
			//  $this->error('這張券不存在');
            $respData = [
                'status'  => false,
                'message' => $this->lang_menu['優惠券已全數領取完畢']
            ];
        }elseif ($coupon_count > $limit_num){
			//  $this->error('優惠券已領取完畢');
            $respData = [
                'status'  => false,
                'message' => $this->lang_menu['您已達優惠券領取上限']
            ];
        }else{
            $respData = [
                'status'  => true,
                'message' => $this->lang_menu['操作成功']/*領取成功*/
            ];
        }
        return $respData;
    }

	public function cart() {
		$Proposal = Proposal::withTeamMembersAndRequire(
			['GetCartData'],
			['user_id' => $this->user['id']]
		);

		$Proposal = MemberFactory::createNextMember($Proposal);

		$cartData = [];
		$product_id_Data=[];
		$cartTotal=0;
		foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
			
			$singleData = Cart::get_singleData($key);

			$singleData['num'] = $value;
			array_push($cartData, $singleData);
			$cartTotal=$cartTotal+($singleData['countPrice']*$singleData['num']);
		}

		return $cartTotal;
	}

	public function reg() {
		if(empty(session('user')['id'])){
			$this->error($this->lang_menu['請先登入會員']);
		}
		if(empty(session('user')['name'])){
			$this->error($this->lang_menu['請確認會員後台已填寫會員姓名']);
		}

		if(empty( Db::table('excel')->where("product_name = '".$_POST['product_name']."'")->select() )){
			$this->error($this->lang_menu['請先登入會員']);
		}

		$compare_code = Db::table('excel')->where("product_name = '".$_POST['product_name']."' and product_code = 'XXXXXXXXXXXXXX'")->select();
		if(empty($compare_code)){ // 當沒有特殊產品序號，要比對有無產品序號
			if(empty(Db::table('excel')->where("product_code = '".$_POST['product_code']."' and product_name = '".$_POST['product_name']."'")->select() )){
				$this->error($this->lang_menu['產品序號不存在(請注意前後是否有輸入到空白或英文數字以外的符號)']);
			}
		}
		
		// 比對是否被註冊過
		if(!empty(Db::table('excel')->where("product_code = '".$_POST['product_code']."' and product_name = '".$_POST['product_name']."' and status != '0'")->select() )){
			$this->error($this->lang_menu['產品序號已註冊']);
		}

		// 比對是否僅輸入數字+英文
		if(preg_match('/^([0-9A-Za-z]+)$/',$_POST['product_code']) ==0 ){
			$this->error($this->lang_menu['產品序號僅可輸入英文及數字']);
		}

		$width = 370; $height = 370;
		$image = Request::instance()->file('image');
		if($image){
			$DBFileConnecter = new DBFileConnecter();
			$_POST['pic'] = 	$DBFileConnecter->fixedFileUp($image, 'reg_' . time(), $width, $height);
		}else{
			$this->error($this->lang_menu['請上傳購買證明']);
		}

		$_POST['regtime'] = date('Y-m-d');
		$_POST['account_number'] = session('user')['id'];
		$_POST['status'] = '0';
		$_POST['pro_id'] = Db::table('excel_reply')->where("regtime = '".date('Y-m-d')."'")->count();
		$_POST['pro_id']++;
		$_POST['pro_id']  = date('Ymd').str_pad($_POST['pro_id'],5,'0',STR_PAD_LEFT);
		$_POST['account_name'] = session('user')['name'];

		Db::table('excel_reply')->insert($_POST);
		$mailBody = "
			<html>
				<head></head>
				<body>
					<div>
						親愛的管理員您好：<br>
						會員 <sapn style='color:red;'>".session('user')['name']."</sapn> 於<sapn style='color:red;'>".$_POST['regtime']."</sapn>送出註冊商品資訊<br>
						回函編號為<sapn style='color:red;'>".$_POST['pro_id']."</sapn><br>
						再麻煩至後台審核<br>
						<br><br>
						<br><br>
					</div>
					<div style='color:red;'>
					≡ 此信件為系統自動發送，請勿直接回覆 ≡
					</div>
				</body>
			</html>
		";
		$mail_return = parent::Mail_Send($mailBody,'admin','',"註冊商品資訊");
		$this->success($this->lang_menu['寄送成功，我們已收到您的註冊資料，將盡速為您處理']);
	}

	/*依商品id取得品項*/
	static public function get_price($productinfo_id){
		$db = Db::table('productinfo_type py');
		$price = $db->field('py.*,py.num as number')
			->where('py.product_id = '.$productinfo_id.' AND py.online')
			->order('py.order_id asc, py.id asc')
			->select();
		// dump($db->getLastSql());
		return $price;
	}
	/*依商品id標記優惠券*/
	static public function find_prod_coupon($id){
		$coupon_button='';
		$coupon_confirm=[];
		$coupon_confirm = Db::table('coupon')
                            ->where("(area_id='".$id."' or area='0') and online='1' and end > '".time()."' and  type='0' ")
                            ->order('area desc,end desc,price')->select();
		if(!empty($coupon_confirm)){
			if(count($coupon_confirm)>1){
				$coupon_button='href="/index/product/coupon.html?prod_id='.$id.'"';
			}else if ($coupon_confirm[0]['price'] == 0){
                $coupon_button='href="javascript:void(0)" onclick="getCoupon('.$coupon_confirm[0]['id'].')"';
            }else{
                $coupon_button='href="/index/product/couponinfo.html?id='.$coupon_confirm[0]['id'].'"';
            }
		}

		return $coupon_button;
	}
	/*依商品id標記活動or折扣*/
	static public function find_prod_act($id){
		$act_confirm = DB::table('act')->alias('a')
			->join('act_product ap','ap.act_id = a.id')
			->where('ap.prod_id='.$id)
			->where("a.online = 1 
					AND (
						a.end <= 0 
						OR 
						( a.start < " . time() . " AND  a.end > " . time() . ")
						)")
			->select();

		$act_data = ['act_type'=>0, 'type_name'=>'', 'act_button'=>'', 'name'=>'', 'content'=>''];
		if(!empty($act_confirm)){
			$act_data['type_name'] = $act_confirm[0]['act_type'] == 1 ? "活動" : "折扣";
			$act_data['act_type'] = $act_confirm[0]['act_type'];
			$act_data['link'] = 'href="/index/product/activity.html?id='.$act_confirm[0]['id'].'"';
			$act_data['name'] = $act_confirm[0]['name'];
			$act_data['content'] = $act_confirm[0]['content'];
		}
		return $act_data;
	}
	/*依商品顯示品項選項*/
	public function get_product_price_option($item){
		// dump($item);
		$return_item = [];

		if( $this->control_register ==1 && $item['is_registrable'] ==1 && $item['expire_date'] < time() && $item['expire_date'] !=-28800){ // 需填寫註冊資料 且 截止日期小於現在時間 且 截止日期不等於-28800
			$return_item[0]['subtitle'] = '';
			$return_item[0]['has_price'] = $this->lang_menu['報名已截止'];
			$return_item[0]['idtype'] = '';

			$return_item[0]['originalPrice']	= '';
            $return_item[0]['offerPrice'] 		= '<span class="price">'.$this->lang_menu['報名已截止'].'</span>';
		}else if ($item['has_price'] == 1){
								
            /*取得商品所有品項*/						
            $return_item = $this->get_price($item['id']);
            
			if(!empty($return_item)){
				foreach($return_item as $sk => $sv){
					$return_item[$sk]['subtitle'] = $sv['title'];
					$return_item[$sk]['has_price'] = '$' .  $sv['count'];
					$return_item[$sk]['idtype'] = $sv['id'];
					$return_item[$sk]['pic_index'] = $sv['pic_index'];

					if($sv['price']==$sv['count']){
						$return_item[$sk]['originalPrice']	= '';
            			$return_item[$sk]['offerPrice'] 	= dolar.dolar_mark.' '.'<span class="price">'.$sv['count'].'</span> '.$this->lang_menu['起'];
					}else{
						$return_item[$sk]['originalPrice']	= dolar.dolar_mark.' '.$sv['price'];
            			$return_item[$sk]['offerPrice'] 	= dolar.dolar_mark.' '.'<span class="price">'.$sv['count'].'</span> '.$this->lang_menu['起'];
					}
				}
			}else{
				$return_item[0]['subtitle'] = '';
				$return_item[0]['has_price'] = $this->lang_menu['請詢價'];
				$return_item[0]['idtype'] = '';
				$return_item[0]['pic_index'] = '';

				$return_item[0]['originalPrice']	= '';
            	$return_item[0]['offerPrice'] 		= '<span class="price">'.$this->lang_menu['請詢價'].'</span>';
			}

		}else{
			$return_item[0]['subtitle'] = '';
			$return_item[0]['has_price'] = $this->lang_menu['請詢價'];
			$return_item[0]['idtype'] = '';
			$return_item[0]['pic_index'] = '';

			$return_item[0]['originalPrice']	= '';
            $return_item[0]['offerPrice'] 		= '<span class="price">'.$this->lang_menu['請詢價'].'</span>';
		}

		// dump($return_item);
		return $return_item;
	}

	static public function get_addprice_products($type_ids){
		if(!$type_ids) return ['addprice_type_group'=>[], 'addprice_group'=>[]];
		/*製作符合品項的搜尋條件*/
		$type_count=[];
		foreach ($type_ids as $value) {
			if( !isset($type_count[$value]) ){
				$type_count[$value] = 1;
			}else{
				$type_count[$value] += 1;
			}
		}
		$type_ids_where = $type_ids ? 'product_type_id in ('.implode(',', $type_ids).')' : "1=1";
		
		/*製作正在啟用中的加價購搜尋條件*/
		$addprices = Addprice::getListByDate($start=date('Y-m-d'),$end=date('Y-m-d'), $frontEnd=true);
		$addprices_list=[];
		foreach ($addprices as $key => $value) {
			array_push($addprices_list, $value['id']);
		}
		$addprices_where = $addprices_list ? 'addprice_id in ('.implode(',', $addprices_list).')' : "1=0"; 

		/*找出 正在啟用中 且 條件商品是此品項的 全部條件商品*/
		$ruleProds = Db::table('addprice_rule')
				->field('pt.*,
						 pi.title pi_title,pi.pic, product_type_id, addprice_id as addprice_id')
				->join('productinfo_type pt', 'pt.id = product_type_id')
				->join('productinfo pi','pi.id = pt.product_id')
				
				->where($addprices_where)
				->where($type_ids_where)
				->select();
		$addprice_type_group = []; /*跟據各品項整理可加價購商品*/
		$addprice_group = []; /*整理全部可購買加價購商品*/
		foreach ($ruleProds as $rp_k => $rp_v) {
			/*根據條件商品 找出對應加價購的優惠折扣、加價購商品*/
			$actProd = Db::table('addprice_product')->alias('adp_p')
				->field('pt.*,
						 pi.title pi_title,pi.pic, pi.content, 
						 adp_p.limit_num as adp_p_num, adp.discount as adp_dis')
				->join('productinfo_type pt', 'pt.id = adp_p.product_type_id')
				->join('productinfo pi','pi.id = pt.product_id')
				->join('addprice adp','adp.id = adp_p.addprice_id')
				->where('adp_p.addprice_id', $rp_v['addprice_id'])
				->select();
			$rp_v['pic'] = json_decode($rp_v['pic'],true);
			$rule_type_id = 'type'.$rp_v['id'];
			foreach ($actProd as $ap_k => $ap_v) {
				$product_type_id = 'type'.$ap_v['id'];
				$ap_v['cart_id'] = $ap_v['id'].'_add';
				$ap_v['pic'] = json_decode($ap_v['pic'],true);
				$ap_v['adp_p_num'] = ($ap_v['adp_p_num'] * $type_count[ $rp_v['product_type_id'] ]); /*依給的type_ids修正可加購數量*/
				$ap_v['adp_dis'] = round($ap_v['adp_dis'] * $ap_v['count']);
				/*整理addprice_type_group-----------------------------*/
				/*檢查是否已存在此加價購條件商品*/
				if( !isset($addprice_type_group[$rule_type_id]) ){ /*加入條件商品*/
					$addprice_type_group[$rule_type_id] = [
						'rule_info' => $rp_v,
						'product'	=> [],
					];
				}
				/*檢查是否已存在此加價購商品*/
				if( !isset($addprice_type_group[$rule_type_id]['product'][$product_type_id]) ){ /*不存在*/
					$addprice_type_group[$rule_type_id]['product'][$product_type_id] = $ap_v; /*加入加價購商品*/

				}else{/*存在*/
					$addprice_type_group[$rule_type_id]['product'][$product_type_id]['adp_p_num'] += $ap_v['adp_p_num']; /*購買上限數量相加*/
					if($addprice_type_group[$rule_type_id]['product'][$product_type_id]['adp_dis']>$ap_v['adp_dis']){ /*檢查優惠折扣，取較小者*/
						$addprice_type_group[$rule_type_id]['product'][$product_type_id]['adp_dis']=$ap_v['adp_dis'];
					}
				}
				/*----------------------------------------------------*/

				/*整理addprice_group----------------------------------*/
				if( !isset($addprice_group[$product_type_id]) ){ /*加入條件商品*/
					$addprice_group[$product_type_id] = $ap_v;
				}else{
					$addprice_group[$product_type_id]['adp_p_num'] += $ap_v['adp_p_num']; /*購買上限數量相加*/
					if($addprice_group[$product_type_id]['adp_dis']>$ap_v['adp_dis']){ /*檢查優惠折扣，取較小者*/
						$addprice_group[$product_type_id]['adp_dis']=$ap_v['adp_dis'];
					}
				}
				/*----------------------------------------------------*/
			}
		}

		return ['addprice_type_group'=>$addprice_type_group, 'addprice_group'=>$addprice_group];
	}

	/*依商品取得人氣紀錄*/
	static public function get_love_record($id)
	{
		$result = self::get_record('product_love', $id);
		return $result;
	}
	/*依商品取得收藏紀錄*/
	static public function get_store_record($id)
	{
		$result = self::get_record('product_store', $id);
		return $result;
	}
	/*依給定資料表、商品id取得紀錄*/
	static public function get_record($tableName, $id)
	{
		$user_id = Session('user.id');
		/*是否有點過*/
		$record = 0;
		if($user_id){
			$record = Db::table($tableName)->where('product_id', $id)->where('user_id', $user_id)->select();
			$record = $record ? 1 : 0;
		}

		/*此商品被多少人點了*/
		$num = Db::table($tableName)->where('product_id', $id)->count();

		return [
			'record' => $record,
			'num' => $num,
		];
	}
}

