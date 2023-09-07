<?php
	namespace app\ajax\controller;

	use think\Controller;
	use think\Request;
	use think\Db;

	use pattern\PointRecords;

	class Points extends Controller
	{	

		public function check_expire()
		{
			$PointRecords = new PointRecords(0);
			$records = $PointRecords->get_member_has_points(); /*取得所有要檢查的會員id*/
			// dump($records);

			foreach ($records as $key => $value) {
				$PointRecords->change_user_id($value['user_id']);	/*更換檢查對象*/
				$result = $PointRecords->set_point_expire();		/*扣除過期點數*/

				if($result['expired_poiints']!=0){
					dump("會員：\t".$result['user_id']."\t點數調整：\t".$result['expired_poiints']);
				}
			}

			dump('處理完畢');
		}
		
	}

?>			






























