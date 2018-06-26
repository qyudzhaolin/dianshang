<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:zhaoLin(97139915@qq.com)
// +----------------------------------------------------------------------

class investorModule extends BaseModule{
	/*
	 * 个人中心
	 * @param int $id 用户ID
	 * @author zhaoLin
	*/
	public function index(){
        $id=820;
         //个人资料
        $personal= $GLOBALS['db']->getRow("select img_user_logo,user_name,user_type,per_degree,email,mobile,province,city from ".DB_PREFIX."user where id = {$id}");
         $vo = array(
			'province'=>'101'
		);
		// 初始化省份城市下拉列表
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['id'] == $vo['province'])
			{
				$region_pid = $region_lv2[$k]['id'];  
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		if($region_pid>0)
		{
			$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where  pid = ".$region_pid." order by py asc");  //三级地址
			$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		}
		//获得图片
		if(strim($personal ['img_user_logo'] )){
			$personal['real_img']=getQiniuPath($personal['img_user_logo'],'img');
			$personal['real_img']=$personal['real_img']."?imageView2/1/w/500/h/500";
		}
		$user_type_list = app_enum_conf('USER_TYPE_LIST');
		$user_type=$user_type_list[$personal['user_type']];
       	$per_degree_list = app_enum_conf('EDUCATION_LIST');
       	$GLOBALS['tmpl']->assign("user_type",$user_type);
		$GLOBALS['tmpl']->assign("per_degree_list",$per_degree_list);
        $GLOBALS['tmpl']->assign("personal",$personal);
		$GLOBALS['tmpl']->display("personal.html");
	}	
	public function  personal_update(){
		$res = array('status'=>0,'info'=>'','data'=>'');
		$user['img_user_logo'] = strim($_POST['img_user_logo']);
		$user['user_name'] = strim($_POST['user_name']);
		$user['user_type'] = strim($_POST['user_type']);
		$user['province'] = strim($_POST['province']);
		$user['city'] = strim($_POST['city']);
		$user['per_degree'] = strim($_POST['degree']);
		$user['id'] = 820;
		$user['email'] = strim($_POST['email']);
 		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");
		if(!$result){
			$res['status'] = 1;
			$res['info'] = "更新个人信息失败";
		}else{
           $res['status'] = 2;
			$res['info'] = "更新个人成功";

		}
		ajax_return($res);
	}	

    public function  company_index(){
    	$id=3;
        $company= $GLOBALS['db']->getRow("select company_name,company_title,com_legal,com_tel,com_web,company_brief from  ".DB_PREFIX."deal  where id = {$id}");
        $GLOBALS['tmpl']->assign("company",$company);
        $GLOBALS['tmpl']->display("personal.html");
    }
     public function company_update(){
        $res = array('status'=>0,'info'=>'','data'=>'');
		$deal['company_name'] = strim($_REQUEST['company_name']);
		$deal['company_title'] = strim($_REQUEST['company_title']);
		$deal['com_legal'] = strim($_REQUEST['com_legal']);
		$deal['com_tel'] = strim($_REQUEST['com_tel']);
		$deal['com_web'] = strim($_REQUEST['com_web']);
		$deal['company_brief'] = strim($_REQUEST['company_brief']);
		$id =3;
 		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,"UPDATE","id=".$id,"SILENT");
		if(!$result){
			$res['status'] = 1;
			$res['info'] = "更新个人信息失败";
		}else{
           $res['status'] = 2;
			$res['info'] = "更新个人成功";
		}
		ajax_return($res);
	}	
}
?>