<?php

namespace app\admin\controller\Template;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\SoleType;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class TemplateArticle extends MainController implements SoleType
{
    const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;

    private $controllerName;
    private $DBTextConnecter;
    private $resTableName;
    private $pic_width;
    private $pic_height;

    //this resources's frontend use Single Page Web, some CURD method is useless
    public function edit(){}
    public function create(){}

    public function __construct(
        $controller_name,
        $table_name,
        $pic_width = 700, 
        $pic_height = 475
    ){
        parent::__construct();
        $this->controllerName   = $controller_name;
        $this->DBTextConnecter  = DBTextConnecter::withTableName($table_name);
        $this->resTableName     = $table_name;
        $this->pic_width        = $pic_width;
        $this->pic_height       = $pic_height;
    }

    public function index()
	{
        $searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
        $activity = Db::table($this->resTableName)
                    ->where("title LIKE '%$searchKey%'")
                    ->order('orders asc, time desc')
                    ->paginate(
                        self::PER_PAGE_ROWS,
                        self::SIMPLE_MODE_PAGINATE,
                        [
                            'query' => [
                                'searchKey' => $searchKey
                            ]
                        ]
                    );
        $this->assign($this->resTableName, $activity);

        /*用於取得前台某選單的文字*/
        $this->assign('table_name', $this->resTableName);

		return $this->fetch('index');
    }

    public function doCreate($finish = true){
        $newData = Request::instance()->post();
        unset($newData['id']);
        
        try{
            /*創建資料*/
            $this->DBTextConnecter->setDataArray($newData);
            $mainId = $this->DBTextConnecter->createTextRow();

            /*處理圖片*/
            $image = Request::instance()->file('image');
            if($image){
                $DBFileConnecter = new DBFileConnecter();
                $newData['pic'] =
                    $DBFileConnecter->fixedFileUp($image, $this->resTableName.'_'.$mainId, $this->pic_width, $this->pic_height);
                $newData['id'] = $mainId;
                
                /*更新圖片欄位*/
                $this->DBTextConnecter->setDataArray($newData);
                $this->DBTextConnecter->upTextRow();
            }

            if( isset($newData['orders']) ){
                // 自動更新排序
                $table = $this->resTableName;
                $column = 'orders';
                $order_num = $newData['orders'];
                $primary_key = 'id';
                $primary_value = $mainId;
                parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);
                unset($newData['orders']);
            }

            if($finish){
                ob_clean();
                echo('<h1>上傳成功</h1>');die();
            }else{
                return $mainId;
            }

		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
    }

    public function update($finish = true){
        $newData = Request::instance()->post();
        
        try{
            /*處理圖片*/
            $image = Request::instance()->file('image');
            if($image){
                $DBFileConnecter = new DBFileConnecter();
                $newData['pic'] = $DBFileConnecter->fixedFileUp($image, $this->resTableName.'_'.$newData['id'], $this->pic_width, $this->pic_height);
            }

            /*更新資料*/
            $this->DBTextConnecter->setDataArray($newData);
            $this->DBTextConnecter->upTextRow();
            

            if( isset($newData['orders']) ){
                // 自動更新排序
                $table = $this->resTableName;
                $column = 'orders';
                $order_num = $newData['orders'];
                $primary_key = 'id';
                $primary_value = $newData['id'];
                parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);
                unset($newData['orders']);
            }
            
            if($finish){
                ob_clean();
                echo('<h1>上傳成功</h1>');die();
            }else{
                return $newData['id'];
            }

		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
    }

    public function delete(){
        $id = Request::instance()->get('id');
        try{
            Db::table($this->resTableName)->delete($id);
        } catch (\Exception $e){
            $this->dumpException($e);
        }

        $this->success('刪除成功', url($this->controllerName.'/index'));
    }

    public function multiDelete()
    {
        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
                Db::table($this->resTableName)->delete($idList);
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }

        $this->success('刪除成功', url($this->controllerName.'/index'));
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

