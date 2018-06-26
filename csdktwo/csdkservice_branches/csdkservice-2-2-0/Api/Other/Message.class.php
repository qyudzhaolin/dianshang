<?php
/**
 * | 用户反馈相关api
 */

namespace Api\Other;
use System\Base;
use System\Logs;

class Message extends BASE {
    const INSERT_URL = '/feedbackAction!insertFeedback.action'; // 提交反馈信息接口

    private $tablePrefix = null;
    // 魔术方法，构造函数
    public function __construct() {
        parent::__construct();
        $this->tablePrefix = $this->getMainDbTablePrefix();
    }
    /**
     *  获取意见反馈列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Message.listFeedback </p>
     * <p>response：array $result 意见反馈列表</p>
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listFeedback() {
        // sql 查询语句,需要内容，ｉｐ，创建时间不为空，不是属于操作历史记录
        $sql = "SELECT * FROM {$this->tablePrefix}feedback WHERE content!=:content AND ip!=:ip AND create_time!=:create_time and history=:history ORDER BY id DESC";
        $args = array(
            'content' => '',
            'ip' => '',
            'history' => 0,
            'create_time' => ''
        );
        // 执行查询语句 返回结果列表
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }

    /**
     * 添加新的反馈信息
     * <p>请求参数说明:</p>
     * <p>func: Other.Message.addFeedback</p>
     * <p>params: { uname :用户名, uid:用户id, content:反馈信息内容, create_time:反馈信息时间,ip：客户端IP …… }</p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public  function addFeedback($params) {
        $today = strtotime(date('Y-m-d H:i:s'));
        $uid = intval($params['uid']);
        $uname = $params['uname'];
        $create_time = intval( $params['create_time'] );
        $ip =  $params['ip'] ;
        $content = $params['content'];
        $contact = $params['contact'];
        $feedback_about = $params['feedback_about'];
        /*
        // 查询今日提交数
        $todayFeedbackSql = "select count(id) as feedback_num from 16860_feedback where create_time > $today and history=0 and source=1 and uid=$uid";
        $todayFeedback = $this->executeQuery($todayFeedbackSql);
        // 查看用户今日提交数，超过五条，不能提交
        if ($todayFeedback['0']['feedback_num'] >= 5) {
            return $this->endResponse(null, 555);
        }
        */
        // 检查参数 创建时间，ip ，内容不能为空
        if (empty($create_time) || empty($ip) || empty($content) || empty($feedback_about) ) {
            return $this->endResponse(null, 2001);
        }
        if (mb_strlen($content,'UTF-8')>500) {
            return $this->endResponse(null, 5);
        }

        $url = C("DATA_API_HOST") . self::INSERT_URL;
        $param = array(
            'uid' => $uid,
            'uname' => $uname,
            'content' => $content,
            'time' => $today,
            'type' => $feedback_about,
            'source' => 1,  //来源 1:网站 2:app 3:dos
            'contact' => $contact,
        );
        $data = $this->getDateFromDos($url,$param,'addFeedback');
        if($data['result'] == true){
            return $this->endResponse(1);
        } else {
            return $this->endResponse(null,4);
        }
    }


    /**
     * 获取单条意见反馈详细信息
     * <p>请求参数说明</p>
     * <p>func:Other.Message.detailFeedback</p>
     * <p>response：array $result 单条意见反馈详细信息</p>
     * @params  int    $id    意见信息id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */

    public function detailFeedback($params) {
        // sql 查询语句 根据ID 查询
        $sql = "SELECT * FROM {$this->tablePrefix}feedback WHERE id=:id and history=:history";
        $args = array(
            'history' => 0,
            'id' => $params['id']
        );
        // 执行查询语句 返回结果
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }

    /**
     * 更新单条意见反馈详细信息
     * <p>请求参数说明</p>
     * <p>func:Other.Message.updateFeedback</p>
     * <p>params:{id:意见信息id ，type :意见处理当前测状态 ，content：内容，result ：意见反馈处理结果}</p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @params  array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     * */

    public function updateFeedback($params) {
        // sql 更新语句
        $sql = "UPDATE  {$this->tablePrefix}feedback SET ".'type='.":type, content=:content, result=:result WHERE id=:id and type!=3 and history=:history";
        $args = array(
            'history' => 0,
            'id' => $params['id'],
            'type' => $params['type'],
            'content' => $params['content'],
            'result' => $params['result']
        );
        // 执行更新语句 返回结果
        $result = $this->executeNonQuery($sql, $args);
        return $this->endResponse($result);
    }

    /**
     * 添加反馈信息操作
     * <p>请求参数说明:</p>
     * <p>func: Other.Message.addHistoryFeedback</p>
     * <p>params: { uname :用户名, type:处理状态, create_time:历史操作时间,result：操作结果,id :建议反馈的自增id …… }</p>
     * <p>response:false 失败 | integer 成功返回1 </p>
     * @param   array  $params
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */

    public  function addHistoryFeedback($params) {
        //获取对应需要的数据
        $uname = $params['uname'];
        $create_time = intval( $params['create_time'] );
        $type = $params['type'];
        $result = $params['result'];
        $id = $params['id'];
        //检查参数 创建时间ip ，内容不能为空
        if(empty($uname) || empty($create_time) || empty($result) )
            return $this->endResponse(null, 2001);
        // 拼接sql 语句
        $fields = 'uname, create_time, type, result, history, fid';
        $sql = "INSERT INTO {$this->tablePrefix}feedback(".$fields.') VALUES( :'.str_replace(', ', ', :', $fields).')';
        $args = array(
            'uname' => $uname,
            'create_time' => $create_time,
            'type' => $type,
            'result' => $result,
            'history' => 1,
            'fid' => $id
        );
        // 执行sql语句
        $sqlResult = $this->executeNonQuery($sql, $args);
        return $this->endResponse($sqlResult);
    }

    /**
     *  获取意见反馈历史操作列表
     * <p>请求参数说明:</p>
     * <p>func: Other.Message.listHistoryFeedback </p>
     * <p>response：array $result 意见反馈历史操作列表</p>
     * @param   int    $id  意见反馈id
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function listHistoryFeedback($params) {
        // sql 查询语句
        $sql = "SELECT * FROM {$this->tablePrefix}feedback WHERE history=:history AND fid=:fid "." ORDER BY create_time DESC LIMIT 4";
        $args = array(
            'history' => 1,
            'fid' => $params['id']
        );
        // 执行查询语句 返回结果
        $result = $this->executeQuery($sql,$args);
        return $this->endResponse($result);
    }



    /**
     * 获取dos数据
     * <p>请求参数说明</p>
     * <p>func: Other.Message.getDateFromDos</p>
     * @params  $url 地址
     * @params  $param 参数
     * @params  $action 调用接口标识（日志用）
     * @return  void | array
     * @version 1.0
     * @author  肖云强<xiaoyunqiang@vacn.com.cn>
     */
    public function getDateFromDos($url , $param , $action = ''){
        if ($action) {
            Logs::info('Api.Feedback.'.$action, Logs::LOG_FLAG_NORMAL,['请求参数:',$param,'请求地址:',$url]);
        }
        $get_data = $this-> request_api_dc($url, $param);
        $r_data = json_decode($get_data, true);
        if ($action) {
            Logs::info('Api.Feedback.'.$action, Logs::LOG_FLAG_NORMAL,['返回数据:',$get_data]);
        }
        $status = $r_data['status']; //0.成功 1.查询无数据 2.查询失败,其它错误 3.排序条件参数错误 4.用户类型参数错误
        if ($status < 0) {
            return '';
        }
        $data = $r_data['obj'];
        return $data;
    }

    /**
     * 私有方法，获取curl地址
     */
    protected function request_api_dc($url,$post_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}