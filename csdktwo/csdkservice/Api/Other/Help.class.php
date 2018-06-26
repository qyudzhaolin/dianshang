<?php
/**
 * | 用户反馈相关api
 */

namespace Api\Other;
use System\Base;
class Help extends BASE {
    private $tablePrefix = null;
    // 魔术方法，构造函数
    public function __construct() {
        parent::__construct();
        $this->tablePrefix = $this->getMainDbTablePrefix();
    }

    /**
     *
     * 父级目录列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Help.listParent </p>
     * <p>response：array $result 父级目录列表</p>
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listParent() {
        // sql 查询语句,需要内容，根据type==1 获取父目录
        $sql = "SELECT * FROM {$this->tablePrefix}help WHERE " . " type=:type AND parent_name!=:parent_name ORDER BY parent_weight";
        $args = array(
            'type' => 1,
            'parent_name' => ''
        );
        // 执行查询语句 返回结果列表
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }

    /**
     * 添加父级目录
     * <p>请求参数说明:</p>
     * <p>func: Other.Help.addParent </p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   string $parentName   父级目录名字
     * @param   string $parentWeight 父级目录排序权重
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public  function addParent($params) {
        //获取对应需要的数据，父级目录名字parentName ， 父级目录排序权重parentWeight
        $parentName = $params['parentName'];
        $parentWeight = intval($params['parentWeight']);
        //检查参数 父级目录名字，父级目录排序权重 不能为空
        if(empty($parentName) || empty($parentWeight) )
            return $this->endResponse(null, 2001);
        // 拼接sql 语句，父级目录名字,父级目录排序权重,父级目录type=1
        $fields = 'parent_name, type, parent_weight';
        $sql = "INSERT INTO {$this->tablePrefix}help(".$fields.') VALUES( :'.str_replace(', ', ', :', $fields).')';
        $args = array(
            'parent_name' => $parentName,
            'parent_weight' => $parentWeight,
            'type' => 1
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }

    /**
     *
     * 子目录列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Help.listChild </p>
     * <p>response：array $result 子目录列表</p>
     * @param   int    $id  父级目录的 ID
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listChild($params) {
        $parentId = $params['id'] ;
        // sql 查询语句,需要内容，根据type==2 和 父ID 获取子目录
        $sql = "SELECT * FROM {$this->tablePrefix}help WHERE " . " type=:type AND child_name!=:child_name AND child_weight!=:child_weight AND parent_id=:parent_id ORDER BY child_weight";
        $args = array(
            'type' => 2,
            'parent_id' => $parentId,
            'child_weight' => '',
            'child_name' => ''
        );
        // 执行查询语句 返回结果列表
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }

    /**
     * 添加子目录
     * <p>请求参数说明:</p>
     * <p>func: Other.Help.addChild </p>
     * <p>params: {  childName:子目录名字,childWeight:子目录排序权重,childContent:子目录对应的内容,parentId:父级目录对应的ID}</p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public  function addChild($params) {
        //获取对应需要的数据，子目录名字childName,子目录排序权重childWeight,子目录对应的内容childContent,父级目录对应的ID parentId:
        $childName = $params['childName'];
        $childWeight = intval($params['childWeight']);
        $childContent = $params['childContent'];
        $parentId = intval($params['parentId']) ;
        //检查参数 父级目录名字，父级目录排序权重 不能为空
        if(empty($childName) || empty($childWeight) || empty($childContent) || empty($parentId) )
            return $this->endResponse(null, 2001);
        // 拼接sql 语句
        $fields = 'child_name, type, child_weight, child_content, parent_id';
        $sql = "INSERT INTO {$this->tablePrefix}help(".$fields.') VALUES( :'.str_replace(', ', ', :', $fields).')';
        $args = array(
            'child_name' => $childName,
            'child_weight' => $childWeight,
            'child_content' => $childContent,
            'parent_id' => $parentId,
            'type' => 2
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }


    /**
     * 编辑子目录名字，内容以及排序
     * <p>请求参数说明：</p>
     * <p>func: Other.Help.editChild</p>
     * <p>params:{editChildName: 子目录名字, editChildWeight:子目录排序权重, editChildContent:子目录详细内容,editId:编辑改变的ID}</p>
     * <p>response :false 失败 | integer 成功返回1 </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     * */
    public function editChild($params){
        $editChildName = $params['editChildName'] ;
        $editChildWeight = $params['editChildWeight'] ;
        $editChildContent = $params['editChildContent'] ;
        $editId = $params['editId'] ;
        if (empty($editChildName) || empty($editChildWeight) || empty($editChildContent) || empty($editId)) {
            return $this->endResponse(null, 2001);
        }
        $sql = "UPDATE  {$this->tablePrefix}help SET child_name=:child_name, child_weight=:child_weight, child_content=:child_content WHERE id=:id ";
        $args = array(
            'child_name' => $editChildName,
            'child_weight' => $editChildWeight,
            'child_content' => $editChildContent,
            'id' => $editId
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        if ($result) {
            $redis = R();
            $redis ->del('help');
        }
        return $this->endResponse($result);
    }


    /**
     * 获取/和更新关于目录中所有的信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Help.allInfo </p>
     * <p>response:array $result 返回目录具体的形式 </p>
     * @param   string $htUpdate
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     * */
    public function allInfo($params) {
        $htUpdate = $params['htUpdate'];
        // 获取帮助中心目录缓存
        $redis = R();
        // 判断是否是后台更新，如果是后台更新，那么直接通过数据库更新数据
        if ($htUpdate) {
            $allInfo = null;
        } else {
            $allInfo = json_decode($redis->get('help'));
        }
        // 不存在缓存 从数据库重新获取，存在缓存，那么读取缓存
        if ($allInfo == null) {
            // 获取目录的id,一级目录id，一级目录名字，一级目录权重，二级目录权重，二级目录名字
            $sql = "SELECT id, parent_id, parent_name, parent_weight, child_weight, child_name FROM {$this->tablePrefix}help ORDER BY parent_weight ASC,child_weight ASC";
            $result = $this->executeQuery($sql);
            $allInfo = array();
            $parent_ids = array();
            $other = array();
            // 构造一级目录
            foreach ($result as $v){
                // 如果目录parent_id等于0 同事目录名不为空，那么就是一级目录
            	if($v['parent_id'] == 0 && !empty($v['parent_name'])){
                    // 把一级目录所有的id 存到$parent_ids中   把一级目录所有的信息存到$allInfo里面
            		$parent_ids[] = $v['id'];   // 一级目录所有id
            		$allInfo[$v['id']] = $v;      //一级目录 所有信息
            	} else {
                    $other[] = $v;     // 一级目录之外的所有目录信息
            	}
            }
//            // 把以及目录之外的目录信息分类到一级目录中
            foreach ($other as $value){
                // 其他的目录parent_id 存在于$parent_ids 那么对二级目录进行存储，存到一级目录的childDetail字段中
            	if(in_array($value['parent_id'], $parent_ids)){
            		$allInfo[$value['parent_id']]['childDetail'][$value['id']] = $value;
            	}
            }
            $redis->set('help',json_encode($allInfo));
        }
        return $this->endResponse($allInfo);
    }

    /**
     * 编辑父目录名字以及排序
     * <p>请求参数说明：</p>
     * <p>func: Other.Help.editParent</p>
     * <p>params:{editParentName: 父级目录名字, editParentWeight:父级目录排序权重，editId:编辑改变的ID}</p>
     * <p>response :false 失败 | integer 成功返回1 </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function editParent($params){
        $editParentName = $params['editParentName'] ;
        $editParentWeight = $params['editParentWeight'] ;
        $editId = $params['editId'] ;
        if (empty($editParentName) || empty($editParentWeight) || empty($editId)) {
            return $this->endResponse(null, 2001);
        }
        $sql = "UPDATE  {$this->tablePrefix}help SET parent_name=:parent_name, parent_weight=:parent_weight WHERE id=:id ";
        $args = array(
            'parent_name' => $editParentName,
            'parent_weight' => $editParentWeight,
            'id' => $editId
        );
        // 执行sql语句
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }
    /**
     * 删除目录
     * <p>请求参数说明：</p>
     * <p>func: Other.Help.delete</p>
     * <p>response :false 失败 | integer 成功返回1 </p>
     * @param   int    $id  目录id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */

    public function delete($params){
        $id = $params['id'];
        $sql = "DELETE FROM {$this->tablePrefix}help WHERE id=:id";
        $args = array('id' => $id);
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }

    /**
     * 获取一条目录信息
     * <p>请求参数说明：</p>
     * <p>func: Other.Help.getOneList</p>
     * <p>response :false 失败 | integer 成功返回1 </p>
     * @param   int    $id  目录id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */

    public function getOneList($params){
        $id = $params['id'] ;
        // sql 查询语句,根据ID 获取目录
        $sql = "SELECT * FROM {$this->tablePrefix}help WHERE id=:id ";
        $args = array(
            'id' => $id
        );
        // 执行查询语句 返回结果列表
        $result = $this->executeQuery($sql,$args);
        $chinese = htmlspecialchars_decode($result['0']['child_content']);
        // 正则判断字符串是否有中文
        $check_chinese = preg_match('/[\x80-\xff]./', $chinese);
        $result['0']['chinese'] = $check_chinese;
        return $this->endResponse($result);

    }






}