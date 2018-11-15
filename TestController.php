<?php
namespace api\controllers;

use Yii;
use yii\web\Controller;


class TestController extends Controller{
	public $enableCsrfValidation = false;


	//接收excel文件并入库
	public function actionImport(){
		//将接收到的xls文件保存到服务器
		$dir = '/phpstudy/www/lnmp/yiinew/api/upload/excel.xls';
		$reg = move_uploaded_file($_FILES['excel']['tmp_name'],$dir);
		//这个是接收的文件名称
		$title = Yii::$app->request->post("title");

		//引入phpexcel类文件
		require (__DIR__.'/../../common/libs/PHPExcel.php');

		header("content-type:text/html;charset=utf8");
		$objPHPExcel = \PHPExcel_IOFactory::load($dir);

		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

		unset($sheetData[1]);

		$type = array_flip(Yii::$app->params['type']);

		$score = Yii::$app->params['score'];
		
		// 	echo "<pre>";
		// var_dump($type);exit;
		foreach($sheetData as $k =>$v){
		
			$arr = array(
				'tg'=>$v['C'],
				'yf'=>'10',
				'dy'=>$title,
				'type'=>$type[$v['B']],
				'time'=> date("Y-m-d H:i:s"),
				'fack'=> $score[$type[$v['B']]],
				'people'=>'ybb俗称B哥'
			);

			//写入数据库
			$res = Yii::$app->db->createCommand()->insert('tm',$arr)->execute();
			
			$tid = Yii::$app->db->getLastInsertID();

			//题号
			$an_num = array('D'=>'A','E'=>'B','F'=>'C','G'=>'D','H'=>'E','I'=>'F');

			//正确答案
			$yesinfo = str_split($v['J'],1);
			// echo "<pre>";
			// var_dump($is_true);exit;
			//试题答案
			for($i='D';$i<='I';$i++){


				if(empty($v[$i]) ) continue;
				
				$is_true = in_array($an_num[$i],$yesinfo) ? 1:0;
				

				$arr2 = array(
					'tid'=>$tid,
					'choice'=>$v[$i],
					'is_true'=>$is_true
				);
				$ress = Yii::$app->db->createCommand()->insert('da',$arr2)->execute();


			}
			
		}

		if($res && $ress){
			echo 1;
		}
		
	}


	public function actionShow(){
		
		header("content-type:text/html;charset=utf8");
		$sql = "select * from tm";

		$data = Yii::$app->db->createCommand($sql)->queryAll();
		print_r($data);

		// echo json_encode($data);

	}
}