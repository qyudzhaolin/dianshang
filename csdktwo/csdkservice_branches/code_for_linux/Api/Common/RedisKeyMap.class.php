<?php
/**
 * | Redis内相关的key映射类
 */

namespace Api\Common;
/**
 *  Redis内相关的key映射类
 * @author  刘靖<liujing@vacn.com.cn>
 *
 */
class RedisKeyMap{
	//商品类型映射数据
	private static $goodsTypeMap = array(1=>'goods', 2=>'seckill_goods', 3=>'special_goods');
	
	
    /**
     * 获取Redis内指定属性的商品stock的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getStockKey</p>
     * @param   integer $goodsId
     * @param   integer $goodsType
     * @param   integer $attrId
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getStockKey($goodsId, $goodsType, $attrId, $extra = "") {
        //如果是数字, 则自动转换为商品类型, 否则则直接默认其已经是商品类型
        $goodsType = is_int($goodsType) ? self::$goodsTypeMap[$goodsType] : $goodsType;
        if ($extra) {
            return "stock:{$goodsType}:{$goodsId}:{$attrId}_{$extra}";
        } else {
            return "stock:{$goodsType}:{$goodsId}:{$attrId}";
        }
    }

    /**
     * 获取Redis内指定用户购买指定商品的记录数量的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getBuyCountKey</p>
     * @param   integer $uid
     * @param   integer $goodsId
     * @param   integer $goodsType
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getBuyCountKey($uid, $goodsId, $goodsType){
    	//如果是数字, 则自动转换为商品类型, 否则则直接默认其已经是商品类型
    	$goodsType = is_int($goodsType) ? self::$goodsTypeMap[$goodsType] : $goodsType;
    	return "buyCount:{$goodsType}:{$goodsId}:{$uid}";
    }
    
    
    /**
     * 获取Redis内指定用户的临时订单的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getTempOrderKey</p>
     * @param   integer $uid
     * @param   integer $toid	零食订单id
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getTempOrderKey($uid, $toid){
    	return "order:tempOrder:{$uid}:{$toid}";
    }
    
    /**
     * 获取Redis内指定用户指定订单号订单的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getUnpayOrderKey</p>
     * @param   integer $uid
     * @param   integer $orderId
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getUnpayOrderKey($uid, $orderId){
    	return "order:unpayOrder:{$uid}:{$orderId}";
    }
    
    /**
     * 获取Redis内指定用户所有待付款订单号列表的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getUnpayOrderListKey</p>
     * @param   integer $uid
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getUnpayOrderListKey($uid){
    	return "order:unPayOrder:{$uid}:unpayIdList";
    }

    /**
     * 获取Redis内指定用户所有仓库订购的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getHouseOrderGoodsKey</p>
     * @param   integer $store_id
     * @return  string  $store_id
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getHouseOrderGoodsKey($store_id){
        return "stores:housegoods:{$store_id}";
    }
    /**
     * 获取Redis内指定用户所有仓库订购的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getEditGoods</p>
     * @param   integer $store_id
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getEditGoods($key){
        return "stores:editGoods:{$key}";
    }


    /**
     * 获取Redis内指定用户所有供应商直发列表的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getSupOrderGoodsKey</p>
     * @param   integer $store_id
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getSupOrderGoodsKey($store_id){
        return "stores:supgoods:{$store_id}";
    }
    /**
     * 获取Redis内指定用户所有供应商非生鲜列表的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getShOrderGoodsKey</p>
     * @param   integer $store_id
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getShOrderGoodsKey($store_id){
        return "stores:shgoods:{$store_id}";
    }
    /**
     * 获取Redis内指定便利店订货的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getEcGoodsKey</p>
     * @param   integer $store_id
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn>
     */
    public static function getEcGoodsKey($store_id){
        return "stores:ecgoods:{$store_id}";
    }
    /**
     * 获取Redis内订单编码 和 订单标识(uid:orderId)之间对应关系的映射的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getOrderSNMapKey</p>
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getOrderSNMapKey(){
    	return "order:sn_id_map";
    }
    
    /**
     * 获取Redis内指定用户已取消的订单的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getCanceledOrderKey</p>
     * @param   integer $uid
     * @param   integer $orderId
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getCanceledOrderKey($uid, $orderId){
    	return "order:canceled:{$uid}:{$orderId}";
    }
    
    /**
     * 获取Redis内指定用户已取消的订单列表的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getCanceledOrderListKey</p>
     * @param   integer $uid
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getCanceledOrderListKey($uid){
    	return "order:canceled:{$uid}:cancelIdList";
    }
    
    /**
     * 获取Redis内指定用户购物车总数的keypath
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getUserCartTotalKey</p>
     * @param   integer $uid
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getUserCartTotalKey($uid){
    	return "cart_{$uid}";
    }
    
     /**
     * 获取Redis内同步进度的key
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getUnpaySyncKey</p>
     * @param   integer $uid
     * @param   integer $orderId
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getUnpaySyncKey($uid,$orderId){
    	return "order:unpaysync:{$uid}:{$orderId}";
    }
    
     /**
     * 获取Redis内同步订单的锁
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getUnpayLockKey</p>
     * @param   integer $uid
     * @param   integer $orderId
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getUnpayLockKey($uid,$orderId){
    	return "order:unpaylock:{$uid}:{$orderId}";
    }
    
    /**
     * 获取Redis内已支付的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getOrderPayKey</p>
     * @param   integer $uid
     * @param   integer $orderId
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getOrderPayKey($uid,$orderId){
    	return "order:payOrder:{$uid}:{$orderId}";
    }
    
    /**
     * 获取Redis内某用户所有订单的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getAllOrderKey</p>
     * @param   integer $uid
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getAllOrderKey($uid,$pagenum){
        return "order:AllOrder:{$uid}:{$pagenum}";
    }
    
    /**
     * 获取Redis内某商品频道页面的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getGoodsChannelKey</p>
     * @param   string $channel_flag 商品频道标识
     * @param   intval $p 当前页数
     * @param   string $sort排序字段字符串
     * @param   int    $is_share
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getGoodsChannelKey($channel_flag, $p, $sort, $is_share){
        return "cache:goodschannel:{$channel_flag}_{$p}_{$sort}_{$is_share}";
    }
    
     /**
     * 获取Redis内预览的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getPreviewKey</p>
     * @param   string $channel_flag 商品频道标识
     * @param   int    $id redis中的自增id
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getPreviewKey($id){
        return "preview:{$id}";
    }

    /**
     * 获取Redis内某商品分类页面的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getCateGoodsKey</p>
     * @param   string $cid 前台商品2级分类id
     * @param   intval $p 当前页数
     * @param   string $sort 排序字段字符串
     * @param   int    $is_share
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getCateGoodsKey($cid, $p, $sort, $is_share){
        return "cache:categoods:{$cid}_{$p}_{$sort}_{$is_share}";
    }
    
    /**
     * 门店限购键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getStoreBuyCount</p>
     * @param   intval $storeId
     * @param   intval $goodsId
     * @param   intval $goodsType
     * @param   int    $is_share
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getStoreBuyCount($storeId, $goodsId, $goodsType){
        $goodsType = is_int($goodsType) ? self::$goodsTypeMap[$goodsType] : $goodsType;
        return "store:{$goodsType}:{$goodsId}:{$storeId}";
    }

	/**
     * 获取Redis内某次MYSQL监控日志的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getMonitorMysqlKey</p>
     * @param   int $type 记录类型
     * @param   int $guid GUID
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getMonitorMysqlKey($type, $guid) {
    	return "monitor:mysql:{$type}:{$guid}";
    }
	
	/**
     * 获取Redis内搜索商品的键
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getSearchSphinxGoodsKey</p>
     * @param   string $keyword 商品频道标识
     * @param   string $sort 排序字段字符串
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static function getSearchSphinxGoodsKey($keyword, $sort){
        return "cache:searchgoodssphinx:{$keyword}_{$sort}";
    }
    
    //支付锁定key
    public static  function getOrderLockKey($uid,$orderId){
        return "orderLock:{$uid}:{$orderId}";
    }
    /**
     * 获取支付回调锁
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getPayLockKey</p>
     * @param   int     $buyFrom
     * @param   string  $orderSN
     * @return  string
     * @version 1.0
     * @author  API     <websiteteam@vacn.com.cn
     */
    public static  function getPayLockKey($buyFrom,$orderSN){
        return "payLock:{$buyFrom}:{$orderSN}";
    }
    /**
     * 支付回调信息
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getPayInfoKey</p>
     * @param   string $keyword 商品频道标识
     * @param   string $sort 排序字段字符串
     * @return  string
     * @version 1.0
     * @author  API    <websiteteam@vacn.com.cn
     */
    public static function getPayInfoKey($buyFrom,$orderSN){
        return "payInfo:{$buyFrom}:{$orderSN}";
    } 
    /**
     * 支付宝支付的时候添加做个标记
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getAliPayFlag</p>
     * @param   string $orderSN
     * @return  string $orderSN
     * @version 1.0
     * @author  API    <websiteteam@vacn.com.cn
     */
    public static function getAliPayFlag($orderSN){
        return "alipayflag:{$orderSN}";
    }
    
    /**
     * 创建支付任务时的锁
     * <p>请求参数说明</p>
     * <p>func: Common.RedisKeyMap.getCreatePayTask</p>
     * @param    string $orderSN
     * @return   string $orderSN
     * @version  1.0
     * @author   API    <websiteteam@vacn.com.cn
     */
    public static function getCreatePayTask($orderSN){
        return "payTask:{$orderSN}";
    } 
}

?>
