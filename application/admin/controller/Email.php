<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\Fixed;
use DBtool\DBTextConnecter;

class Email extends MainController implements Fixed
{

    private $DBTextConnecter;
    private $resTableName;

    public function __construct() 
    {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('email');
        $this->resTableName = 'email';
    }

	public function index()
	{
        $email = Db::table($this->resTableName)->find(1);
        $this->assign('email', $email);
		return $this->fetch('index');
	}

    public function update()
    {
        $data = Request::instance()->post();
        try{
            $this->DBTextConnecter->setDataArray($data);
            $this->DBTextConnecter->upTextRow();
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('更新成功');

    }
}