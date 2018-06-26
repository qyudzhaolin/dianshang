<?php

class DealModel extends Model {
	private $filt_arr  = array(
				's_name' => '',//项目简称
				//'province' => 0, //所在省份，默认0，全部
				//'city' => 0,	//所在城市，默认0，全部
				//'deal_cate' => 0, //所属行业，默认0，全部
				//'deal_period' => 0,//融资轮次，默认0，全部
				'vis' => 0,//项目可见度，默认0，全部
				'deal_name' => '',//项目名称
				//'company_name' => '',//所属机构
				//'user_name' => '',//推荐人
				'is_effect' => 5,//项目状态，默认5，全部
				//'is_best' => 0,//是否精品项目，默认0，全部
		);
	private $fs = "cd.`id`,/*项目ID*/
				  cd.`name`,/*项目名称*/
				  cd.`s_name`, /*项目简称*/
			  	  cd.`province`,/*所在省份*/
			 	  cd.`city`,/*所在城市*/
				  cd.`company_name`, /*所属机构*/
				  cd.`cate_choose`,
				  /*cdc.`name` AS `deal_cate_name`, */ /*所属行业*/
				  cd.`period_id`,
				  cdp.`name` AS `deal_period_name`, /*融资轮次*/
				  cd.`user_id`,
				  cu.`user_name`, /*项目推荐人*/
				  cd.`is_effect`,
				CASE
				WHEN cd.`is_effect`=0 THEN '申请中' 
				WHEN cd.`is_effect`=1 THEN '编辑中'
				WHEN cd.`is_effect`=2 THEN '拟投中'
				WHEN cd.`is_effect`=3 THEN '已投资'
				WHEN cd.`is_effect`=4 THEN '已撤回'
				END AS `deal_status`, /*项目状态*/
				  IF(cd.`vis`=1,'认证会员可见','全部会员可见') AS `visible`, /*项目可见度*/
				  IF(cd.`is_best`=1,'是','否') AS `is_best`,  /*项目是否精品，1是，0不是*/
				 
				  FROM_UNIXTIME(
				    cd.`create_time`,
				    '%Y-%m-%d %H:%i:%S'
				  ) AS `create_time`,/*创建时间*/
				  
				  FROM_UNIXTIME(
				    IF(
				      cd.`update_time`,
				      cd.`update_time`,
				      cd.`create_time`
				    ),
				    '%Y-%m-%d %H:%i:%S'
				  ) AS `update_time`  /*最近修改时间*/";
	
	private $from = "FROM
				  `cixi_deal` AS `cd`
				  LEFT JOIN `cixi_user` AS `cu`
				    ON cu.`id` = cd.`user_id`
				  LEFT JOIN `cixi_deal_period` AS `cdp`
				    ON cdp.`id` = cd.`period_id`
				    WHERE 1=1 AND cd.`is_delete`!=1 ";
	
	/**
	 * 筛选项目列表
	 * @param array $filt_arr 筛选条件，数组格式
	 * @param string $limit 分页
	 * @param boolean $is_total 是否获取总条数
	 */
	public function pub_get_project($filt_arr,$limit,$is_total = false){
		$limit = 'LIMIT '.$limit;
		$order_by = ' ORDER BY cd.`id` desc ';
		
		if($is_total){
			$base_sql =	"SELECT ". $this->fs .', '."count(cd.`id`) AS `total` ".$this->from;
		}else{
			$base_sql =	"SELECT ". $this->fs .  $this->from;
		}
		
		if($filt_arr == $this->filt_arr){
			if($is_total){
				$sql = $base_sql;
			}else{
				$sql = $base_sql.$order_by.$limit;
			}
			$res = $this->query($sql);
		}else{
			$where = '';
			//项目简称
			if(!empty($filt_arr['s_name'])){
				$where .=  " AND cd.`s_name` LIKE '%{$filt_arr['s_name']}%' ";
			}
			//项目名称
			if(!empty($filt_arr['deal_name'])){
				$where .=  " AND cd.`name` LIKE '%{$filt_arr['deal_name']}%' ";
			}
			//项目可见度
			if(!empty($filt_arr['vis'])){
				$where .=  " AND cd.`vis`='{$filt_arr['vis']}' ";
			}
			//项目状态
			if($filt_arr['is_effect'] != 5){
				$where .=  " AND cd.`is_effect`='{$filt_arr['is_effect']}' ";
			}
			/* //所属机构
			 if(!empty($filt_arr['company_name'])){
			 $where .=  " AND cd.`company_name` LIKE '%{$filt_arr['company_name']}%' ";
			 }
			 //推荐人
			 if(!empty($filt_arr['user_name'])){
			 $where .=  " AND cu.`user_name` LIKE '%{$filt_arr['user_name']}%' ";
			 }
			 //所属行业
			 if(!empty($filt_arr['deal_cate'])){
			 $where .=  " AND cdsc.`cate_id`='{$filt_arr['deal_cate']}' ";
			 }
			 //融资轮次
			 if(!empty($filt_arr['deal_period'])){
			 $where .=  " AND cu.`period_id` LIKE '%{$filt_arr['deal_period']}%' ";
			 }
			 //是否精品项目
			 if(!empty($filt_arr['is_best'])){
			 $where .=  " AND cd.`is_best`='{$filt_arr['is_best']}' ";
			 }
			 //所在省份
			 if(!empty($filt_arr['province'])){
			 $where .=  " AND cd.`province`='{$filt_arr['province']}' ";
			 }
			 //所在市
			 if(!empty($filt_arr['city'])){
			 $where .=  " AND cd.`city`='{$filt_arr['city']}' ";
			 } */
			
			if($is_total){
				$sql = $base_sql.$where;
			}else{
				$sql = $base_sql.$where.$order_by.$limit;
			}
			
			$res = $this->query($sql);
		}
		
		if($res && !$is_total){
			$cat_arr = array();
			foreach ($res as $key=> &$val){
				$val['cat_arr'] = explode('_', $val['cate_choose']);
				foreach ($val['cat_arr'] as $k=>$v){
					array_push($cat_arr, $v);
				}
			}
			
			$cate = $this->pri_get_deal_cate_by_id(implode(',', $cat_arr));
			foreach ($res as $key=> &$val){
				$val['deal_cate_name'] = '';
				foreach ($val['cat_arr'] as $k=>$v){
					if($v == $cate[$v]['id']){
						$val['deal_cate_name'] .= $cate[$v]['name'].'、';
					}
				}
				$val['deal_cate_name'] = mb_substr($val['deal_cate_name'],0,mb_strlen($val['deal_cate_name'],'utf-8') - 1,'utf-8');
			}
			
		}
		
		if($is_total){
			return $res[0]['total'];
		}else{
			return $res;
		}
		
	}
	
	/**
	 * 获取项目所属的行业，也即是项目所属的分类
	 */
	public function pub_get_deal_cate(){
		$res = M("DealCate")->findAll();
		return $res;
	}
	
	/**
	 * 获取项目融资轮次
	 */
	public function pub_get_deal_period(){
		$res = M("DealPeriod")->findAll();
		return $res;
	}
	
	/**
	 * 获取行业分类
	 * @param string $cate_id  分类ID
	 * @return array   		         行业数据
	 */
	private function pri_get_deal_cate_by_id($cate_id){
		if(empty($cate_id)){
			return '';
		}
		
		$sql = "SELECT * FROM cixi_deal_cate WHERE id IN({$cate_id})";
		$res = $this->query($sql);
		$data = array();
		if($res){
			foreach ($res as $key=> $val){
				$data[$val['id']] = $val;
			}
		}
		unset($res);
		return $data;
	}
	
}