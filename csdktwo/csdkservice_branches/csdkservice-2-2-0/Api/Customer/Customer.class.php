<?php
/**
 * | 线下会员接口
 */
namespace Api\Customer;

use Api\Common\RedisKeyMap;
use System\Base;
use System\Logs;


class Customer extends Base {
    private $tablePrefix = null;
    private $table = null;
    private $table_info = null;
    private $table_card = null;
    private $table_card_bind = null;
    private $table_card_order = null;
    private $table_card_type = null;
    private $table_operate_log = null;
    private $table_rechage_log = null;
    private $table_recharge_type = null;
    private $table_order = null;

    public function __construct() {
        parent::__construct();
        $this->tablePrefix = $this->getMainDbTablePrefix();
        $this->table = "{$this->tablePrefix}customer"; # 会员表
        $this->table_info = "{$this->tablePrefix}custormer_info"; # 会员资料表
        $this->table_card = "{$this->tablePrefix}member_cards"; # 会员卡表
        $this->table_card_bind = "{$this->tablePrefix}card_bind_relation"; # 会员卡绑定信息（新表）
        $this->table_card_order = "{$this->tablePrefix}card_order"; # 会员卡订单表
        $this->table_card_type = "{$this->tablePrefix}card_type"; # 会员卡类型
        $this->table_operate_log = "{$this->tablePrefix}operate_log"; # 会员卡操作日志
        $this->table_rechage_log = "{$this->tablePrefix}rechage_log"; # 金额日志表
        $this->table_recharge_type = "{$this->tablePrefix}recharge_type"; # 会员卡充值类型
        $this->table_order  = "{$this->tablePrefix}member_order";  # 会员订单表
        $this->table_recommend  = "{$this->tablePrefix}customer_recommend";  # 会员推荐关系表
        $this->table_store  = "{$this->tablePrefix}new_store";  # 会员推荐关系表
    }
    /**
     * 取门店名称
     * <p>请求参数说明</p>
     * <p>funct: Customer.Customer.getStoreInfo</p>
     * @param   int  $store_code
     * @return  array $data
     * @version 1.0
     * @author  zym
     */
     public function getStoreInfo($params) {

         $store_code = isset($params['store_code'])? $params['store_code'] : '';

         if (empty($store_code)) {
             return $this->endResponse(null,2001);
         }
         $args = array('id' =>$store_code );
         $sql = "select *  from  {$this->tablePrefix}new_store where id=:id";
         $data = $this->executeQuery($sql, $args); # 会员信息

         return $this->endResponse($data);
     }
    /**
     * 取门店会员信息
     * <p>请求参数说明</p>
     * <p>func: Customer.Customer.getCustomerList</p>
     * <p>param:{store_code:10,status:1,page:1,offer:10}</p>
     * @param   array $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function getCustomerList($params) {
         $store_code = isset($params['store_code']) ? $params['store_code'] : 0; # 门店编码
         $page = isset($params['page']) ? intval($params['page']) : 1; # 当前页
         $offer = isset($params['offer']) ? intval($params['offer']) : 10; # 每页显示条数
         $content = isset($params['content']) ? $params['content'] : '';

         if (empty($store_code) || empty($page) || empty($offer)) {
             return $this->endResponse(null, 2001); # 参数不正确
         }
         // todo 总共多少条数据
         $args = array("store_code" => $store_code);
         if (!empty($content)) {
             $sql = "select COUNT(*) as CNT from  {$this->table} where store_code=$store_code and ( (real_name like '%{$content}%' and not isnull(real_name) )or (mobile like '%{$content}%' and not isnull(mobile) and mobile<>''))";
         } else {
             $sql = "select COUNT(*) as CNT  from  {$this->table} where store_code=$store_code";
         }
         $buyerCount = $this->executeQuery($sql);

         $data['totalNum'] = $buyerCount[0]['CNT'];
         if ($data['totalNum'] == 0) {
             $data['list'] = array();
             return $this->endResponse($data);
         }

         // 查询门店会员信息
         if (!empty($content)) {
             if (is_numeric($content)) {
                 $sql = "select *  from  {$this->table} where store_code=$store_code and mobile like '%{$content}%' order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
             } else {
                 $sql = "select *  from  {$this->table} where store_code=$store_code and real_name like '%{$content}%' order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
             }
         } else {
             $sql = "select *  from  {$this->table} where store_code=$store_code order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
         }


         $data['list'] = $this->executeQuery($sql); # 会员信息
         # 取会员对应的会员卡信息
         foreach ($data['list'] as $k=>$v) {
             // 查询会员绑定会员卡信息
             $cardInfo = $this->invoke('Customer.Customer.getCustomerCard', array('customer_id' => $v['id'],'store_code'=>$v['store_code']));
             $data['list'][$k]['card_no'] = $cardInfo['response'][0]['card_no']; # 会员卡号
             $data['list'][$k]['card_status'] = $cardInfo['response'][0]['status']; # 会员卡状态 1 已绑定未支付 2 激活 3 禁用 4 挂失 5异常
         }
         // 返回数据
         return $this->endResponse($data);
     }
    /**
     * 查询会单个会员信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerOne</p>
     * @param   int    $customer_id
     * @param   int    $store_code
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function getCustomerOne($params){
         $store_code = isset($params['store_code'])?$params['store_code'] : 0;
         $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
         if (empty($store_code) || empty($customer_id)) {
             return $this->endResponse(null,2001);
         }
         $args = array('id'=>$customer_id,'store_code'=>$store_code);
         $sql = "select *  from  {$this->table} where id=:id and store_code=:store_code";
         $result = $this->executeQuery($sql, $args); # 会员信息
         return $this->endResponse($result);

     }
    /**
     * 查询会员自增id
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerId</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerId($params){
        $sql = "select id from  {$this->table} order by id desc limit 1";
        $result = $this->getOne($sql); # 会员信息
        return $this->endResponse($result);
    }
    /**
     * 查询会员卡类型
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCardType</p>
     * @param   int    $type_code
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCardType($params){
        $type_code = isset($params['type_code']) ? $params['type_code'] : '';
        if (empty($type_code)) {
            return $this->endResponse(null,2001);
        }

        $args = array('id'=>$type_code);
        $sql = "select *  from  {$this->table_card_type} where id=:id limit 1";
        $result = $this->executeQuery($sql, $args); # 会员信息
        return $this->endResponse($result);
    }
    /**
     * 查询会员卡生成信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCard</p>
     * @param   int    $card_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCard($params){
        $id = isset($params['card_id']) ? $params['card_id'] : '';
        if (empty($id)) {
            return $this->endResponse(null,2001);
        }
        $args = array('id'=>$id);
        $sql = "select *  from  {$this->table_card} where id=:id limit 1";
        $result = $this->executeQuery($sql, $args); # 会员信息
        return $this->endResponse($result);
    }
    /**
     * 查询会员卡开卡时间，操作者等
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCardTime</p>
     * <p>param:{bind_card_id:会员卡绑定id,card_no:会员卡号，type:操作类型}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCardTime($params){
        $bind_card_id = isset($params['bind_card_id']) ? $params['bind_card_id'] : 0;
        $card_no = isset($params['card_no']) ? $params['card_no'] : '';
        $type = isset($params['type']) ? $params['type'] : 0;
        if (empty($bind_card_id) || empty($card_no) || empty($type)) {
            return $this->endResponse(null,2001);
        }
        if($type == 2){
            $args = array('bd_card_id'=>$bind_card_id,'card_no'=>$card_no);
            $where = "bd_card_id=:bd_card_id and card_no=:card_no and (type=2 OR (type IN (4, 5,7) AND status=1))";
            $sql = "select operator,upt_time,remark from  {$this->table_operate_log} where {$where} ORDER BY upt_time DESC limit 1";
            $result = $this->executeQuery($sql, $args); # 会员信息
        } else {
           // $args = array('bd_card_id'=>$bind_card_id,'card_no'=>$card_no,'type'=>$type);
            $sql = "select operator,upt_time,remark from  {$this->table_operate_log} where bd_card_id=$bind_card_id and card_no='{$card_no}' and type in(4,5,7) AND status=2 ORDER BY upt_time DESC LIMIT 0,1";
            $result = $this->executeQuery($sql); # 会员信息
        }



        return $this->endResponse($result);
    }
    /**
     * 查询会员扩展信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerInfo</p>
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerInfo($params){
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : '';
        if (empty($customer_id)) {
            return $this->endResponse(null,2001);
        }
        $sql = "select * from  {$this->table_info} where custormer_id=$customer_id";

        $result = $this->getOne($sql); # 会员家庭，健康，扩展信息
        return $this->endResponse($result);
    }
    /**
     * 查询会员卡信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerCard</p>
     * @param   int    $customer_id
     * @param   int    $store_code
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function getCustomerCard($params){
         $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
         $store_code = isset($params['store_code']) ? $params['store_code'] : 0;
         if (empty($customer_id)) {
             return $this->endResponse(null, 2001); # 参数不正确
         }
         $args = array('customer_id'=>$customer_id,'store_code' => $store_code);
         $sql = "select *  from  {$this->table_card_bind} where customer_id=:customer_id and store_code=:store_code";
         $result = $this->executeQuery($sql, $args); # 会员信息
         if(!empty($result)){
            $customer_ids = array_column($result, 'customer_id');        
             $getAccountsInfo = $this -> invoke( 'TM.Account.getAccountsInfo', array("customerId"=>$customer_ids,"accountType"=>1) );
             if(!empty($getAccountsInfo) && isset($getAccountsInfo['response']) && $getAccountsInfo[status]==0 ){
                foreach ($getAccountsInfo['response'] as $key => $value) {
                    $result[$key]['account_balance'] = $value['availableBalance'];
                }
             }    
         }             
        
         return $this->endResponse($result);
     }
    /**
     * 禁用会员
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.delCustomer</p>p>
     * @param   int    $customer_id
     * @param   int    $store_code
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function delCustomer($params){
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
        $store_code = isset($params['store_code']) ? $params['store_code'] : 0;
        $status = isset($params['status']) ? $params['status'] : 0;
        if (empty($customer_id) || empty($store_code)) {
            return $this->endResponse(null, 2001); # 参数不正确
        }
        if ($status == 0) {
            $update['status'] = 1; #启用会员
        } else {
            $update['status'] = 0; #禁用会员
        }
        $this->executeSingleTableUpdate("{$this->table}", $update, array('id' => $customer_id));

        return $this->endResponse(true);
    }

    /**
     * 添加会员信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.addCustomer</p>
     * <p>param:{....}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function addCustomer($params){
         if (empty($params)) {
             return $this->endResponse(null,2001);
         }
         $username = $this->invoke('Customer.Customer.getCustomerId',array());

         unset($params['card_no']);
         unset($params['hobbies']);
         unset($params['customer_type']);
         $params['username'] = 'st'.($username['response']['id'] +1); # 会员账号
         $params['real_name'] = isset($params['real_name']) ? $params['real_name'] : ''; # 客户姓名不能为空
         //$params['card_no'] = isset($params['card_no']) ? $params['card_no'] : ''; # 会员卡号不能为空
         $params['card_id'] = isset($params['card_id']) ? $params['card_id'] : ''; # 身份证号码不能为空
         // todo 阳历生日不能为空
         $params['mobile'] = isset($params['mobile']) ? $params['mobile'] : ''; # 手机号码和电话号码必须填写一项
         $params['phone_zone'] = isset($params['phone_zone']) ? $params['phone_zone'] : ''; # 固定电话的区号
         $params['phone_number'] = isset($params['phone_number']) ? $params['phone_number'] : ''; # 固定电话

         $params['province'] = isset($params['province']) ? $params['province'] : ''; # 所在省份
         $params['city'] = isset($params['city']) ? $params['city'] : ''; # 所在市
         $params['district'] = isset($params['district']) ? $params['district'] : ''; # 所在地区
         $params['address'] = isset($params['address']) ? $params['address'] : '111'; # 详细地址必填


        #操作人
         $createUser = $params['createUser'];
         unset($params['createUser']);

         #操作人uid
         $createUid = $params['createUid'];
         unset($params['createUid']);



         if (empty($params['username']) || empty($params['card_id']) ||  empty($params['province']) || empty($params['city'])
                || empty($params['district']) || empty($params['address'])) {
             return $this->endResponse(null,2001);
         }
         //手机号码和固定电话必须2选一
         if (empty($params['mobile']) && empty($params['phone_number'])) {
             return $this->endResponse(null,2001);
         }

         #更改推荐人字段（referrer）存储数据  原先存推荐人real_name  现在存customer_id
         $t_card_no = isset($params['referrer']) ? $params['referrer'] : '';  #推荐人会员卡号
         unset($params['referrer']);

         $hyInfo = array();
         if($t_card_no != ""){
             #获取推荐人ID
             $args = array('card_no'=>$t_card_no);
             $sql = "select customer_id  from  {$this->table_card_bind} where card_no=:card_no";
             $hyInfo = $this->executeQuery($sql, $args); # 通过会员卡获取推荐人ID
             if(empty($hyInfo)){
                 return $this->endResponse(null,12010);
             }
         }

         // 保存数据
         if (isset($params['customer_id']) && !empty($params['customer_id'])) {
             // 修改会员数据
             $customer_id = $params['customer_id'];
             unset($params['customer_id']);
             unset($params['username']);
             unset($params['referrer']);

             //unset($params['card_id']);
             $res = $this->executeSingleTableUpdate("{$this->table}", $params, array('id' => $customer_id));
            // $customer_id = $params['customer_id'];
         } else {

             //添加数据
             unset($params['customer_id']);


             if(!empty($hyInfo)){
                 $params['referrer'] = $hyInfo[0]['customer_id'];
             }


             $res = $this->executeSingleTableCreate("{$this->table}", $params);
             $customer_id = $this->getLastInsertId();



             #添加会员推荐关系  by wuzhizhong 2015-9-14
             if($res && $t_card_no != ""){
                if(!empty($hyInfo)){
                     #插入数据
                     $r_data['create_uid'] = $createUid;
                     $r_data['customer_id'] = $customer_id;
                     $r_data['store_id'] = $params['store_code'];
                     $r_data['pid'] = $hyInfo[0]['customer_id'];
                     $r_data['create_time'] =time();
                     $r_data['create_user'] = $createUser;
                     $recommend_rez = $this->executeSingleTableCreate("{$this->table_recommend}", $r_data);
                 }
              }




             //积分体系之添加main_account
             $apiPath = 'TM.Account.createMainAccount';
             $param1 = array(
             	'userId' => $customer_id,
             	'cardId' => $params['card_id'],
             	'userName' =>$params['username'],
             	'mobile' => $params['mobile'],
             	'email' => empty($params['email']) ? '' : $params['email'],
             	'createUser' => $createUser,
             );
             $result = $this->invoke($apiPath, $param1);
             if(isset($result['status']) && $result['status'] != 0 ){
             	Logs::err('Customer.Customer.addCustomer', LOG_FLAG_ERROR, ['店员新增顾客--创建main_account失败', $param1, $result]); 
             }else{
             	Logs::info('Customer.Customer.addCustomer', LOG_FLAG_NORMAL, ['店员新增顾客--创建main_account成功', $param1, $result]);
             }
             
             //积分体系之添加points_account
             $apiPath = 'TM.Account.createPointsAccount';
             $param2 = array(
             		'userId' => $customer_id,
             		'createUser' => $createUser,
             );
             $create_points_push = $this -> push_queue( 'createPointsAccount', $apiPath, $param2 );
             if( $create_points_push === false ){
             	Logs::err('Customer.Customer.addCustomer', LOG_FLAG_ERROR, ['店员新增顾客--创建积分账户推入队列失败', $param2]);
             }else{
             	Logs::info('Customer.Customer.addCustomer', LOG_FLAG_NORMAL, ['店员新增顾客--创建积分账户推入队列成功', $param2]);
             }
         }

         if ($res == 0 || $res) {
             return $this->endResponse($customer_id);
         } else {
             return $this->endResponse(false);
         }
     }

    /**
     * 添加会员信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.cardIdOnly</p>
     * @param   string  $card_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function cardIdOnly($params){

        // 验证身份证号码唯一
        if (empty($params)) {
            return $this->endResponse(null,2001);
        }

        // $args = array('card_id'=>$params['card_id'],'store_code'=>$params['store_code']);
        // $sql = "select *  from  {$this->table} where card_id=:card_id and store_code=:store_code";
        // 身份证验证全表唯一
        $args = array('card_id'=>$params['card_id']);
        $sql = "select *  from  {$this->table} where card_id=:card_id ";
        $result = $this->executeQuery($sql, $args); # 会员信息

        if (count($result)>0) {
            if (!empty($params['customer_id']) && isset($params['customer_id'])) {
                $customer_id = $params['customer_id'];
                // $sql = "select *  from  {$this->table} where card_id=:card_id and store_code=:store_code and id != $customer_id";
                // 身份证验证全表唯一
                $sql = "select *  from  {$this->table} where card_id=:card_id and id != $customer_id";
                $res = $this->executeQuery($sql, $args); # 会员信息
                if (!empty($res)) {
                    return $this->endResponse(false);
                } else {
                    return $this->endResponse(true);
                }
            } else {
                return $this->endResponse(false);
            }
        } else {
            return $this->endResponse(true);
        }
    }
     /**
     * 查询会员充值记录
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.rechargeLog</p>
     * <p>param:{customer_id:会员卡ID,store_code:门店id,change_name:1,page:1,offer:10, isShowOnMall:是否为前台展示}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function rechargeLog($params) {
        // 通过会员卡号 ，查看会员卡充值记录
         $customer_id = isset($params['customer_id'])? $params['customer_id'] : ''; # 会员id
         $store_code = isset($params['store_code']) ? $params['store_code'] : ''; # 门店id
         $change_name = isset($params['change_name']) ? $params['change_name'] : 0; # 交易名称 1、充值；2、消费；3、补卡；4、提现；5、赔偿 6、退卡 7、开卡充值 8、退款'
         $page = isset($params['page']) ? intval($params['page']) : 1; # 当前页
         $offer = isset($params['offer']) ? intval($params['offer']) : 10; # 每页显示条数
         $isShowOnMall = isset($params['isShowOnMall']) ? $params['isShowOnMall'] : false;	#是否为前台展示

         if ( empty($store_code) || empty($page) || empty($offer)) {
             return $this->endResponse(null, 2001); # 参数不正确
         }
         $bind = $this->invoke('Customer.Customer.getCustomerCard',array('store_code'=>$store_code,'customer_id'=>$customer_id));
         $data = array();
         if (empty($bind['response'])) {
             return $this->endResponse($data);
         }
         $bind_id = $bind['response'][0]['id'];



         // 总共多少条数据
         if($isShowOnMall == false){
	          if ($change_name == 0) {
	             //取全部数据
	             $args = array('bd_card_id'=>$bind_id,'change_type'=>array("IN",'1,2,3,7,8'));
	         }  else {
	             $args = array('bd_card_id'=>$bind_id,'change_type'=>$change_name);
	         }
	         $rechargeCount = $this->executeSingleTableQuery($this->table_rechage_log, 'COUNT(*) as CNT', $args);
	         
         }else{
         	//网站前台展示
	         if($change_name == 0){
	         	$sqlCount = " SELECT count(id) AS CNT FROM (
					         	SELECT a.*,b.store_name FROM  {$this->table_rechage_log} AS a
					         		LEFT JOIN {$this->tablePrefix}new_store AS b on a.store_code = b.id
					         		WHERE a.bd_card_id=$bind_id AND a.change_name in (1,2,3,7,8) AND a.change_name <> 4
					         	UNION ALL
					         	SELECT a.*, b.store_name FROM {$this->table_rechage_log} AS a
						         	LEFT JOIN {$this->tablePrefix}new_store AS b ON a.store_code = b.id
						         	LEFT JOIN {$this->tablePrefix}card_order AS c on a.card_order_id=c.id
						         	WHERE a.bd_card_id=$bind_id AND a.change_name=4 AND a.change_type=2 AND c.pay_status=1 AND a.is_freeze=1
					         	) AS tmp
					         	";
	         }else{
	         	$sqlCount = " SELECT count(id) AS CNT FROM (
					         	SELECT a.*,b.store_name FROM  {$this->table_rechage_log} AS a
					         		LEFT JOIN {$this->tablePrefix}new_store AS b on a.store_code = b.id
					         		WHERE a.bd_card_id=$bind_id AND a.change_name=$change_name AND a.change_name <> 4
					         	UNION ALL
					         	SELECT a.*, b.store_name FROM {$this->table_rechage_log} AS a
						         	LEFT JOIN {$this->tablePrefix}new_store AS b ON a.store_code = b.id
						         	LEFT JOIN {$this->tablePrefix}card_order AS c on a.card_order_id=c.id
						         	WHERE a.bd_card_id=$bind_id AND a.change_name=4 AND a.change_type=2 AND c.pay_status=1 AND a.is_freeze=1
					         	) AS tmp
					         	";
	         }
	         $rechargeCount = $this->executeQuery($sqlCount);
         	
         }
         
         $data['totalNum'] = $rechargeCount[0]['CNT'];

         // 查询会员卡充值消费记录
         if($isShowOnMall == false){
	         if ($change_name == 0) {
	             $sql = "select a.*,b.store_name from  {$this->table_rechage_log} as a
	                            left join {$this->tablePrefix}new_store as b on a.store_code = b.id
	                            where a.bd_card_id=$bind_id and a.change_name in (1,2,3,7,8) 
	                        order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
	         } else {
	             $sql = "select a.*,b.store_name  from  {$this->table_rechage_log} as a
	                            left join {$this->tablePrefix}new_store as b on a.store_code = b.id 
	                            where a.bd_card_id=$bind_id and a.change_name=$change_name 
	                        order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
	         }
	         $data['list'] = $this->executeQuery($sql, $args); # 充值记录
         	
         }else{
	         if($change_name == 0){
	         	$sql = "SELECT a.*,b.store_name FROM  {$this->table_rechage_log} AS a
					    	LEFT JOIN {$this->tablePrefix}new_store AS b on a.store_code = b.id
					        WHERE a.bd_card_id=$bind_id AND a.change_name IN (1,2,3,7,8) AND a.change_name <> 4
					    UNION ALL
					    SELECT a.*, b.store_name FROM {$this->table_rechage_log} AS a
							LEFT JOIN {$this->tablePrefix}new_store AS b ON a.store_code = b.id
						    LEFT JOIN {$this->tablePrefix}card_order AS c on a.card_order_id=c.id
						    WHERE a.bd_card_id=$bind_id AND a.change_name=4 AND a.change_type=2 AND c.pay_status=1 AND a.is_freeze=1
	         			order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
	         }else{
	         	$sql = "SELECT a.*,b.store_name FROM  {$this->table_rechage_log} AS a
					    	LEFT JOIN {$this->tablePrefix}new_store AS b on a.store_code = b.id
					        WHERE a.bd_card_id=$bind_id AND a.change_name=$change_name AND a.change_name <> 4
					    UNION ALL
					    SELECT a.*, b.store_name FROM {$this->table_rechage_log} AS a
						   	LEFT JOIN {$this->tablePrefix}new_store AS b ON a.store_code = b.id
							LEFT JOIN {$this->tablePrefix}card_order AS c on a.card_order_id=c.id
						   	WHERE a.bd_card_id=$bind_id AND a.change_name=4 AND a.change_type=2 AND c.pay_status=1 AND a.is_freeze=1
						order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
	         }
         	
	         $data['list'] = $this->executeQuery($sql); # 充值记录
         }
         if(!empty($data['list']) && count($data['list'] > 0)){
             foreach ($data['list'] as $k=>$v){
                 if ($v['remark'] == '便利店会员卡支付') {
                     $data['list'][$k]['store_name'] = '便利店';
                 }else if($v['remark'] == '便利店会员卡退款'){
                    $wmsStoreId = $v['store_code'];
                    $sql = "SELECT name FROM {$this->tablePrefix}wms_store WHERE id={$wmsStoreId} LIMIT 1";
                    $wmsStoreInfo = $this->getOne($sql);
                    $data['list'][$k]['store_name'] = $wmsStoreInfo['name'];
                }
             }         	
         }
         // 返回充值记录
         return $this->endResponse($data);
     }
    /**
     * 查询当前会员的会员卡的绑定记录
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.cardBindLog</p>
     * <p>param:{customer_id:会员ID,store_code:门店id,status:2,page:1,offer:10}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function customerBindLog($params){
         $bd_card_id = isset($params['bd_card_id']) ? $params['bd_card_id']: '';
         $store_code = isset($params['store_code']) ? $params['store_code'] : '';
         //$status = isset($params['status']) ? $params['status'] : 0; # 默认取激活记录
         $page = isset($params['page']) ? intval($params['page']) : 1; # 当前页
         $offer = isset($params['offer']) ? intval($params['offer']) : 10; # 每页显示条数
         if ( empty($bd_card_id)) {
             return $this->endResponse(null,2001);
         }
         // 总共多少条数据
         $type = array(4,5);
         $where = "bd_card_id=$bd_card_id and status=2 and type in (".implode(',', $type).") and remark != '补卡订单确认' and remark != '补卡成功废弃原卡'";
         $rechargeCount = $this->executeQuery("SELECT COUNT(*) as CNT FROM {$this->table_operate_log} WHERE {$where}");
         $data['totalNum'] = $rechargeCount[0]['CNT'];

         if ($data['totalNum'] == 0) {
             $data['list'] = array();
             return $this->endResponse($data);
         }

         // 查询会员的会员卡绑定记录
         $sql = "select *  from  {$this->table_operate_log} where {$where} order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
         $data['list'] = $this->executeQuery($sql); # 会员卡绑定记录

         // 返回充值记录
         return $this->endResponse($data);
     }

    /**
     * 查询当前会员的消费记录
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.consumLog</p>
     * <p>param:{customer_id:会员ID,store_code:门店id;change_name:2,page:1,offer:10}</p>
     * <p>response: Integer 成功失败  status = 0  成功   status = 1  失败</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function consumLog($params){
         $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
         $store_code = isset($params['store_code']) ? $params['store_code'] : 0;
         $change_name = isset($params['change_name']) ? $params['change_name'] : 2; # 交易名称 1、充值；2、消费；3、换卡；4、提现；5、赔偿 6、退卡'
         $page = isset($param['page']) ? intval($param['page']) : 1; # 当前页
         $offer = isset($params['offer']) ? intval($params['offer']) : 10; # 每页显示条数

         if (empty($customer_id) || empty($store_code)) {
             return $this->endResponse(null,2001);
         }

         // 根据会员id，取绑定记录
         $bind = $this->invoke('Customer.Customer.getCustomerCard',array('customer_id'=>$customer_id,'store_code'=>$store_code));
         $data = array();
         if (!empty($bind['response'])) {
             foreach ($bind['response'] as $k=>$v) {
                $ids[] = $v['id'];
             }
             $bd_card_id = implode(',',$ids);

             // 总共多少条数据
             $args = array('bd_card_id'=>array("IN",$bd_card_id));
             $rechargeCount = $this->executeSingleTableQuery($this->table_rechage_log, 'COUNT(*) as CNT', $args);
             $data['totalNum'] = $rechargeCount[0]['CNT'];
             $sql = "select *  from  {$this->table_rechage_log} where bd_card_id in ($bd_card_id) order by id desc limit  " . ($page - 1) * $offer . "," . $offer;
             $data['list'] = $this->executeQuery($sql); # 会员卡绑定记录
             print_r($data);exit;

         } else {
             $data['totalNum'] = 0;
             return $this->endResponse($data);
         }
     }
    /**
     * 查询会员的家庭信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getFamilyInfo</p>
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getFamilyInfo($params) {
        $customer_id = $params['customer_id'];
        if (empty($customer_id)) {
            return $this->endResponse(null,2001);
        }
        $args = array('custormer_id'=>$customer_id);
        $sql = "select *  from  {$this->table_info} where custormer_id=:custormer_id";
        $result = $this->executeQuery($sql, $args); # 会员信息
        return $this->endResponse($result);
    }
    /**
     * 添加会员的家庭状况
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.addFamily</p>
     * <p>param:{customer_id:会员ID....}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function addFamily($params) {
         $params['customer_id'] = isset($params['customer_id']) ? $params['customer_id'] : ''; # 会员id
         $params['name'] =  isset($params['name']) ? $params['name'] : ''; # 配偶姓名
         $params['birthday'] = isset($params['birthday']) ? $params['birthday'] : ''; # 配偶生日
         $params['is_together'] = isset($params['is_together']) ? $params['is_together'] : '否'; # 是否和子女一起居住
         $params['visit_time'] = isset($params['visit_time']) ? $params['visit_time'] : '一个月以上'; # 一天一次，三天一次，一周一次，一个月一次，一个月以上
         $params['economic'] = isset($params['economic']) ? $params['economic'] : ''; # 完全自主 自主要告知 配偶做主 双方商量 子女做主
         $params['insurance'] = isset($params['insurance']) ? $params['insurance'] : ''; # 社保 商业保险 社保+商业保险 无保险 保密
         $params['economic'] = isset($params['economic']) ? $params['economic'] : ''; # 完全自主 自主要告知 配偶做主 双方商量 子女做主
         $params['consum'] = isset($params['consum']) ? $params['consum'] : ''; # 1000以下/月 1000-2000/月 2000-5000/月 5000-10000/月 10000以上/月
         $params['family_status'] = isset($params['family_status']) ? $params['family_status'] : ''; # 相互和睦  配偶不和睦 子女不和睦 保密

         # 新增子女手机号和家庭月收入  by wuzhizhong 2015-9-11
         $params['cmobile'] =  isset($params['cmobile']) ? $params['cmobile'] : ''; # 子女手机号
         $params['fincome'] =  isset($params['fincome']) ? $params['fincome'] : ''; # 家庭月收入


         $data['custormer_id'] = $params['customer_id'];
         $data['family_info'] = json_encode($params);
         //查看表中信息是否存在
         $info = $this->invoke("Customer.Customer.getCustomerInfo",array('customer_id'=>$params['customer_id']));

         if (empty($info['response'])) {
             // 添加数据
             $data['custormer_id'] = $params['customer_id'];
             $res = $this->executeSingleTableCreate("{$this->table_info}", $data);
         } else {
             //修改数据
             $data['upt_time'] = date("Y-m-d H:i:s");

             $res = $this->executeSingleTableUpdate("{$this->table_info}", $data, array('id' => $info['response']['id']));
         }
         $customer_id = $params['customer_id'];
         return $this->endResponse($customer_id);
     }
    /**
     * 添加健康信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.addHealth</p>
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function addHealth($params) {
         if(empty($params)) {
             return $this->endResponse(null,2001);
         }
         $customer_id = isset($params['customer_id']) ? $params['customer_id'] : '';
         if (empty($customer_id)) {
             return $this->endResponse(null,2001);
         }

         /*$data['eating_habits'] = $params['eating_habits'] ; # 饮食习惯
         $data['exercise_habits'] = $params['exercise_habits']; # 锻炼习惯
         $data['exercise_time'] = $params['exercise_time']; # 锻炼时间*/
         // 要添加的数据组合

         $insert['health_info'] = json_encode($params);
         //查看表中信息是否存在
         $info = $this->invoke("Customer.Customer.getFamilyInfo",array('customer_id'=>$params['customer_id']));
         if (empty($info['response'])) {
             // 添加数据
             $insert['custormer_id'] = $customer_id;
             $res = $this->executeSingleTableCreate("{$this->table_info}", $insert);
         } else {
             //修改数据
             $insert['upt_time'] = date("Y-m-d H:i:s");
             $res = $this->executeSingleTableUpdate("{$this->table_info}", $insert, array('id' => $info['response'][0]['id']));
         }
         return $this->endResponse(true);
     }
    /**
     * 健康情况
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.healthInfo</p>
     * <p>param:{'key':....}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function healthInfo($params){
         $data = array(
             'h1' => array('恶心','呕吐','咳嗽','咳嗽咳痰','呼吸困难','吞咽困难',
                 '记忆力减退','睡眠不安','意识障碍','情绪不稳定',
                 '肌肉萎缩','肢体麻木','关节疼痛','关节摩擦音','关节肿胀','关节僵硬','活动障碍',
                 '视物模糊','头晕头疼','肢体无力','失眠健忘','不自主颤抖','心悸胸闷',
                 '瘫痪','心绞痛','食欲减退','便秘','腰部疼痛','上腹痛','腹胀',
                 '疼痛','反复出血','其他'
             ),
             'h2'=> array(
                 array(
                     'name' =>'常见病',
                     'children' => array('高血压','高血脂','高血糖','糖尿病','老年痴呆症','老年性耳聋','痛风','中风','类风湿性关节炎','其他'),
                 ),
                 array(
                     'name' => '呼吸系统疾病',
                     'children' => array('急性上呼吸道感染','支气管炎','急性支气管炎','肺炎球菌性肺炎','气胸','肺炎','哮喘','支原体肺炎'),
                 ),
                 array(
                     'name' => '心脑血管疾病',
                     'children' => array('心血管动脉硬化','心肌病','心肌炎','心肌梗塞','冠心病','心肌供血不足'),
                 ),
                 array(
                     'name' => '耳鼻喉疾病',
                     'children' => array('眼底出血','白内障','黄斑变性','干眼病','青光眼','耳痛','耳鸣','内耳炎','鼻炎','鼻息肉','鼻囊肿','支气管扩张'),
                 ),
                 array(
                     'name' => '消化系统疾病',
                     'children' => array('胃炎','急性胃炎','急性肠胃炎','胃溃疡','十二脂肠溃疡'),
                 ),
                 array(
                     'name' => '神经系统疾病',
                     'children' => array('三叉神经痛','面神经痛'),
                 ),
                 array(
                     'name' => '风湿骨科疾病',
                     'children' => array('周期性瘫痪','骨折','骨质疏松','骨癌','股骨头坏死','骨关节病','骨风湿病'),
                 ),
                 array(
                     'name' => '泌尿系统疾病',
                     'children' => array('肾盂肾炎','膀胱炎','尿道炎'),
                 ),

             ),
             'h3' => array('安琪硒','纳豆激酶','壳寡糖','白桦茸','魔芋膳食纤维','亚布胶囊','安利','汤臣倍健','完美','太太','交大昂立','无极限','碧生源','天狮','椰岛','紫光古汉','其他'),
             'h4' => array('氨咖甘片','元顺','施泰乐','安乃近片','氨咖黄敏片','氨咖黄敏胶囊','尔同舒','丙磺舒片','爱尔凯因','苯溴马隆胶囊','阿莫西林胶囊','其他'),
         );
         return $this->endResponse($data);
     }



    /**
     * 查询会员卡绑定的操作人员
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getUserName</p>
     * @param   int    $staff_ids
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getUserName($params) {
        // 去掉重复的id
        $user_ids = array_unique($params['staff_ids']);
        if (empty($user_ids)) {
            return $this->endResponse(null,2001);
        }
        $user_ids = implode(",",$user_ids);

        $sql = "select *  from  {$this->tablePrefix}user where id in ($user_ids)";
        $result = $this->executeQuery($sql); # 会员信息
        return $this->endResponse($result);
    }
    /**
     * 查询会员的绑定记录
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerBind</p>
     * @param   int    $store_code
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerBind($params) {
        $store_code = isset($params['store_code']) ? $params['store_code'] : '0';
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : '0';
        if (empty($store_code) || empty($customer_id)) {
            return $this->endResponse(null,2001);
        }
        $args = array('customer_id' =>$customer_id,'store_code'=>$store_code );

        $sql = "select *  from  {$this->table_card_bind} where customer_id=:customer_id and store_code=:store_code limit 1";
        $data = $this->executeQuery($sql, $args); # 会员信息

        return $this->endResponse($data);

    }
    /**
     * 查询会员对应的会员卡 （返回多条数据）
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCardInfo</p>
     * @param   int    $customer_ids
     * @param   int    $store_code
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCardInfo($params) {
        // 去掉重复的id
        $customer_ids = isset($params['customer_ids']) ? $params['customer_ids'] : 0;
        $store_code = isset($params['store_code']) ? $params['store_code'] : 0;
        if (empty($customer_ids) || empty($store_code)) {
            return $this->endResponse(null, 2001); # 参数不正确
        }
        $ids = implode(",",$customer_ids);
        $sql = "select *  from  {$this->table_card_bind} where customer_id in ($ids) and store_code=$store_code";
        $result = $this->executeQuery($sql); # 会员的会员卡信息
        $result = $this->_getCardExtendInfo($result, $customer_ids);
        
        return $this->endResponse($result);
    }

    /**
     * 查询会员订单数据 （返回多条数据）
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerOrder</p>
     * <p>param:{uid,customer_id,start_time,end_time,page,offer}</p>
     * @param   array $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerOrder($params){
        $uid = isset($params['uid']) ? $params['uid'] : '';
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : '';
        $start_time = empty($params['start_time']) ? '' :$params['start_time'];
        $end_time = empty($params['end_time']) ? '' :$params['end_time'];
        $page = isset($params['page']) ? intval($params['page']) : 1; # 当前页
        $offer = isset($params['offer']) ? intval($params['offer']) : 10; # 每页显示条数
        if ( empty($customer_id) || empty($page) || empty($offer)) {
            return $this->endResponse(null,2001);
        }
        if (empty($uid)) {
            $where[] = "mo.muid = $customer_id";
        } else {

            $where[] = "mo.muid = $customer_id and mo.uid=$uid";
        }

        if (!empty($start_time) && !empty($end_time)) {
            $where[] = "mo.create_time >=$start_time and mo.create_time <=$end_time";
        }
        if (!empty($start_time) && empty($end_time)) {
            $where[] = "mo.create_time >=$start_time";
        }
        if (empty($start_time) && !empty($end_time)) {
            $where[] = "mo.create_time <=$end_time";
        }
        $where = ' WHERE ' . implode( ' AND ', $where );
        $from_table = "FROM {$this->tablePrefix}member_order mo
    	               LEFT JOIN {$this->tablePrefix}supplier_order so ON mo.supplier_order_id=so.id
    	               LEFT JOIN {$this->tablePrefix}user_supplier us ON us.uid=so.supplier_id
    	               LEFT JOIN {$this->tablePrefix}order_goods og ON mo.order_goods_id=og.id";

        $order = ' ORDER BY mo.id DESC';

        // 查询总数
        $sql = "SELECT COUNT(*) as CNT {$from_table}" . $where;
        $total = $this->executeQuery($sql);
        $data['totalNum'] = $total[0]['CNT'];


        $sql = "select mo.uid,mo.id,mo.create_time,mo.amount,mo.full_pay,mo.m_order_sn,mo.gather_time,mo.create_time,mo.supplier_order_id,mo.order_id,mo.offline,so.shipping_status,so.pay_status,us.company,og.goods_name
                       FROM {$this->tablePrefix}member_order mo
    	               LEFT JOIN {$this->tablePrefix}supplier_order so ON mo.supplier_order_id=so.id
    	               LEFT JOIN {$this->tablePrefix}user_supplier us ON us.uid=so.supplier_id
    	               LEFT JOIN {$this->tablePrefix}order_goods og ON mo.order_goods_id=og.id" . $where .
    	               $order . " limit " .($page - 1) * $offer . "," . $offer;

        $data['list'] = $this->executeQuery($sql); # 会员信息
        return $this->endResponse($data);

    }

    /**
     * 导入表中数据接口
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.importBuyer</p>
     * @param   array $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function importBuyer($params){
        ini_set("max_execution_time",0);
        $sql = "select count(*) total_num from {$this->tablePrefix}buyer";
        $total = $this->executeQuery($sql);

        $total_num = $total[0]['total_num'];

        // 执行的次数
        $t = 1;
        if ($total_num%5000 == 0) {
            $t = intval($total_num/5000);
        } else {
            $t = intval($total_num/5000) + 1;
        }
        for ($i = 1;$i<=$t; $i++) {
            $page = ($i-1)*5000;
            $sql = "select * from {$this->tablePrefix}buyer limit $page,5000";
            $result = $this->executeQuery($sql);
            //echo $sql."<br/>";
          foreach ($result as $v) {

                $username = $this->invoke('Customer.Customer.getCustomerId',array());
                if (empty($username['response']['id']) || !isset($username['response']['id'])) {
                    $username['response']['id'] = 0;
                }
                $data['store_code'] = $v['store_id']; #
                $data['card_id'] = $v['card_id'];
                $data['username'] = 'st'.($username['response']['id'] +1); # 会员账号
                $data['real_name'] = $v['name'];
                $data['customer_type'] = 1;
                $data['sex'] = $v['sex'];
                $data['store_name'] = $v['store_name'];
                $data['nation'] = '';
                $data['qq_number'] = '';
                $data['wx_number'] = '';
                $data['age'] = '';
                $data['province'] = $v['province'];
                $data['city'] = $v['city'];
                $data['district'] = $v['district'];
                $data['address'] = $v['address'];
                $data['birthday'] = '';
                $data['yl_birthday'] = '';
                $data['dp_province'] = '';
                $data['dp_city'] = '';
                $data['mobile'] = $v['mobile'];
                $data['phone_zone'] = $v['phone_zone'];
                $data['phone_number'] = $v['phone_number'];
                $data['referrer'] = '';
                $data['hobby'] = '';
                $data['remark'] = $v['remark'];
                $data['status'] = $v['status'];
                $data['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
                $res = $this->executeSingleTableCreate("{$this->table}", $data);
            }
        }
        return $this->endResponse(true);
    }

    /**
     * 会员是否开启短信提示
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.SMSCheck</p>
     * @param   int    $id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function SMSCheck($params){
        if (empty($params) || empty($params['id'])) {
            return $this->endResponse(null,2001);
        }
        $args = array('id'=>$params['id']);
        $sql = "SELECT sms_status FROM  {$this->table} WHERE id=:id AND `status`=1";
        $result = $this->executeQuery($sql, $args);
        if (count($result)>0) {
            if ( $result[0]['sms_status'] == 0) {
                return $this->endResponse('false');
            } else {
                return $this->endResponse('true');
            }
        }else {
            return $this->endResponse('false');
        }
    }

    /**
     * 会员今日签到列表
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getSignInInfo</p>
     * <p>param:{store_id:门店ID,card_no:会员卡号,page:当前页,offer:每页数据数}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getSignInInfo($params){
        $store_id = isset($params['store_id']) ? $params['store_id'] : 0;
        $card_no = isset($params['card_no']) ? $params['card_no'] : 0;
        $page = isset($params['page']) ? $params['page'] : 1;
        $offer = isset($params['offer']) ? $params['offer'] : 10;
        $time = date('Ymd');
        $result = array();
        //今日签到总数
        $today_total = $this->getTodayTotalBySignIn($store_id);
        //历史签到总数
        $total = $this->getTotalBySignIn($store_id);
        if (empty($store_id)) {
            return $this->endResponse(array('today_total' => $today_total, 'total' => $total), 2001);
        }
        //验证会员卡是否存在
        if (!empty($card_no)) {
            $args = array('card_no' => $card_no);
            $sql = "SELECT `customer_id` FROM {$this->tablePrefix}card_bind_relation
                    WHERE `card_no`=:card_no";
            $res = $this->executeQuery($sql, $args);
            if (empty($res)) {
                return $this->endResponse(array('today_total' => $today_total, 'total' => $total), 13000, '会员卡不存在');
            }
        }
        $args = array('store_id' => $store_id);
        $sql = "SELECT
                ci.`card_no`,
                ci.`store_id`,
                ci.`create_time`,
                ci.`create_date`,
                ci.`customer_id`,
                cus.`real_name`,
                cus.`sex`,
                cus.`province`,
                cus.`city`,
                cus.`district`
                FROM {$this->tablePrefix}customer_sign_in AS ci
                LEFT JOIN {$this->tablePrefix}customer AS cus
                ON ci.`customer_id` = cus.`id`
                WHERE ci.`create_date` = $time
                AND ci.`store_id`=:store_id";
        $sql_page = "SELECT
                    count(*) AS page
                    FROM {$this->tablePrefix}customer_sign_in AS ci
                    WHERE ci.`create_date` = $time
                    AND ci.`store_id`=:store_id";
        if (!empty($card_no)) {
            $args['card_no'] = $card_no;
            $sql .= " AND ci.`card_no`=:card_no ";
            $sql_page .= " AND ci.`card_no`=:card_no ";
        }
        $sql .=  " ORDER BY ci.`create_time` DESC LIMIT " . ($page - 1) * $offer . "," . $offer;
        $sql_page .=  " ORDER BY ci.`create_time` DESC ";
        $result = $this->executeQuery($sql,$args);
        $result_page = $this->executeQuery($sql_page,$args);
        $result['today_total'] = $today_total;
        $result['total'] = $total;
        $result['page'] = $result_page[0]['page'];
        return $this->endResponse($result);
    }

    /**
     * 会员历史签到列表
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getHistorySignInInfo</p>
     * <p>param:{store_id:门店id,strat_time:开始时间,end_time:结束时间,card_no:会员卡号,page:当前页,offer:每页数据数}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getHistorySignInInfo($params){
        $store_id = isset($params['store_id']) ? $params['store_id'] : 0;
        $strat_time = isset($params['strat_time']) ? $params['strat_time'] : 0;
        $end_time = isset($params['end_time']) ? $params['end_time'] : 0;
        $card_no = isset($params['card_no']) ? $params['card_no'] : 0;
        $page = isset($params['page']) ? $params['page'] : 1;
        $offer = isset($params['offer']) ? $params['offer'] : 10;
        $time = date('Ymd');
        if (empty($store_id)) {
            return $this->endResponse(null, 2001);
        }
        //今日签到总数
        $today_total = $this->getTodayTotalBySignIn($store_id);
        //历史签到总数
        $total = $this->getTotalBySignIn($store_id);
        //验证会员卡是否存在
        if (!empty($card_no)) {
            $arg = array('card_no' => $card_no);
            $sql = "SELECT `customer_id` FROM {$this->tablePrefix}card_bind_relation
                    WHERE `card_no`=:card_no";
            $result = $this->executeQuery($sql, $arg);
            if (empty($result)) {
                return $this->endResponse(array('today_total' => $today_total, 'total' => $total), 13000, '会员卡不存在');
            }
        }
        if (!empty($strat_time) && !empty($end_time) && $strat_time >= $end_time) {
            return $this->endResponse(array('today_total' => $today_total, 'total' => $total), 13003, '时间参数非法');
        }
        $args = array('store_id' => $store_id);
        $sql = "SELECT
                ci.`card_no`,
                ci.`store_id`,
                ci.`create_time`,
                ci.`create_date`,
                ci.`customer_id`,
                cic.`count_data`,
                cus.`real_name`,
                cus.`sex`,
                cus.`province`,
                cus.`city`,
                cus.`district`
                FROM {$this->tablePrefix}customer_sign_in AS ci
                LEFT JOIN {$this->tablePrefix}customer_sign_in_count AS cic
                ON ci.`customer_id` = cic.`customer_id`
                LEFT JOIN {$this->tablePrefix}customer AS cus
                ON ci.`customer_id` = cus.`id`
                WHERE ci.`store_id`=:store_id";
        //分页总数
        $sql_page = "SELECT
                cic.*
                FROM {$this->tablePrefix}customer_sign_in_count AS cic
                LEFT JOIN {$this->tablePrefix}customer_sign_in AS ci
                ON ci.`customer_id` = cic.`customer_id`
                WHERE ci.`store_id`=:store_id";
        if (!empty($strat_time)) {
            $time_min = $strat_time;
            $args['time_min'] = $time_min;
            $sql .= " AND ci.`create_date`>=:time_min ";
            $sql_page .= " AND ci.`create_date`>=:time_min ";
        }
        if (!empty($end_time)) {
            $time_max = $end_time;
            $args['time_max'] = $time_max;
            $sql .= " AND ci.`create_date`<=:time_max ";
            $sql_page .= " AND ci.`create_date`<=:time_max ";
        }
        if (!empty($card_no)) {
            $args['card_no'] = $card_no;
            $sql .= " AND ci.`card_no`=:card_no ";
            $sql_page .= " AND ci.`card_no`=:card_no ";
        }
        $sql .= " ORDER BY ci.`create_time` DESC ";
        // $sql_final 里order by条件必须与 $sql 一致！
        $sql_final = " SELECT * FROM ( ".$sql." ) AS a GROUP BY a.`customer_id` ORDER BY a.`create_time` DESC LIMIT " . ($page - 1) * $offer . "," . $offer;
        $sql_page .= " GROUP BY ci.customer_id ORDER BY ci.`create_time` DESC ";
        $sql_page_final = " SELECT COUNT(*) AS page FROM ( ".$sql_page." ) AS b ";
        $result = $this->executeQuery($sql_final,$args);
        $result_page = $this->executeQuery($sql_page_final,$args);
        $result['today_total'] = $today_total;
        $result['total'] = $total;
        $result['page'] = $result_page[0]['page'];
        return $this->endResponse($result);
    }

    /**
     * 会员个人签到列表
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerSignInInfo</p>
     * <p>param:{customer_id:会员ID,strat_time:开始时间,end_time:结束时间,page:当前页,offer:每页数据数}</p>
     * @param   array  $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerSignInInfo($params){
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
        $strat_time = isset($params['strat_time']) ? $params['strat_time'] : 0;
        $end_time = isset($params['end_time']) ? $params['end_time'] : 0;
        $page = isset($params['page']) ? $params['page'] : 1;
        $offer = isset($params['offer']) ? $params['offer'] : 10;
        if (empty($customer_id)) {
            return $this->endResponse(null, 2001);
        }
        //验证会员是否存在
        if (!empty($customer_id)) {
            $args = array('customer_id' => $customer_id);
            $sql = "SELECT `id` FROM {$this->tablePrefix}customer
                    WHERE `id`=:customer_id";
            $result = $this->executeQuery($sql, $args);
            if (empty($result)) {
                return $this->endResponse('', 13000, '会员卡不存在');
            }
        }
        if (!empty($strat_time) && !empty($end_time) && $strat_time >= $end_time) {
            return $this->endResponse('', 13003, '时间参数非法');
        }
        $args = array();
        $args['customer_id'] = $customer_id;
        $sql = "SELECT
                ci.`card_no`,
                ci.`create_time`,
                ci.`create_date`,
                ci.`store_id`,
                ns.`store_name`,
                ci.`user_id`,
                us.`real_name` AS user_name,
                cus.`id` AS customer_id,
                cus.`real_name`,
                cus.`sex`,
                cus.`province`,
                cus.`city`,
                cus.`district`,
                ns.`province` AS s_province,
                ns.`city` AS s_city,
                ns.`district` AS s_district
                FROM {$this->tablePrefix}customer_sign_in AS ci
                LEFT JOIN {$this->tablePrefix}customer AS cus
                ON ci.`customer_id` = cus.`id`
                LEFT JOIN {$this->tablePrefix}new_store AS ns
                ON ci.`store_id` = ns.`id`
                LEFT JOIN {$this->tablePrefix}user_staff AS us
                ON ci.`user_id` = us.`uid`
                WHERE ci.`customer_id`=:customer_id ";
        //分页总数
        $sql_page = "SELECT
                count(*) AS page
                FROM {$this->tablePrefix}customer_sign_in AS ci
                WHERE ci.`customer_id`=:customer_id";
        if (!empty($strat_time)) {
            $time_min = $strat_time;
            $args['time_min'] = $time_min;
            $sql .= " AND ci.`create_date`>=:time_min ";
            $sql_page .= " AND ci.`create_date`>=:time_min ";
        }
        if (!empty($end_time)) {
            $time_max = $end_time;
            $args['time_max'] = $time_max;
            $sql .= " AND ci.`create_date`<=:time_max ";
            $sql_page .= " AND ci.`create_date`<=:time_max ";
        }
        $sql .= " ORDER BY ci.`create_time` DESC LIMIT " . ($page - 1) * $offer . "," . $offer;
        $sql_page .= " ORDER BY ci.`create_time` DESC ";
        $result = $this->executeQuery($sql,$args);
        $result_page = $this->executeQuery($sql_page,$args);
        $result['page'] = $result_page[0]['page'];
        return $this->endResponse($result);
    }

    /**
     * 会员签到-个人信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerInfoBySignIn</p>
     * @param   int    $param
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerInfoBySignIn($params){
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
        if (empty($customer_id)) {
            return $this->endResponse(null, 2001);
        }
        $args['customer_id'] = $customer_id;
        $sql = "SELECT
                cus.*,
                cb.`card_no`,
                cb.`type_code`,
                ct.`card_name`
                FROM {$this->tablePrefix}customer cus
                LEFT JOIN {$this->tablePrefix}card_bind_relation cb
                ON cus.`id` = cb.`customer_id`
                LEFT JOIN {$this->tablePrefix}card_type ct
                ON cb.`type_code` = ct.`id`
                WHERE cus.`id`=:customer_id";
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }

    /**
     * 会员签到-签到完成后信息展示用数据
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerBySignIn</p>
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCustomerBySignIn($params){
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : '';
        if (empty($customer_id)) {
            return $this->endResponse(null, 2001);
        }
        $args = array('customer_id' => $customer_id);
        $sql = "SELECT
                cus.`id` AS customer_id,
                cus.`real_name`,
                cus.`sex`,
                cus.`province`,
                cus.`city`,
                cus.`district`
                FROM {$this->tablePrefix}customer AS cus
                WHERE cus.`id`=:customer_id";
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }

    /**
     * 会员签到-今日签到总数
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getTodayTotalBySignIn</p>
     * @param   int    $store_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getTodayTotalBySignIn($store_id){
        if (empty($store_id)) {
            return 0;
        }
        $time = date('Ymd');
        $args_total = array('store_id' => $store_id);
        $sql_total = "SELECT
                count(*) AS count
                FROM {$this->tablePrefix}customer_sign_in AS ci
                WHERE ci.`create_date` = $time
                AND ci.`store_id`=:store_id";
        $result = $this->executeQuery($sql_total, $args_total);
        return $result[0]['count'];
    }

    /**
     * 会员签到-历史签到总数
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getTotalBySignIn</p>
     * @param   int    $store_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getTotalBySignIn($store_id){
        if (empty($store_id)) {
            return 0;
        }
        $args_total = array('store_id' => $store_id);
        /*$sql_total = "SELECT
                count(*) AS count
                FROM {$this->tablePrefix}customer_sign_in_count  AS cic
                LEFT JOIN {$this->tablePrefix}customer_sign_in AS ci
                ON ci.`id` = cic.`last_sign_id`
                WHERE ci.`store_id`=:store_id";*/
        $sql_total = "SELECT count(DISTINCT(customer_id)) AS count FROM {$this->tablePrefix}customer_sign_in WHERE store_id=:store_id";
        $result = $this->executeQuery($sql_total, $args_total);
        return $result[0]['count'];
    }

    /**
     * 会员签到-省市区
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getRegionBySignIn</p>
     * @param   int    $ids
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getRegionBySignIn($params){
        $ids = isset($params['ids']) ? $params['ids'] : '';
        if (empty($ids)) {
            return $this->endResponse(null, 2001);
        }
        $sql = "SELECT `id`,`name` FROM `{$this->tablePrefix}region` WHERE `id` IN ($ids)";
        $result = $this->executeQuery($sql);
        return $this->endResponse($result);
    }

    /**
     * 会员签到-查询会单个会员信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerOneBySignIn</p>
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function getCustomerOneBySignIn($params){
         $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
         if (empty($customer_id)) {
             return $this->endResponse(null,2001);
         }
         $args = array('id'=>$customer_id);
         $sql = "select *  from  {$this->table} where id=:id ";
         $result = $this->executeQuery($sql, $args); # 会员信息
         return $this->endResponse($result);
     }

     /**
     * 会员签到-查询会员卡信息
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCustomerCardBySignIn</p>
     * @param   int    $customer_id
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
     public function getCustomerCardBySignIn($params){
         $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
         if (empty($customer_id)) {
             return $this->endResponse(null, 2001);
         }
         $args = array('customer_id'=>$customer_id);
         $sql = "select *  from  {$this->table_card_bind} where customer_id=:customer_id";
         $result = $this->executeQuery($sql, $args); # 会员信息
         $customerIdArr = array_column($result, 'customer_id');
         $result = $this->_getCardExtendInfo($result, $customerIdArr);
         
         return $this->endResponse($result);
     }

      /**
     * 查询会员对应的会员卡 （返回多条数据）
     * <p>请求参数说明</p>
     * <p>func:Customer.Customer.getCardInfo</p>
     * @param   int    $customer_ids
     * @param   int    $store_code
     * @return  void | Array
     * @version 1.0
     * @author  zym
     */
    public function getCardInfo123($params) {
        // 去掉重复的id
        $customer_ids = isset($params['customer_ids']) ? $params['customer_ids'] : 0;
        $store_code = isset($params['store_code']) ? $params['store_code'] : 0;
        if (empty($customer_ids) || empty($store_code)) {
            return $this->endResponse(null, 2001); # 参数不正确
        }
        $ids = implode(",",$customer_ids);
        $sql = "select *  from  {$this->table_card_bind} where customer_id in ($ids) and store_code=$store_code";
        $result = $this->executeQuery($sql); # 会员的会员卡信息
        return $this->endResponse($result);
    }



    /**
     * 根据用户ID 获取他的推荐关系信息
     * @auther wuzhizhong、
     * @Time 2015-9-14
     * @param   int    $customer_ids
     */
    public function getRecommendInfo($params){

        #接收参数customer_id
        $customer_ids = isset($params['customer_id']) ? $params['customer_id'] : 0;
        $page = isset($params['page']) ? $params['page'] : 1;
        $pagesize = isset($params['pagesize']) ? $params['pagesize'] : 10;


        #判断参数有效性
        if (empty($customer_ids)) {
            return $this->endResponse(null, 2001); # 参数不正确
        }


        #查询推荐我的人数据
        $sql = "select
                p1.create_user,
                p1.create_time,
                p2.id,
                p2.real_name,
                p2.mobile,
                p3.store_name,
                p4.card_no,
                p5.real_name as my_real_name
                from `{$this->table_recommend}` as p1
                left join `{$this->table}` as p2 on p1.pid=p2.id
                left join `{$this->table_store}` as p3 on p1.store_id=p3.id
                left join `{$this->table_card_bind}` as p4 on p1.pid=p4.customer_id
                left join `{$this->table}` as p5 on p1.customer_id=p5.id
                where p1.customer_id=".$customer_ids." order by create_time desc";
        $t_userInfo = $this->executeQuery($sql); # 推荐我的信息



        #查询我推荐的人数据
        $bsqlt = "select
                count(*) as allcount
                from `{$this->table_recommend}` as p1
                left join `{$this->table}` as p2 on p1.customer_id=p2.id
                left join `{$this->table_store}` as p3 on p1.store_id=p3.id
                left join `{$this->table_card_bind}` as p4 on p1.customer_id=p4.customer_id
                where p1.pid=".$customer_ids;
        $all_b_userInfo = $this->executeQuery($bsqlt); # 被我推荐的人信息分页总数



        $limitStart = ($page-1)*$pagesize;
        $limit = " limit $limitStart,$pagesize"; #分页数据
        $bsql = "select
                p1.create_user,
                p1.create_time,
                p2.id,
                p2.real_name,
                p2.mobile,
                p3.store_name,
                p4.card_no
                from `{$this->table_recommend}` as p1
                left join `{$this->table}` as p2 on p1.customer_id=p2.id
                left join `{$this->table_store}` as p3 on p1.store_id=p3.id
                left join `{$this->table_card_bind}` as p4 on p1.customer_id=p4.customer_id
                where p1.pid=".$customer_ids." order by create_time desc ".$limit;
                 $b_userInfo = $this->executeQuery($bsql); # 被我推荐的人信息



               $result['t_user'] = $t_userInfo;                               #推荐我的人
               $result['b_user'] = $b_userInfo;                               #被我推荐的人当页数据
               $result['b_user_count'] = $all_b_userInfo[0]['allcount'];      #被我推荐的人总数
               $result['b_user_page'] = $page;                                #被我推荐的页数
               $result['b_user_pagesize'] = $pagesize;                        #被我推荐的显示多少条数据


              return $this->endResponse($result);
    }

    
    /**
     * 从card_bind_relation表获取的会员卡数据添加余额、密码、状态字段
     * @param unknown $list
     * @param unknown $customerIdArr
     * @return multitype:
     */
    private function _getCardExtendInfo($list, $customerIdArr){
    	$list = $this->resetArrayIndex($list, 'customer_id');
        $params = array(
            'accountType' => 1,
            'customerId' => $customerIdArr,
        );
    	$cardExtentInfo = $this->invoke('TM.Account.getAccountsInfo', $params)['response'];
    	foreach($cardExtentInfo as $k=>$v){
            if(isset($list[$v['customerId']])){
        		$list[$v['customerId']]['status'] = $v['status'];
        		$list[$v['customerId']]['account_balance'] = $v['availableBalance'];
                $list[$v['customerId']]['frozen_amount'] = $v['totalBalance'] - $v['availableBalance'];
            }
    	}
    	$list = array_values($list);
    	return $list;
    }
}