<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class LogAction extends CommonAction{
	public function index()
	{
		if(trim($_REQUEST['mobile'])!='')
		{
			$map['mobile'] = array('like','%'.trim($_REQUEST['mobile']).'%');			
		}
		
		$log_begin_time  = trim($_REQUEST['log_begin_time'])==''?0:to_timespan($_REQUEST['log_begin_time']);
		$log_end_time  = trim($_REQUEST['log_end_time'])==''?0:to_timespan($_REQUEST['log_end_time']);
		if($log_end_time==0)
		{
			$map['log_time'] = array('gt',$log_begin_time);	
		}
		else
		$map['log_time'] = array('between',array($log_begin_time,$log_end_time));	
		
		$this->assign("default_map",$map);
		parent::index();
	}
	//用户日活统计
	public function user_index()
	{    
		if(trim($_REQUEST['mobile'])!='')
		{
			$map['mobile'] = trim($_REQUEST['mobile']);		
		}
			if(trim($_REQUEST['login_type'])!='')
		{
			$map['login_type'] = trim($_REQUEST['login_type']);		
		}
		 $log_begin_time  = trim($_REQUEST['log_begin_time'])==''?0:to_timespan($_REQUEST['log_begin_time']);	
	     $log_end_time  = trim($_REQUEST['log_end_time'])==''?0:to_timespan($_REQUEST['log_end_time']);
		    $login_type = array(
            array(
                'id' => 1,
                'name' => 'pc'
            ),
            array(
                'id' => 3,
                'name' => 'app'
            )
          
        );
        	$list_sql = "select  user.id as id,mobile ,user_name,log.login_time as login_time,log.login_type as login_type,log.login_ip as login_ip"
					." from ".DB_PREFIX."user as user,".DB_PREFIX."user_login_log as log"
					." where  user.id=log.user_id";
		    $count_sql = "select count(1) "
					." from ".DB_PREFIX."user as user,".DB_PREFIX."user_login_log as log"
					." where  user.id=log.user_id";
            if($map['mobile'] != ''){
			$list_sql .= " and mobile like '%".$map['mobile']."%' ";	
			$count_sql .= " and mobile like '%".$map['mobile']."%' ";	
		   }
            if($map['login_type'] != ''){
			$list_sql .= " and login_type like '%".$map['login_type']."%' ";	
			$count_sql .= " and login_type like '%".$map['login_type']."%' ";	
		   }
         if(trim($_REQUEST['log_begin_time'])!=''&&trim($_REQUEST['log_end_time'])!=''){
            $list_sql .= " and log.login_time BETWEEN  $log_begin_time  and  $log_end_time ";	
			$count_sql .= " and log.login_time BETWEEN  $log_begin_time  and  $log_end_time ";
			}	
           if(trim($_REQUEST['log_begin_time'])!=''&&trim($_REQUEST['log_end_time'])=='')
		{ 	
			$list_sql .= " and log.login_time >=$log_begin_time   ";	
			$count_sql .= " and log.login_time >= $log_begin_time  ";		
		}

		  if(trim($_REQUEST['log_begin_time'])==''&&trim($_REQUEST['log_end_time'])!='')
		{ 	
			$list_sql .= " and log.login_time <=$log_end_time   ";	
			$count_sql .= " and log.login_time <=$log_end_time  ";		
		}
             $list_sql .= ' order by login_time desc ';
             $list = $this->_list_multi_table($count_sql, $list_sql,$map);
            foreach ($list as $k => $v) {
            if ($v['login_type'] == '1') {
                $list[$k]['login_type'] ='PC'; 
            }elseif ($v['login_type'] == '3'||$v['login_type'] == '4') {
            	    $list[$k]['login_type'] ='APP'; 
            }
        }
         if(trim($_REQUEST['id'])==1){
   
          $list = $GLOBALS['db']->getAll($list_sql);
           foreach ($list as $k => $v) {
            if ($v['login_type'] == '1') {
                $list[$k]['login_type'] ='PC'; 
            }elseif ($v['login_type'] == '3'||$v['login_type'] == '4') {
            	    $list[$k]['login_type'] ='APP'; 
            }
        }

       /** Error reporting */
      error_reporting(E_ALL);
      /** PHPExcel */
      require APP_ROOT_PATH.'admin/Classes/PHPExcel.php';
      /** PHPExcel_Writer_Excel2003用于创建xls文件 */
      include_once 'PHPExcel/Writer/Excel5.php';
      // Create new PHPExcel object
      
	  $excel = new PHPExcel();
	  $excel->getActiveSheet()->setTitle('user_login_log');
      //Excel表格式,
      $excel->getActiveSheet()->setCellValue('A1', '编号'); 
      $excel->getActiveSheet()->setCellValue('B1', '姓名'); 
      $excel->getActiveSheet()->setCellValue('C1', '手机号'); 
      $excel->getActiveSheet()->setCellValue('D1', '登录时间'); 
      $excel->getActiveSheet()->setCellValue('E1', '登陆方式'); 
      $excel->getActiveSheet()->setCellValue('F1', '操作地址'); 
      $i = 2; 
      
      foreach($list as $item){ 
      $item['login_time'] = date("Y-m-d H:i:s",$item['login_time']);
      $excel->getActiveSheet()->setCellValue('A' . $i, $item['id']); 
      $excel->getActiveSheet()->setCellValue('B' . $i, $item['user_name']); 
      $excel->getActiveSheet()->setCellValue('C' . $i, $item['mobile']); 
      $excel->getActiveSheet()->setCellValue('D' . $i, $item['login_time']); 
      $excel->getActiveSheet()->setCellValue('E' . $i, $item['login_type']); 
      $excel->getActiveSheet()->setCellValue('F' . $i, $item['login_ip']); 
      $i ++; 
      }
 
       $write = new PHPExcel_Writer_Excel5($excel);
       ob_end_clean();//清除缓冲区,避免乱码
       header("content-type:text/html;charset=utf-8");
       header("Pragma: public");

       header("Expires: 0");

       header("Cache-Control:must-revalidate, post-check=0, pre-check=0");

       header("Content-Type:application/force-download");

       header("Content-Type:application/vnd.ms-execl;charset=utf-8");

       header("Content-Type:application/octet-stream");

       header("Content-Type:application/download");;

       header('Content-Disposition:attachment;charset=utf-8;filename="user_login_log.csv"');

       header("Content-Transfer-Encoding:binary");
       $write->save('php://output');

       exit;
         }
		   $this->assign("login_type",$login_type);
		   $this->assign("list",$list);
           $this->display();
	}
  
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );			
				
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
					
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
		
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
}
?>