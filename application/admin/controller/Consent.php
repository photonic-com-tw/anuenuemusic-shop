<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;

class Consent extends MainController
{

    private $DBTextConnecter;
    private $fixedResourcesRowId = 1;

    public function __construct()
    {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('consent');
    }

	public function index()
	{
        $consent = Db::table('consent')->find($this->fixedResourcesRowId);
        $this->assign('consent', $consent);
		return $this->fetch('consent');
    }
    
    /*AJAX*/
	public function cellCtrl()
	{
		try{
			$updateData = Request::instance()->post();
			$this->DBTextConnecter->setDataArray($updateData);
			$this->DBTextConnecter->upTextRow();
			$outputData = [
				'status' => true,
				'message' => 'success'
			];
		}catch (\Exception $e){
			$outputData = [
				'status' => false,
				'message' => $e->getMessage()
			];
			return json($outputData, 200);
		}
		return json($outputData, 200);
	}
}