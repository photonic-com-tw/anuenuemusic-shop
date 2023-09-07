<?php



namespace pattern\recursiveCorrdination\discountRC;



use think\Db;



class Total extends TeamMember {

    

    public static function createFactory($Proposal) {

        $instance = new self($Proposal);

		return $instance->participate();

	}



    public function participate() {

        $Total = $this->getTotal();

        $this->Proposal->projectData['Total'] = $Total;

        return $this->send2Next();

    }



    private function getTotal() {

        $Total = 0;

        $require = $this->Proposal->getRequire();        

        array_walk($require['cartData'], function ($value) use (&$Total){

            $Total += (int)($value['countPrice']) * (int)$value['num'];

        });

        return $Total;

    }

}

