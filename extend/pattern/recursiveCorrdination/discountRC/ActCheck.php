<?php

namespace pattern\recursiveCorrdination\discountRC;

use think\Db;

class ActCheck extends TeamMember {
    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
		return $instance->participate();
	}

    public function participate() {
        $acts = $this->getActs();
        $acts = array_map(function ($value){
            $Total = 0; $Count = 0;
            if($value['location']){
                $Total = $this->getTotalByLocation($value['location']);
            } else {
                $Total = $this->getTotal();
            }
            if($value['location']){
                $Count = $this->getCountByLocation($value['location']);
            } else {
                $Count = $this->getCount();
            }
            $value['Total'] = $Total;
            $value['Count'] = $Count;
            return $value;
        }, $acts);

        $acts = array_filter($acts, function ($value) {
            switch ($value['type']) {
                case 1:
                case 2:
                    if($value['condition1'] <= $value['Total']){
                        return true;
                    }
                    break;
                case 3:
                case 4:
                    if($value['condition1'] <= $value['Count']){
                        return true;
                    }
                    break;
            }
            return false;
        });
        $this->Proposal->projectData['acts'] = $acts;
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
                ->where("online = 1 
                AND (
                    end <= 0 
                    OR 
                    (start < " . time() . " AND end > " . time() . ")
                )")
                ->select();
    }

    private function getTotal() {
        $Total = 0;
        $require = $this->Proposal->getRequire();        
        array_walk($require['cartData'], function ($value) use (&$Total){
            $Total += (int)($value['countPrice']) * (int)$value['num'];
        });
        return $Total;
    }

    private function getTotalByLocation($locationId) {
        $Total = 0;
        $require = $this->Proposal->getRequire();        
        array_walk($require['cartData'], function ($value) use (&$Total, $locationId){
            if($value['productLocation'] == $locationId) {
                $Total += (int)($value['countPrice']) * (int)$value['num'];
            }
        });
        return $Total;
    }

    private function getCount() {
        $Total = 0;
        $require = $this->Proposal->getRequire();        
        array_walk($require['cartData'], function ($value) use (&$Total){
            $Total += (int)($value['num']);
        });
        return $Total;
    }

    private function getCountByLocation($locationId) {
        $Total = 0;
        $require = $this->Proposal->getRequire();        
        array_walk($require['cartData'], function ($value) use (&$Total, $locationId){
            if($value['productLocation'] == $locationId) {
                $Total += (int)$value['num'];
            }
        });
        return $Total;
    }
}
