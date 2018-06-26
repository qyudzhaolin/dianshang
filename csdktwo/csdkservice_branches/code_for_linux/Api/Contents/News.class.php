<?php
namespace Api\Contents;

use System\Base;
use System\Logs;

/**
 * 新闻模块相关的API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: wangdongxing <wangdongxing@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */

define("PAGE_SIZE", 10);

class News extends Base{
	private $tablePrefix = null;
	private $newsTable = null;
	
	public function __construct(){
		parent::__construct();
		$this->tablePrefix = $this->getMainDbTablePrefix();
		$this->newsTable = "{$this->tablePrefix}news";
	}
	
	/**
	 * 新闻列表
	 */
	public function queryNewsByCondition($params){
		$page_num 	= isset($params['page'])?trim($params['page']):1;
		$n_class 	= isset($params['n_class'])?trim($params['n_class']):1;
		$deal_id	= isset($params['deal_id'])?trim($params['deal_id']):NULL;
		$is_deal_cate_news	= isset($params['is_deal_cate_news'])? trim($params['is_deal_cate_news']):NULL;
		$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
		$page_rows 	= PAGE_SIZE;
		if(empty($params)){
			return $this->endResponse(null,5);
		}
		$args = ['n_class'=>$n_class];
		$sql_news="select id,n_title,n_brief,n_list_img,n_source,create_time  from cixi_news where   n_channel<>2 and  n_publish_state=2  ";
		if(!is_null($deal_id)){
			if($is_deal_cate_news==2){
				/**********获取项目所属行业******************/
				$args = ['n_deal'=>$deal_id];
				$sql_cate_deal = " select cate_id from cixi_deal_select_cate where deal_id=:n_deal  ";
				//$r_cate_deal = PdbcTemplate::query($sql_cate_deal,$para_cate_deal);
				$r_cate_deal = $this->executeQuery($sql_cate_deal,$args);
				$cate_deal_list=  "";
				if(!empty($r_cate_deal))
				{
					foreach ($r_cate_deal as $key => $val)
					{
						$cate_deal_list= $cate_deal_list.' FIND_IN_SET("'.$val['cate_id'].'",n_cate) or';
					}
				}
				$cate_deal_list=(substr($cate_deal_list,0,strlen($cate_deal_list)-2));
				$condition = " and ($cate_deal_list) and id not in(select id from cixi_news where n_deal = $deal_id and n_publish_state=2 and n_channel in (1,3))" ;
			}
			else{
				$condition = " and n_deal=$deal_id " ;}
		}
		else
		{        $condition = "and n_class=:n_class " ;}
		 
		$sql_page	="
		order by create_time desc,id desc limit {$page_offset}, {$page_rows}";
		$sql_final = $sql_news." ".$condition." ".$sql_page;
		$result = $this->executeQuery($sql_final,$args);
		return  $this->endResponse($result,0);
	}
	
	public function queryNewsDetailByCondition($params){
		if(empty($params)){
			return $this->endResponse(null,5);
		}
		$id = isset($params['id'])?trim($params['id']):NULL;
		
		$args = ['id'=>$id];
		$sql_detail = "
				select * from	cixi_news	news
		where 	news.n_publish_state=2 and news.id = :id		";
		$result = $this->executeQuery($sql_detail,$args);
		return  $this->endResponse($result,0);
	}
	
}
?>