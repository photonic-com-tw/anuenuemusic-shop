<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

use pattern\recursiveCorrdination\cartRC\MemberFactory;
use pattern\recursiveCorrdination\cartRC\Proposal;
use app\index\controller\Cart;

class Coupondirect extends Controller
{
	public static function get_discount($user_code=false)
	{
		$user_id = session('user.id');
		if($user_id === null) return ['status'=>0, 'discount'=> 0, 'msg'=>$this->lang_menu['請先登入會員']];

		$user_code = $user_code ? $user_code : $_POST['user_code'];
		if(!$user_code) return ['status'=>0, 'discount'=> 0, 'msg'=>$this->lang_menu['請輸入優惠券代碼']];

		/*找出適用的優惠券*/
		$end_time = strtotime(date('Y-m-d'));
		$coupon_direct = Db::table('coupon_direct')
			->where('user_code', $user_code)
			->where('online', 1)
			->where('start', "<=", time())
			->where(function ($query) use ($end_time){
                $query->whereOr('end', -28800)
                	  ->whereOr('end',  ">=", $end_time);
            })
			->find();
		if(!$coupon_direct) return ['status'=>0, 'discount'=> 0, 'msg'=>$this->lang_menu['查無此優惠券']];

		/*檢查是否已使用過*/
		$coupon_direct_record = Db::table('coupon_direct_record')
			->where('coupon_id', $coupon_direct['id'])
			->where('user_id', $user_id)
			->select();
		if($coupon_direct_record) return ['status'=>0, 'discount'=> 0, 'msg'=>$this->lang_menu['您已達此優惠券的使用上限']];

		$coupon_product = Db::table('coupon_direct_product')->where('coupon_id', $coupon_direct['id'])->select();
        $total = 0; // 優惠券商品總金額
        $count = 0; // 優惠券商品總件數

        /* 取得購物車商品 */
        $Proposal = Proposal::withTeamMembersAndRequire(
            ['GetCartData'],
            ['user_id' => $user_id]
        );
        $Proposal = MemberFactory::createNextMember($Proposal);
        foreach (json_decode($Proposal->projectData['data'], true) as $key => $value) {
            $singleData = Cart::get_singleData($key); /* 取得商品資料 */

            if($singleData['key_type']=='normal'){ // 如果是一般商品
            	foreach ($coupon_product as $k => $v) {
            		if($v['prod_id']==$singleData['type_product_id']){ // 如果屬於優惠券商品
	            		$total += $value * $singleData['countPrice']; 	// 優惠商品金額+
	            		$count += $value;								// 優惠商品數量+
                        break;
            		}
            	}
            }
        }

        // 計算優惠金額
        $discountReault = self::discountCycle($coupon_direct, $total, $count);
        if($discountReault['disTotal']<0){ // 如果折扣後金額為負，要調整成為0
            $discountReault['discount'] += $discountReault['disTotal'];
            $discountReault['disTotal'] = 0;
        }

        if($discountReault['discount']==0) return ['status'=>0, 'discount'=> 0, 'msg'=>$this->lang_menu['不符合此優惠券的使用條件']];

		return ['status'=>1, 'id'=>$coupon_direct['id'], 'name'=> $coupon_direct['name'], 'discount'=> $discountReault['discount']];
	}

    public static function discountCycle($act, $total, $count){
        switch($act['type']){
            case 1: // 滿幾元打幾折
                if(($total >= $act['condition3'])&&($act['online3']==1)){
                    $disTotal = round($total*$act['discount3']/10.0);
                }elseif (($total >= $act['condition2'])&&($act['online2']==1)){
                    $disTotal = round($total*$act['discount2']/10.0);
                }elseif (($total >= $act['condition1'])&&($act['online1']==1)){
                    $disTotal = round($total*$act['discount1']/10.0);
                }else{
                    $disTotal = $total;
                }
                $discount = $total-$disTotal;
                break;

            case 2: // 滿幾元扣幾元
                $discount = 0;
                if(($total >= $act['condition3'])&&($act['online3']==1)){
                    $discountReault = self::discountCycle($act, $total-$act['condition3'], $count);
                    $discount += $act['discount3'] + $discountReault['discount'];
                }elseif (($total >= $act['condition2'])&&($act['online2']==1)){
                    $discountReault = self::discountCycle($act, $total-$act['condition2'], $count);
                    $discount += $act['discount2'] + $discountReault['discount'];;
                }elseif (($total >= $act['condition1'])&&($act['online1']==1)){
                    $discountReault = self::discountCycle($act, $total-$act['condition1'], $count);
                    $discount += $act['discount1'] + $discountReault['discount'];;
                }else{
                    $discount += 0;
                }
                $disTotal = round($total-$discount);
                break;

            case 3: // 滿幾件打幾折
                if(($count >= $act['condition3'])&&($act['online3']==1)){
                    $disTotal = round($total*$act['discount3']/10.0);
                }elseif (($count >= $act['condition2'])&&($act['online2']==1)){
                    $disTotal = round($total*$act['discount2']/10.0);
                }elseif (($count >= $act['condition1'])&&($act['online1']==1)){
                    $disTotal = round($total*$act['discount1']/10.0);
                }else{
                    $disTotal = $total;
                }
                $discount = $total-$disTotal;
                break;

            case 4: // 滿幾件扣幾元
                $discount = 0;
                if(($count >= $act['condition3'])&&($act['online3']==1)){
                    $discountReault = self::discountCycle($act, $total, $count-$act['condition3']);
                    $discount += $act['discount3'] + $discountReault['discount'];
                }elseif (($count >= $act['condition2'])&&($act['online2']==1)){
                    $discountReault = self::discountCycle($act, $total, $count-$act['condition2']);
                    $discount += $act['discount2'] + $discountReault['discount'];
                }elseif (($count >= $act['condition1'])&&($act['online1']==1)){
                    $discountReault = self::discountCycle($act, $total, $count-$act['condition1']);
                    $discount += $act['discount1'] + $discountReault['discount'];
                }else{
                    $discount += 0;
                }
                $disTotal = round($total-$discount);
                break;
        }
        return ['type'=>$act['type'],'disTotal'=>$disTotal,'discount'=>$discount];
    }
}

