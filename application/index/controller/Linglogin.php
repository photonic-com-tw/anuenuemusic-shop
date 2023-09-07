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

	public function callBack() {

		$this->assign('code', $_GET['code']);
		return $this->fetch('callBack');
    }
		public function open() {

			$this->assign('code', $_GET['code']);
			return $this->fetch('open');
			}
	public function Jwt() {


		return $this->fetch('Jwt');

    }
}
