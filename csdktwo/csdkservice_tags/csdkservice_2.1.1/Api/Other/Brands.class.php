<?php
/**
 * | 品牌街
 */
//命名类空间，在api/other 下面 引入base类
namespace Api\Other;
use System\Base;
use Api\Common\RedisKeyMap;
class Brands extends Base {
    private $tablePrefix = null;
    // 魔术方法，构造函数，获取表名前缀实际的是16860_
    public function __construct() {
        parent::__construct();
        $this->tablePrefix = $this->getMainDbTablePrefix();
    }
    /**
     * 获取所有网页活动列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Brands.listBrands </p>
     * <p>response:array()所有的网页活动数据 </p>
     * @param   int    $eid     品牌街品牌id
     * @param   int    $keyPath 浏览模式判断
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listBrands($params) {
        // 获取品牌id 以及浏览模式
        $eid = $params['eid'];
        if(empty($eid) )  return $this->endResponse(null, 2001);
        $keyPath = $params['keyPath'];
        // 判断是否是预览  是预览状态，那么就不用过滤上下架
        if($keyPath){
            //查看是否可以预览
            $apiPath = 'Other.Brands.isPreview';
            $params = array(
                'keyPath'=>$keyPath,
            );
            $res = $this->invoke($apiPath,$params);
            if($res['response'] == 0){
                return $this->endResponse(false,2001);
            }
            $where = "";
            $args = array(
                'id' => $eid,
             );
        }else{
            $args = array(
                'id' => $eid,
                'status'=>1
            );
            $where = "and status=:status ";
        }
        // sql 品牌查询语句,status 必须是上架状态
        $streetSql = "SELECT * FROM {$this->tablePrefix}brand_street WHERE identifier=:id " . $where;
        
        $street = $this->executeQuery($streetSql, $args);
        // sql 品牌商品查询, type 类型必须是品牌街 根据同一个品牌街identifier 获取所有商品  商品不为删除状态 根据排序获取商品
        $goodsSql = "SELECT * FROM {$this->tablePrefix}goods_collection_item WHERE " . "`type`=:type AND identifier=:identifier and status=:status ORDER BY sort";
        $args = array(
            'identifier'=>$street['0']['id'],
            'status'=>1,
            'type'=>1,
        );
        $goods  = $this->executeQuery($goodsSql, $args);
        // 如果存在品牌返回品牌信息，不存在返回错误信息
        if ($goods) {
            // 遍历同一品牌下所有商品， 同时根据商品类型（1：普通商品，2：特卖商品） 获取对应商品数据
            foreach ($goods as $key => $value) {
                $goodsOne = '';
                //普通 商品必须为上架未删除商品
                if ($value['goods_type'] == 1) {
                    $goodsOneSql = "SELECT a.*,b.image as superscript_image ,b.status as superscript_status,b.localion FROM {$this->tablePrefix}goods a left join {$this->tablePrefix}superscript b on a.script_id = b.id WHERE  a.id=:id and a.status=:status ";
                    $argsOne= array(
                        'id'=>$value['goods_id'],
                        'status'=>1
                    );
                    $goodsOne  = $this->executeQuery($goodsOneSql, $argsOne);
                    //特卖 商品必须为上架未删除商品
                }elseif ($value['goods_type'] == 3) {
                    $now_time = time();
                    $goodsOneSql = "SELECT a.*,b.image as superscript_image ,b.status as superscript_status,b.localion FROM {$this->tablePrefix}special_goods a left join {$this->tablePrefix}superscript b on a.script_id = b.id WHERE a.id=:id and a.status=:status ";
                    $argsOne= array(
                        'id'=>$value['goods_id'],
                        'status'=>1
                    );
                    $goodsOne  = $this->executeQuery($goodsOneSql, $argsOne);
                    if ($goodsOne) {
                        $goodsOne['0']['goods_number'] = $goodsOne['0']['last_number'];
                    }
                }
                $goodsTwoSql = "SELECT * FROM {$this->tablePrefix}goods_attr WHERE  attr_goods_id=:id AND type=:type  and status=:status ";

                $argsTwo= array(
                        'id'=>$value['goods_id'],
                        'type'=>$value['goods_type']-1,
                        'status'=>1
                );
                $goodsTwo  = $this->executeQuery($goodsTwoSql, $argsTwo);

                if ($goodsOne) {
                    //-------------
                    //分时特卖处理
                    if (isset($goodsOne['0']['divide_time']) && $goodsOne['0']['divide_time'] == 1) {
                        if ($this->checkTime($goodsOne['0']['divide_start'],$goodsOne['0']['divide_end'])) {
                            $arr["$key"]['on_special_goods'] = 1;
                        }
                    }
                    //-------------------------
                    // 筛选商品需要的数据
                    $arr["$key"]['id'] = $goodsOne['0']['id'];
                    $arr["$key"]['goods_img'] = $goodsOne['0']['goods_img'];
                    $arr["$key"]['no_number'] = $goodsOne['0']['goods_number'];
                    $arr["$key"]['goods_type'] = $value['goods_type'];
                    $arr["$key"]['goods_name'] = $goodsOne['0']['goods_name'];
                    $arr["$key"]['shop_price'] = $goodsOne['0']['shop_price'];
                    $arr["$key"]['special_price'] = $goodsOne['0']['special_price'];
                    $arr["$key"]['market_price'] = $goodsOne['0']['market_price'];
                    $arr["$key"]['sell_number'] = $goodsOne['0']['sell_number'];
                    $arr["$key"]['is_share'] = $goodsOne['0']['is_share'];
                    $arr["$key"]['share_price'] = $goodsTwo['0']['share_price'];
                    $arr["$key"]['localion'] = $goodsOne['0']['localion'];
                    $arr["$key"]['superscript_image'] = $goodsOne['0']['superscript_image'];
                    $arr["$key"]['superscript_status'] = $goodsOne['0']['superscript_status'];
                }
            }
        } elseif ($street) {
            return $this->endResponse($street);
        } else {
            return $this->endResponse(null, 2001);
        }
        $street['0']['goods'] = $arr;
        // 执行查询语句 返回结果列表
        return $this->endResponse($street);
    }
    
    /**
     * 设置预览值
     * <p>请求参数说明:</p>
     * <p>func: Other.Brands.setPerviewKey </p>
     * <p>response:array() </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function setPerviewKey($params){
        $redis = R();
        $preview_id = $redis->incr('preview_id');
        $previewKeyPath = RedisKeyMap::getPreviewKey($preview_id);
        $redis->set($previewKeyPath,1);
        return $this->endResponse($preview_id);
    }
    
    /**
     * 判断是否可以预览
     * <p>请求参数说明</p>
     * <p>func: Other.Brands.isPreview </p>
     * <p>response:0 不可以预览   1 可以预览</p>
     * @param   int    $keyPath
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function isPreview($params){
        $redis = R();
        $preview_id = $params['keyPath'];
        $keyPath = 'preview:'.$preview_id;
        $preview = $redis->get($keyPath);
        //不可以预览
        if(!$preview){
            return $this->endResponse(0);
        }
        //可以预览
        $redis->delete($keyPath);
        return $this->endResponse(1);
    }

    /**
     * 分时特卖商品时间检测    内部方法
     */
    protected  function checkTime($start , $end){
        $now = time();
        $start = strtotime($start);
        $end = strtotime($end);
        if ($now >= $start && $now < $end) {
            return true;
        }else{
            return true;
        }
    }

}
?>