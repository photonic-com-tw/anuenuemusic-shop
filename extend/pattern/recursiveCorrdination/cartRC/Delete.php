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

class Delete extends TeamMember
{
    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
        $require = $instance->Proposal->getRequire();
        if($require['cmd'] != 'delete') {
            return $instance->send2Next();
        }
		return $instance->participate();
	}

    public function participate() {
        $require = $this->Proposal->getRequire();
        unset($this->Proposal->projectData['data'][$require['id']]);
        return $this->send2Next();
    }
}
