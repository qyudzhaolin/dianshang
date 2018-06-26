<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class UserAction extends CommonAction
{

    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH . "/system/libs/user.php";
        // 加载公共函数
        include_once APP_ROOT_PATH . "system/common.php";
    }

    public function index_investor()
    {
        if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        if (trim($_REQUEST['email']) != '') {
            $map['email'] = array(
                'like',
                '%' . trim($_REQUEST['email']) . '%'
            );
        }
        
        if (intval($_REQUEST['province']) > 0) {
            $map['province'] = intval($_REQUEST['province']);
        }
        
        if (intval($_REQUEST['city']) > 0) {
            $map['city'] = intval($_REQUEST['city']);
        }
        
        if (intval($_REQUEST['role']) > 0) {
            $map['role'] = intval($_REQUEST['role']) - 1;
        }
        
        if (intval($_REQUEST['is_review']) > 0) {
            $map['is_review'] = intval($_REQUEST['is_review']) - 1;
        }
        
        $map['user_type'] = 1;
        $map['is_effect'] = 1;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                $auth_type = C(AUTH_TYPE);
                $list[$k]['is_review_zh'] = $auth_type[$v['is_review']];
                $list[$k]['role_zh'] = $v['role'] == 1 ? '是' : '否';
            }
            $this->assign("list", $list);
        }
        
        // 初始化省份城市下拉列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        $this->assign("region_lv2", $region_lv2);
        
        $this->assign('is_review_list', C(AUTH_TYPE_LIST));
        
        $this->assign('refresh',isset($_REQUEST['refresh']) ? 1 : 0);
        $this->display();
    }

    /*
     * 会员回收站
     */
    public function delete_investor()
    {
    if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        if (trim($_REQUEST['email']) != '') {
            $map['email'] = array(
                'like',
                '%' . trim($_REQUEST['email']) . '%'
            );
        }
        
        if (intval($_REQUEST['province']) > 0) {
            $map['province'] = intval($_REQUEST['province']);
        }
        
        if (intval($_REQUEST['city']) > 0) {
            $map['city'] = intval($_REQUEST['city']);
        }
        
        if (intval($_REQUEST['role']) > 0) {
            $map['role'] = intval($_REQUEST['role']) - 1;
        }
        
        if (intval($_REQUEST['is_review']) > 0) {
            $map['is_review'] = intval($_REQUEST['is_review']) - 1;
        }
        
        $map['user_type'] = 1;
        $map['is_effect'] = 0;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                $auth_type = C(AUTH_TYPE);
                $list[$k]['is_review_zh'] = $auth_type[$v['is_review']];
                $list[$k]['role_zh'] = $v['role'] == 1 ? '是' : '否';
            }
            $this->assign("list", $list);
        }
        
        // 初始化省份城市下拉列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        $this->assign("region_lv2", $region_lv2);
        
        $this->assign('is_review_list', C(AUTH_TYPE_LIST));
        
        $this->display();
    }
    
    /*
     * 会员审核列表
     */
    public function audit_investor()
    {
    if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        if (trim($_REQUEST['email']) != '') {
            $map['email'] = array(
                'like',
                '%' . trim($_REQUEST['email']) . '%'
            );
        }
        
        if (intval($_REQUEST['province']) > 0) {
            $map['province'] = intval($_REQUEST['province']);
        }
        
        if (intval($_REQUEST['city']) > 0) {
            $map['city'] = intval($_REQUEST['city']);
        }
        
        if (intval($_REQUEST['role']) > 0) {
            $map['role'] = intval($_REQUEST['role']) - 1;
        }
        
        $map['is_review'] = 2;  #认证中
        $map['user_type'] = 1;
        $map['is_effect'] = 1;  #启用
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                $auth_type = C(AUTH_TYPE);
                $list[$k]['is_review_zh'] = $auth_type[$v['is_review']];
                $list[$k]['role_zh'] = $v['role'] == 1 ? '是' : '否';
            }
            $this->assign("list", $list);
        }
        
        // 初始化省份城市下拉列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        $this->assign("region_lv2", $region_lv2);
        
        $this->assign('is_review_list', C(AUTH_TYPE_LIST));
        
        $this->display();
    }

    public function user_audit(){
        
        $id = (int)$_REQUEST['id'];
        
        if(!$id){
            $this->error("无法获取用户信息!");
        }
        
        if (! check_empty($_REQUEST['is_audit'])) {
            $this->error("请选择审核状态");
        }
        if (! check_empty($_REQUEST['audit_desc'])) {
            $this->error("请输入审核意见");
        }
        
        if (utf8_strlen($_REQUEST['audit_desc']) > 30) {
            $this->error("请确保审核意见在30个字以内");
        }
        
        $this->assign("jumpUrl", u(MODULE_NAME.'/audit_investor'));
        
        $adm_session = es_session::get(md5(conf("AUTH_KEY")));
        
        $data['user_id'] = $id;
        $data['audit_status'] = $_REQUEST['is_audit'];
        $data['audit_desc'] = $_REQUEST['audit_desc'];
        $data['business_type'] = 1;
        $data['create_time'] = time();
        $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
        
        $re = M('user_audit_log')->add($data);
        
        if($re){
            // 发消息
            $user_notify['user_id'] = $id;
            $user_notify['url'] = "";
            $user_notify['log_time'] = time();
            $user_notify['is_read'] = 0;
            
            
            if($_REQUEST['is_audit'] == 1){
                // 修改会员认证状态
                M('user')->where('id='.$id)->setField('is_review',1);
                $msg = getSendSmsTemplate("admin_user_audit_ok");
                
                // 发送短消息
                $user_notify['log_info'] = $msg;
                M("user_notify")->add($user_notify);
                
                // 发送短信
                $params = array(
                    "mobile"    => $_REQUEST['mobile'],
                    "content"   => $msg,
                    "type"      => getSendSmsType("admin_user_audit"),
                );
                $result = request_service_api("Common.Sms.sendSms",$params);
                
                $this->success("会员认证已通过");
            }else{
                // 修改会员认证状态
                M('user')->where('id='.$id)->setField('is_review',0);
                $msg = getSendSmsTemplate("admin_user_audit_fail",array($_REQUEST['audit_desc']));
                
                // 发送短消息
                $user_notify['log_info'] = $msg;
                M("user_notify")->add($user_notify);
                
                // 发送短信
                $params = array(
                    "mobile"    => $_REQUEST['mobile'],
                    "content"   => $msg,
                    "type"      => getSendSmsType("admin_user_audit"),
                );
                $result = request_service_api("Common.Sms.sendSms",$params);
                
                $this->success("会员认证被驳回");
            }
        }else{
            $this->error("审核失败");
        }
        
    }
    
    public function check_mobile_repeat()
    {
        $ajax = intval($_REQUEST['ajax']);
        $mobile = trim($_REQUEST['mobile']);
        $reuslt = M(MODULE_NAME)->where('mobile=' . $mobile)->find();
        
        if (! empty($reuslt)) {
            $this->success("手机号已被注册!", $ajax);
        } else {
            $this->error("手机号尚未被注册!", $ajax);
        }
    }

    public function check_mobile_change()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = trim($_REQUEST['id']);
        $mobile = trim($_REQUEST['mobile']);
        $reuslt2 = M(MODULE_NAME)->where("id=" . $id)->find();
        if ($reuslt2['mobile'] == $mobile) {
            $this->error("手机号未更改!", $ajax);
        } else {
            $reuslt = M(MODULE_NAME)->where("mobile=" . $mobile)->find();
            if (! empty($reuslt)) {
                $this->success("手机号已被注册!", $ajax);
            } else {
                $this->error("手机号尚未被注册!", $ajax);
            }
        }
    }

    public function submit_investor()
    {
        if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        if (trim($_REQUEST['email']) != '') {
            $map['email'] = array(
                'like',
                '%' . trim($_REQUEST['email']) . '%'
            );
        }
        $map['user_type'] = 1;
        // $map['is_review'] = 0;
        $is_review = 1;
        $map['is_review'] = array(
            'in',
            $is_review
        );
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                if ($v['is_review'] == '1') {
                    $list[$k]['is_review_zh'] = '已审核';
                } elseif ($v['is_review'] == '2') {
                    $list[$k]['is_review_zh'] = '审核中';
                } else {
                    $list[$k]['is_review_zh'] = '未审核';
                }
                if ($v['apply_help_time'] != '') {
                    $list[$k]['apply_help_time'] = '是';
                } else {
                    $list[$k]['apply_help_time'] = '否';
                }
            }
            $this->assign("list", $list);
        }
        
        $this->display();
    }

    public function index_estp()
    {
        if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        if (trim($_REQUEST['email']) != '') {
            $map['email'] = array(
                'like',
                '%' . trim($_REQUEST['email']) . '%'
            );
        }
        $map['user_type'] = 0;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                if ($v['is_review'] == '1') {
                    $list[$k]['is_review_zh'] = '已审核';
                } elseif ($v['is_review'] == '2') {
                    $list[$k]['is_review_zh'] = '审核中';
                } else {
                    $list[$k]['is_review_zh'] = '未审核';
                }
            }
            $this->assign("list", $list);
        }
        // var_dump($list);die();
        $this->display();
    }

    public function submit_estp()
    {
        if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        if (trim($_REQUEST['email']) != '') {
            $map['email'] = array(
                'like',
                '%' . trim($_REQUEST['email']) . '%'
            );
        }
        $map['user_type'] = 0;
        $is_review = array(
            '0',
            '2'
        );
        $map['is_review'] = array(
            'in',
            $is_review
        );
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                if ($v['is_review'] == '1') {
                    $list[$k]['is_review_zh'] = '已审核';
                } elseif ($v['is_review'] == '2') {
                    $list[$k]['is_review_zh'] = '审核中';
                } else {
                    $list[$k]['is_review_zh'] = '未审核';
                }
            }
            $this->assign("list", $list);
        }
        
        $this->display();
    }

    public function apply_help()
    {
        if (trim($_REQUEST['user_name']) != '') {
            $map['user_name'] = array(
                'like',
                '%' . trim($_REQUEST['user_name']) . '%'
            );
        }
        if (trim($_REQUEST['mobile']) != '') {
            $map['mobile'] = array(
                'like',
                '%' . trim($_REQUEST['mobile']) . '%'
            );
        }
        $map['apply_help_time'] = array(
            'NEQ',
            ''
        );
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            $this->assign("list", $list);
        }
        
        $this->display();
    }

    public function add()
    {
        // 初始化省份城市下拉列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        $this->assign("region_lv2", $region_lv2);
        // 查询用户学历
        $education_degrees = M(education_degree)->findAll();
        $this->assign("per_degree_list", $education_degrees);
        $this->assign("user_type_list", C(USER_TYPE_LIST));
        $this->display();
    }

    public function insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(MODULE_NAME)->create();
        
        // 开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));
        
        $data['user_name'] = $_REQUEST['user_name'];
        $data['mobile'] = $_REQUEST['mobile'];
        $data['user_type'] = 1;
        $data['role'] = isset($_REQUEST['role']) ? (int)$_REQUEST['role'] : 0;
        $data['province'] = $_REQUEST['province'];
        $data['city'] = $_REQUEST['city'];
        $data['per_degree'] = $_REQUEST['per_degree'];
        $data['email'] = $_REQUEST['email'];
        $data['img_user_logo'] = $_REQUEST['img_user_logo'];
        $data['id_cardz_logo'] = $_REQUEST['id_cardz_logo'];
        $data['id_cardf_logo'] = $_REQUEST['id_cardf_logo'];
        $data['img_card_logo'] = $_REQUEST['img_card_logo'];
        if (trim($_REQUEST['user_pwd']) != "") {
            $data['user_pwd'] = MD5($_REQUEST['user_pwd']);
        }
        $data['is_review'] = 1; #后台添加统一为已认证
        $data['create_time'] = time();
        $data['update_time'] = time();
        
        $data_extend['org_name'] = $_REQUEST['org_name'];
        $data_extend['org_title'] = $_REQUEST['org_title'];
        $data_extend['org_linkman'] = $_REQUEST['org_linkman'];
        $data_extend['org_mobile'] = $_REQUEST['org_mobile'];
        $data_extend['org_url'] = $_REQUEST['org_url'];
        $data_extend['org_desc'] = $_REQUEST['org_desc'];
        
        if (! check_empty($_REQUEST['user_name'])) {
            $this->error("请输入真实姓名");
        }
        if (utf8_strlen($_REQUEST['user_name']) > 14) {
            $this->error("请确保真实姓名在14个字以内");
        }
        if (! check_empty($_REQUEST['mobile'])) {
            $this->error("请输入手机号");
        }
        if (! check_empty($_REQUEST['user_pwd'])) {
            $this->error("请输入会员登录密码");
        }
        
        $id = M("User")->where("mobile=" . $_REQUEST['mobile'])->getField("id");
        if ($id) {
            $this->error("手机号码已注册");
        }
        
        $list = M(MODULE_NAME)->add($data);
        
        if (false !== $list) {
            $data_extend['user_id'] = $list;
            $list_extend = M("user_ex_investor")->add($data_extend);
            
            // 发送短消息
            $user_notify['user_id'] = $list;
            $user_notify['log_info'] = "欢迎您使用磁斯达克，您可以在磁斯达克网站（www.cisdaq.com ）的帮助中心内查看我们为您提供的服务。";
            $user_notify['url'] = "";
            $user_notify['log_time'] = time();
            $user_notify['is_read'] = 0;
            M("user_notify")->add($user_notify);
            
            // 发送短信
            $params = array(
                "mobile"    => $data['mobile'],
                "content"   => getSendSmsTemplate("admin_user_add",array($data['mobile'],$_REQUEST['user_pwd'])),
            );
            $result = request_service_api("Common.Sms.sendSmsOnly",$params);
            
            if (intval($data['is_review']) == 1) {
                // 发送短消息
                $user_notify['user_id'] = $list;
                $user_notify['log_info'] = getSendSmsTemplate("admin_user_add_cert");
                $user_notify['url'] = "";
                $user_notify['log_time'] = time();
                $user_notify['is_read'] = 0;
                M("user_notify")->add($user_notify);
                
                // 发送短信
                $params = array(
                    "mobile"    => $data['mobile'],
                    "content"   => getSendSmsTemplate("admin_user_add_cert"),
                    "type"      => getSendSmsType("admin_user_add_cert")
                );
                $result = request_service_api("Common.Sms.sendSms",$params);
            }
        }
        $log_info = $data['mobile'];
        if (false !== $list) {
            // 成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            $this->success(L("INSERT_SUCCESS"));
        } else {
            // 错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }

    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        // 获取会员对象
        $vo = M(MODULE_NAME)->where($condition)->find();
        
        // 处理页面显示图片
        if (trim($vo['img_user_logo'])) {
            $vo['header_real_url'] = trim($vo['img_user_logo']);
            $vo['header_real_url'] = getQiniuPath($vo['header_real_url'], "img");
        }
        if (trim($vo['img_card_logo'])) {
            $vo['card_real_url'] = trim($vo['img_card_logo']);
            $vo['card_real_url'] = getQiniuPath($vo['card_real_url'], "img");
        }
        if (trim($vo['id_cardz_logo'])) {
            $vo['id_cardz_real_url'] = trim($vo['id_cardz_logo']);
            $vo['id_cardz_real_url'] = getQiniuPath($vo['id_cardz_real_url'], "img");
        }
        if (trim($vo['id_cardf_logo'])) {
            $vo['id_cardf_real_url'] = trim($vo['id_cardf_logo']);
            $vo['id_cardf_real_url'] = getQiniuPath($vo['id_cardf_real_url'], "img");
        }
        $this->assign('vo', $vo);
        
        $user_investor_cover = M("user_ex_investor")->where("user_id=" . $vo['id'])->find();
        $this->assign('vo_extend', $user_investor_cover);
        
        // 初始化省份城市下拉列表
        $region_pid = 0;
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        foreach ($region_lv2 as $k => $v) {
            if ($v['id'] == $vo['province']) {
                $region_pid = $region_lv2[$k]['id'];
                break;
            }
        }
        $this->assign("region_lv2", $region_lv2);
        
        if ($region_pid > 0) {
            $region_lv3 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where pid = " . $region_pid . " order by py asc"); // 三级地址
            $this->assign("region_lv3", $region_lv3);
        }
        
        // 查询用户学历
        $education_degrees = M(education_degree)->findAll();
        $this->assign("per_degree_list", $education_degrees);
        
        $this->assign("user_type_list", C(USER_TYPE_LIST));
        
        $this->display();
    }

    public function user_detail()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        // 获取会员对象
        $vo = M(MODULE_NAME)->where($condition)->find();
        
        // 处理页面显示图片
        if (trim($vo['img_user_logo'])) {
            $vo['header_real_url'] = trim($vo['img_user_logo']);
            $vo['header_real_url'] = getQiniuPath($vo['header_real_url'], "img");
        }
        if (trim($vo['img_card_logo'])) {
            $vo['card_real_url'] = trim($vo['img_card_logo']);
            $vo['card_real_url'] = getQiniuPath($vo['card_real_url'], "img");
        }
        if (trim($vo['id_cardz_logo'])) {
            $vo['id_cardz_real_url'] = trim($vo['id_cardz_logo']);
            $vo['id_cardz_real_url'] = getQiniuPath($vo['id_cardz_real_url'], "img");
        }
        if (trim($vo['id_cardf_logo'])) {
            $vo['id_cardf_real_url'] = trim($vo['id_cardf_logo']);
            $vo['id_cardf_real_url'] = getQiniuPath($vo['id_cardf_real_url'], "img");
        }
        $this->assign('vo', $vo);
        
        $user_investor_cover = M("user_ex_investor")->where("user_id=" . $vo['id'])->find();
        $user_investor_cover['vip_begin_time'] = intval($user_investor_cover['vip_begin_time']) != 0 ? to_date($user_investor_cover['vip_begin_time'], 'Y-m-d') : '';
        $user_investor_cover['vip_end_time'] = intval($user_investor_cover['vip_end_time']) != 0 ? to_date($user_investor_cover['vip_end_time'], 'Y-m-d') : '';
        $user_investor_cover['vip_money'] = intval($user_investor_cover['vip_money']) != 0 ? $user_investor_cover['vip_money'] : '';
        $this->assign('vo_extend', $user_investor_cover);
        
        // 初始化省份城市下拉列表
        $region_pid = 0;
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        foreach ($region_lv2 as $k => $v) {
            if ($v['id'] == $vo['province']) {
                $region_pid = $region_lv2[$k]['id'];
                break;
            }
        }
        $this->assign("region_lv2", $region_lv2);
        
        if ($region_pid > 0) {
            $region_lv3 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where pid = " . $region_pid . " order by py asc"); // 三级地址
            $this->assign("region_lv3", $region_lv3);
        }
        
        $this->assign("user_type_list", C(USER_TYPE_LIST));
        // 查询用户学历
        $education_degrees = M(education_degree)->findAll();
        $this->assign("per_degree_list", $education_degrees);
        
        // 来源
        $this->assign("is_audit", (int)$_REQUEST['audit']);
        
        $this->display();
    }

    public function delete()
    {
        // 彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        $reason = $_REQUEST['reason'];
        if (!empty($id) && !empty($reason)) {
            
            // 在基金管理公司的团队成员有无引用
            $use = M('fund_managers_team')->where('user_id='.$id)->find();
            if($use){
                $fund = M('fund_managers')->where('id='.$use['managers_id'])->getField('short_name');
                $this->error('该会员在 '.$fund.' 管理公司的团队成员中有引用，不可删除！', $ajax);
            }
            
            $condition = array(
                'id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['user_name'];
            }
            if ($info) $info = implode(",", $info);
            $ids = explode(',', $id);
            $reason = explode(',', $reason);
            $time = time();
            foreach ($ids as $k => $uid) {
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set is_effect=0,delete_reason ='{$reason[$k]}',update_time='{$time}' where id =" . $uid); // 删除会员
            }
            save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
            $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function restore()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array(
                'id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['user_name'];
            }
            if ($info) $info = implode(",", $info);
            $ids = explode(',', $id);
            $time = time();
            foreach ($ids as $k => $uid) {
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set is_effect=1,update_time='{$time}' where id =" . $uid); // 恢复会员
            }
            save_log($info . l("RESTORE_SUCCESS"), 1);
            $this->success(l("RESTORE_SUCCESS"), $ajax);
        } else {
            $this->error(l("RESTORE_EMPTY_WARNING"), $ajax);
        }
    }

    public function update()
    {
        B('FilterString');
        $log_info = M(MODULE_NAME)->where("id=" . intval($_REQUEST['id']))->getField("mobile");
        // 开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array(
            "id" => $_REQUEST['id']
        )));
        
        if (! check_empty($_REQUEST['user_name'])) {
            $this->error("请输入真实姓名");
        }
        if (utf8_strlen($_REQUEST['user_name']) > 14) {
            $this->error("请确保真实姓名在14个字以内");
        }
        if (! check_empty($_REQUEST['mobile'])) {
            $this->error("请输入手机号");
        }
        
        /*
         * if(!check_empty($_REQUEST['img_user_logo']))
         * {
         * $this->error("请上传图片");
         * }
         * if(!check_empty($_REQUEST['province']) )
         * {
         * $this->error("请输入所在地区");
         * }
         * if(!check_empty($_REQUEST['city']) )
         * {
         * $this->error("请输入所在地区");
         * }
         */
        
        /*
         * if(utf8_strlen($data['per_sign'])>28)
         * {
         * $this->error("请确保个人签名在28个字以内");
         * }
         */
        /*
         * if(!check_empty($_REQUEST['per_brief']))
         * {
         * $this->error("请输入个人简介");
         * }
         */
        /*
         * if(utf8_strlen($_REQUEST['per_brief'])>28)
         * {
         * $this->error("请确保个人简介在28个字以内");
         * }
         * if(utf8_strlen($_REQUEST['edu_history'])>100)
         * {
         * $this->error("请确保教育经历在100个字以内");
         * }
         * if(utf8_strlen($_REQUEST['per_history'])>100)
         * {
         * $this->error("请确保个人履历在100个字以内");
         * }
         */
        /*
         * if ($_REQUEST['user_type']==1) {
         * // if($_REQUEST['vip_money']<10)
         * // {
         * // $this->error("会员保证金必须大于10万元");
         * // }
         */
        /*
         * if(!check_empty($_REQUEST['org_name']))
         * {
         * $this->error("请输入机构名称");
         * }
         * if(utf8_strlen($_REQUEST['org_name'])>14)
         * {
         * $this->error("请确保机构名称在14个字以内");
         * }
         * if(!check_empty($_REQUEST['org_linkman']))
         * {
         * $this->error("请输入机构联系人");
         * }
         * if(utf8_strlen($_REQUEST['org_linkman'])>14)
         * {
         * $this->error("请确保机构联系人在14个字以内");
         * }
         * if(!check_empty($_REQUEST['org_mobile']))
         * {
         * $this->error("请输入机构联系方式");
         * }
         * if(!check_empty($_REQUEST['org_desc']))
         * {
         * $this->error("请输入机构简介");
         * }
         */
        /*
         * if(!check_empty($_REQUEST['org_url']))
         * {
         * $this->error("请输入机构网址");
         * }
         */
        /*
         * if(utf8_strlen($_REQUEST['org_desc'])>100)
         * {
         * $this->error("请确保机构简介在100个字以内");
         * }
         * if(empty($_REQUEST['period_choose']))
         * {
         * $this->error("请选择投资阶段");
         * }
         * if(empty($_REQUEST['cate_choose']))
         * {
         * $this->error("请选择项目分类");
         * }
         * if(empty($_REQUEST['mark_info']))
         * {
         * $this->error("请输入投资成绩");
         * }
         * if(empty($_REQUEST['invest_style']))
         * {
         * $this->error("请输入投资风格");
         * }
         * }
         */
         
        $user_id = intval($_REQUEST['id']);
        $data['id'] = $user_id;
        $data['user_name'] = $_REQUEST['user_name'];
        $data['mobile'] = $_REQUEST['mobile'];
        $data['user_type'] = 1;
        $data['role'] = isset($_REQUEST['role']) ? (int)$_REQUEST['role'] : 0;
        $data['province'] = $_REQUEST['province'];
        $data['city'] = $_REQUEST['city'];
        $data['per_degree'] = $_REQUEST['per_degree'];
        $data['email'] = $_REQUEST['email'];
        $data['img_user_logo'] = $_REQUEST['img_user_logo'];
        $data['id_cardz_logo'] = $_REQUEST['id_cardz_logo'];
        $data['id_cardf_logo'] = $_REQUEST['id_cardf_logo'];
        $data['img_card_logo'] = $_REQUEST['img_card_logo'];
//         if($data['role'] == 1){
//             $data['is_review'] = 1; # 是渠道合伙人，改为已认证
//         }else{
//             // 不做修改
//         }
        $data['update_time'] = time();
        $data_extend['org_name'] = $_REQUEST['org_name'];
        $data_extend['org_title'] = $_REQUEST['org_title'];
        $data_extend['org_linkman'] = $_REQUEST['org_linkman'];
        $data_extend['org_mobile'] = $_REQUEST['org_mobile'];
        $data_extend['org_url'] = $_REQUEST['org_url'];
        $data_extend['org_desc'] = $_REQUEST['org_desc'];
        
        $list = M(MODULE_NAME)->save($data);
        $res = M("user_ex_investor")->where("user_id=" . intval($data['id']))->find();
        if ($res) {
            // 已存在，update
            $list_extend = M("user_ex_investor")->where("user_id=" . intval($data['id']))->save($data_extend);
        } else {
            // 不存在，insert
            $data_extend['user_id'] = intval($data['id']);
            $list_extend = M("user_ex_investor")->add($data_extend);
        }
        
        // 修改会员时不能修改认证状态 sunerju 2016-08-12
//         if (false !== $list && false !== $list_extend) {
//             if (intval($data['is_review']) == 1 && intval($_REQUEST['old_is_review']) != 1) {
//                 // 发消息
//                 $user_notify['user_id'] = $user_id;
//                 $user_notify['log_info'] = "尊敬的投资人，您的会员认证申请已经通过！";
//                 $user_notify['url'] = "";
//                 $user_notify['log_time'] = time();
//                 $user_notify['is_read'] = 0;
//                 M("user_notify")->add($user_notify);
                
//                 // 发送短信
//                 $params = array(
//                     "mobile"    => $data['mobile'],
//                     "content"   => getSendSmsTemplate("admin_user_add_cert"),
//                     "type"      => getSendSmsType("admin_user_add_cert")
//                 );
//                 $result = request_service_api("Common.Sms.sendSms",$params);
//             }
//         }
        
        if (false !== $list && false !== $list_extend) {
            // 成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            // 错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"));
        }
    }

    public function edit_user_pwd()
    {
        $user_id = intval($_REQUEST['id']);
        $from = intval($_REQUEST['from']);
        if($from){
            $backurl = u(MODULE_NAME.'/delete_investor');
        }else{
            $backurl = u(MODULE_NAME.'/index_investor');
        }
        $this->assign('backurl',$backurl);
        $this->display();
    }

    public function update_user_pwd()
    {
        /* var_dump($_REQUEST);die(); */
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        
        $log_info = M(MODULE_NAME)->field('mobile,user_name')
            ->where("id=" . intval($data['id']))
            ->find();
        
        // 开始验证有效性
        $password = trim($_REQUEST['password']);
        if (! check_empty($password)) {
            $this->error("请输入用户密码");
        }
        if ($password != $_REQUEST['confirm_password']) {
            $this->error("两次输入的密码不一致");
        }
        
        
        $data['id'] = $_REQUEST['id'];
        $data['user_pwd'] = md5($password);
        $data['update_time'] = time();
        
        $list = M(MODULE_NAME)->save($data);
        
        if (false !== $list) {
            $this->assign("jumpUrl", u(MODULE_NAME.'/index_investor',array('refresh'=>1)));
            
            // 成功提示
            save_log($log_info['user_name'] . L("UPDATE_SUCCESS"), 1);
            
            // 发送短信
            $params = array(
                "mobile"    => $log_info['mobile'],
                "content"   => getSendSmsTemplate("admin_reset_password",array($password)),
            );
            $result = request_service_api("Common.Sms.sendSmsOnly",$params);
            
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            // 错误提示
            save_log($log_info['user_name'] . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $info = M(MODULE_NAME)->where("id=" . $id)->getField("user_name");
        $c_is_effect = M(MODULE_NAME)->where("id=" . $id)->getField("is_effect"); // 当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; // 需设置的状态
        M(MODULE_NAME)->where("id=" . $id)->setField("is_effect", $n_is_effect);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    public function account()
    {
        $user_id = intval($_REQUEST['id']);
        $user_info = M("User")->getById($user_id);
        $this->assign("user_info", $user_info);
        $this->display();
    }

    public function modify_account()
    {
        $user_id = intval($_REQUEST['id']);
        $money = doubleval($_REQUEST['money']);
        $msg = trim($_REQUEST['msg']) == '' ? l("ADMIN_MODIFY_ACCOUNT") : trim($_REQUEST['msg']);
        modify_account(array(
            'money' => $money
        ), $user_id, $msg);
        save_log(l("ADMIN_MODIFY_ACCOUNT"), 1);
        $this->success(L("UPDATE_SUCCESS"));
    }

    public function account_detail()
    {
        $user_id = intval($_REQUEST['id']);
        $user_info = M("User")->getById($user_id);
        $this->assign("user_info", $user_info);
        $map['user_id'] = $user_id;
        
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        
        $model = M("UserLog");
        if (! empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }

    public function foreverdelete_account_detail()
    {
        
        // 彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array(
                'id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M("UserLog")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['id'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M("UserLog")->where($condition)->delete();
            
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function consignee()
    {
        $user_id = intval($_REQUEST['id']);
        $user_info = M("User")->getById($user_id);
        $this->assign("user_info", $user_info);
        $map['user_id'] = $user_id;
        
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        
        $model = M("UserConsignee");
        if (! empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }

    public function foreverdelete_consignee()
    {
        
        // 彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array(
                'id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M("UserConsignee")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['id'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M("UserConsignee")->where($condition)->delete();
            
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function weibo()
    {
        $user_id = intval($_REQUEST['id']);
        $user_info = M("User")->getById($user_id);
        $this->assign("user_info", $user_info);
        $map['user_id'] = $user_id;
        
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        
        $model = M("UserWeibo");
        if (! empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }

    public function foreverdelete_weibo()
    {
        
        // 彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array(
                'id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M("UserWeibo")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['id'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M("UserWeibo")->where($condition)->delete();
            
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function check_user()
    {
        if (intval($_REQUEST['id']) > 0) {
            $uinfo = M("User")->getById(intval($_REQUEST['id']));
            if ($uinfo) {
                $result['status'] = true;
                ajax_return($result);
            } else {
                $result['status'] = false;
                ajax_return($result);
            }
        }
        $result['status'] = false;
        ajax_return($result);
    }

    public function add_style()
    {
        $len = intval($_REQUEST['len']) + 1;
        $this->assign("len", $len);
        $this->display();
    }

    public function add_point()
    {
        $len = intval($_REQUEST['len']) + 1;
        $this->assign("len", $len);
        $this->display();
    }

    public function mark_info()
    {
        $len = intval($_REQUEST['len']) + 1;
        $this->assign("len", $len);
        $this->display();
    }
    
    /*
     * 获取用户信息
     * ps: 基金->基金管理公司->团队成员 有使用，修改请兼容 sunerju 2016-01-04
     * @author sunerju
     */
    public function get_user_info(){
        if (intval($_REQUEST['id']) > 0) {
            $info = M("User")->getById(intval($_REQUEST['id']));
            if ($info) {
                $info['real_logo'] = getQiniuPath($info['img_user_logo'], "img");;
                $result['data'] = $info;
                $result['status'] = true;
                ajax_return($result);
            } else {
                $result['status'] = false;
                ajax_return($result);
            }
        }
        $result['status'] = false;
        ajax_return($result);
    }
}
?>