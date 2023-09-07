<?php

namespace pattern\recursiveCorrdination\discountRC;

use think\Db;
use app\index\controller\Member;

class MemberDiscount extends TeamMember {

    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
		return $instance->participate();
	}

    public function participate() {

        $member = Member::get_member_data();

        // 首購優惠
        $orderform = Db::connect(config('main_db'))->table('orderform')
                    ->where('status!="Cancel" and status!="Return"')
                    ->where('user_id', $member['id'])
                    ->select();

        $Total = 0;
        $require = $this->Proposal->getRequire();        
        array_walk($require['cartData'], function ($value) use (&$Total){
            $Total += (int)($value['countPrice']) * (int)$value['num'];
        });
        // dump($Total);

        if(!$orderform){ // 未有 新或完成 的訂單
            $this->Proposal->projectData['firstBuyDiscount'] = $this->get_discount('0', $Total);
        }else{
            $this->Proposal->projectData['firstBuyDiscount'] = [
                'can_use' => 0,
                'discount' => 0,
                'vip_name' => "",
                'note'  => "",
            ];
        }
        
        // Vip等級優惠優惠
        if($member['vip_id']!=0){ // 等級不是0
            $this->Proposal->projectData['vipDiscount'] = $this->get_discount($member['vip_id'], $Total); 
        }else{
            $this->Proposal->projectData['vipDiscount'] = [
                'can_use' => 0,
                'discount' => 0,
                'vip_name' => "",
                'note'  => "",
            ];
        }
        return $this->send2Next();
    }

    public function get_discount($vip_id, $total){
        $vip_type = Db::connect(config('main_db'))->table('vip_type')->find($vip_id);

        if($vip_type['type']=="0"){ // 打折
            $discount = round( (1 - ((float)$vip_type['discount'] / 10.0)) * $total );
        }else if($vip_type['type']=="1"){ // 扣元
            $discount = $vip_type['discount'] > $total ? $total : $vip_type['discount'];
        }else{
            $discount = 0;
        }

        return [
                'can_use' => 1,
                'discount' => $discount,
                'vip_name'  => $vip_type['vip_name'],
                'note'  => $vip_type['note'],
        ];
    }
}
