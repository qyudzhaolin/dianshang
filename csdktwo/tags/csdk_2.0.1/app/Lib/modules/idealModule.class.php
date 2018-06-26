<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class idealModule extends BaseModule
{
    // 我要推荐项目
    public function index()
    {
        
        // 按钮的显示与隐藏
        $user_info = $GLOBALS['user_info'];
        $user_id = $user_info['id'];
        
        if ($user_id) {
            $GLOBALS['tmpl']->assign("login_user", $login_user = 1);
            $user_is_review = $GLOBALS['db']->getOne("select is_review from " . DB_PREFIX . "user where id = {$user_id}");
            
            if ($user_is_review == '0' || $user_is_review == '2') {
                $GLOBALS['tmpl']->assign("login_user", $login_user = 2);
            }
        } else {
            $GLOBALS['tmpl']->assign("login_user", $login_user = 0);
        }
        
        // 所属行业
        $dealcate = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "deal_cate");
        $GLOBALS['tmpl']->assign("dealcate", $dealcate);
        // 所属地区
        $region_pid = 0;
        $region_lv2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where region_level = 2 order by py asc"); // 二级地址
        foreach ($region_lv2 as $k => $v) {
            if ($v['id'] == $home_user_info['province']) {
                $region_lv2[$k]['selected'] = 1;
                $region_pid = $region_lv2[$k]['id']; // var_dump($region_lv2[$k]);
                $region_pname_province = $region_lv2[$k]['name']; // var_dump($region_lv2[$k]);
                break;
            }
        }
        $region_lv3 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "region_conf where pid=" . $region_pid);
        foreach ($region_lv3 as $k => $v) {
            if ($v['id'] == $home_user_info['city']) {
                $region_lv3[$k]['selected'] = 1;
                $region_pid = $region_lv3[$k]['id']; // var_dump($region_lv2[$k]);
                $region_pid = $region_lv3[$k]['id']; // var_dump($region_lv2[$k]);
                $region_pname_city = $region_lv3[$k]['name'];
                break;
            }
        } // 二级地址
        $period_list = $GLOBALS['db']->getAll("select id,mapname from " . DB_PREFIX . "deal_period GROUP BY mapname 
    ORDER BY sort asc");
        $GLOBALS['tmpl']->assign("period_list", $period_list);
        $GLOBALS['tmpl']->assign("region_lv2", $region_lv2);
        $GLOBALS['tmpl']->assign("region_lv3", $region_lv3);
        $GLOBALS['tmpl']->assign("pageType", PAGE_MENU_RECOM_DEAL);
        $GLOBALS['tmpl']->display("i_deal.html");
    }

    public function deal_save()
    {
        $user_info = $GLOBALS['user_info'];
        $user_id = $user_info['id'];
        $res = array(
            'status' => 1,
            'info' => '',
            'data' => ''
        ); // 用于返回的数据
        $deal['name'] = strim($_REQUEST['deal_name']);
        $deal_name = strim($_REQUEST['deal_name']);
        $dealnameresult = $GLOBALS['db']->getRow("select id,name from " . DB_PREFIX . "deal where user_id=$user_id and name='{$deal_name}'");
        if ($dealnameresult) {
            $res['status'] = 3;
            ajax_return($res);
        }
        $res = check_len($deal['name'], 14, 1, "项目名称");
        if (trim($deal['name']) != "") {
            if ($res['status'] == 0) {
                ajax_return($res);
            }
        }
        $deal['deal_sign'] = strim($_REQUEST['deal_sign']);
        $res = check_len($deal['deal_sign'], 28, 1, "项目一句话：");
        if (trim($deal['deal_sign']) != "") {
            if ($res['status'] == 0) {
                ajax_return($res);
            }
        }
        $dealcateid = trim($_REQUEST['deal_cate'], ',');
        $cateidresult = explode(',', $dealcateid, 3);
        if (empty($cateidresult)) {
            $res['status'] == 0;
            ajax_return($res);
        }
        $deal['province'] = strim($_REQUEST['province']);
        $res = check_region($deal['province'], "地区");
        if (trim($deal['province']) != "") {
            if ($res['status'] == 0) {
                ajax_return($res);
            }
        }
        $deal['city'] = strim($_REQUEST['city']);
        $res = check_region($deal['city'], "地区");
        if (trim($deal['city']) != "") {
            if ($res['status'] == 0) {
                ajax_return($res);
            }
        }
        $deal['period_id'] = strim($_REQUEST['deal_period']);
        $res = check_region($deal['period_id'], "融资轮次");
        if (trim($deal['period_id']) != "") {
            if ($res['status'] == 0) {
                ajax_return($res);
            }
        }
        $deal['deal_brief'] = strim($_REQUEST['deal_brief']);
        $res = check_len($deal['deal_brief'], 200, 1, "项目简介");
        if (trim($deal['deal_brief']) != "") {
            if ($res['status'] == 0) {
                ajax_return($res);
            }
        }
        
        $is_delete = 0;
        $deal['is_delete'] = $is_delete;
        $is_effect = 1;
        $deal['is_effect'] = $is_effect;
        $deal['create_time'] = time();
        $deal['vis'] = 0;   # 0 请选择可见度
        // 用户id
        if ($user_id) {
            $deal['user_id'] = $user_id;
            $result = $GLOBALS['db']->autoExecute(DB_PREFIX . "deal", $deal, "INSERT", "SILENT");
        }
        if ($result) {
            $deal_id = $GLOBALS['db']->insert_id();
            $values = '';
            foreach ($cateidresult as $value) {
                $values .= "('',{$deal_id},{$value}),";
            }
            $values = rtrim($values, ',');
            $cateresult = $GLOBALS['db']->query("iNSERT INTO " . DB_PREFIX . "deal_select_cate VALUES {$values}");
        } else {
            $res['status'] = 0;
            $res['info'] = "推荐项目失败";
        }
        ajax_return($res);
    }
}
?>