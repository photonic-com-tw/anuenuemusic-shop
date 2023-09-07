<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use controllerInterface\resources\Fixed;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class About extends MainController implements Fixed {

    private $DBTextConnecter;
    private $fixedResourcesRowId = 1;

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('about_story');
    }

	public function index() {
        $about_story = Db::table('about_story')->find($this->fixedResourcesRowId);
        $this->assign('about_story', $about_story);
		return $this->fetch('about');
	}

    public function update() {
        $newData = Request::instance()->post();
        $newData['id'] = $this->fixedResourcesRowId;
        $picNameList = ['image_left_top', 'image_right_top', 'image_right_bottom'];
        $picSizeList = [
            ['width' => 200, 'height' => 200], 
            ['width' => 382, 'height' => 255], 
            ['width' => 380, 'height' => 254]
        ];

        try{
            $DBFileConnecter = new DBFileConnecter();
            for($i = 1; $i <= 3; $i++){
                $image = Request::instance()->file('image' . $i);
                if($image){

                    $imageName = 'about_' . $picNameList[$i-1];
                    $newDataColumnName = $picNameList[$i-1];

                    $newData[$newDataColumnName] = 
                        $DBFileConnecter->fixedFileUp(
                            $image, 
                            $imageName, 
                            $picSizeList[$i-1]['width'], 
                            $picSizeList[$i-1]['height']
                        );
                }
            }
            $this->DBTextConnecter->setDataArray($newData);
            $this->DBTextConnecter->upTextRow();
        } catch (\Exception $e) {
            $this->dumpException($e);
        }
        $this->success('更新成功');
    }
}