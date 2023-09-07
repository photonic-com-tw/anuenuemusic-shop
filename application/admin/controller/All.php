<?php



namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use app\admin\controller\Productinfo;
// productinfo/show_array

class All extends MainController{

	const PER_PAGE_ROWS = 20;
	const SIMPLE_MODE_PAGINATE = false;

	public function index() {

		// 是否使用特價商品
		$use_sepc_price = Db::connect('main_db')->table('excel')->where('id = 10')->find()['value1'];
		$this->assign('use_sepc_price', $use_sepc_price);

		// 是否可複製商品
		$copy_product = Db::connect('main_db')->table('excel')->where('id = 12')->find()['value1'];
		$this->assign('copy_product', $copy_product);


        // 分館分類搜尋
		$searchPrev = Request::instance()->get('searchPrev') ?? 0;
		$searchBranch = Request::instance()->get('searchBranch') ?? 0;
		if($searchBranch !=0){
			// 使用分類搜尋
			$searchPrev = Db::table('typeinfo')->where('id = '.$searchBranch)->find()['parent_id'];
			$cate_serach = '"branch_id":"'.$searchBranch.'"';

			// 依商品在該分類的order_id排序
			$productinfo_orders_where = 'AND po.branch_id='.$searchBranch;
			$order_sql = "po.order_id asc, productinfo.id desc";
		}elseif($searchPrev != 0){
			// 使用分館搜尋
			$cate_serach = '"prev_id":"'.$searchPrev.'","branch_id":"0","parent_id":"0"';

			// 依商品在該分館的order_id排序
			$productinfo_orders_where = 'AND (po.prev_id='.$searchPrev.' AND po.branch_id=0)';
			$order_sql = "po.order_id asc, productinfo.id desc";
		}else{
			// 不使用分館分類搜尋
			$cate_serach = '';

			// 依 productinfo 本身的order_id 排序
			$productinfo_orders_where = "";
			$order_sql = "productinfo.order_id asc, productinfo.id desc";
		}
		$cate_serach = $cate_serach == '' ? "" : "and final_array like '%".$cate_serach."%'";
		$this->assign('searchPrev', $searchPrev);
		$this->assign('searchBranch', $searchBranch);


		// 商品名稱、品項、條碼搜尋
		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
		if(!empty($searchKey)){
			$seach_where = "( UPPER(productinfo.title) like  UPPER('%".$searchKey."%') OR productinfo.product_id like '%".$searchKey."%' OR productinfo.ISBN like '%".$searchKey."%' )";
		}else{
			$seach_where = 'true';
		}


		// 庫存編碼搜尋
		$position_id = Request::instance()->get('position_id') ?? '';
		$this->assign('position_id', $position_id);
		if($position_id != ''){
			$seach_where .= " AND ( position_portion.position_id = '".$position_id."' )";
		}
		$position_number = Request::instance()->get('position_number') ?? '';
		$this->assign('position_number', $position_number);
		if($position_number != ''){
			$seach_where .= " AND ( position_portion.position_number =  '".$position_number."' )";
		}


		// 標籤勾選搜尋(人氣、店長...)
		$ck = $_GET['ck']??'';
		$ck_where='';
		if(!empty($_GET['ck'])){
			foreach($_GET['ck'] as $k => $v){
				$ck_where .= ' and '.$k.'.product_id IS NOT NULL ';
			}
		}


		// 網紅搜尋
		$kol_id = Request::instance()->get('kol_id') ?? '-1';
		$this->assign('kol_id', $kol_id);
		if($kol_id == -1){ // 不依搜尋網紅
        	$kol_search = ' AND ( kp.is_using=1 OR kp.kol_id is null)';
        }else if($kol_id == 0){ // 搜尋無網紅
        	$kol_search = ' AND ( (kp.kol_id =0 AND kp.is_using=1) OR kp.kol_id is null )';
        }else{ // 搜尋某網紅的
        	$kol_search = ' AND kp.kol_id ='.$kol_id.' AND kp.is_using=1';
        }

        // dump( $seach_where.$ck_where.$cate_serach.$kol_search.$productinfo_orders_where );
		$productinfo = Db::table('productinfo')
			->field('productinfo.title, productinfo.id, productinfo.order_id, po.order_id po_order_id, productinfo.product_id, productinfo.pic, productinfo.has_price, productinfo.updatetime, productinfo.online,expiring_product.product_id as expiring_product,hot_product.product_id as hot_product,recommend_product.product_id as recommend_product,spe_price_product.product_id as spe_price_product, productinfo.final_array, kol.kol_name')
			->join('position_portion',' productinfo.id = position_portion.product_id', 'left')
			->join('expiring_product',' productinfo.id = expiring_product.product_id','left')
			->join('hot_product',' productinfo.id = hot_product.product_id','left')
			->join('recommend_product',' productinfo.id = recommend_product.product_id','left')
			->join('spe_price_product',' productinfo.id = spe_price_product.product_id','left')
			->join('kol_productinfo kp', 'kp.productinfo_id = productinfo.id', 'left')
			->join('kol','kol.id = kp.kol_id', 'left')
			->join('productinfo_orders po','po.prod_id = productinfo.id', 'left')
			->where(" {$seach_where} {$ck_where} {$cate_serach} {$kol_search} {$productinfo_orders_where}")
			->group('productinfo.id')
			->order($order_sql)->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE, 
				[
					'query' => [
						'searchKey' => $searchKey,
						'searchPrev' => $searchPrev,
						'searchBranch' => $searchBranch,
						'ck'=>$ck,
						'position_id'=>$position_id,
						'position_number'=>$position_number,
						'kol_id'=>$kol_id,
					]
				]
			);

		$expiring_product = Db::table('expiring_product')->field('product_id')->where('product_id <> 0')->select();
		$hot_product = Db::table('hot_product')->field('product_id')->where('product_id <> 0')->select();
		$recommend_product = Db::table('recommend_product')->field('product_id')->where('product_id <> 0')->select();
		$spe_price_product = Db::table('spe_price_product')->field('product_id')->where('product_id <> 0')->select();

		$this->assign('productinfo', $productinfo);
		$productinfoItem = $productinfo->items();

		//dump($productinfoItem);exit;
		foreach ($productinfoItem as $key => $value) {
			$value['pic'] = json_decode($value['pic'],true);
			$_POST['array'] = $value['final_array'];
			$value['show_array'] = Productinfo::show_array();
			$value['show_array'] = str_replace(	'","', '<br>', $value['show_array']);
			$value['show_array'] = str_replace(	'["', '', $value['show_array']);
			$value['show_array'] = str_replace(	'"]', '', $value['show_array']);
			$value['item'] = '[]';
			if ($value['has_price'] == 1){
				$value['item'] = json_encode(Db::table('productinfo_type')
						->where('product_id = ' . $value['id'])
						->where('online = 1')
						->order('order_id asc')->order('id asc')->select());
			}

			$value['expiring_product'] = in_array(
				['product_id' => $value['id']], 
				$expiring_product
			) ? 1 : 0;

			$value['hot_product'] = in_array(
				['product_id' => $value['id']], 
				$hot_product
			) ? 1 : 0;

			$value['recommend_product'] = in_array(
				['product_id' => $value['id']], 
				$recommend_product
			) ? 1 : 0;

			$value['spe_price_product'] = in_array(
				['product_id' => $value['id']], 
				$spe_price_product
			) ? 1 : 0;

			$productinfoItem[$key] = $value;
		}
		$this->assign('productinfoItem', $productinfoItem);

		$top_option_html = $this->top_option();
		$this->assign('top_option_html', $top_option_html);

		$kol = Db::table('kol')->order('id desc')->select();
		$this->assign('kol', $kol);

		$position = Db::table('position')->order('name asc, id desc')->select();
		$this->assign('position', $position);

		//var_dump($parent);die();
		return $this->fetch('item-product');
	}

	public function indexOld() {
		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);

		$orderGet = Request::instance()->get('orderGet');
		$order = "productinfo_create_time desc";
		if(!($orderGet == '' || $orderGet == null)){
			switch ($orderGet) {
				case 'num':
					$order = 'productinfo_type_num desc';
					break;
				case 'total':
					$order = 'productinfo_type_total desc';
					break;
			}
        }

		$num = Db::table('productinfo')
			->field('
				product.title AS product_title,
				typeinfo.title AS typeinfo_title,
				typeinfo.id AS typeinfo_id,
				productinfo.title AS productinfo_title,
				productinfo.create_time AS productinfo_create_time,
				productinfo.product_id AS productinfo_product_id,
				productinfo.id AS productinfo_id'				
				// ,
				// productinfo_type.title AS productinfo_type_title,
				// productinfo_type.num AS productinfo_type_num,
				// productinfo_type.total AS productinfo_type_total
			)
			->where(
				// productinfo_type.title like  '%" . $searchKey . "%' OR
				"productinfo.product_id like  '%" . $searchKey . "%' OR
				productinfo.title like  '%" . $searchKey . "%'
			")
			// ->join('productinfo_type','productinfo_type.product_id = productinfo.id')
			->join('typeinfo','productinfo.parent_id = typeinfo.id')
			->join('product','typeinfo.parent_id = product.id')
			->order($order)
			->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE,
				[
					'query' => [
						'searchKey' => $searchKey,
						'orderGet' => $orderGet
					]
				]
			);
			//dump($num);

		$this->assign('num', $num);
		return $this->fetch('all');
	}

	public function top_option() {
		$option_html = "";
		$top_layer = Db::table('product')->field('id, title')->order('order_id asc, id asc')->select();

		foreach ($top_layer as $key => $value) {
			$option_html .='<option value="'.$value['id'].'">'.$value['title'].'</option>';
		}

		return $option_html;
	}
	public function layer_option() {

		$option_html = '<option value="0">選擇分類</option>';

		$next_layer = Db::table('typeinfo')->field('id, title')->where('parent_id ='.$_GET['prev_id'].' and branch_id=0')->order('order_id asc, id asc')->select();
		foreach ($next_layer as $nk => $nv) {
			$option_html .='<option value="'.$nv['id'].'">'.$nv['title'].'</option>';
			$option_html .= $this->get_next_option($nv['id'],'__');
		}

		return $option_html;
	}
	public function get_next_option($branch_id, $prefix) {
		$return_html = "";
		$next_layer = Db::table('typeinfo')->field('id, title')->where('branch_id ='.$branch_id)->order('order_id asc, id asc')->select();

		if(!empty($next_layer)){
			foreach ($next_layer as $nk => $nv) {
				$return_html .='<option value="'.$nv['id'].'">'.$prefix.$nv['title'].'</option>';
				$next_prefix = $prefix . '__';
				$return_html .= $this->get_next_option($nv['id'], $next_prefix);
			}
		}
		return $return_html;
	}
}

