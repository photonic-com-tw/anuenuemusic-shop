<?php



namespace pattern\recursiveCorrdination\discountRC;



use think\Db;



class ActCalculate extends TeamMember {

    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
		return $instance->participate();
	}



    public function participate() {

        $acts = $this->getActs();

        ////////////////////////////////
        // Calculate act and none-act
        ////////////////////////////////

        //
        //---- rearrange activity ---
        //
        $activity = [];
        foreach($acts as $actKey => $actValue){
            $actId = $actValue['id'];
            $activity[$actId]['type'] = $actValue['type'];
            $activity[$actId]['name'] = $actValue['name'];

            $activity[$actId]['discount'][] = ['condition'=>$actValue['condition1'],'discount' =>$actValue['discount1'],'online'=>$actValue['online1']];

            $activity[$actId]['discount'][] = ['condition'=>$actValue['condition2'],'discount' =>$actValue['discount2'],'online'=>$actValue['online2']];

            $activity[$actId]['discount'][] = ['condition'=>$actValue['condition3'],'discount' =>$actValue['discount3'],'online'=>$actValue['online3']];
        }//foreach


        foreach($activity as $acKey => $asValue){
            usort($asValue['discount'],function($a,$b){
                return ($b['condition']-$a['condition']);
            });

            $activity[$acKey] = $asValue;
        }//foreach


        //
        //--- actCart & noneActCart --- /*區分商品是否有適用活動*/
        //

        $require = $this->Proposal->getRequire();
        $cart = $require['cartData'];
        $actCart     = [];
        $noneActCart = [];
        $prodTotal = 0;
        $prodCount = 0;
        foreach($cart as $caKey => $caValue){
            $actProduct = Db::table('act_product')->alias('ap')
                            ->join('act','act.id = ap.act_id')
                            ->where('prod_id',$caValue['type_product_id'])
                            //where condition is sam as $this->getActs()
                            ->where("
                                        online = 1 AND 
                                        (
                                            end <= 0 
                                            OR 
                                            (start < " . time() . " AND end > " . time() . ")
                                        ) AND 
                                        act_type = 1
                            ")
                            ->find();

            if ($actProduct && ($caValue['key_type']=='normal'||substr($caValue['key_type'],0, 3)=='kol') ){ /*有搜尋到活動 且是 (一般商品 或 網紅商品)*/
                $actId = $actProduct['act_id'];
                $act = DB::table('act')->where('id',$actProduct['act_id'])->find();
                $actCart[$actId]['type']    = $act['type'];
                $actCart[$actId]['name']    = $act['name'];
                $actCart[$actId]['prod'][]  = $caValue;

                if (!isset($actCart[$actId]['total']))  $actCart[$actId]['total'] = 0;
                if (!isset($actCart[$actId]['count']))  $actCart[$actId]['count'] = 0;
                $prodTotal += $caValue['countPrice']*$caValue['num'];
                $prodCount += $caValue['num'];
                $actCart[$actId]['total'] += $caValue['countPrice']*$caValue['num'];
                $actCart[$actId]['count'] += $caValue['num'];

            }else{
                $noneActCart['prod'][] = $caValue;
                if (!isset($noneActCart['total'])) $noneActCart['total'] = 0;
                if (!isset($noneActCart['count'])) $noneActCart['count'] = 0;
                $prodTotal += $caValue['countPrice']*$caValue['num'];
                $prodCount += $caValue['num'];
                $noneActCart['total'] += $caValue['countPrice']*$caValue['num'];
                $noneActCart['count'] += $caValue['num'];
            }
        }//foreach

        //
        //--- calcute each discount of act product ---
        //

        foreach ($actCart as $acKey => $acValue){
                $actCart[$acKey]['calculated'] = $this->getDiscount($acKey,$acValue,$activity);
        }//foreach



        // check inconsistent act product
        $inconsistentActProd = [];

        foreach($actCart as $acKey => $acValue){
            $acDiscount = $acValue['calculated']['discount'];
            if($acDiscount == 0){
                $inconsistentActProd[] = $acKey;
            }
        }

        //
        //--- re-arrange if there are inconsistent act product
        //
        if (count($inconsistentActProd)>0){
            foreach($inconsistentActProd as $incKey => $incValue){
                $incActProd = $actCart[$incValue]['prod'];
                unset($actCart[$incValue]);
                foreach($incActProd as $incProdKey => $incProdValue){
                    $noneActCart['prod'][] = $incProdValue;
                }
            }
        }

        //
        //--- sum ---
        //
        $sum = 0; /*折扣後總金額*/
        $sumNoneGetPoint = 0; /*不可計算點數的金額(立馬省商品)*/
        foreach($actCart as $acartKey => $acartValue){
            $sum += $acartValue['calculated']['disTotal']; /*加各個活動優惠後的金額*/
        }
        /*加非活動商品的金額*/
        if (isset($noneActCart['prod'])){
            foreach($noneActCart['prod'] as $noneActKey => $noneActValue){
                if($noneActValue['key_type']=='normal' || substr($noneActValue['key_type'],0, 3)=='kol'){ /*一般商品 或 網紅商品 需檢查立馬省*/
                	$cashDiscount = DB::table('act')->alias('a')
    									->join('act_product ap','a.id = ap.act_id')
    									->where('a.online',1)
    									->where('ap.prod_id',$noneActValue['type_product_id'])
                                        ->where('a.act_type', 2)
    									->find();

                	if ($cashDiscount){
                        /*有套用立馬省*/
    					$sum += ($noneActValue['type_count']-$cashDiscount['discount1'])*$noneActValue['num']; /*加此品項扣除立馬省*數量*/
                        $sumNoneGetPoint += ($noneActValue['type_count']-$cashDiscount['discount1'])*$noneActValue['num'];
    				}else{
                        /*無套用立馬省*/
    					$sum += $noneActValue['type_count']*$noneActValue['num'];
                        if(substr($noneActValue['key_type'],0, 3)=='kol'){/*網紅商品沒套用立馬省也不計算點數*/
                            $sumNoneGetPoint += $noneActValue['type_count']*$noneActValue['num'];
                        }
    				}
                }else{
                    $sum += $noneActValue['type_count']*$noneActValue['num'];
                    if($noneActValue['key_type']=='add'){ /*加價購商品 不計算點數*/
                        $sumNoneGetPoint += $noneActValue['type_count']*$noneActValue['num'];
                    }
                }
            }
        }

        $discount = [
            'sum'       => $sum,
            'sumNoneGetPoint'    => $sumNoneGetPoint,
            'actCart'   => $actCart,
        ];

        $this->Proposal->projectData['acts'] = $discount;//$acts;
        return $this->send2Next();
    }

    private function getActs() {
        $require = $this->Proposal->getRequire();
        return Db::table('act')
                ->field('
                    id, name, type, location,
                    online1, condition1, discount1,
                    online2, condition2, discount2,
                    online3, condition3, discount3
                ')
                ->where("
                            online = 1 AND 
                            (
                                end <= 0 
                                OR 
                                (start < " . time() . " AND end > " . time() . "
                            ) AND 
                            act_type = 1
                        )")
                ->select();
    }

    private function getDiscount($actId,$actProd,$activity){
        $act = $activity[$actId];
        $total = $actProd['total'];
        $count = $actProd['count'];

        // switch($act['type']){
        //     case 1:
        //         if(($total >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
        //             $disTotal =  round($actProd['total']*$act['discount'][0]['discount']/10);
        //             $discount = $act['discount'][0]['discount'];
        //         }elseif (($total >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
        //             $disTotal =  round($actProd['total']*$act['discount'][1]['discount']/10);
        //             $discount = $act['discount'][1]['discount'];
        //         }elseif (($total >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
        //             $disTotal =  round($actProd['total']*$act['discount'][2]['discount']/10);
        //             $discount = $act['discount'][2]['discount'];
        //         }else{
        //             $disTotal = $actProd['total'];
        //             $discount = 0;
        //         }
        //         return ['type'=>1,'disTotal'=>$disTotal,'discount'=>$discount];
        //         break;

        //     case 2:
        //         if(($total >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
        //             $disTotal =  round($actProd['total']-$act['discount'][0]['discount']);
        //             $discount = $act['discount'][0]['discount'];
        //         }elseif (($total >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
        //             $disTotal =  round($actProd['total']-$act['discount'][1]['discount']);
        //             $discount = $act['discount'][1]['discount'];
        //         }elseif (($total >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
        //             $disTotal =  round($actProd['total']-$act['discount'][2]['discount']);
        //             $discount = $act['discount'][2]['discount'];
        //         }else{
        //             $disTotal = $actProd['total'];
        //             $discount = 0;
        //         }
        //         return ['type'=>2,'disTotal'=>$disTotal,'discount'=>$discount];
        //         break;

        //     case 3:
        //         if(($count >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
        //             $disTotal =  round($actProd['total']*$act['discount'][0]['discount']/10);
        //             $discount = $act['discount'][0]['discount'];
        //         }elseif (($count >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
        //             $disTotal =  round($actProd['total']*$act['discount'][1]['discount']/10);
        //             $discount = $act['discount'][1]['discount'];
        //         }elseif (($count >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
        //             $disTotal =  round($actProd['total']*$act['discount'][2]['discount']/10);
        //             $discount = $act['discount'][2]['discount'];
        //         }else{
        //             $disTotal = $actProd['total'];
        //             $discount = 0;
        //         }
        //         return ['type'=>3,'disTotal'=>$disTotal,'discount'=>$discount];
        //         break;

        //     case 4:
        //         if(($count >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
        //             $disTotal =  round($actProd['total']-$act['discount'][0]['discount']);
        //             $discount = $act['discount'][0]['discount'];
        //         }elseif (($count >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
        //             $disTotal =  round($actProd['total']-$act['discount'][1]['discount']);
        //             $discount = $act['discount'][1]['discount'];
        //         }elseif (($count >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
        //             $disTotal =  round($actProd['total']-$act['discount'][2]['discount']);
        //             $discount = $act['discount'][2]['discount'];
        //         }else{
        //             $disTotal = $actProd['total'];
        //             $discount = 0;
        //         }
        //         return ['type'=>3,'disTotal'=>$disTotal,'discount'=>$discount];
        //         break;
        // }

        $discountReault = $this->discountCycle($act, $actProd, $total, $count);
        if($discountReault['disTotal']<0){ // 如果折扣後金額為負，要調整成為0
            $discountReault['discount'] += $discountReault['disTotal'];
            $discountReault['disTotal'] = 0;
        }
        // dump($discountReault);

        return $discountReault;
    }

    private function discountCycle($act, $actProd, $total, $count){
        switch($act['type']){
            case 1: // 滿幾元打幾折
                if(($total >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
                    $disTotal =  round($actProd['total']*$act['discount'][0]['discount']/10.0);
                    $discount = $act['discount'][0]['discount'];
                }elseif (($total >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
                    $disTotal =  round($actProd['total']*$act['discount'][1]['discount']/10.0);
                    $discount = $act['discount'][1]['discount'];
                }elseif (($total >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
                    $disTotal =  round($actProd['total']*$act['discount'][2]['discount']/10.0);
                    $discount = $act['discount'][2]['discount'];
                }else{
                    $disTotal = $actProd['total'];
                    $discount = 0;
                }

                return ['type'=>1,'disTotal'=>$disTotal,'discount'=>$discount];
                break;

            case 2: // 滿幾元扣幾元
                $discount = 0;
                if(($total >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
                    $discountReault_discount = $act['discount'][0]['condition']>0 ? $this->discountCycle($act, $actProd, $total-$act['discount'][0]['condition'], $count)['discount'] : 0;
                    $discount += $act['discount'][0]['discount'] + $discountReault_discount;
                }elseif (($total >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
                    $discountReault_discount = $act['discount'][1]['condition']>0 ? $this->discountCycle($act, $actProd, $total-$act['discount'][1]['condition'], $count)['discount'] : 0;
                    $discount += $act['discount'][1]['discount'] + $discountReault_discount;
                }elseif (($total >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
                    $discountReault_discount = $act['discount'][2]['condition']>0 ? $this->discountCycle($act, $actProd, $total-$act['discount'][2]['condition'], $count)['discount'] : 0;
                    $discount += $act['discount'][2]['discount'] + $discountReault_discount;
                }else{
                    $discount += 0;
                }

                $disTotal =  round($actProd['total']-$discount);

                return ['type'=>2,'disTotal'=>$disTotal,'discount'=>$discount];
                break;

            case 3: // 滿幾件打幾折
                if(($count >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
                    $disTotal =  round($actProd['total']*$act['discount'][0]['discount']/10.0);
                    $discount = $act['discount'][0]['discount'];
                }elseif (($count >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
                    $disTotal =  round($actProd['total']*$act['discount'][1]['discount']/10.0);
                    $discount = $act['discount'][1]['discount'];
                }elseif (($count >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
                    $disTotal =  round($actProd['total']*$act['discount'][2]['discount']/10.0);
                    $discount = $act['discount'][2]['discount'];
                }else{
                    $disTotal = $actProd['total'];
                    $discount = 0;
                }

                return ['type'=>3,'disTotal'=>$disTotal,'discount'=>$discount];
                break;

            case 4: // 滿幾件扣幾元
                $discount = 0;
                if(($count >= $act['discount'][0]['condition'])&&($act['discount'][0]['online']==1)){
                    $discountReault_discount = $act['discount'][0]['condition']>0 ? $this->discountCycle($act, $actProd, $total, $count-$act['discount'][0]['condition'])['discount'] : 0;
                    $discount += $act['discount'][0]['discount'] + $discountReault_discount;
                }elseif (($count >= $act['discount'][1]['condition'])&&($act['discount'][1]['online']==1)){
                    $discountReault_discount = $act['discount'][1]['condition']>0 ? $this->discountCycle($act, $actProd, $total, $count-$act['discount'][1]['condition'])['discount'] : 0;
                    $discount += $act['discount'][1]['discount'] + $discountReault_discount;
                }elseif (($count >= $act['discount'][2]['condition'])&&($act['discount'][2]['online']==1)){
                    $discountReault_discount = $act['discount'][2]['condition']>0 ? $this->discountCycle($act, $actProd, $total, $count-$act['discount'][2]['condition'])['discount'] : 0;
                    $discount += $act['discount'][2]['discount'] + $discountReault_discount;
                }else{
                    $discount += 0;
                }

                $disTotal =  round($actProd['total']-$discount);

                return ['type'=>3,'disTotal'=>$disTotal,'discount'=>$discount];
                break;
        }
    }
}

