<?php

namespace pattern\recursiveCorrdination\cartRC;

use think\Db;
use think\Session;

class GetCartData extends TeamMember {
    
    public static function createFactory($Proposal) {
		$instance = new self($Proposal);
        $returnData = $instance->participate();
        $instance->filterData($returnData);
        $returnData->projectData['data'] = json_encode($returnData->projectData['data']);
        return $returnData;
	}
    public function participate() {
        $require = $this->Proposal->getRequire();
        $this->Proposal->projectData = $this->createOrGetData($require['user_id']);

        return $this->send2Next();
    }

    private function createOrGetData($id) {
       // $cartData = Db::table('cart')->where('user_id', $id)->find();
        $cartData['data'] = Session::get('cart');
        if($cartData['data']) {
            $cartData['data'] = json_decode($cartData['data']) ? json_decode($cartData['data'], true) : [];
            return $cartData;
        }
        return $this->createData($id);
    }

    private function createData($id) {
        $cartData = [
            'user_id' => $id,
            'data' => '[]'
        ];
        $cartData['id'] = Db::table('cart')->insertGetId($cartData);

        $cartData['data'] = [];
        return $cartData;
    }

    private function filterData($returnData){
        array_walk($returnData->projectData['data'], function ($value, $key) use (&$returnData) {

            $key_list = explode('_', $key);
            $id = $key_list[0];

            if( count($key_list)==1 ){ // 是一般商品
                if ( !Db::table('productinfo_type')->find($id) ) {
                    unset($returnData->projectData['data'][$key]);
                }
            }elseif ( $key_list[1] == 'normal' ) { // 是一般商品
                if ( !Db::table('productinfo_type')->find($id) ) {
                    unset($returnData->projectData['data'][$key]);
                }
            }elseif ( $key_list[1] == 'counpon' ) { // 是優惠券商品
                if ( !Db::table('productinfo_type')->where('coupon_pool ='.$id.' and owner is null' )->find() ) {
                    unset($returnData->projectData['data'][$key]);
                }
            }
        });
    }
}
