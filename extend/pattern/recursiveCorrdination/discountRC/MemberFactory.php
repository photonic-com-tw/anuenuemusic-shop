<?php



namespace pattern\recursiveCorrdination\discountRC;



class MemberFactory {

    public static function createNextMember($Proposal) {

        $nextMember = $Proposal->getNextMember();

        $memberClassName = 'pattern\\recursiveCorrdination\\discountRC\\' . $nextMember;

        return $memberClassName::createFactory($Proposal);

    }

}