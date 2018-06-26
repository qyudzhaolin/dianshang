<?php
/**
* | Mysql 监控应用层
*/

namespace Api\Monitor;

class Mysql {
	
	public function __construct() {}
	
	/**
	 * 销毁存储的数据.
	 * <p>请求参数说明</p>
	 * <p>func: Monitor.Mysql.destroyMetadata</p>
	 * <p>response: return response depends on destory process</p>
	 * @param   string $pipeline
	 * @return  void | Array
	 * @version 1.0
	 * @author  guoerji <guoerji@vacn.com.cn >
	 */
	public function destroyMetadata($param) {
	    $pipeline = $param['pipeline'];
	    
	    if ($pipeline !== 'REDIS' && $pipeline !== 'SQLITE') {
	        $this->response('Parameter pipeline must be set to "REDIS" or "SQLITE"');
	    }
	    
	    if ($pipeline == 'REDIS') {
	        $effectRow = \System\Monitor\MonitorMysql::destroy();
	        $this->response("a total $effectRow of metadata has been destroyed in Redis.");
	    }
	    else {
	        $effectRow = \System\Monitor\MonitorMysql::destroySQLite();
	        $this->response("a total $effectRow of metadatametadata has been destroyed in SQLite.");
	    }
	}
	
	/**
     * 获取一个CSV格式的数据报告.
     * <p>请求参数说明</p>
     * <p>func: Monitor.Mysql.getReportCSV</p>
     * <p>response: return response depends on generation process</p>
     * @param   string $filename
     * @return  void | Array
     * @version 1.0
     * @author  guoerji <guoerji@vacn.com.cn >
     */
	public function getReportCSV($param) {
	    $filename = $param['filename'] ? $param['filename'] : 'monitor_' . time();
		$flag = \System\Monitor\MonitorMysql::report(new \System\Report\Report(), $format='csv', $filename);
		if ($flag) {
            $this->response("Monitor report has been generated!");
		}
		$this->response("Monitoring report generation failure!");
	}
	
	/**
	 * 获取一个json格式的数据报告.
	 * <p>请求参数说明</p>
	 * <p>func: Monitor.Mysql.getReportJson</p>
	 * <p>param: {example1: example1, example2: example2}</p>
	 * <p>response: return response depends on generation process</p>
	 * @param   array  $param
	 * @return  void | Array
	 * @version 1.0
	 * @author  guoerji <guoerji@vacn.com.cn >
	 */
	public function getReportJson($param) {
		die(\System\Monitor\MonitorMysql::report(new \System\Report\Report(), 'json'));
	}
	
	/**
	 * 发送反馈
	 * @param $msg Message Body
	 */
	private function response($msg) {
	    die(json_encode(array('message' => $msg)));
	}
}