<?php
/**
 * | 网页活动模版API
 */
//命名类空间，在api/other 下面 引入base类
namespace Api\Other;
use Library\idBase; # id加密函数
use System\Base;
class Activity extends Base {
    private $tablePrefix = null;
    // 魔术方法，构造函数，获取表名前缀实际的是16860_
    public function __construct() {
        parent::__construct();
        $this->tablePrefix = $this->getMainDbTablePrefix();
    }
    /**
     * 获取所有网页活动列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.listWebActivity </p>
     * <p>response:array()所有的网页活动数据 </p>
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listWebActivity() {
        // sql 查询语句,status 删除标记必须不等于1， ID倒序获取
        $sql = "SELECT * FROM {$this->tablePrefix}web_activity WHERE status!=:status ORDER BY id DESC";
        $args = array(
            'status' => '1'
        );
        // 执行查询语句 返回结果列表
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }
    /**
     * 添加一个网站活动
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.addWebActivity </p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   string $activityName    网站活动名字
     * @param   string $activityContent 网站活动内容
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function addWebActivity($params) {
        // 获取网站活动名字，网站活动内容
        $activityName = $params['activityName'];
        $activityContent = $params['activityContent'];
        // 网站活动名字必须不能为空，为空的话，则退出返回错误
        if(empty($activityName) || empty($activityContent) )
            return $this->endResponse(null, 2001);
        // 拼接sql 语句  添加name content 两个字段，ID自增
        $fields = 'name, content';
        $sql = "INSERT INTO {$this->tablePrefix}web_activity(".$fields.') VALUES( :'.str_replace(', ', ', :', $fields).')';
        $args = array(
            'name' => $activityName,
            'content' => $activityContent
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);

    }
    /**
     * 获取一条网页活动列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.listOneWebActivity </p>
     * <p>response:array() 对应的一条网页活动数据 </p>
     * @param   int    $id   网站活动ID
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listOneWebActivity($params) {
        // 获取的id需要是整型
        $base = new idBase();
        $id = $base->decode($params['id']);
        // sql 查询语句, status 删除标记必须不等于1，ID 为需要的ID
        $sql = "SELECT * FROM {$this->tablePrefix}w_activity WHERE status!=:status AND id=:id";
        $args = array(
            'id'=>$id,
            'status' => '1'
        );
        // 执行查询语句 返回结果列表
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }
    /**
     * 添加一个网站活动
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.editWebActivity </p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   string $activityName    网站活动名称
     * @param   string $activityContent 网站活动内容
     * @param   int    $id              网站活动id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function editWebActivity($params) {
        // 获取网站活动名字，网站活动内容，网站活动ID ，ID 必须为整型
        $id = intval($params['id']);
        $activityName = $params['activityName'];
        $activityContent = $params['activityContent'];
        // 网站活动名字，网站活动内容，网站活动ID必须不能为空
        if(empty($activityName) || empty($activityContent) || empty($id) )
            return $this->endResponse(null, 2001);
        // 拼接sql 语句，更新网站活动ID 对应的数据，更新数据的时候，网站活动不能已经删除，即status!=1
        $sql = "UPDATE  {$this->tablePrefix}web_activity SET "."name=:name".", content=:content WHERE id=:id AND status!=:status";
        $args = array(
            'status'=>'1',
            'name' => $activityName,
            'id' => $id,
            'content' => $activityContent
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }
    /**
     * 删除一个网站活动
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.deleteWebActivity </p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   int    $id   网站活动id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function deleteWebActivity($params) {
        //获取网站活动ID 网站活动ID必须为整型
        $id = intval($params['id']);
        //网站活动ID 必须不能为空
        if(empty($id))
            return $this->endResponse(null, 2001);
        // 拼接sql 语句，逻辑删除网站活动，即更新status=1
        $sql = "UPDATE  {$this->tablePrefix}web_activity SET  status=:status WHERE id=:id ";
        $args = array(
            'status' => '1',
            'id' => $id
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }
    /**
     * 加密网站活动ID
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.encode </p>
     * <p>response:false 失败 | array 成功返回网站活动数据 </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function encode($params){
        $base = new idBase();
        foreach ($params as $k => $v) {
            $result["$k"] = $v;
            $result["$k"]['encodeId'] = $base->encode($v['id']);
        }
        return $this->endResponse($result);
    }
    
    /**
     * 获取会员日默认楼层信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.getDefaultFloorInfo</p>
     * <p>response: array 商品楼层信息</p>
     * @param   int    $activity_id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function getDefaultFloorInfo ($params) {
        $activityId = $params['activity_id'];
        $startTime = strtotime(date('Y-m-d H:i:s'));
        if (empty($activityId)) {
        	$sql = "select * from 16860_activity where status=1 limit 1";
        } else {
            $sql = "select * from 16860_activity where id='$activityId' limit 1";
        }
    	
    	// 执行sql语句
    	$result = $this->executeQuery($sql);
    	return $this->endResponse($result);
    }
    /**
     * 获取会员日其他楼层信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Activity.getNormalFloorInfo</p>
     * <p>response: array 商品楼层信息</p>
     * @param   int    $activity_id    活动ID
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function getNormalFloorInfo($params) {
        $activityId = $params['activity_id'];
        
        $sql = "SELECT gs.name,gs.floor_img,gs.floor_lvl,g.goods_name,g.goods_number,g.goods_img,gr.gid,
	            g.sell_number,g.shop_price,g.market_price,gr.price FROM 16860_goods_recommend gr
                LEFT JOIN 16860_goods_showcase gs ON gr.wid = gs.id
                LEFT JOIN 16860_goods g ON gr.gid = g.id
    	        LEFT JOIN 16860_activity  ay on gr.activity_id = ay.id
    	         WHERE gs.del_flag = 1 AND gr.del_flag = 1 AND vip_floor = 1 and g.status=1";
        
        if (empty($activityId)) {
        	$sql .= " and ay.status=1 ORDER BY gs.floor_lvl,gr.rand";
        } else {
            $sql .= " and ay.id='$activityId' ORDER BY gs.floor_lvl,gr.rand";
        }
    	
    	// 执行sql语句
    	$result = $this->executeQuery($sql);
    	return $this->endResponse($result);
    }
    
    /**
     * 预览频道
     * <p>请求参数说明</p>
     * <p>func: Other.Activity.preViewChannel</p>
     * @param   string $data
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function preViewChannel($params) {
        $data = $params['data'];
        $redis = R();
        $redis->del('channel_preview');
    	$flag = $redis->setex('channel_preview',10,$data);
    	return $this->endResponse($flag);
    }
    /**
     * 获得预览数据
     * <p>请求参数说明</p>
     * <p>func: Other.Activity.getPreViewData</p>
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function getPreViewData() {
        $redis = R();
        $data = $redis->get('channel_preview');
        return $this->endResponse(json_decode($data));
    }
}
?>