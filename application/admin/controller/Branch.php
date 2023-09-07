<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Branch extends MainController
{
	private $DBTextConnecter;
	private $DBFileConnecter;

	public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('typeinfo');
        $this->DBFileConnecter = DBFileConnecter::withTableName('typeinfo');
	}
	


	public function index($id) {
		
		
		$typeinfo = Db::table('typeinfo')
				->where("typeinfo.branch_id = '".$id."'")
				->order('typeinfo.order_id')
				->select();
				
		//dump($typeinfo);exit;

		foreach ($typeinfo as &$value) {
			$value['product_num'] = 
				Db::table('productinfo')
					->where('(branch_id = ' . $value['id'] . " or final_array like '%\"branch_id\": \"".$value['id']."\"%' ) AND online = 1 ")
					->count();
		}
		//dump(Db::table('productinfo')->getLastSql());
		$this->assign('typeinfo', $typeinfo);
		
		/***階層位置***/
		$parent_title = Db::table('typeinfo')->field('id,branch_id,parent_id, title, online')->find($id);	

		$title_branch_id = $parent_title['branch_id'];
		//$title_array[0] = $parent_title['title'];
		$title_array[0] = "<a href=\"".url('branch/index', ['id' => $parent_title['id']])."\">".$parent_title['title']."</a>";
		
		$i = 1;

		while($title_branch_id != 0){
			$new_title = Db::table('typeinfo')->field('id,branch_id,parent_id, title, online')->find($title_branch_id);	
			$title_branch_id = $new_title['branch_id'];
			//$title_array[$i] = $new_title['title'];
			$title_array[$i] = "<a href=\"".url('branch/index', ['id' => $new_title['id']])."\">".$new_title['title']."</a>";
			$i++;
		}
		
		if(empty($new_title['parent_id']))
			$new_title['parent_id'] = $parent_title['parent_id'];

		$parent_array = Db::table('product')->field('id, title, online')->find($new_title['parent_id']);	
		//$title_array[$i] = $parent_array['title'];
		$title_array[$i] = "<a href=\"".url('typeinfo/index', ['parent_id' => $parent_array['id']])."\">".$parent_array['title']."</a>";
		
		$final_title ='';
		for($i=count($title_array)-1;$i >=0;$i--){
			
			$final_title .= $title_array[$i];
			if($i >=1)
				$final_title = $final_title.' > ';
		}
		//dump($parent_title);
		$this->assign('parent_title', $parent_title);
		$this->assign('final_title', $final_title);
		
		/***階層位置***/



		
		
		return $this->fetch('branch');
	}
	public function create() {
		$width = 80;
		$height = 365;
		$data = Request::instance()->post();
		unset($data['id']);
		$image = Request::instance()->file('image');
		try{
			if($image){
				$data['pic'] = $this->DBFileConnecter->fixedFileUp($image, 'typeinfo'.md5(rand().time()), $width, $height);
			}
			$this->DBTextConnecter->setDataArray($data);
			$id = $this->DBTextConnecter->createTextRow();
			ob_clean();
			echo('<h1>上傳成功</h1>');die();
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
	}
	public function update() {
        $width = 80; $height = 365;
		$newData = Request::instance()->post();

		$branch = $newData['branch']??false;
		unset($newData['branch']);
        try{
            $image = Request::instance()->file('image');
            if($image){
                $newData['pic'] = 
                    $this->DBFileConnecter->fixedFileUp($image, 'typeinfo_' . $newData['id'], $width, $height);
            }
            $this->DBTextConnecter->setDataArray($newData);
            $this->DBTextConnecter->upTextRow();
			
			
			if($branch){
				ob_clean();
				echo('<script>alert("更新成功")</script>');
				echo('<script>history.go(-1);</script>');
				die();
			}else{
				ob_clean();
				echo('<h1>上傳成功</h1>');
				die();
			}
			
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
	}
	public function delete() {
        $id = Request::instance()->get('id');
		$deleteData = Db::table('productinfo')->field('id')->where('branch_id', $id)->select();
		if (sizeof($deleteData)) {
			$this->error('內部還有商品，請先清光');
		}
		Db::table('typeinfo')->delete($id);
        $this->success('刪除成功');
    }
	public function cellCtrl() {
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
	public function edit() {
		
		
		$type = $_POST['type'];
		$id = $_POST['id'];
		
		
		switch ($type) {
		  case "add":
			$data['name'] = $_POST['name'];
			$data['number'] = $_POST['number'];
			if(Db::table($this->tableName)->insert($data)){
				echo 'success';
			}else{
				echo '新增失敗';
			}
			break;
		  case "delete":
			if(Db::table($this->tableName)->where('id',$id)->delete()){
				echo 'success';
			}else{
				echo '失敗';
			}
			break;		
		  case "update":
			$data['number'] = $_POST['number'];
			if(Db::table($this->tableName)->where('id',$id)->update($data)){
				echo 'success';
			}else{
				echo '新增失敗';
			}
			break;
			

		}

		
	}
	




}




























































