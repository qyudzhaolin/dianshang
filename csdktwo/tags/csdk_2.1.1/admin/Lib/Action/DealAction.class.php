<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class DealAction extends CommonAction
{

    public function all_index()
    {
        if (trim($_REQUEST['name']) != '') {
            $map['name'] = array(
                'like',
                '%' . trim($_REQUEST['name']) . '%'
            );
        }
        if (trim($_REQUEST['s_name']) != '') {
            $map['s_name'] = array(
                'like',
                '%' . trim($_REQUEST['s_name']) . '%'
            );
        }
        
        if (intval($_REQUEST['vis']) > 0) {
            $map['vis'] = intval($_REQUEST['vis']);
        }
        
        if (intval($_REQUEST['is_effect']) > 0) {
            $map['is_effect'] = intval($_REQUEST['is_effect']) - 1;
        }
        if (intval($_REQUEST['is_publish']) > 0) {
            $map['is_publish'] = intval($_REQUEST['is_publish']);
        }
        if (intval($_REQUEST['is_best']) > 0) {
            $map['is_best'] = intval($_REQUEST['is_best']) - 1;
        }
        $map['is_delete'] = 0;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        
        $model = D($name);
        if (! empty($model)) {
            $list = $this->_list($model, $map);
            foreach ($list as $k => $v) {
                
                $list[$k]['deal_button'] = '状态值错误';
                
                // “申请中”、“待发布”
                if($v['is_effect'] == 0 && $v['is_publish'] == 1){
                    $list[$k]['is_effect_zh'] = '申请中';
                    $list[$k]['is_publish_zh'] = '待发布';
                    $list[$k]['deal_button'] = '<a href="javascript:edit(' . $list[$k]['id'] . ')">编辑</a>
                                                <a href="javascript:del(' . $list[$k]['id'] . ')">删除</a>';
                }
                // “编辑中”、“待发布”
                if($v['is_effect'] == 1 && $v['is_publish'] == 1){
                    $list[$k]['is_effect_zh'] = '编辑中';
                    $list[$k]['is_publish_zh'] = '待发布';
                    $list[$k]['deal_button'] = '<a href="javascript:edit(' . $list[$k]['id'] . ')">编辑</a>
                                                <a href="javascript:deal_event(' . $list[$k]['id'] . ')">管理融资记录</a>
                                                <a href="javascript:deal_preview(' . $list[$k]['id'] . ')">项目预览</a>
                                                <a href="javascript:deal_see(' . $list[$k]['id'] . ')">查看</a>
                                                <a href="javascript:del(' . $list[$k]['id'] . ')">删除</a>';
                }
                // “拟投资”、“待发布”
                if($v['is_effect'] == 2 && $v['is_publish'] == 1){
                    $list[$k]['is_effect_zh'] = '拟投资';
                    $list[$k]['is_publish_zh'] = '待发布';
                    $list[$k]['deal_button'] = '<a href="javascript:edit(' . $list[$k]['id'] . ')">编辑</a>
                                                <a href="javascript:deal_event(' . $list[$k]['id'] . ')">管理融资记录</a>
                                                <a href="javascript:deal_completion(' . $list[$k]['id'] . ')">融资完成</a>
                                                <a href="javascript:deal_preview(' . $list[$k]['id'] . ')">项目预览</a>
                                                <a href="javascript:deal_see(' . $list[$k]['id'] . ')">查看</a>
                                                <a href="javascript:deal_publish(' . $list[$k]['id'] . ')">发布上线</a>';
                }
                // “拟投资”、“已发布”
                if($v['is_effect'] == 2 && $v['is_publish'] == 2){
                    $list[$k]['is_effect_zh'] = '拟投资';
                    $list[$k]['is_publish_zh'] = '已发布';
                    $list[$k]['deal_button'] = '<a href="javascript:deal_event(' . $list[$k]['id'] . ')">管理融资记录</a>
                                                <a href="javascript:deal_completion(' . $list[$k]['id'] . ')">融资完成</a>
                                                <a href="javascript:deal_see(' . $list[$k]['id'] . ')">查看</a>
                                                <a href="javascript:deal_withdraw(' . $list[$k]['id'] . ')">撤回</a>';
                }
                // “已投资”、“待发布”
                if($v['is_effect'] == 3 && $v['is_publish'] == 1){
                    $list[$k]['is_effect_zh'] = '已投资';
                    $list[$k]['is_publish_zh'] = '待发布';
                    $list[$k]['deal_button'] = '<a href="javascript:edit(' . $list[$k]['id'] . ')">编辑</a>
                    							<a href="javascript:deal_event(' . $list[$k]['id'] . ')">管理融资记录</a>
                                                <a href="javascript:deal_preview(' . $list[$k]['id'] . ')">项目预览</a>
                                                <a href="javascript:deal_see(' . $list[$k]['id'] . ')">查看</a>
                                                <a href="javascript:deal_publish(' . $list[$k]['id'] . ')">发布上线</a>
                                                <a href="javascript:deal_new_finance(' . $list[$k]['id'] . ')">申请新融资</a>';
                }
                // “已投资”、“已发布”
                if($v['is_effect'] == 3 && $v['is_publish'] == 2){
                    $list[$k]['is_effect_zh'] = '已投资';
                    $list[$k]['is_publish_zh'] = '已发布';
                    $list[$k]['deal_button'] = '<a href="javascript:deal_event(' . $list[$k]['id'] . ')">管理融资记录</a>
                                                <a href="javascript:deal_see(' . $list[$k]['id'] . ')">查看</a>
                                                <a href="javascript:deal_withdraw(' . $list[$k]['id'] . ')">撤回</a>
                                                <a href="javascript:deal_new_finance(' . $list[$k]['id'] . ')">申请新融资</a>';
                }

                if ($v['is_best'] == '1') {
                    $list[$k]['is_best'] = '是';
                } else {
                    $list[$k]['is_best'] = '否';
                }
                
                $list[$k]['event_name'] = $GLOBALS['db']->getOne("SELECT period.`name` FROM " . DB_PREFIX . "deal_period AS period ," . DB_PREFIX . "deal AS deal WHERE deal.period_id=period.id and deal.id=" . $v['id'] . "");
                $userinfo = $GLOBALS['db']->getRow("select  mobile,user_name from " . DB_PREFIX . "user    where  id=" . $v['user_id'] . " ");
                $list[$k]['userinfo'] = $userinfo['user_name'].'-'.$userinfo['mobile'];
                $list[$k]['create_time'] = date('Y-m-d', $v['create_time']);
            }
            $this->assign("list", $list);
        }
        
//         $period_list = M("Deal_period")->findAll();
//         $this->assign("period_list", $period_list);
        $this->display();
    }
    
    // 查找推荐人
    public function find_investor()
    {
        $user_name = trim($_REQUEST ['linkValue']);
        if (!empty($user_name)) {
            $rel_data = M('user')->where("user_name like '%{$user_name}%' and is_effect=1 and user_type=1")->findAll();
            if ($rel_data !== false) {
                if ($rel_data != null) {
                    foreach ($rel_data as $sub_rel_data) {
                        $return_data['id'] = $sub_rel_data['id'];
                        $return_data['name'] = $sub_rel_data['user_name'].' （'.$sub_rel_data['mobile'].'）';
                        $return_data['third_data'] = $sub_rel_data['mobile'];
                        $return_datas[] = $return_data;
                    }
                     $this->ajaxReturn($return_datas, '', 1);
                }else{
                     $this->ajaxReturn($return_datas, '', 0);
                }
            } else {
                $this->ajaxReturn('','未查询到数据',2);
            }
        } else {
          $this->ajaxReturn('','没有输入数据',3);
    
        }
    }

    public function intend_list()
    {
        // 接收参数
        $name = $_REQUEST['name'];
        $mobile = $_REQUEST['mobile'];
        $deal_short_name = $_REQUEST['deal_short_name'];
        $deal_name = $_REQUEST['deal_name'];
        $state = empty($_REQUEST['state']) ? 0 : intval($_REQUEST['state']);
        
        $query = "select intend.id as id,intend.state as state,intend.create_time,user.id as user_id,user.user_name,user.mobile,deal.id as deal_id,deal.name as deal_name,deal.s_name as deal_short_name from " . DB_PREFIX . "deal_intend_log as intend," . DB_PREFIX . "user as user," . DB_PREFIX . "deal as deal where intend.user_id=user.id and intend.deal_id=deal.id and intend.create_time<>0 and user.is_review=1 and deal.is_effect in (2,3) and user.is_effect=1";
        if (! empty($name)) {
            $query .= " and user.user_name like '%$name%'";
        }
        if (! empty($mobile)) {
            $query .= " and user.mobile = '$mobile'";
        }
        if (! empty($deal_short_name)) {
            $query .= " and deal.s_name like '%$deal_short_name%'";
        }
        if (! empty($deal_name)) {
            $query .= " and deal.name like '%$deal_name%'";
        }
        if ($state != 999) {
            $query .= " and intend.state = $state";
        }
        $query .= " order by intend.create_time desc";
        $result = $GLOBALS['db']->getAll($query);
        // 状态字段枚举值数组
        $deal_intends = convert_array(C(DEAL_INTEND_LIST));
        foreach ($result as $k => $v) {
            $result[$k]['state'] = $deal_intends[$v['state']];
        }
        $this->assign("deal_intend_list", C(DEAL_INTEND_LIST));
        $this->assign("list", $result);
        $this->display('intend_list');
    }

    public function dealinted_edit()
    {
        $id = intval($_REQUEST['id']);
        $result = $GLOBALS['db']->getRow("select intend.id as id,intend.state as state,intend.idea as idea,intend.create_time,user.id as user_id,user.user_name,user.mobile,deal.id as deal_id,deal.name as deal_name,deal.s_name as deal_sname from " . DB_PREFIX . "deal_intend_log as intend," . DB_PREFIX . "user as user," . DB_PREFIX . "deal as deal where  intend.user_id=user.id and intend.deal_id=deal.id and intend.create_time<>0  and intend.id=$id   ");
        $result['create_time'] = $result['create_time'] != 0 ? date("Y-m-d H:i:s", $result['create_time']) : '';
        
        $this->assign("deal_intend_list", C(DEAL_INTEND_LIST));
        
        $this->assign("result", $result);
        $this->display();
    }

    public function dealinted_update()
    {
        $adm_session = es_session::get(md5(conf("AUTH_KEY")));
        $ids = intval($adm_session['adm_id']);
        $name = $GLOBALS['db']->getOne("select adm_name from " . DB_PREFIX . "admin where id='$ids'");
        $data['id'] = intval($_REQUEST['id']);
        $data['state'] = $_REQUEST['state'];
        $data['user_name'] = $name;
        $data['idea'] = $_REQUEST['idea'];
        
        $list = M(deal_intend_log)->save($data);
        if (false !== $list) {
            // 成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            // 错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function delete_index()
    {
        if (trim($_REQUEST['name']) != '') {
            $map['name'] = array(
                'like',
                '%' . trim($_REQUEST['name']) . '%'
            );
        }
        if (intval($_REQUEST['cate_id']) > 0) {
            $map['cate_id'] = intval($_REQUEST['cate_id']);
        }
        if (intval($_REQUEST['user_id']) > 0) {
            $map['user_id'] = intval($_REQUEST['user_id']);
        }
        $map['is_delete'] = 1;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (! empty($model)) {
            $this->_list($model, $map);
        }
        
        $cate_list = M("DealCate")->findAll();
        $this->assign("cate_list", $cate_list);
        $this->display();
    }

    public function add()
    {
        // $cate_list = M("DealCate")->findAll();
        $cate_list = M(MODULE_NAME)->findAll();
        $this->assign("cate_list", $cate_list);
        
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        $this->assign("region_lv2", $region_lv2);
        // 项目分类
        $deal_cate = M('Deal_cate')->select();
        
        $this->assign('deal_cate', $deal_cate);
        
        // 投资阶段
        $deal_period = M('Deal_period')->order("sort asc")->select();
        $this->assign('deal_period', $deal_period);
        // 推荐人
        $deal_user = $GLOBALS['db']->getAll("select id,user_name from " . DB_PREFIX . "user where is_review = 1 and is_effect=1 order by id desc"); // 二级地址
        $this->assign('deal_user', $deal_user);
        
        $this->assign("new_sort", M("Deal")->max("sort") + 1);
        // var_dump($this);
        $this->display();
    }

    public function insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(MODULE_NAME)->create();
        
        /**
         * *********ADD get initiator user id which will be insert into cixi_deal table*****************************
         */
        // $user_data = M('user')->create ();
        // $user_data['id'] = M("User")->where("mobile=".$_REQUEST['mobile']." and user_type=0")->getField("id");
        // //var_dump($user_data['id']);
        // $data['user_id'] = $user_data['id'];
        // //查看此会员是否以创建
        // $user_deal= M("Deal")->where("user_id=".$data['user_id'])->getField("id");
        // if($user_deal){
        // //不允许创建
        // $this->error("此会员已创建过项目！");
        // }
        $data['img_deal_logo'] = trim($_REQUEST['img_deal_logo']);
        $data['bp_url'] = trim($_REQUEST['bp_url']);
        $data['period_id'] = $_REQUEST['period_id'];
        if ($_REQUEST['cate_choose']) {
            $data['cate_choose'] = implode("_", $_REQUEST['cate_choose']);
        }
        // 开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));
        
        if (! check_empty($data['name'])) {
            $this->error("请输入项目名称");
        }
        if (utf8_strlen($data['name']) > 42) {
            $this->error("请确保项目名称在14个字以内");
        }
        if (! check_empty($data['s_name'])) {
            $this->error("请输入项目简称");
        }
        if (utf8_strlen($data['s_name']) > 24) {
            $this->error("请确保项目简称在6个字以内");
        }
        
        if (! check_empty($data['img_deal_logo'])) {
            $this->error("请上传项目logo");
        }
        
        if (! check_empty($data['company_name'])) {
            $this->error("请输入公司名称");
        }
        if (utf8_strlen($data['company_name']) > 42) {
            $this->error("请确保公司名称在14个字以内");
        }
        if (! check_empty($data['deal_sign'])) {
            $this->error("请输入项目简介");
        }
        if (utf8_strlen($data['deal_sign']) > 84) {
            $this->error("请确保项目简介在28个字以内");
        }
        if (! check_empty($data['company_brief'])) {
            $this->error("请输入公司简介");
        }
        if (utf8_strlen($data['company_brief']) > 600) {
            $this->error("请确保公司简介在200个字以内");
        }
        
        if (empty($_REQUEST['team_name'])) {
            $this->error("请输入团队成员");
        }
        if (! check_empty($data['deal_brief'])) {
            $this->error("请输入项目简介");
        }
        if (utf8_strlen($data['deal_brief']) > 600) {
            $this->error("请确保项目简介在400个字以内");
        }
        if (! check_empty($_REQUEST['period_id'])) {
            $this->error("请输入项目阶段");
        }
        if (empty($_REQUEST['cate_choose'])) {
            $this->error("请输入项目方向");
        }
        
        /*
         * if(!check_empty($data['business_mode']))
         * {
         * $this->error("请输入商业模式");
         * }
         * if(utf8_strlen($data['business_mode'])>600)
         * {
         * $this->error("请确保商业模式在200个字以内");
         * }
         */
//         if (! preg_match("/^\d+$/", $data['pe_amount_plan']) || intval($_REQUEST['pe_amount_plan']) <= 0) {
//             $this->error("请在融资计划输入数字");
//         }
        
//         if (! preg_match("/^\d+$/", $data['pe_least_amount']) || intval($_REQUEST['pe_least_amount']) < 100) {
//             $this->error("请在单笔最低投资额度输入数字");
//         }
        
        /*
         * if(empty($_REQUEST['user_point_event']))
         * {
         * $this->error("请输入项目亮点简介");
         * }
         */
        if (intval($_REQUEST['is_case']) == 1 && empty($_REQUEST['capital'])) {
            $this->error("成功案例必须填写投资回报信息");
        }
        $data['com_time'] = trim($data['com_time']) == '' ? 0 : to_timespan($data['com_time']);
        $data['create_time'] = time();
        $data['is_effect'] = 0; # 新增项目，设为申请中状态
        $data['is_delete'] = 0;
        $data['comment_count'] = $data['support_count'];
        $list = M(MODULE_NAME)->add($data);
        // $list_1=M('user')->add($user_data);
        if (false !== $list) {
            // 成功提示
            foreach ($_REQUEST['cate_choose'] as $k => $v) {
                if (trim($v) != "") {
                    $deal_cate = array();
                    $deal_cate['deal_id'] = $list;
                    $deal_cate['cate_id'] = trim($v);
                    M("Deal_select_cate")->add($deal_cate);
                }
            }
            // var_dump($_REQUEST['team_name']);die();
            foreach ($_REQUEST['team_name'] as $k => $v) {
                // cixi_deal_team
                if (trim($v) != "" || trim($_REQUEST['title'][$k]) != '') {
                    $team = array();
                    $team['deal_id'] = $list;
                    $team['name'] = trim($v);
                    $team['title'] = trim($_REQUEST['title'][$k]);
                    $team['brief'] = trim($_REQUEST['brief'][$k]);
                    $team['img_logo'] = trim($_REQUEST['image_key'][$k]);
                    M("deal_team")->add($team);
                }
            }
            /*
             * foreach($_REQUEST['create_time_event'] as $k=>$v)
             * {
             * //cixi_deal_envent
             * if(trim($v)!=""||trim($_REQUEST['brief_event'][$k])!='')
             * {
             * $event = array();
             * $event['deal_id'] = $list;
             * $event['create_time'] = strtotime(trim($v));
             * $event['brief'] = trim($_REQUEST['brief_event'][$k]);
             * M("deal_event")->add($event);
             * }
             * }
             *
             * //项目亮点
             * foreach($_REQUEST['point_event'] as $k=>$v)
             * {
             * if(trim($v)!=""||trim($_REQUEST['point_event'][$k])!='')
             * {
             * $event = array();
             * $event['deal_id'] = $list;
             * $event['create_time'] = time();
             * $event['point_info'] = trim($_REQUEST['point_event'][$k]);
             * M("deal_sign_point")->add($event);
             * }
             * }
             * //用户亮点
             * foreach($_REQUEST['user_point_event'] as $k=>$v)
             * {
             * //cixi_deal_sign_point
             * if(trim($v)!=""||trim($_REQUEST['user_point_event'][$k])!='')
             * {
             * $event = array();
             * $event['deal_id'] = $list;
             * $event['create_time'] = time();
             * $event['point_info'] = trim($_REQUEST['user_point_event'][$k]);
             * M("deal_brief_point")->add($event);
             * }
             * }
             * foreach($_REQUEST['question'] as $k=>$v)
             * {
             *
             * if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
             * {
             * $qa = array();
             * $qa['deal_id'] = $list;
             * $qa['question'] = trim($v);
             * $qa['answer'] = trim($_REQUEST['answer'][$k]);
             * $qa['sort'] = intval($k)+1;
             * M("DealFaq")->add($qa);
             * }
             * }
             */
            // syn_deal($list);
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
        $deal_see = intval($_REQUEST['deal_see']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        if ($vo['user_id'] == 0) $vo['user_id'] = '';
        $vo['com_time'] = date("Y-m-d", $vo['com_time']);
        
        /*
         * if($vo['interview_time']){
         * $vo['interview_time'] = date("Y-m-d",$vo['interview_time']);
         * }
         */
//         $vo['pe_evaluate'] = $vo['pe_amount_plan'] / $vo['pe_sell_scale'] * 100;
//         $len = strpos($vo['pe_evaluate'], ".");
//         if ($len) {
//             $vo['pe_evaluate'] = substr($vo['pe_evaluate'], 0, $len);
//         }
        if (trim($vo['img_deal_logo'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['real_url'] = trim($vo['img_deal_logo']);
            $vo['real_url'] = getQiniuPath($vo['real_url'], "img");
            // var_dump( $vo['img_deal_logo']);
        }
        if (trim($vo['img_deal_cover'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['real_url_cover'] = trim($vo['img_deal_cover']);
            $vo['real_url_cover'] = getQiniuPath($vo['real_url_cover'], "img");
            // var_dump( $vo['img_deal_logo']);
        }
        if (trim($vo['img_deal_app_cover'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['real_url_app_cover'] = trim($vo['img_deal_app_cover']);
            $vo['real_url_app_cover'] = getQiniuPath($vo['real_url_app_cover'], "img");
            // var_dump( $vo['img_deal_logo']);
        }
        if (trim($vo['bp_url'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['real_url_bp'] = trim($vo['bp_url']);
            // $vo['real_url_bp']=getQiniuPath($vo['real_url_bp'],"bp");
            $vo['real_url_bp'] = APP_ROOT . "bp_viewer/get_bp.php?key=" . $vo['bp_url'];
            // var_dump($vo['real_url_bp'],$vo);
        }
        
        if (trim($vo['spot_app_img'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['spot_app_img'] = trim($vo['spot_app_img']);
            $vo['spot_app_img_url'] = getQiniuPath($vo['spot_app_img'], "img");
        }
        if (trim($vo['spot_pc_img'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['spot_pc_img'] = trim($vo['spot_pc_img']);
            $vo['spot_pc_img_url'] = getQiniuPath($vo['spot_pc_img'], "img");
        }
        if (trim($vo['profession_app_img'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['profession_app_img'] = trim($vo['profession_app_img']);
            $vo['profession_app_img_url'] = getQiniuPath($vo['profession_app_img'], "img");
        }
        if (trim($vo['profession_pc_img'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['profession_pc_img'] = trim($vo['profession_pc_img']);
            $vo['profession_pc_img_url'] = getQiniuPath($vo['profession_pc_img'], "img");
        }
        if (trim($vo['vision_app_img'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['vision_app_img'] = trim($vo['vision_app_img']);
            $vo['vision_app_img_url'] = getQiniuPath($vo['vision_app_img'], "img");
        }
        if (trim($vo['vision_pc_img'])) {
            // 获取缩略图完整url地址
            include_once APP_ROOT_PATH . "system/common.php";
            $vo['vision_pc_img'] = trim($vo['vision_pc_img']);
            $vo['vision_pc_img_url'] = getQiniuPath($vo['vision_pc_img'], "img");
        }
        /*
         * if(trim($vo['img_achievement'])){
         * //获取缩略图完整url地址
         * include_once APP_ROOT_PATH."system/common.php";
         * $vo['real_url_img_achievement']=trim($vo['img_achievement']);
         * //$vo['real_url_bp']=getQiniuPath($vo['real_url_bp'],"bp");
         * $vo['real_url_img_achievement']=getQiniuPath($vo['real_url_img_achievement'],"img");
         * }
         */
        /*
         * if(trim($vo['img_vision'])){
         * //获取缩略图完整url地址
         * include_once APP_ROOT_PATH."system/common.php";
         * $vo['real_url_img_vision']=trim($vo['img_vision']);
         * //$vo['real_url_bp']=getQiniuPath($vo['real_url_bp'],"bp");
         * $vo['real_url_img_vision']=getQiniuPath($vo['real_url_img_vision'],"img");
         * }
         */
        
        // 准备推荐人数据
        $user = M("User")->where("id=" . $vo['user_id'])->find();
        $vo['user_name'] = $user['user_name'];
        $vo['user_mobile'] = $user['mobile'];
        $this->assign('vo', $vo);
     
        // var_dump($vo['real_url'],$vo['img_deal_logo'] );//die();
        // 用户信息
        $this->assign('user', $user);
        
        $cate_list = M("DealCate")->findAll();
        $this->assign("cate_list", $cate_list);
        
        // 项目已选择的分类
        $deal_select_cate = M("Deal_select_cate")->where("deal_id =" . $id)->findAll();
        $this->assign("deal_select_cate", $deal_select_cate);
        // 项目分类
        $deal_cate = M('Deal_cate')->select();
        foreach ($deal_cate as $k => $v) {
            foreach ($deal_select_cate as $ks => $vs) {
                if ($vs['cate_id'] == $v['id']) {
                    $deal_cate[$k]['check'] = 1;
                }
            }
        }
        $this->assign('deal_cate', $deal_cate); // var_dump($deal_cate);die();
                                                   
        // 投资阶段
        $deal_period = M('Deal_period')->order("sort asc")->select();
        $this->assign('deal_period', $deal_period);
        
        $region_pid = 0;
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        foreach ($region_lv2 as $k => $v) {
            if ($v['id'] == $vo['province']) {
                $region_lv2[$k]['selected'] = 1;
                $region_pid = $region_lv2[$k]['id'];
                break;
            }
        }
        $this->assign("region_lv2", $region_lv2);
        
        if ($region_pid > 0) {
            $region_lv3 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where pid = " . $region_pid . " order by py asc"); // 三级地址
            foreach ($region_lv3 as $k => $v) {
                if ($v['id'] == $vo['city']) {
                    $region_lv3[$k]['selected'] = 1;
                    break;
                }
            }
            $this->assign("region_lv3", $region_lv3);
        }
        
        // $qa_list = M("deal_team")->where("deal_id=".$vo['id'])->findAll();
        $qa_list = M("deal_team")->order('id')
            ->where("deal_id=" . $vo['id'])
            ->findAll();
        // var_dump($qa_list);
        foreach ($qa_list as $k => $v) {
            if (trim($v['img_logo'])) {
                // 获取缩略图完整url地址
                include_once APP_ROOT_PATH . "system/common.php";
                $real_url = trim($v['img_logo']);
                $qa_list[$k]['real_url'] = getQiniuPath($real_url, "img");
                // var_dump( $qa_list[$k]['real_url'],$v['image_logo']);
            }
        }
        // 行业调查数据
        /*
         * $deal_profession_data = M("deal_profession_data")->order('id')->where("deal_id=".$vo['id'])->findAll();
         * $this->assign("deal_profession_data",$deal_profession_data);
         */
        
        // 商业模式
        $deal_operation_steps = M("deal_operation_steps")->order('id')
            ->where("deal_id=" . $vo['id'])
            ->findAll();
        foreach ($deal_operation_steps as $k => $v) {
            if (trim($v['img_deal_opera_steps'])) {
                // 获取缩略图完整url地址
                include_once APP_ROOT_PATH . "system/common.php";
                $img_deal_opera_steps = trim($v['img_deal_opera_steps']);
                $deal_operation_steps[$k]['real_url'] = getQiniuPath($img_deal_opera_steps, "img");
            }
        }
        $this->assign("deal_operation_steps", $deal_operation_steps);
        
        // 发展现状
        $deal_data_img = M("deal_data_img")->order('id')
            ->where("deal_id=" . $vo['id'])
            ->findAll();
        foreach ($deal_data_img as $k => $v) {
            if (trim($v['img_data_url'])) {
                // 获取缩略图完整url地址
                include_once APP_ROOT_PATH . "system/common.php";
                $img_data_url = trim($v['img_data_url']);
                $deal_data_img[$k]['real_url'] = getQiniuPath($img_data_url, "img");
            }
        }
        $this->assign("deal_data_img", $deal_data_img);
        
        // 推荐
        /*
         * $deal_recommend = M("deal_recommend")->order('id')->where("deal_id=".$vo['id'])->findAll();
         * $this->assign("deal_recommend",$deal_recommend);
         */
        
        // 磁斯达克的采访
        /*
         * $deal_interviem = M("deal_interviem")->order('id')->where("deal_id=".$vo['id'])->findAll();
         * $this->assign("deal_interviem",$deal_interviem);
         */
        
        /*
         * $event_list = M("deal_event")->where("deal_id=".$vo['id'])->order('create_time desc')->findAll();
         * foreach($event_list as $k=>$v){
         * $event_list[ $k]['create_time']=date("Y-m-d",$v['create_time']);
         * //$deal_point_list[ $k]['brief']=$v['brief'];
         * }
         */
        
        // 项目亮点
        /*
         * $deal_point_event_list = M("deal_sign_point")->order('id')->where("deal_id=".$vo['id'])->findAll();
         * foreach($deal_point_event_list as $k=>$v){
         * $deal_point_list[ $k]['create_time']=date("Y-m-d",$v['create_time']);
         * $deal_point_list[ $k]['point_info']=$v['point_info'];
         * }
         */
        // 亮点数据
        /*
         * $deal_point_user_event_list = M("deal_brief_point")->order('id')->where("deal_id=".$vo['id'])->findAll();
         * foreach($deal_point_user_event_list as $k=>$v){
         * $deal_user_point_list[ $k]['create_time']=date("Y-m-d",$v['create_time']);
         * $deal_user_point_list[ $k]['point_info']=$v['point_info'];
         * }
         */
        // var_dump($deal_user_point_list) ;
        // var_dump($deal_trade_event_list,empty($deal_trade_event_list));
        $this->assign("faq_list", $qa_list);
        // $this->assign("faq_event_list",$event_list);
        $this->assign("deal_see", $deal_see);
        // $this->assign("deal_point_list",$deal_point_list);
        // $this->assign("deal_user_point_list",$deal_user_point_list);
        $this->assign("deal_trade_event_list", $deal_trade_event_list);
        
        $this->display();
    }

    public function update()
    {
        
//         var_dump($_REQUEST);die();
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("name");
        // 开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array(
            "id" => $data['id']
        )));
        if (! check_empty($_REQUEST['name'])) {
            $this->error("请输入项目名称");
        }
        if (utf8_strlen($_REQUEST['name']) > 42) {
            $this->error("请确保项目名称在14个字以内");
        }
        
        if (! check_empty($_REQUEST['img_deal_logo'])) {
            $this->error("请上传项目logo");
        }
        if (! check_empty($_REQUEST['company_name'])) {
            $this->error("请输入公司名称");
        }
        
        if (! check_empty($_REQUEST['deal_sign'])) {
            $this->error("请输入项目简介");
        }
        if (utf8_strlen($_REQUEST['deal_sign']) > 84) {
            $this->error("请确保项目简介在28个字以内");
        }
        if (! check_empty($_REQUEST['company_brief'])) {
            $this->error("请输入公司简介");
        }
        if (utf8_strlen($_REQUEST['company_brief']) > 600) {
            $this->error("请确保公司简介在200个字以内");
        }
        
        if (empty($_REQUEST['team_name'])) {
            $this->error("请输入团队成员");
        }
        if (! check_empty($_REQUEST['deal_brief'])) {
            $this->error("请输入项目简介");
        }
        if (utf8_strlen($_REQUEST['deal_brief']) > 600) {
            $this->error("请确保项目简介在200个字以内");
        }
//         if (! check_empty($_REQUEST['period_id'])) {
//             $this->error("请输入项目阶段");
//         }
        if (empty($_REQUEST['cate_choose'])) {
            $this->error("请输入项目所属行业");
        }
        if (! check_empty($_REQUEST['province'])) {
            $this->error("请输入项目所在地区");
        }
        /*
         * if(!check_empty($_REQUEST['business_mode']))
         * {
         * $this->error("请输入商业模式");
         * }
         */
        /*
         * if(utf8_strlen($_REQUEST['business_mode'])>600)
         * {
         * $this->error("请确保商业模式在200个字以内");
         * }
         */
//         if (! preg_match("/^\d+$/", $_REQUEST['pe_amount_plan']) || intval($_REQUEST['pe_amount_plan']) <= 0) {
//             $this->error("请在预期融资输入数字");
//         }
        
//         if (! preg_match("/^\d+$/", $_REQUEST['pe_least_amount']) || intval($_REQUEST['pe_least_amount']) < 100) {
//             $this->error("请在起投金额输入数字");
//         }
        
        /*
         * if(empty($_REQUEST['user_point_event']))
         * {
         * $this->error("请输入项目亮点简介");
         * }
         */
        if (intval($_REQUEST['is_case']) == 1 && empty($_REQUEST['capital'])) {
            $this->error("成功案例必须填写投资回报信息");
        }
        // $data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
        // $data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
        // $data['create_time'] = get_gmtime();
        // $data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
        
        $data['spot_app_img'] = trim($_REQUEST['spot_app_img']);
        $data['spot_app_img_scale'] = trim($_REQUEST['spot_app_img_scale']);
        $data['spot_pc_img'] = trim($_REQUEST['spot_pc_img']);
        $data['spot_pc_img_scale'] = trim($_REQUEST['spot_pc_img_scale']);
        $data['profession_app_img'] = trim($_REQUEST['profession_app_img']);
        $data['profession_app_img_scale'] = trim($_REQUEST['profession_app_img_scale']);
        $data['profession_pc_img'] = trim($_REQUEST['profession_pc_img']);
        $data['profession_pc_img_scale'] = trim($_REQUEST['profession_pc_img_scale']);
        $data['vision_app_img'] = trim($_REQUEST['vision_app_img']);
        $data['vision_app_img_scale'] = trim($_REQUEST['vision_app_img_scale']);
        $data['vision_pc_img'] = trim($_REQUEST['vision_pc_img']);
        $data['vision_pc_img_scale'] = trim($_REQUEST['vision_pc_img_scale']);
        
        $data['img_deal_logo'] = trim($_REQUEST['img_deal_logo']);
        $data['img_deal_cover'] = trim($_REQUEST['img_deal_cover']);
        $data['img_deal_app_cover'] = trim($_REQUEST['img_deal_app_cover']);
        $data['bp_url'] = trim($_REQUEST['bp_url']);
//         $data['pe_amount_plan'] = $_REQUEST['pe_amount_plan'];
//         $data['pe_sell_scale'] = $_REQUEST['pe_sell_scale'];
//         $data['financing_amount'] = intval($_REQUEST['financing_amount']);
//         $data['pe_least_amount'] = intval($_REQUEST['pe_least_amount']);
//         $data['period_id'] = intval($_REQUEST['period_id']);
        // 1.0新增字段
        $data['entry_info'] = $_REQUEST['entry_info'];
        $data['solve_pain_info'] = $_REQUEST['solve_pain_info'];
        $data['recommend_reason'] = $_REQUEST['recommend_reason'];
//         $data['operation_info'] = $_REQUEST['operation_info'];
        $data['mark_data_info'] = $_REQUEST['mark_data_info'];
        $data['achievement_info'] = $_REQUEST['achievement_info'];
        $data['img_achievement'] = $_REQUEST['img_achievement'];
        $data['vision_info'] = $_REQUEST['vision_info'];
        $data['img_vision'] = $_REQUEST['img_vision'];
        $data['financing_info'] = $_REQUEST['financing_info'];
        $data['interview_time'] = ! empty($_REQUEST['interview_time']) ? strtotime($_REQUEST['interview_time']) : "";
        $data['com_time'] = ! empty($_REQUEST['com_time']) ? strtotime($_REQUEST['com_time']) : "";
        $data['focus_count'] = $_REQUEST['focus_count'];
        $data['support_count'] = $_REQUEST['support_count'];
        if($_REQUEST['old_is_effect'] == 0){
            $data['is_effect'] = 1;    # 编辑项目后，状态改变:申请中->编辑中
        }
        $data['update_time'] = time();
//         $version = 1;
        if (empty($data['support_count'])) {
            $data['support_count'] = 0;
        }
        $GLOBALS['db']->query("update " . DB_PREFIX . "deal set version = version+1 where id = " . $data['id']);
        $GLOBALS['db']->query("update " . DB_PREFIX . "deal set comment_count = " . $data['focus_count'] . "+" . $data['support_count'] . " where id = " . $data['id']);
        if (empty($data['interview_time'])) {
            $data['interview_time'] = NULL;
        } else {
            $data['interview_time'] = intval($data['interview_time']);
        }
        $data['profession_info'] = $_REQUEST['profession_info'];
        
        if ($_REQUEST['cate_choose']) {
            $data['cate_choose'] = implode("_", $_REQUEST['cate_choose']);
        }
        // 行业调查
        /*
         * M("deal_profession_data")->where(' deal_id='.$data['id'])->delete();
         * foreach($_REQUEST['data_info'] as $k=>$v)
         * {
         * //cixi_deal_sign_point
         * if(trim($v)!="" && trim($_REQUEST['data_info'][$k])!='')
         * {
         * $deal_profession_data = array();
         * $deal_profession_data['deal_id'] = $data['id'];
         * $deal_profession_data['create_time'] = time();
         * $deal_profession_data['data_info'] = trim($_REQUEST['data_info'][$k]);
         * M("deal_profession_data")->add($deal_profession_data);
         * }
         * }
         */
        // 商业模式
        M("deal_operation_steps")->where(' deal_id=' . $data['id'])->delete();
        foreach ($_REQUEST['opera_steps_name'] as $k => $v) {
            // cixi_deal_sign_point
            if (trim($_REQUEST['img_deal_opera_steps'][$k]) != '') {
                $deal_operation_steps = array();
                $deal_operation_steps['deal_id'] = $data['id'];
                $deal_operation_steps['create_time'] = time();
                $deal_operation_steps['opera_steps_name'] = trim($_REQUEST['opera_steps_name'][$k]);
                $deal_operation_steps['opera_steps_brief'] = trim($_REQUEST['opera_steps_brief'][$k]);
                $deal_operation_steps['img_deal_opera_steps'] = trim($_REQUEST['img_deal_opera_steps'][$k]);
                M("deal_operation_steps")->add($deal_operation_steps);
            }
        }
        // 发展现状
        M("deal_data_img")->where(' deal_id=' . $data['id'])->delete();
        foreach ($_REQUEST['img_data_url'] as $k => $v) {
            // cixi_deal_sign_point
            if (trim($v) != "" && trim($_REQUEST['img_data_url'][$k]) != '') {
                $deal_data_img = array();
                $deal_data_img['deal_id'] = $data['id'];
                $deal_data_img['create_time'] = time();
                $deal_data_img['img_data_url'] = trim($_REQUEST['img_data_url'][$k]);
                M("deal_data_img")->add($deal_data_img);
            }
        }
        // 推荐
        /*
         * M("deal_recommend")->where(' deal_id='.$data['id'])->delete();
         * foreach($_REQUEST['recommend_person'] as $k=>$v)
         * {
         * //cixi_deal_sign_point
         * if(trim($v)!="" && trim($_REQUEST['recommend_person'][$k])!='')
         * {
         * $deal_recommend = array();
         * $deal_recommend['deal_id'] = $data['id'];
         * $deal_recommend['create_time'] = time();
         * $deal_recommend['recommend_info'] = trim($_REQUEST['recommend_info'][$k]);
         * $deal_recommend['recommend_person'] = trim($_REQUEST['recommend_person'][$k]);
         * M("deal_recommend")->add($deal_recommend);
         * }
         * }
         */
        
        // 磁斯达克的采访
        /*
         * M("deal_interviem")->where(' deal_id='.$data['id'])->delete();
         * foreach($_REQUEST['problem_info'] as $k=>$v)
         * {
         * //cixi_deal_sign_point
         * if(trim($v)!="" && trim($_REQUEST['problem_info'][$k])!='')
         * {
         * $deal_interviem = array();
         * $deal_interviem['deal_id'] = $data['id'];
         * $deal_interviem['create_time'] = time();
         * $deal_interviem['problem_info'] = trim($_REQUEST['problem_info'][$k]);
         * $deal_interviem['answer_info'] = trim($_REQUEST['answer_info'][$k]);
         * M("deal_interviem")->add($deal_interviem);
         * }
         * }
         */
        M("deal_team")->where(' deal_id=' . $data['id'])->delete();
        // var_dump($_REQUEST['team_name']);die();
        foreach ($_REQUEST['team_name'] as $k => $v) {
            // cixi_deal_team
            if (trim($v) != "" || trim($_REQUEST['title'][$k]) != '') {
                $team = array();
                $team['deal_id'] = $data['id'];
                $team['name'] = trim($v);
                $team['title'] = trim($_REQUEST['title'][$k]);
                $team['brief'] = trim($_REQUEST['brief'][$k]);
                $team['img_logo'] = trim($_REQUEST['image_key'][$k]);
                // $team['image_url'] = trim($_REQUEST['image_key'][$k]);
                M("deal_team")->add($team);
            }
        }
        //
        /*
         * M("deal_event")->where(' deal_id='.$data['id'])->delete();
         * // var_dump($_REQUEST['brief_event']);
         * foreach($_REQUEST['create_time_event'] as $k=>$v)
         * {
         *
         * //cixi_deal_log
         * if(trim($v)!="" && trim($_REQUEST['brief_event'][$k])!='')
         * {
         * $event = array();
         * $event['deal_id'] = $data['id'];
         * $event['create_time'] =strtotime(trim($v));
         * $event['brief'] = trim($_REQUEST['brief_event'][$k]);
         * M("deal_event")->add($event);
         * }
         * }
         */
        
        // 项目亮点
        /*
         * M("deal_sign_point")->where(' deal_id='.$data['id'])->delete();
         * foreach($_REQUEST['point_event'] as $k=>$v)
         * {
         * //cixi_deal_sign_point
         * if(trim($v)!="" && trim($_REQUEST['point_event'][$k])!='')
         * {
         * $event = array();
         * $event['deal_id'] = $data['id'];
         * $event['create_time'] = time();
         * $event['point_info'] = trim($_REQUEST['point_event'][$k]);
         * M("deal_sign_point")->add($event);
         * }
         * }
         * //亮点数据
         * M("deal_brief_point")->where(' deal_id='.$data['id'])->delete();
         * foreach($_REQUEST['user_point_event'] as $k=>$v)
         * {
         * //cixi_deal_sign_point
         * if(trim($v)!="" && trim($_REQUEST['user_point_event'][$k])!='')
         * {
         * $event = array();
         * $event['deal_id'] = $data['id'];
         * $event['create_time'] = time();
         * $event['point_info'] = trim($_REQUEST['user_point_event'][$k]);
         * M("deal_brief_point")->add($event);
         * }
         * }
         */
        
        $list = M(MODULE_NAME)->save($data);
        // var_dump(M(MODULE_NAME)->getLastsql());die();
        M("Deal_select_cate")->where(' deal_id=' . $data['id'])->delete();
        if (! empty($_REQUEST['cate_choose'])) {
            foreach ($_REQUEST['cate_choose'] as $k => $v) {
                // 插入
                $deal_select_cate['cate_id'] = $v;
                $deal_select_cate['deal_id'] = $data['id'];
                M("Deal_select_cate")->add($deal_select_cate);
            }
        }
        
        if (false !== $list) {
            if (intval($_REQUEST['old_is_effect']) != 2 && intval($_REQUEST['is_effect']) == 2) {
                $user_notify['user_id'] = $_REQUEST['user_id'];
                $user_notify['log_info'] = "恭喜您的项目: " . $_REQUEST['s_name'] . "已成功发布上线，请到【项目介绍】中查看。";
                $user_notify['url'] = "http://www.cisdaq.com/xieyi.html";
                $user_notify['log_time'] = time();
                $user_notify['is_read'] = 0;
                M("user_notify")->add($user_notify);
            }
            // 成功提示
            M("DealFaq")->where("deal_id=" . $data['id'])->delete();
            foreach ($_REQUEST['question'] as $k => $v) {
                if (trim($v) != "" || trim($_REQUEST['answer'][$k]) != '') {
                    $qa = array();
                    $qa['deal_id'] = $data['id'];
                    $qa['question'] = trim($v);
                    $qa['answer'] = trim($_REQUEST['answer'][$k]);
                    $qa['sort'] = intval($k) + 1;
                    M("DealFaq")->add($qa);
                }
            }
            // syn_deal($data['id']);
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            // 错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M("Deal")->where("id=" . $id)->getField("name");
        if (! check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("Deal")->where("id=" . $id)->setField("sort", $sort);
        save_log($log_info . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }

    public function delete()
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
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M(MODULE_NAME)->where($condition)->setField("is_delete", 1);
            $list = M(MODULE_NAME)->where($condition)->setInc("version");
            
            if ($list !== false) {
                save_log($info . "成功移到回收站", 1);
                $this->success("成功移到回收站", $ajax);
            } else {
                save_log($info . "移到回收站出错", 0);
                $this->error("移到回收站出错", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function restore()
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
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M(MODULE_NAME)->where($condition)->setField("is_delete", 0);
            $list = M(MODULE_NAME)->where($condition)->setInc("version");
            if ($list !== false) {
                save_log($info . "恢复成功", 1);
                $this->success("恢复成功", $ajax);
            } else {
                save_log($info . "恢复出错", 0);
                $this->error("恢复出错", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function foreverdelete()
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
            $link_condition = array(
                'deal_id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
                M("DealFaq")->where($link_condition)->delete();
                M("DealComment")->where($link_condition)->delete();
                M("DealFocusLog")->where($link_condition)->delete();
                M("DealItem")->where($link_condition)->delete();
                M("DealItemImage")->where($link_condition)->delete();
                M("DealOrder")->where($link_condition)->delete();
                M("DealPayLog")->where($link_condition)->delete();
                M("DealSupportLog")->where($link_condition)->delete();
                M("DealVisitLog")->where($link_condition)->delete();
                M("DealLog")->where($link_condition)->delete();
                M("UserDealNotify")->where($link_condition)->delete();
                M("DealNotify")->where($link_condition)->delete();
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

    public function del_deal_item()
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
            $rel_data = M("DealItem")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $deal_id = $data['deal_id'];
                $info[] = format_price($data['price']);
            }
            if ($info)
                $info = implode(",", $info);
            $info = "项目ID" . $deal_id . ":" . $info;
            $list = M("DealItem")->where($condition)->delete();
            if ($list !== false) {
                
                // syn_deal($deal_id);
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
    
    // 管理融资记录
    public function event_list()
    {
        $id = intval($_REQUEST['deal_id']);
        
        // 当前融资状态“已投资”的项目 没有【添加融资记录】的权限
        $effect      = M('deal')->where('id='.$id)->getField('is_effect');
        $deal_period = M('Deal_period')->order("sort asc")->getField('id,name');
        
        $result = $GLOBALS['db']->getAll("SELECT dte.* FROM  " . DB_PREFIX . "deal_trade_event AS dte INNER JOIN " . DB_PREFIX . "deal_period AS dp ON dte.period = dp.id WHERE dte.deal_id = " . $id . " ORDER BY dp.sort DESC");
        // foreach ($result as $k => $v) 
        for($k=0,$l=count($result);$k<$l;$k++){
        	$v = $result[$k];
            $result[$k]['period']           = $deal_period[$v['period']];
            $result[$k]['investor_amount']  = M('deal_trade_fund_relation')->where('deal_trade_event_id='.$v['id'])->sum('investor_amount');
            $result[$k]['investor_time']    = $result[$k]['investor_time'] ? date('Y-m-d', $v['investor_time']) : '-';
            $result[$k]['investor_amount']  = $result[$k]['investor_amount'] ? $result[$k]['investor_amount'] : 0;
            $result[$k]['investor_record_type_cn'] = $v['investor_record_type'] == '1' ? '是' : '否';
            if($v['investor_record_type'] == '1' && $result[$k]['investor_amount'] == 0){
                $result[$k]['can_edit'] = 1;
            }else{
                $result[$k]['can_edit'] = 0;
            }
            // 如果当前维护的最近一个轮次的基金，则可以修改投资基金信息，否则不可以修改投资基金信息
            if($k != 0){
            	$result[$k]['can_edit_fund'] = 0;
            } else{
            	$result[$k]['can_edit_fund'] = 1;
            }
            
        }
        $this->assign("effect", $effect == 3 ? 0 : 1);
        $this->assign("list", $result);
        $this->display('event_list');
    }

    public function event_add()
    {
        $deal_id = intval($_REQUEST['deal_id']);
        $this->assign("deal_id", $deal_id);
        // 已投项目不能操作
        if(M('deal')->where('id='.$deal_id)->getField('is_effect') == 3){
            $this->error("已投项目不能添加融资记录");
        }
        
        // 只显示可投资的轮次，目前按sort去排除，如果sort规则有变动，请同步修改此处逻辑 sunerju 2016-08-01
        $period_has     = M('deal_trade_event')->where('deal_id = '.$deal_id)->getField('id,period');
        $max_sort       = M('Deal_period')->where('id in('.implode(',',$period_has).')')->max('sort');
        $deal_period    = M('Deal_period')->where('sort > '.$max_sort)->order("sort asc")->select();
        //如果没有可投的轮次，显示所有轮次
        if($deal_period==false){
        $deal_period    = M('Deal_period')->order("sort asc")->select();
         $this->assign('deal_period', $deal_period);
        }else{
         $this->assign('deal_period', $deal_period);
        }
        // 所有磁系基金
        $fund_list = $GLOBALS['db']->getAll("select id,name,short_name from " . DB_PREFIX . "fund where is_csdk_fund=1 and is_delete=1 and status=2 order by id desc"); // 二级地址
        $this->assign("fund_list", $fund_list);
        
        // 是否有最新轮次
        $record_type = M('deal_trade_event')->where('deal_id='.$deal_id.' and investor_record_type=1')->find();
        $this->assign('is_record_type',$record_type ? 1 : 0);
        
        $this->display('event_add');
    }

    public function event_insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(deal_trade_event)->create();
        // 开始验证有效性
        if($data['investor_record_type'] == 1 && M('deal_trade_event')->where('deal_id='.$data['deal_id'].' and investor_record_type=1')->find()){
            $this->error("已有最新轮次，不能再添加");
        }
        if (! check_empty($data['investor_record_type'])) {
            $this->error("请选择轮次信息");
        }
        if (! check_empty($data['period'])) {
            $this->error("请选择融资轮次");
        }
        if (! check_empty($data['pe_amount_plan'])) {
            $this->error("请输入预期融资金额");
        }
        if (! check_empty($data['pe_sell_scale'])) {
            $this->error("请输入出让股权");
        }
        if (! check_empty($data['investor_before_evalute'])) {
            $this->error("请输入融资前估值");
        }
        if($data['investor_record_type'] == 2){
        
            $data2 = M(deal_trade_fund_relation)->create();
            $name_tmps = $fund_tmps = array();
            foreach($data2['is_csdk_fund'] as $k=>$v){
                
                if (! check_empty($data2['investor_date'][$k])) {
                    $this->error("请输入投资日期");
                }
                // 是否磁斯达克参与
                if($v == 2){
                    if (in_array(trim($_REQUEST['name'][$k]),$name_tmps) !== false) {
                        $this->error("{$_REQUEST['name'][$k]}基金重复");
                    }
                    $name_tmps[$k] = trim($_REQUEST['name'][$k]);
                    if (! check_empty($_REQUEST['name'][$k])) {
                        $this->error("请输入参与基金名称");
                    }
                }else{
                    if (in_array(trim($_REQUEST['fund_id'][$k]),$fund_tmps) !== false) {
                        $this->error("{$_REQUEST['short_name'][$k]}基金重复");
                    }
                    $fund_tmps[$k] = trim($_REQUEST['fund_id'][$k]);
                    if (! check_empty($data2['fund_id'][$k])) {
                        $this->error("请选择参与基金");
                    }
                }
                
                if (! check_empty($_REQUEST['short_name'][$k])) {
                    $this->error("请输入基金简称");
                }
                if (! check_empty($data2['investor_amount'][$k])) {
                    $this->error("请输入投资金额");
                }
            }
        }
        
        $this->assign("jumpUrl", u(MODULE_NAME . "/event_list", array(
            'deal_id' => $data['deal_id']
        )));
        
        // 添加融资记录
        if($data['investor_record_type'] == 1){
            $add_data = array(
                'deal_id'               => $data['deal_id'],
                'period'                => $data['period'],
                'create_time'           => time(),
                'pe_amount_plan'        => $data['pe_amount_plan'],
                'investor_before_evalute'         => $data['investor_before_evalute'],
                'pe_sell_scale'         => $data['pe_sell_scale'],
                'investor_record_type'  => $data['investor_record_type'],
            );
            $result = M('deal_trade_event')->add($add_data);

            if (false !== $result) {
                // 如果选择当前轮次，项目的状态变为“拟投资”，融资轮次为当前轮次
                M('deal')->where('id='.$data['deal_id'])->save(array('is_effect'=>2,'period_id'=>$data['period']));
                M(MODULE_NAME)->where('id='.$data['deal_id'])->setInc("version");
                // 成功提示
                save_log('添加融资记录' . L("INSERT_SUCCESS"), 3);
                $this->success(L("INSERT_SUCCESS"));
            }
            
        }else{
            $data['create_time']    = time();
            // $data['investor_time']  = strtotime($data['investor_time']);
            unset($data['investor_payback']);   #删掉多余字段
            $add_data   = $data;

            $result     = M('deal_trade_event')->add($add_data);
            
            if (false !== $result) {
                save_log('添加融资记录' . L("INSERT_SUCCESS"), 3);
                
                $adm_session    = es_session::get(md5(conf("AUTH_KEY")));
                $adm_id       = $adm_session['adm_id'];
                
                // 添加投资机构
                foreach($data2['is_csdk_fund'] as $k=>$v){
                    if($v == 2){
                        // 不是csdq
                        $fund_data = array(
                            'name'          => $_REQUEST['name'][$k],
                            'short_name'    => $_REQUEST['short_name'][$k],
                            'status'        => 2,
                            'operator'      => $adm_id,
                            'update_time'   => time(),
                            'is_delete'     => 1,
                            'is_csdk_fund'  => 2,
                        );
                        $fund_id_tmp = M('fund')->add($fund_data);
                    }
                    $add_data2 = array(
                        'deal_trade_event_id'   => $result,
                        'deal_id'               => $data2['deal_id'],
                        'fund_id'               => $v == 2 ? $fund_id_tmp : $data2['fund_id'][$k],
                        'investor_amount'       => $data2['investor_amount'][$k],
                        // 'investor_rate'         => $data2['investor_rate'][$k],
                        'investor_date'         => strtotime($data2['investor_date'][$k]),
                        'is_csdk_fund'          => $data2['is_csdk_fund'][$k],
                        // 'remark'                => $data2['remark'][$k],
                        'create_time'           => time(),
                        'update_time'           => time(),
                        'operator'              => $adm_id,
                    );
                    
                    // 不做成功失败判断了，后期放事务处理（不放事务判断没有意义）
                    M('deal_trade_fund_relation')->add($add_data2);
                }
                
                M(MODULE_NAME)->where('id='.$data['deal_id'])->setInc("version");
                
                // 执行基金投资占比、基金投资人收益计算任务
                $param = array(
                		'dealId' => $data2['deal_id']
                );
                request_service_api('Deal.NewDealInvestCalc.calcDealData',$param);
                $this->success(L("INSERT_SUCCESS"));
            }
            
        }
        
        // 错误提示
        save_log('添加融资记录' . L("INSERT_FAILED"), 0);
        $this->error(L("INSERT_FAILED"));
    }

    function deal_event_see(){
        $id = intval($_REQUEST['id']);
        $edit = intval($_REQUEST['edit']);
        $can_edit = intval($_REQUEST['can_edit']);
        // 融资记录
        $event = M('deal_trade_event')->where('id='.$id)->find();
        $event['period'] = M('deal_period')->where('id='.$event['period'])->getField('name');
        $event['pe_evalute'] = round($event['pe_amount_plan']/$event['pe_sell_scale']*100,2);
        $event['investor_time'] = date("Y-m-d",$event['investor_time']);
        
        // 投资基金
        $fund = M('deal_trade_fund_relation')->where('deal_trade_event_id='.$event['id'])->select();
        if($fund){
            foreach($fund as $k=>&$v){
                $v['investor_date'] = date("Y-m-d",$v['investor_date']);
                $tmp = M('fund')->where('id='.$v['fund_id'])->find();
                $v['name'] = $tmp['name'];
                $v['short_name'] = $tmp['short_name'];
            }
            unset($v);
            $isfund=1;
        }else{
            $isfund=0;
        }
       
        // 所有磁系基金
        $fund_list = $GLOBALS['db']->getAll("select id,name,short_name from " . DB_PREFIX . "fund where is_csdk_fund=1 and is_delete=1 and status=2 order by id desc"); // 二级地址
        $this->assign("fund_list", $fund_list);
        
        $this->assign('event',$event);
        $this->assign('fund',$fund);
        $this->assign('isfund',$isfund);
        $this->assign('can_edit',$can_edit);
        if($edit){
            $this->display('deal_trade_fund_edit');
        }else{
            $this->display();
        }
    }
    
    function deal_trade_event_edit(){
        $id = intval($_REQUEST['id']);
        
        // 融资记录
        $event = M('deal_trade_event')->where('id='.$id)->find();
        $event['pe_evalute'] = round($event['pe_amount_plan']/$event['pe_sell_scale']*100,2);
        $this->assign('event',$event);
        
        // 融资轮次
        $deal_period = M('Deal_period')->order("sort asc")->select();
        $this->assign('deal_period', $deal_period);
        
        $this->display();
    }
    
    function deal_trade_event_update(){
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(deal_trade_event)->create();
        // 开始验证有效性
        // 预期融资信息
        if (! check_empty($data['period'])) {
            $this->error("请选择融资轮次");
        }
        if (! check_empty($data['pe_amount_plan'])) {
            $this->error("请输入预期融资金额");
        }
        if (! check_empty($data['pe_sell_scale'])) {
            $this->error("请输入出让股权");
        }
         if (! check_empty($data['investor_before_evalute'])) {
            $this->error("请输入融资前估值");
        }

        $this->assign("jumpUrl", u(MODULE_NAME . "/event_list", array(
            'deal_id' => $data['deal_id']
        )));
        
        $add_data = array(
            'id'                    => $data['id'],
            'period'                => $data['period'],
            'pe_amount_plan'        => $data['pe_amount_plan'],
            'pe_sell_scale'         => $data['pe_sell_scale'],
            'investor_before_evalute'         => $data['investor_before_evalute'],
        );
        $result = M('deal_trade_event')->save($add_data);
        if (false !== $result) {
            M(MODULE_NAME)->where('id='.$_REQUEST['deal_id'])->setInc("version");
            // 成功提示
            save_log('更新融资记录' .$data['id']. L("UPDATE_SUCCESS"), 3);
            $this->success(L("UPDATE_SUCCESS"));
        }else{
            // 错误提示
            save_log('更新融资记录' .$data['id']. L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"));
        }
    }
    
    /*
     *
     * @since 2.1.1 对于cixi_user_fund_deal_relation的收益计算，在更新数据前，首先处理原有数据 suenrju
     *              规则：删除与该项目相关的收益（所有基金，所有投资人）
     */
    function deal_trade_fund_update(){
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        // 投资基金
        $data2 = M(deal_trade_fund_relation)->create();
        
        $name_tmps = $fund_tmps = array();
        foreach($data2['is_csdk_fund'] as $k=>$v){
        
            if (! check_empty($data2['investor_date'][$k])) {
                $this->error("请输入投资日期");
            }
        
            // 是否磁斯达克参与
            if($v == 2){
                if (in_array(trim($_REQUEST['name'][$k]),$name_tmps) !== false) {
                    $this->error("{$_REQUEST['name'][$k]}基金重复");
                }
                $name_tmps[$k] = trim($_REQUEST['name'][$k]);
                if (! check_empty($_REQUEST['name'][$k])) {
                    $this->error("请输入参与基金名称");
                }
            }else{
                if (in_array(trim($_REQUEST['fund_id'][$k]),$fund_tmps) !== false) {
                    $this->error("{$_REQUEST['short_name'][$k]}基金重复");
                }
                $fund_tmps[$k] = trim($_REQUEST['fund_id'][$k]);
                if (! check_empty($data2['fund_id'][$k])) {
                    $this->error("请选择参与基金");
                }
            }
        
            if (! check_empty($_REQUEST['short_name'][$k])) {
                $this->error("请输入基金简称");
            }
            if (! check_empty($data2['investor_amount'][$k])) {
                $this->error("请输入投资金额");
            }
        
        }
        
        $this->assign("jumpUrl", u(MODULE_NAME . "/event_list", array(
            'deal_id' => $data2['deal_id']
        )));
        
        $adm_session  = es_session::get(md5(conf("AUTH_KEY")));
        $adm_id       = $adm_session['adm_id'];
        
        // 投资机构
        foreach($data2['is_csdk_fund'] as $k=>$v){
            
            if($data2['id'][$k]){
                // 更新
                $old_fund = M('fund')->where('id='.$_REQUEST['old_fund_id'][$k])->find();
                if($v == 2){
                    // 不是csdq
                    if($old_fund['is_csdk_fund'] == 1){
                        // 若旧机构是csdq，新的不是，则添加新的基金->update
                        $fund_data = array(
                            'name'          => $_REQUEST['name'][$k],
                            'short_name'    => $_REQUEST['short_name'][$k],
                            'status'        => 2,
                            'operator'      => $adm_id,
                            'update_time'   => time(),
                            'is_delete'     => 1,
                            'is_csdk_fund'  => 2,
                        );
                        $fund_id_tmp = M('fund')->add($fund_data);
                    }else{
                        // 若旧机构不是csdq，新的不是，则比较两个基金，相同不操作，不同del->add->update
                        if($old_fund['name'] == $_REQUEST['name'][$k]){
                            $fund_id_tmp = $old_fund['id'];
                        }else{
                            M('fund')->where('id='.$old_fund['id'])->delete();
                            $fund_data = array(
                                'name'          => $_REQUEST['name'][$k],
                                'short_name'    => $_REQUEST['short_name'][$k],
                                'status'        => 2,
                                'operator'      => $adm_id,
                                'update_time'   => time(),
                                'is_delete'     => 1,
                                'is_csdk_fund'  => 2,
                            );
                            $fund_id_tmp = M('fund')->add($fund_data);
                        }
                    }
                    
                }else{
                    // 是csdq
                    if($old_fund['is_csdk_fund'] == 1){
                        // 若旧机构是csdq，新的是，则更新fund_id即可
                        $fund_id_tmp = $data2['fund_id'][$k];
                    }else{
                        // 若旧机构不是csdq，新的是，则del->update
                        M('fund')->where('id='.$old_fund['id'])->delete();
                        $fund_id_tmp = $data2['fund_id'][$k];
                    }
                }
                
                $up_data = array(
                    'fund_id'           => $fund_id_tmp,
                    'investor_amount'   => $data2['investor_amount'][$k],
                    // 'investor_rate'     => $data2['investor_rate'][$k],
                    'investor_date'     => strtotime($data2['investor_date'][$k]),
                    'is_csdk_fund'      => $data2['is_csdk_fund'][$k],
                    'update_time'       => time(),
                    'operator'          => $adm_id,
                );
                M('deal_trade_fund_relation')->where('id='.$data2['id'][$k])->save($up_data);
                
            }else{
                // 新增
                if($v == 2){
                    // 不是csdq
                    $fund_data = array(
                        'name'          => $_REQUEST['name'][$k],
                        'short_name'    => $_REQUEST['short_name'][$k],
                        'status'        => 2,
                        'operator'      => $adm_id,
                        'update_time'   => time(),
                        'is_delete'     => 1,
                        'is_csdk_fund'  => 2,
                    );
                    $fund_id_tmp = M('fund')->add($fund_data);
                }
                
                $add_data2 = array(
                    'deal_trade_event_id'   => (int)$_REQUEST['deal_trade_event_id'],
                    'fund_id'               => $v == 2 ? $fund_id_tmp : $data2['fund_id'][$k],
                    'deal_id'               => $data2['deal_id'],
                    'investor_amount'       => $data2['investor_amount'][$k],
                    //'investor_rate'         => $data2['investor_rate'][$k], 
                    'investor_date'         => strtotime($data2['investor_date'][$k]),
                    'is_csdk_fund'          => $data2['is_csdk_fund'][$k],
                    'create_time'           => time(),
                    'update_time'           => time(),
                    'operator'              => $adm_id,
                );
                
                // 不做成功失败判断了，后期放事务处理（不放事务判断没有意义）
                M('deal_trade_fund_relation')->add($add_data2);
                
            }
        }  
        // 已投，更新项目融资轮次
        if(M('deal')->where('id='.$data2['deal_id'])->getField('is_effect') == 3){
            $event = M('deal_trade_fund_relation')->where('deal_id='.$data2['deal_id'].' and is_csdk_fund=1')->order('investor_date desc')->find();
            if($event){
                $period_id = M('deal_trade_event')->where('id='.$event['deal_trade_event_id'])->getField('period');
                M('deal')->where('id='.$data2['deal_id'])->setField('period_id',$period_id);
            }
        }
        
        M(MODULE_NAME)->where('id='.$data2['deal_id'])->setInc("version");
        
        // 处理收益数据（因为投资机构如果修改了，旧数据应该删除，服务层单用REPLACE INTO无法实现）
        M('user_fund_deal_relation')->where('deal_id = '.$data2['deal_id'])->delete();
        
        // 执行基金投资占比、基金投资人收益计算任务
        $param = array(
        		'dealId' => $data2['deal_id']
        );
        request_service_api('Deal.NewDealInvestCalc.calcDealData',$param);
        
        $this->success(L("UPDATE_SUCCESS"));
        
    }

    function deal_trade_fund_del(){
        $id = intval($_REQUEST['id']);
        
        if(!$id){
            $this->ajaxReturn('','无法获取参数信息',0);
        }
        // 至少要有一个投资基金
        $info = M('deal_trade_fund_relation')->where('id='.$id)->find();
        if(M('deal_trade_fund_relation')->where('deal_trade_event_id='.$info['deal_trade_event_id'])->count('id') == 1){
            $this->ajaxReturn('','至少要有一个投资基金',0);
        }
        
        // 删除投资基金：若是csdq直接删除，若不是，则先删除基金，再删除记录
        if($info['is_csdk_fund'] == 1){
            M('deal_trade_fund_relation')->where('id='.$id)->delete();
        }else{
            M('fund')->where('id='.$info['fund_id'])->delete();
            M('deal_trade_fund_relation')->where('id='.$id)->delete();
        }
        M(MODULE_NAME)->where('id='.$info['deal_id'])->setInc("version");
        
        // 执行基金投资占比、基金投资人收益计算任务
        $param = array(
        		'dealId' => $info['deal_id']
        );
        request_service_api('Deal.NewDealInvestCalc.calcDealData',$param);
        
        $this->ajaxReturn('','删除成功',1);
    }
    function event_edit()
    {
        $id = intval($_REQUEST['id']);
        $deal_period = M('Deal_period')->order("sort asc")->select();
        $this->assign('deal_period', $deal_period);
        $fund_list = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "fund where is_delete=1  order by id desc"); // 二级地址
        $this->assign("fund_list", $fund_list);
        // 准表单数据
        $vo = M(deal_trade_event)->where("id=" . $id)->find();
        $vo['investor_time'] = date("Y-m-d", $vo['investor_time']);
        $this->assign('vo', $vo);
        
        $this->display('event_edit');
    }

    function event_update()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(deal_trade_event)->create();
        
        // 开始验证有效性
        // 预期融资信息
        if (! check_empty($data['period'])) {
            $this->error("请选择融资轮次");
        }
        if (! check_empty($data['pe_amount_plan'])) {
            $this->error("请输入预期融资金额");
        }
        if (! check_empty($data['pe_sell_scale'])) {
            $this->error("请输入出让股权");
        }
        
        if($data['investor_record_type'] == 2){
        
            // 投资基金
            $data2 = M(deal_trade_fund_relation)->create();
            foreach($data2['is_csdk_fund'] as $k=>$v){
        
                if (! check_empty($data2['investor_date'][$k])) {
                    $this->error("请输入投资日期");
                }
        
                // 是否磁斯达克参与
                if($v == 2){
                    if (! check_empty($_REQUEST['name'][$k])) {
                        $this->error("请输入参与基金名称");
                    }
                }else{
                    if (! check_empty($data2['fund_id'][$k])) {
                        $this->error("请选择参与基金");
                    }
                }
        
                if (! check_empty($_REQUEST['short_name'][$k])) {
                    $this->error("请输入基金简称");
                }
                if (! check_empty($data2['investor_amount'][$k])) {
                    $this->error("请输入投资金额");
                }
                if (! check_empty($data2['investor_rate'][$k])) {
                    $this->error("请输入投资占比");
                }
                if (! check_empty($data2['remark'][$k])) {
                    $this->error("请输入备注");
                }
        
            }
        }
        
//         $this->assign("jumpUrl", u(MODULE_NAME . "/event_list", array(
//             'deal_id' => $data['deal_id']
//         )));
        
        // 更新预期融资信息
        if($data['investor_record_type'] == 1){
            $add_data = array(
                'id'                    => $data['id'],
                'period'                => $data['period'],
                'pe_amount_plan'        => $data['pe_amount_plan'],
                'pe_sell_scale'         => $data['pe_sell_scale'],
            );
            $result = M('deal_trade_event')->save($add_data);
            if (false !== $result) {
                M(MODULE_NAME)->where('id='.$data['deal_id'])->setInc("version");
                // 成功提示
                save_log('更新融资记录' .$data['id']. L("UPDATE_SUCCESS"), 3);
                $this->success(L("UPDATE_SUCCESS"));
            }
        
        }else{
        
            if (false !== $result) {
                save_log('更新融资记录' .$data['id']. L("UPDATE_SUCCESS"), 3);
        
                $adm_session    = es_session::get(md5(conf("AUTH_KEY")));
                $adm_id       = $adm_session['adm_id'];
        
                // 添加投资机构
                foreach($data2['is_csdk_fund'] as $k=>$v){
                    if($v == 2){
                        // 不是csdq
                        $fund_data = array(
                            'name'          => $_REQUEST['name'][$k],
                            'short_name'    => $_REQUEST['short_name'][$k],
                            'status'        => 2,
                            'operator'      => $adm_id,
                            'update_time'   => time(),
                            'is_delete'     => 1,
                            'is_csdk_fund'  => 2,
                        );
                        $fund_id_tmp = M('fund')->add($fund_data);
                    }
        
                    $add_data2 = array(
                        'deal_trade_event_id'   => $result,
                        'deal_id'               => $data2['deal_id'],
                        'fund_id'               => $v == 2 ? $fund_id_tmp : $data2['fund_id'][$k],
                        'investor_amount'       => $data2['investor_amount'][$k],
                        'investor_rate'         => $data2['investor_rate'][$k],
                        'investor_date'         => strtotime($data2['investor_date'][$k]),
                        'is_csdk_fund'          => $data2['is_csdk_fund'][$k],
                        'remark'                => $data2['remark'][$k],
                        'create_time'           => time(),
                        'update_time'           => time(),
                        'operator'              => $adm_id,
                    );
        
                    // 不做成功失败判断了，后期放事务处理（不放事务判断没有意义）
                    M('deal_trade_fund_relation')->add($add_data2);
                }
                M(MODULE_NAME)->where('id='.$data['deal_id'])->setInc("version");
                $this->success(L("UPDATE_SUCCESS"));
            }
        
        }
        
        // 错误提示
        save_log('更新融资记录' .$data['id']. L("UPDATE_FAILED"), 0);
        $this->error(L("UPDATE_FAILED"));
    }

    function event_del()
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
            $rel_data = M(deal_trade_event)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M(deal_trade_event)->where($condition)->delete();
            if ($list !== false) {
                save_log($info . "已删除", 1);
                $this->success("已删除", $ajax);
            } else {
                save_log($info . "删除失败", 0);
                $this->error("删除失败", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
    
    // 管理融资机构
    public function investor_list()
    {
        $id = intval($_REQUEST['event_id']);
        $result = $GLOBALS['db']->getAll("select  *  from " . DB_PREFIX . "deal_event_investor  where event_id=" . $id . " ORDER BY id desc");
        foreach ($result as $k => $v) {
            if ($v['is_csdk_partake'] == '1') {
                $result[$k]['is_csdk_partake'] = '是';
            } else {
                $result[$k]['is_csdk_partake'] = '否';
            }
            $result[$k]['create_time'] = date('Y-m-d', $v['create_time']);
        }
        $this->assign("list", $result);
        $this->display('investor_list');
    }

    public function investor_add()
    {
        $event_id = intval($_REQUEST['event_id']);
        $this->assign("event_id", $event_id);
        $this->display('investor_add');
    }

    public function investor_insert()
    {
        
        // var_dump($_REQUEST['team_name']);die();
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(deal_event_investor)->create();
        
        /**
         * *********ADD get initiator user id which will be insert into cixi_deal table*****************************
         */
        
        // 开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/investor_add", array(
            'event_id' => $_REQUEST['event_id']
        )));
        
        // if(!check_empty($data['o_name']))
        // {
        // $this-> error("请输入基金管理人");
        // }
        
        $data['create_time'] = time();
        $data['event_id'] = $_REQUEST['event_id'];
        
        $list = M(deal_event_investor)->add($data);
        // $list_1=M('user')->add($user_data);
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

    function investor_edit()
    {
        $id = intval($_REQUEST['id']);
        // 准表单数据
        $vo = M(deal_event_investor)->where("id=" . $id)->find();
        $this->assign('vo', $vo);
        
        $this->display('investor_edit');
    }

    function investor_update()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(deal_event_investor)->create();
        
        /**
         * *********ADD get initiator user id which will be insert into cixi_deal table*****************************
         */
        
        // 开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/investor_edit", array(
            "id" => $data['id']
        )));
        
        if (! check_empty($data['investor_amount'])) {
            $this->error("请输入购买份额");
        }
        
        if (! check_empty($data['investor_rate'])) {
            $this->error("请输入购买占比");
        }
        $data['event_id'] = $data['event_id'];
        $list = M(deal_event_investor)->save($data);
        
        if (false !== $list) {
            // 成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            // 错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"));
        }
    }

    function investor_del()
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
            $rel_data = M(deal_event_investor)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M(deal_event_investor)->where($condition)->delete();
            
            if ($list !== false) {
                save_log($info . "已删除", 1);
                $this->success("已删除", $ajax);
            } else {
                save_log($info . "删除失败", 0);
                $this->error("删除失败", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    function deal_intended()
    {
        // 扭转拟投上线
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(deal)->create();
        
        $data['id'] = $_REQUEST['id'];
        $GLOBALS['db']->query("update " . DB_PREFIX . "deal set version = version+1 where id = " . $data['id']);
        $data['is_effect'] = 2;
        $list = M(deal)->save($data);
        if ($list !== false) {
            save_log($info . "", 1);
            $this->success("拟投上线成功", $ajax);
        } else {
            save_log($info . "拟投上线失败", 0);
            $this->error("拟投上线失败", $ajax);
        }
    }

    /*
     * 融资完成，实现各种状态转换+发送短息
     * @param int $id 项目ID
     */
    function deal_completion_check()
    {
        $deal_id = (int)$_REQUEST['id'];
        
        if(!empty($deal_id)){
           
            // 预期融资信息 + 实际融资信息（为空） cixi_deal_trade_event
            $trade = M('deal_trade_event')->where("deal_id={$deal_id} and investor_record_type=1")->find();
            // 判断有无投资基金
            if(M('deal_trade_fund_relation')->where('deal_trade_event_id='.$trade['id'])->find()){
				//融资完成项目状态变更
              	$res = M('deal')->where('id='.$deal_id)->setField('is_effect',3); #拟投->已投
              	if($res === false){
              		save_log($deal_id . "融资完成失败：修改项目融资状态失败", 0);
              		$this->error("融资完成失败：修改项目融资状态失败", 0);
              		exit();
              	}
				//最新融资轮次变更为历史轮次
				$re = M('deal_trade_event')->where("id={$trade['id']}")->setField('investor_record_type',2);
				if ($re === false) {
					save_log($data['deal_id'] . "融资完成失败：保存实际融资信息失败", 0);
					$this->error("融资完成失败：保存实际融资信息失败", 0);
					exit();
				}
				// 项目版本号加1
				M(MODULE_NAME)->where('id='.$deal_id)->setInc("version");
				// 给该项目的所有投资人发送短信
				if($trade){
					// 查询基金(csdq)
					$fund_id = M('deal_trade_fund_relation')->where('deal_trade_event_id='.$trade['id'].' and is_csdk_fund=1')->getField('id,fund_id');
					if($fund_id){
						// 更新项目融资轮次
						M('deal')->where('id='.$trade['deal_id'])->setField('period_id',$trade['period']);
				
						$fund_id = implode(',',$fund_id);
						// 查询基金的所有投资人
						$user_fid = M('user_fund_relation')->field('id,fund_id,user_id')->where('fund_id in('.$fund_id.') and user_type = 1')->select();
						if($user_fid){
						    
							$deal_name  = M('deal')->where('id='.$trade['deal_id'])->getField('s_name');
							$period     = M('deal_period')->where('id='.$trade['period'])->getField('name');
						    
						    foreach ($user_fid as $k=>$v){
						        $uf[$v['user_id']][] = $v['fund_id'];
						    }
						    
							foreach($uf as $kv=>$uv){
							    $fund_name = M('fund')->where('id in('.implode(',',$uv).')')->getField('id,short_name');
							    $msg       = getSendSmsTemplate("admin_finance_finish",array(implode('，',$fund_name),$deal_name,$period));
							    
    							// 发送站内信
								$user_notify = array(
										'user_id'   => $kv,
										'log_info'  => $msg,
										'url'       => "",
										'log_time'  =>time(),
										'is_read'   => 0,
								);
								$a = M("user_notify")->add($user_notify);
								
								// 发送短信
    							$mobile = M('user')->where('id = '.$kv)->getField('mobile');
								$params = array(
								    "mobile"    => $mobile,
								    "content"   => $msg,
								    "type"      => getSendSmsType("admin_finance_finish"),
								);
								$b = request_service_api("Common.Sms.sendSms",$params);
							}
						}
					}else{
						// 更新项目融资轮次
						$event = M('deal_trade_fund_relation')->where('deal_id='.$trade['deal_id'].' and is_csdk_fund=1')->order('investor_date desc')->find();
						if($event){
							$period_id = M('deal_trade_event')->where('id='.$event['deal_trade_event_id'])->getField('period');
							M('deal')->where('id='.$trade['deal_id'])->setField('period_id',$period_id);
						}
					}
				}
				
				save_log($data['deal_id'] . "融资完成成功", 1);
                $this->success(array('info'=>l("success"),'id'=>$trade['id']),1);
            }else{
                $this->error(l("请先维护最新轮次的投资基金信息（入口：管理融资记录->管理投资基金）"), 1);
            }
        }else{
            $this->error(l("INVALID_OPERATION"), 1);
        }
    }
    
    /*
     * 融资完成成功后，跳转页面
     * @param int $id 最新的融资记录ID（即更新前是当前轮次）
     */
    function deal_completion()
    {
        $id = (int)$_REQUEST['id'];
        
        if(!empty($id)){
            
            // 预期融资信息 + 实际融资信息（为空） cixi_deal_trade_event
            $event = M('deal_trade_event')->where("id={$id}")->find();
            $event['period'] = M('deal_period')->where('id='.$event['period'])->getField('name');
            $event['pe_evalute'] = round($event['pe_amount_plan']/$event['pe_sell_scale']*100,2);
            $event['investor_time'] = date('Y-m-d',$event['investor_time']);
            
            // 投资基金
            $fund = M('deal_trade_fund_relation')->where('deal_trade_event_id='.$event['id'])->select();
            if($fund){
            	foreach($fund as $k=>&$v){
            		$v['investor_date'] = date("Y-m-d",$v['investor_date']);
            		$tmp = M('fund')->where('id='.$v['fund_id'])->find();
            		$v['name'] = $tmp['name'];
            		$v['short_name'] = $tmp['short_name'];
            	}
            	unset($v);
            }
            // 所有磁系基金
            $fund_list = $GLOBALS['db']->getAll("select id,name,short_name from " . DB_PREFIX . "fund where is_csdk_fund=1 and is_delete=1 and status=2 order by id desc"); // 二级地址
            $this->assign("fund_list", $fund_list);
            
            $this->assign("fund",$fund);
            $this->assign('event',$event);
            $this->display();
        }else{
            $this->error(l("INVALID_OPERATION"), 0);
        }
    }
    
    /**
     * 2.0.1版本及之前适用，2.1版本后不再使用
     */
    function do_deal_completion()
    {
        // 融资完成上线
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M('deal_trade_event')->create();
        
        // 开始验证有效性
        if (!check_empty((int)$data['id'])) {
            $this->error("非法操作");
        }
        if (!check_empty($data['investor_before_evalute'])) {
            $this->error("请填写融资前估值");
        }
        if (!check_empty($data['investor_after_evalute'])) {
            $this->error("请填写融资后估值");
        }
        if (!check_empty($data['evalute_growth_rate'])) {
            $this->error("请填写估值增长率");
        }
        if (!check_empty($data['investor_payback'])) {
            $this->error("请填写投资回报");
        }
        if (!check_empty($data['investor_time'])) {
            $this->error("请填写融资日期");
        }
        $this->assign("jumpUrl", u(MODULE_NAME."/all_index"));
        
        $data['investor_time'] = strtotime($data['investor_time']);
        $data['investor_record_type'] = 2;  #最新->历史
        
        // 查询最新轮次的融资记录信息(更新之前查询数据，只有一条最新轮次)
        $trade  = M('deal_trade_event')->field('id,deal_id,period')->where("id={$data['id']}")->find();
        
        $re     = M('deal_trade_event')->save($data);
        if ($re === false) {
            save_log($data['deal_id'] . "融资完成失败：保存实际融资信息失败", 0);
            $this->error("融资完成失败：保存实际融资信息失败", 0);
            exit();
        }
            
        $res    = M('deal')->where('id='.$trade['deal_id'])->setField('is_effect',3); #拟投->已投
        if($res === false){
            save_log($data['deal_id'] . "融资完成失败：修改项目融资状态失败", 0);
            $this->error("融资完成失败：修改项目融资状态失败", 0);
            exit();
        }
        M(MODULE_NAME)->where('id='.$trade['deal_id'])->setInc("version");
        // 给该项目的所有投资人发送短信
        if($trade){
            // 查询基金(csdq)
            $fund_id = M('deal_trade_fund_relation')->where('deal_trade_event_id='.$trade['id'].' and is_csdk_fund=1')->getField('id,fund_id');
            if($fund_id){
                // 更新项目融资轮次
                M('deal')->where('id='.$trade['deal_id'])->setField('period_id',$trade['period']);
                
                $fund_id = implode(',',$fund_id);
                // 查询基金的所有投资人
                $user_id = M('user_fund_relation')->where('fund_id in('.$fund_id.') and user_type = 1')->getField('id,user_id');
                if($user_id){
                    $user_id    = array_flip(array_flip($user_id));
                    $deal_name  = M('deal')->where('id='.$trade['deal_id'])->getField('s_name');
                    $period     = M('deal_period')->where('id='.$trade['period'])->getField('name');
                    $msg        = "尊敬的投资人，您购买的基金已成功投资{$deal_name}项目{$period}，您可以随时登录磁斯达克查看项目的最新资讯及收益情况，谢谢！";
                    // 发送站内信
                    foreach($user_id as $uv){
                        $user_notify = array(
                            'user_id'   => $uv,
                            'log_info'  => $msg,
                            'url'       => "",
                            'log_time'  =>time(),
                            'is_read'   => 0,
                        );
                        $a = M("user_notify")->add($user_notify);
                    }
                    
                    $user_id = implode(',',$user_id);
                    $fund_users = M('user')->where('id in('.$user_id.')')->getField('id,mobile');
                    require_once APP_ROOT_PATH.'system/utils/csdk_sms.php';
                    $SMS        = new csdk_sms();
                    $mobiles    = implode(',',$fund_users);
                    $result     = $SMS->sendSMS($mobiles,$msg,array('type'=>csdk_sms::$TYPE_FINANCE_FINISH));
                }
            }else{
                // 更新项目融资轮次
                $event = M('deal_trade_fund_relation')->where('deal_id='.$trade['deal_id'].' and is_csdk_fund=1')->order('investor_date desc')->find();
                if($event){
                    $period_id = M('deal_trade_event')->where('id='.$event['deal_trade_event_id'])->getField('period');
                    M('deal')->where('id='.$trade['deal_id'])->setField('period_id',$period_id);
                }
            }
        }
        
        save_log($data['deal_id'] . "融资完成成功", 1);
        $this->success("融资完成成功", 0);
    }
    
    public function deal_publish()
    {
        $id = intval($_REQUEST['id']);
        if (!empty($id)) {
            // 成功案例(经典案例)必须是已投的情况下， 才允许上线
            $rel_data   = M(MODULE_NAME)->where('id='.$id)->find();
            
            if($rel_data['is_case'] == 1 && $rel_data['is_effect'] != 3){
                $this->error("经典案例必须是已投的情况下， 才允许上线", $ajax);
            }
            
            $list       = M(MODULE_NAME)->where('id='.$id)->setField("is_publish", 2);

            if ($list !== false) {
                M(MODULE_NAME)->where('id='.$id)->setInc("version");
                save_log($rel_data['name'] . "成功发布上线", 1);
                $this->success("成功发布上线", $ajax);
            } else {
                save_log($rel_data['name'] . "发布上线出错", 1);
                $this->error("发布上线出错", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), 1);
        }
    }
    
    function deal_withdraw()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (!empty($id)) {
            $condition = array(
                'id' => array(
                    'in',
                    explode(',', $id)
                )
            );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info) $info = implode(",", $info);
            
            $list = M(MODULE_NAME)->where($condition)->setField("is_publish", 1);

            if ($list !== false) {
                M(MODULE_NAME)->where($condition)->setInc("version");
                save_log($info . "项目成功撤回", 1);
                $this->success("项目成功撤回", $ajax);
            } else {
                save_log($info . "项目撤回出错", 0);
                $this->error("项目撤回出错", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
    
    function deal_new_finance()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = intval($_REQUEST['id']);
        if (!empty($id)) {
            $condition = array(
                'id' => $id
            );
            $rel_data = M(MODULE_NAME)->where($condition)->find();
            $data = array('is_effect'=>1,'is_publish'=>1);
            $list = M(MODULE_NAME)->where($condition)->save($data);
            if ($list !== false) {
                M(MODULE_NAME)->where($condition)->setInc("version");
                save_log($rel_data['name'] . "申请新融资成功", 1);
                $this->success("申请新融资成功", $ajax);
            } else {
                save_log($rel_data['name'] . "申请新融资出错", 0);
                $this->error("申请新融资出错", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
}
?>