<?php

namespace pattern\recursiveCorrdination\discountRC;
use pattern\recursiveCorrdination\discountRC\MemberFactory;

abstract class TeamMember {

	protected $Proposal;

	protected function __construct($Proposal) {
		$this->Proposal = $Proposal;
	}

	abstract public static function createFactory($Proposal);

	protected function send2Next() {
		if(!$this->Proposal->getTeamMemberCount()){
			return $this->Proposal;
        }
        return MemberFactory::createNextMember($this->Proposal);
	}

	protected function rejectProposal() {
		return $this->Proposal;
	}

	abstract protected function participate();

}