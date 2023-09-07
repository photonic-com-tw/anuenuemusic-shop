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

class Increase extends TeamMember
{
    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
        $require = $instance->Proposal->getRequire();
        if($require['cmd'] != 'increase'){
            return $instance->send2Next();
        }
		return $instance->participate();
	}

    public function participate() {
        $require = $this->Proposal->getRequire();
        if($require['num'] <= 0){
            return $this->send2Next();
        }
        $count = $this->GetCount($require['id']);
        $this->Proposal->projectData['data'][$require['id']] = $require['num'] + $count;
        return $this->send2Next();
    }

    private function GetCount($item){
        $projectData = $this->Proposal->projectData['data'];
        $filtered = array_filter($projectData, function ($key) use ($item) {
            return ($key == $item);
        }, ARRAY_FILTER_USE_KEY );
        return $filtered[$item] ?? 0;
    }
}
