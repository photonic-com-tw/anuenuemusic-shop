<?php

namespace pattern\recursiveCorrdination\cartRC;

/*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 16 2017
 * @Description: simpleStaticFactory that create class by Proposal Class
 * @depend: Proposal Class
 *
*/

class MemberFactory
{
    public static function createNextMember($Proposal) {
        $nextMember = $Proposal->getNextMember();
        $memberClassName = 'pattern\\recursiveCorrdination\\cartRC\\' . $nextMember;
        return $memberClassName::createFactory($Proposal);
    }
}