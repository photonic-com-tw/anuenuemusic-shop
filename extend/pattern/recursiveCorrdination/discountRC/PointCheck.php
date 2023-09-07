<?php

namespace pattern\recursiveCorrdination\discountRC;

use think\Db;

class PointCheck extends TeamMember {
    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
		return $instance->participate();
	}

    public function participate() {
        $points = $this->getPoints();
        $this->Proposal->projectData['points'] = $points;
        return $this->send2Next();
    }

    private function getPoints() {
        $require = $this->Proposal->getRequire();
        return Db::connect('main_db')
                ->table('account')
                ->field('point')
                ->select($require['user_id']);
    }
}
