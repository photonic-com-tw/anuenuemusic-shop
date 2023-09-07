<?php

namespace pattern\recursiveCorrdination\cartRC;


 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: 
 * @Description: 
 * @depend: 
 *
*/

class Assign extends TeamMember
{
    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
        $require = $instance->Proposal->getRequire();
        if($require['cmd'] != 'assign'){
            return $instance->send2Next();
        }
		return $instance->participate();
	}

    public function participate() {
        $require = $this->Proposal->getRequire();
        if($require['num'] < 0){
            return $this->send2Next();
        }
        if($require['num'] == 0){
            unset($this->Proposal->projectData['data'][$require['id']]);
            return $this->send2Next();
        }
        $this->Proposal->projectData['data'][$require['id']] = $require['num'];
        return $this->send2Next();
    }
}
