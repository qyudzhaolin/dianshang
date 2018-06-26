<?php

/**
 * | 物流相关操作Api
 */

namespace Api\Other;

use System\Base;

class Express extends Base {

    private $tablePrefix = null;

    public function __construct() {
        parent::__construct();

        $this->tablePrefix = $this->getMainDbTablePrefix();
    }

    /**
     * 根据订单号获取物流信息
     * <p>请求参数说明:</p>
     * <p>func: Wap.Express.getExpress</p>
     * @param    int    $orderId  订单编号id 
     * @param    int    $type      订单类型  1  supplier_order    2 return_order,4:门店
     * @return   void | array
     * @version  1.0
     * @author   刘靖<liujing@vacn.com.cn>
     */
    public function getExpress($params) {
        $orderId = intval($params['orderId']);
        $type = isset($params['type']) ? $params['type'] : 1;
        if ($orderId == 0) {
            return $this->endResponse(null, 2001);
        }
        $sql = "SELECT data_info,com,invoice_no FROM {$this->tablePrefix}express_info WHERE order_id=:orderId and type=:type";
        $result = $this->executeQuery($sql, array('orderId' => $orderId, 'type' => $type));

        if (sizeof($result) == 0) {
            $data = array('time' => date('Y-m-d H:i:s'), "express_message" => ' 快递信息未更新');
            return $this->endResponse($data);
        } else {
            $data = $result[0];

            $sql = "SELECT name FROM {$this->tablePrefix}express WHERE status=1 AND com=:comName";
            $result = $this->executeQuery($sql, array('comName' => $data['com']));
            $data['comName'] = $result[0]['name'];
        }
        return $this->endResponse($data);
    }
    
    /**
     * 根据包裹好获取物流信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Express.getExpressByPackageids</p>
     * @param   array  $packageIdsArr  包裹ID的数组
     * @return  void | array
     * @version 1.0
     * @author  刘靖<liujing@vacn.com.cn>
     */
    public function getExpressByPackageids($params){
    	$packageidsArr = $params;
    	if(empty($packageidsArr)){
    		return $this->endResponse(null, 2001);
    	}
    	
    	$expressInfo = array();
    	foreach($packageidsArr as $val){
    		$sql = "SELECT data_info,com,invoice_no FROM {$this->tablePrefix}express_info WHERE order_id=:orderId and type=6";
        	$result = $this->executeQuery($sql, array('orderId' => $val));
        	if (sizeof($result) == 0) {
        		
        		$sql = "SELECT shipping_name,invoice_no FROM {$this->tablePrefix}package WHERE id=:id";
        		$result = $this->executeQuery($sql, array('id' => $val));
        		$data = $result[0];
        		$data = array('data_info'=>'', 'comName'=>$data['shipping_name'], 'invoice_no'=>$invoice_no);
        	} else {
        		$data = $result[0];
        		
        		$sql = "SELECT name FROM {$this->tablePrefix}express WHERE status=1 AND com=:comName";
        		$result = $this->executeQuery($sql, array('comName' => $data['com']));
        		$data['comName'] = $result[0]['name'];
        		if(!empty($data['data_info'])){
        			$data['data_info'] = json_decode($data['data_info'], true);
        		}
        	}
        	$expressInfo[] = $data;
    	}
    	
    	return $this->endResponse( $expressInfo );
    	
    }

}

?>
