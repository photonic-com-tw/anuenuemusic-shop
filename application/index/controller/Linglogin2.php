<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Env;
use Firebase\JWT\JWT;

class Linglogin extends PublicController
{

    public function index() {


		$this->assign('client_id', '1578554831');
		$this->assign('client_secret', '64e8dd0b44749dbe070f2d91800db4f4');

		return $this->fetch('index');
    }
	public function callBack() {

		$this->assign('code', $_GET['code']);
		$this->assign('client_id', '1578554831');
		$this->assign('client_secret', '64e8dd0b44749dbe070f2d91800db4f4');
		return $this->fetch('callBack');
    }

	public function Jwt() {


		return $this->fetch('Jwt');

    }
}
