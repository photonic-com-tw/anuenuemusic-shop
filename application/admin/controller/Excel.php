<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use app\index\controller\PublicController as PublicController;

class Excel extends MainController
{
	private $DBTextConnecter;
	private $DBFileConnecter;
	const PRODUCTINFO_NUMBAER_ROW_ID = 3;
	const PER_PAGE_ROWS = 50;
	const SIMPLE_MODE_PAGINATE = false;

	public function index() {

		$page = Request::instance()->get('page') ?? 1;
		$searchKey = Request::instance()->get('searchKey') ?? '';
		$searchKey = trim($searchKey);
		$statusKey = Request::instance()->get('statusKey') ?? '';
		$brandKey = Request::instance()->get('brandKey') ?? '';

		$this->assign('searchKey', $searchKey);
		$this->assign('statusKey', $statusKey);
		$this->assign('brandKey', $brandKey);

		$this->assign('brand', $brand=Db::table('excel')->group('product_brand')->field('product_brand')->select());
		//dump($brand);

		if (mb_strlen($searchKey, "UTF-8") == strlen($searchKey)) {
		    $where = "product_brand like '%" . $brandKey . "%' and 
		    		  (
		    		  	product_name like '%" . $searchKey . "%' or 
		    			product_code like '%" . $searchKey . "%' or 
		    			account_number like '%" . $searchKey . "%' or 
		    			createtime like '%" . $searchKey . "%'
		    		   ) and 
		    		   status like '%".$statusKey."%'";
		} else {
		    $where = "product_brand like '%" . $brandKey . "%' and 
		    		  (
		    			product_name like '%" . $searchKey . "%'  or 
		    			product_code like '%" . $searchKey . "%' or 
		    			account_number like '%" . $searchKey . "%'
		    		  ) and 
		    		  status like '%".$statusKey."%'";
		}

		$num = Db::table('excel')
			->where($where)
			->order('status desc,id asc')->paginate(
			self::PER_PAGE_ROWS,
			self::SIMPLE_MODE_PAGINATE, 
			[
				'query' => [
					'searchKey' => $searchKey
				]
			]
			)->each(function($item, $key){
				$user_number = $item["account_number"]; //获取数据集中的id
				$num = Db::connect(config('main_db'))->table('account')->where("id='".$user_number."'")->field('number')->find()["number"]; //根据ID查询相关其他信息
				$item['number'] = $num; //给数据集追加字段num并赋值
				return $item;
			});
		//dump($num);

		$total = Db::table('excel')->where($where)->count();
		$regisetered = Db::table('excel')->where($where)->where('status','=',1)->count();
		$un_regisetered = Db::table('excel')->where($where)->where('status','=',0)->count();

		if($total != 0){
			$regiseter_rate = $regisetered/$total*100;
		}else{
			$regiseter_rate = 0;
		}

		$this->assign('total', $total);
		$this->assign('regisetered', $regisetered);
		$this->assign('un_regisetered', $un_regisetered);

		$this->assign('regiseter_rate', $regiseter_rate);
		$this->assign('page', $page);

		$this->assign('num', $num);
		return $this->fetch('index');

	}

    public function reply() {

		$searchKey = Request::instance()->get('searchKey') ?? '';
		$do = Request::instance()->get('do') ?? '';

		if($do == 1){
			$where = " (pro_id  like  '%" . $searchKey . "%' or product_code like '%".$searchKey."%') and status >= '".$do."'";
		}else{
			$where = " (pro_id  like  '%" . $searchKey . "%' or product_code like '%".$searchKey."%') and status like '%".$do."%'";
		}

		$this->assign('searchKey', $searchKey);

		$qa = Db::table('excel_reply')
			->where($where)
			->order('pro_id desc')->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE, 
				[
					'query' => [
						'searchKey' => $searchKey
					]
				]
			)->each(function($item, $key){
				$user_number = $item["account_number"]; //获取数据集中的id
				$num = Db::connect(config('main_db'))->table('account')->where("id='".$user_number."'")->field('name')->find()["name"]; //根据ID查询相关其他信息
				$item['account_name'] = $num; //给数据集追加字段num并赋值
				return $item;
			});

		//dump($qa);
        $this->assign('qa', $qa);

		return $this->fetch('reply');
    }

	public function Import() {

		//接收檔案
		$files = Request::instance()->file("file");
		$type = explode(".",$_FILES['file']['name']);

		if($type[1]== 'xlsx'){

			//儲存檔案
			$info = $files->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel');

			//檔案路徑
			$filename = ROOT_PATH.'public'.DS.'uploads'. DS . 'excel'. DS .$info->getSaveName();

			self::read_excel($filename);

		}else{
			echo "<script>alert('警告：格式錯誤'); location.href = '".url('Excel/index')."';</script>";
		}
	}


	function read_excel($filename){

		$objPHPExcel = new \PHPExcel();
		//require_once 'Classes/PHPExcel/Reader/Excel5.php';
		$PHPReader = new \PHPExcel_Reader_Excel2007(); 
		$PHPExcel = $PHPReader->load($filename); 
		$sheet = $PHPExcel->getSheet(0); 
		$allRow = $sheet->getHighestRow(); //取得最大的行號 
		$allColumn = $sheet->getHighestColumn(); //取得最大的列號

		if($allRow <= 30001){
			for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) { 

				//獲取A列的值 
				$product_code = trim($PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getValue()); 
				$product_name = trim($PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue()); 
				$product_brand = trim($PHPExcel->getActiveSheet()->getCell("C" . $currentRow)->getValue()); 

				if($product_code != ""){
					$data['product_code'] = $product_code;
					$data['product_name'] = $product_name;
					$data['product_brand'] = $product_brand;
					$data['createtime'] = date('Y-m-d');

					Db::table('excel')->insert($data);
					echo "<script>alert('匯入成功'); location.href = '".url('Excel/index')."';</script>";
				}
			}

		}else{
			echo "<script>alert('匯入資料超過3萬筆'); location.href = '".url('Excel/index')."';</script>";
		}
	}


	public function update(){

		$id = Request::instance()->post('id');
		$val = Request::instance()->post('val');
		$user_id = Request::instance()->post('user_id');
		$user_name = Request::instance()->post('user_name');
		$pro_id = Request::instance()->post('pro_id');
		$name = Request::instance()->post('name');
		$product_code = Request::instance()->post('product_code');
		$pic = Request::instance()->post('pic');
		$regtime = Request::instance()->post('regtime');
		$tax_ID_number = Request::instance()->post('tax_ID_number');
		$buytime = Request::instance()->post('buytime');
		
		$email = Db::connect(config('main_db'))->table('account')->where("id = '".$user_id."'")->find();
		$compare_code = Db::table('excel')->where("product_name = '".$_POST['name']."' and product_code = 'XXXXXXXXXXXXXX'")->select();
		if(empty($compare_code)){ // 當沒有特殊機身碼，要比對是否註冊過
			if(Db::table('excel')->where(" product_code = '".$product_code."' and status = 1")->select() && $val == 1){
				return 4;
				exit;
			}
		}

		$PublicController = new PublicController(Request::instance());
		$globalMailData = $PublicController->getMailData();

		if(Db::table('excel_reply')->where(" id = '".$id."'")->update(['status'=>$val])){
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Host = $globalMailData['mailHost'];
			$mail->Port = 465;
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			$mail->CharSet = "UTF-8";
			$mail->Encoding = "base64";
			$mail->Username = $globalMailData['mailUsername'];
			$mail->Password = $globalMailData['mailPassword'];
			$mail->Subject = $globalMailData['mailSubject'];
			$mail->From = $globalMailData['mailFrom'];
			$mail->FromName = $globalMailData['mailFromName'];

			$mail->AddAddress($email['email']);

			if($val == 1){ // 修改成成功
				if(!empty($compare_code)){ // 當有特殊機身碼，自動建立消費者輸入的資訊
					$data['product_code'] = $product_code;
					$data['product_name'] = $compare_code[0]['product_name'];
					$data['product_brand'] = $compare_code[0]['product_brand'];
					$data['createtime'] = date('Y-m-d');
					Db::table('excel')->insert($data);
				}

				Db::table('excel')->where(" product_code = '".$product_code."'")
					->update([
					'status'=>$val,
					'pic'=>$pic,
					'account_number'=>$user_id,
					'account_name'=>$user_name,
					'regtime'=>$regtime,
					'tax_ID_number'=>$tax_ID_number,
					'buytime'=>$buytime
					]);
				$mail->Body = "
				<html>
				<head></head>
				<body>
					<div>
						親愛的會員您好：<br>
						商品：<sapn style='color:red;'>".$name."</sapn><br>
						機身碼：<sapn style='color:red;'>".$product_code."</sapn><br>
						註冊已審核通過<br>
						詳細內容可至會員專區>註冊商品查看<br>
						<br><br>
						<br><br>
					</div>
					<div style='color:red;'>
						≡ 此信件為系統自動發送，請勿直接回覆 ≡
					</div>
				</body>
				</html>
				";

			}else if($val == 2){ // 修改成失敗
				$mail->Body = "
				<html>
				<head></head>
				<body>
				<div>
					親愛的會員您好：<br>
					商品：<sapn style='color:red;'>".$name."</sapn><br>
					機身碼：<sapn style='color:red;'>".$product_code."</sapn><br>
					註冊審核未通過<br>
					麻煩您確認填寫資料無誤後<br>
					再次提交註冊資訊<br>
					如有任何疑問<br>
					歡迎來電洽詢<br>
					<br><br>
					<br><br>
				</div>
				<div style='color:red;'>
					≡ 此信件為系統自動發送，請勿直接回覆 ≡
				</div>
				</body>
				</html>
				";

			}

			$mail->IsHTML(true);
			if ($mail->Send()) {
				return 1;
			}else{
				return 2;
				//$this->error("Mailer Error(回傳機身碼): ".$mail->ErrorInfo);// 输出错误信息
			}
		}else{
			return 0;
		}
	}

    public function delete() {

        $id = Request::instance()->get('id');
        try{
            Db::table('excel_reply')->delete($id);
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }

    public function multiDelete() {

        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
                Db::table('excel_reply')->delete($idList);
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }

    public function multiDelete_or() {

        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
                Db::table('excel')->delete($idList);
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }
}

