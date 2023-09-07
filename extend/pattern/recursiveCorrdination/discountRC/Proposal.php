<?php

namespace pattern\recursiveCorrdination\discountRC;

class Proposal {

	private $teamMembers;
	private $require;
	public $projectData;

	//overload construct by Static Factory Method Pattern
	private function __construct() {
	}
	public static function withTeamMembers(array $TeamMembers) {
		$instance = new self();
        $instance->teamMembers = $TeamMembers;
        return $instance;
	}
	public static function withTeamMembersAndRequire(array $TeamMembers, $require) {
		$instance = new self();
		$instance->teamMembers = $TeamMembers;
        $instance->require = $require;
        return $instance;
	}

	public function getNextMember() {
		return array_shift($this->teamMembers);
	}

	public function getTeamMembers() {
		return $this->teamMembers;
	}

	public function getTeamMemberCount() {
		return sizeof($this->teamMembers);
	}

	public function getRequire() {
		return $this->require;
	}

}