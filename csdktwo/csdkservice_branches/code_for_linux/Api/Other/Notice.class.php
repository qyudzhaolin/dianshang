<?php
/**
 * | 公告管理相关API
 */

namespace Api\Other;
use System\Base;
class Notice extends BASE {
    private $tablePrefix = null;
    // 魔术方法，构造函数
    public function __construct() {
        parent::__construct();
        $this->tablePrefix = $this->getMainDbTablePrefix();
    }


    /**
     *  获取顶部公告
     * <p>请求参数说明:</p>
     * <p>func: Other.Notice.getTopNotice </p>
     * <p>response：array $result 意见反馈列表</p>
     * @return  void | array
     * @version 1.0
     * @author  tangxiaofeng<tangxiaofeng@vacn.com.cn>
     */
    public function getTopNotice() {
		
		//过滤条件，t.type不能等于系统公告，n.status不能是下线状态
            $sql = "select n.*,t.type from {$this->tablePrefix}notice as n left join {$this->tablePrefix}notice_type as t on n.type_id = t.id where t.type != 2 and n.allot in (1,3) and n.status = 1 and n.endtime > ".NOW_TIME." order by n.id desc";
            $result = $this->executeQuery($sql);
            return $this->endResponse($result);
    }

    /**
     *  获取公告管理信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Notice.getNotice </p>
     * <p>response：array $result 公告列表</p>
     * @param   int    $pageindex   当前页
     * @param   int    $pagesize    当前显示数量
     * @return  void | array
     * @version 1.0
     * @author  tangxiaofeng<tangxiaofeng@vacn.com.cn>
     */
    public function getNotice($params) {

        $pageindex =  empty($params['pageindex']) ? 1 : intval($params['pageindex']);

        $pagesize = empty($params['pagesize']) ? 10 : intval($params['pagesize']);

        if (!is_int($pageindex) || !is_int($pagesize)) {
            return $this->endResponse(null, 2001);
        }
		
		$offset = ($pageindex-1)*$pagesize;

        $sql = "select n.*,t.type_name,t.type as t_type from {$this->tablePrefix}notice as n left join {$this->tablePrefix}notice_type as t on n.type_id = t.id where t.type != 2  and n.allot in (1,3) and n.redirectstatus = 1 and n.status !=2  order by n.id desc limit {$offset},{$pagesize}";
        $result = $this->executeQuery($sql);
        return $this->endResponse($result);
    }


    /**
     *  获取公告管理信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Notice.getNoticeNum </p>
     * <p>response：array $num 公告条数</p>
     * @return  void | int
     * @version 1.0
     * @author  tangxiaofeng<tangxiaofeng@vacn.com.cn>
     */
    public function getNoticeNum() {

		$count_sql = "select count(n.id) as num from {$this->tablePrefix}notice as n left join {$this->tablePrefix}notice_type as t on n.type_id = t.id where t.type != 2 and n.allot in (1,3) and n.redirectstatus = 1 and n.status != 2";
		$count_result = $this->executeQuery($count_sql);

        return $this->endResponse($count_result);
    }
	
    /**
     *  获取公告管理信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Notice.getNoticeInfo </p>
     * <p>response：array $result 公告详细</p>
     * @param   int    $id   主键
     * @return  void | array
     * @version 1.0
     * @author  tangxiaofeng<tangxiaofeng@vacn.com.cn>
     */
	public function getNoticeInfo($params)
	{
		$id =  empty($params['id']) ? 1 : intval($params['id']);
		
		$sql = "select n.*,t.type_name from {$this->tablePrefix}notice as n left join {$this->tablePrefix}notice_type as t on n.type_id=t.id where n.id = ".$id;	
		$result = $this->executeQuery($sql);

        return $this->endResponse($result);
	}
	
	

}