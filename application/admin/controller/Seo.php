<?php



namespace app\admin\controller;



use app\admin\controller\MainController;

use think\Request;

use think\Db;



use DBtool\DBTextConnecter;

use DBtool\DBFileConnecter;



class Seo extends MainController {

    private $DBTextConnecter;
    const FIXED_RESOURCES_ID = 1;

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('seo');

    }


    public function edit() {
		$seo = Db::table('seo')->find(self::FIXED_RESOURCES_ID);

		$this->assign('seo', $seo);
		return $this->fetch('index');
    }


    public function edit_social() {
		$seo = Db::table('seo')->find(self::FIXED_RESOURCES_ID);

		$this->assign('seo', $seo);
		return $this->fetch('index_social');
    }


    public function edit_advance() {
		$seo = Db::table('seo')->find(self::FIXED_RESOURCES_ID);
		try{
			$robots = fopen(ROOT_PATH . "/robots.txt", "r");
			$seo['robot'] =  fread($robots,filesize(ROOT_PATH . "/robots.txt"));
			fclose($robots);
		}catch (\Exception $e){
			$seo['robot'] = "";
		}
		$this->assign('seo', $seo);
		return $this->fetch('index_advance');
    }

    public function update() {

		$newData = Request::instance()->post();

		$this->DBTextConnecter->setDataArray($newData);
        $this->DBTextConnecter->upTextRow();
        $this->success('更新成功');

    }

    public function update_social() {

		$newData = Request::instance()->post();

		$DBFileConnecter = new DBFileConnecter();
		$image = Request::instance()->file('fb_img');
		if($image){
		    $newData['fb_img'] = $DBFileConnecter->fixedFileUp(
		        $image, 
		        'seo_fb_img', 
		        1200, 
		        630
		    );
		}

		$this->DBTextConnecter->setDataArray($newData);
        $this->DBTextConnecter->upTextRow();
        $this->success('更新成功');

    }


    public function update_advance() {

		$newData = Request::instance()->post();
		$robots = fopen(ROOT_PATH . "/robots.txt", "w");
		fwrite($robots, $newData['robot']);
		fclose($robots);
		unset($newData['robot']);

		$DBFileConnecter = new DBFileConnecter();
		$image = Request::instance()->file('fb_img');
		if($image){
		    $newData['fb_img'] = $DBFileConnecter->fixedFileUp(
		        $image, 
		        'seo_fb_img', 
		        1200, 
		        630
		    );
		}

		$DBFileConnecter->setUploadPath(ROOT_PATH . '/');
		$map = Request::instance()->file('map');

		if($map){
		    $newData['map'] = $DBFileConnecter->fixedFileUp(
		        $map, 
		        'sitemap'
		    );

		    $newData['map'] = explode('/', $newData['map'])[2];
		    $newData['map'] = explode('?', $newData['map'])[0];
		}

		$this->DBTextConnecter->setDataArray($newData);
        $this->DBTextConnecter->upTextRow();
        $this->success('更新成功');

    }


}