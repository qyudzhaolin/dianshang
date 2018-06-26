<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:zhaoLin(97139915@qq.com)
// +----------------------------------------------------------------------
class introduceModule extends BaseModule
{

    /*
     * 平台介绍
     * @param int $id 用户ID
     * @author zhaoLin
     */
    public function index()
    {
        // 关于我们轮播
        $field = "id,b_url,b_pc_img";
        $carousel = $GLOBALS['db']->getAll("select {$field} from " . DB_PREFIX . "banner where  b_channel in(2,3) and b_bygroup =3 and  b_publish_state = 2 order by b_sort desc limit 6");
        foreach($carousel as &$v){
		    $v['b_url'] = $v['b_url'] ? (stristr($v['b_url'],'http://') ? $v['b_url'] : 'http://'.$v['b_url']) : '';
		    $v['b_pc_img'] = getQiniuPath($v['b_pc_img'],'img')."?imageView2/1/w/1000/h/360";
		}
        unset($v);
        
        $GLOBALS['tmpl']->assign("carousel", $carousel);
        $GLOBALS['tmpl']->assign("pageType", PAGE_MENU_INTRODUCTION);
        $GLOBALS['tmpl']->display("introduce.html");
    }
}
?>