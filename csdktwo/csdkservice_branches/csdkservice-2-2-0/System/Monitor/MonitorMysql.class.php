<?php
/**
 * | MYSQL 监控类
 */


namespace System\Monitor;

use System\DBHelperApi;
use System\DBConnection;
use PDO;

/**
 * MYSQL 监控类
 * @author 郭尔基 <guoerji@vacn.com.cn>
 * 
 * TODO: 抽象子类
 */
class MonitorMysql {//extends Monitor { 暂时关闭父类继承
	
    static private $_sqlite = null;
    static private $_redisPrefixInst = 'monitor:mysql:inst';
    static private $_redisPrefixPart = 'monitor:mysql:part';
    
	protected $key = 'default';
	protected $log = array();
	
	public function __construct($key='default') {
		$this->key = $key;
	}
	
	public function start($key) {
		if (!empty($this->log[$key])) {
			$this->clear($key);
		}
	}
	
	public function get($key) {
		return $this->log[$key];
	}
	
	public function end($key) {
		unset($this->log[$key]);
	}
	
	public function clear($key) {
		$this->log[$key] = array();
	}
	
	/**
	 * log mysql execution log.
	 * @param PDOStatement $stat
	 * @param $args
	 * @param float $expend
	 */
	public function log(PDOStatement $stat, $args, $expend) {
		$queryString = strtolower($stat->queryString);
		if (empty($args)) {
			$finalQuery = $queryString; //对于未使用格式化语法语句，仍记录两类日志。
		}
		else {
			$finalQuery = $this->getFinalQueryString($queryString, $args);
		}
		
		$this->log[$this->key] = array(
			'rawQuery' => $queryString,
			'finalQuery' => $finalQuery,
			'effectRow' => $stat->rowCount(),
		    'expend' => round($expend * 1000, 4, PHP_ROUND_HALF_UP), 
		);
		
		if (MONITOR_LOG_PIPELINE === 'REDIS') {
			$this->R = R();
			$this->saveParticularLog();
			$this->saveInstanceLog();
		}
		else {
			$config = C('dbhelper');
			if (self::$_sqlite === null) {
				self::$_sqlite = new DBConnection('SQLITE', $config['OTHER']['SQLITE']);
			}
			$this->savePartLog();
			$this->saveInstLog();
		}
	}
	
	/**
	 * Get final query string with bound parameters.
	 * @param $query raw query string
	 * @param $args statement
	 * @return final query string
	 */
	public function getFinalQueryString($query, $args) {
		$keys = array();
		$values = $args;
		foreach ($args as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/:'.$key.'/';
			}
			else {
				$keys[] = '/[?]/';
			}
			if (is_string($value)) {
				$values[$key] = "'" . $value . "'";
			}
			elseif (is_array($value)) {
				$values[$key] = implode(',', $value);
			}
			elseif (is_null($value)) {
				$values[$key] = 'NULL';
			}
		}
		return preg_replace($keys, $values, $query);
	}
	
	/**
	 * 记录特定类型SQL执行记录
	 * TODO:improve data storage format.
	 */
	private function saveParticularLog() {
	    //password_hash($str, PASSWORD_BCRYPT);
	    $row = &$this->log[$this->key];
		$rkey = self::$_redisPrefixPart .':'. substr(MD5($row['rawQuery']), 8, 16);
		if ($this->R->hExists($rkey, 'count')) {
		    $data = $this->R->hGetAll($rkey);
		    $expend = (float) $row['expend'] + (float) $data['expend'];
		    $count = (int)$data['count'] + 1;
		    $this->R->hMSet($rkey, array('expend' => $expend, 'average' => $expend / $count, 'count' => $count));
		}
		else {
		    $this->R->hMSet($rkey, array('sql' => $row['rawQuery'], 'expend' => $row['expend'], 'average' => $row['expend'], 'count' => 1));
		}
	}
	
	/**
	 * 记录特定类型SQL执行记录
	 * schema:CREATE TABLE log_particular(id INTEGER PRIMARY KEY autoincrement,
	 * 	      sql TEXT NOT NULL, expend REAL NOT NULL)
	 */
	private function savePartLog() {
		$row = &$this->log[$this->key];
		$stmt = self::$_sqlite->prepare('INSERT INTO log_particular(sql, expend) VALUES(:sql, :expend)');
		$stmt->bindParam(':sql',    $row['rawQuery']);
		$stmt->bindParam(':expend', $row['expend']);
		$stmt->execute();
	}
	
	/**
	 * 记录每一次SQL执行记录
	 * TODO:improve data storage format.
	 */
	private function saveInstanceLog() {
	    $row = &$this->log[$this->key];
		$rkey = self::$_redisPrefixInst .':'. uniqid();
		$this->R->hMSet($rkey, array('sql' => $row['finalQuery'], 'expend' => $row['expend'], 'effect' => $row['effectRow']));
	}
	
	/**
	 * 记录每一次SQL执行记录
	 * schema:CREATE TABLE log_instance(id INTEGER PRIMARY KEY autoincrement,
	 *        sql TEXT NOT NULL, expend REAL NOT NULL, effect_row INTEGER NOT NULL)
	 */
	private function saveInstLog() {
		$row = &$this->log[$this->key];
		$stmt = self::$_sqlite->prepare('INSERT INTO log_instance(sql, expend, effect_row) VALUES(:sql, :expend, :effect_row)');
		$stmt->execute(array(':sql' => $row['finalQuery'], ':expend' => $row['expend'], ':effect_row' => $row['effectRow']));
	}
	
	/**
	 * Destroy all mysql executive logging in Redis.
	 * @return void
	 */
	public static function destroy() {
		$R = R();
		$rowCountInst = $R->del($R->keys(self::$_redisPrefixInst . '*'));
		$rowCountPart = $R->del($R->keys(self::$_redisPrefixPart . '*'));
		return $rowCountInst + $rowCountPart;
	}
	
	/**
	 * Destroy all mysql executive logging in SQLite.
	 * @return void
	 */
	public static function destroySQLite() {
		$config = C('dbhelper');
		if (self::$_sqlite === null) {
			self::$_sqlite = new DBConnection('SQLITE', $config['OTHER']['SQLITE']);
		}
		$rowCountPart = self::$_sqlite->exec('DELETE FROM log_particular WHERE 1=1');
		$rowCountInst = self::$_sqlite->exec('DELETE FROM log_instance WHERE 1=1');
		return $rowCountInst + $rowCountPart;
	}
	
	/**
	 * Generate analysis report.
	 * @param Report $reportor 
	 * @param string $format 'csv' or 'json'
	 * @param string $fileName
	 */
	public static function report(\System\Report\Report $reportor, $format='csv', $fileName='test') {
		$func = MONITOR_LOG_PIPELINE === 'REDIS' ? 'getMetadataFromRedis' : 'getMetadataFromSQLite';
		extract(self::$func());
		
		if ('csv' == $format) {
			$writer = $reportor->getCSVWriter();
			$writer->addColums(array_keys(current($rowsPart)));
			$writer->addRows(array_values($rowsPart));
			$writer->addNewLine();
			
			$writer->addColums(array_keys(current($rowsInst)));
			$writer->addRows(array_values($rowsInst));
			
			return $writer->exportToFile($fileName);
		}
		else {
			return $reportor->formatToJson(
				array(
					'PartByCount' => array_values($rowsPartByCount),
					'Instance' => array_values($rowsInst),
				)
			);
		}
	}
	
	/**
	 * Get raw report records from REDIS.
	 * @return array processed metadata
	 */
	private static function getMetadataFromRedis() {
		$R = R();
		$rowsPart = self::parsePartLog(array_map(array($R, 'HGETALL'), $R->keys(self::$_redisPrefixPart . '*'))); // 按类型
		$rowsInst = self::parseInstanceLog(array_map(array($R, 'HGETALL'), $R->keys(self::$_redisPrefixInst . '*'))); // 按实例
		return array(
			'rowsPart' => $rowsPart,
			'rowsInst' => $rowsInst
		);
	}
	
	/**
	 * Get raw report records from SQLite.
	 * @return array processed metadata
	 */
	private static function getMetadataFromSQLite() {
		$config = C('dbhelper');
		if (self::$_sqlite === null && isset($config['OTHER']['SQLITE'])) {
			self::$_sqlite = new DBConnection('SQLITE', $config['OTHER']['SQLITE']);
		}
		
		$stmt = self::$_sqlite->query('SELECT sql, count(*) as count, avg(expend) as average FROM log_particular GROUP BY sql ORDER BY count DESC');
		$rowsPartByCount = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		$rowsPartByAvg = $rowsPartByCount;
		uasort($rowsPartByAvg, create_function('$a, $b', 'if ($a["average"] == $b["average"]) { return 0; } return ($a["average"] > $b["average"]) ? -1 : 1;'));
		
		$stmt = self::$_sqlite->query('SELECT sql, expend, effect_row FROM log_instance ORDER BY expend DESC');
		$rowsInst = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		return array(
			'rowsPartByCount' => $rowsPartByCount,
			'rowsPartByAvg' => $rowsPartByAvg,
			'rowsInst' => $rowsInst
		);
	}
	
	/**
	 * Parsing the particular log information.
	 * @param $rec Log record
	 */
	private static function parsePartLog($rec) {
	    array_multisort(array_column($rec, 'count'), SORT_DESC, SORT_NUMERIC, $rec);
		return $rec;
	}
	
	/**
	 * Parsing the particular log information.
	 * @param $rec Log record
	 */
	private static function parseInstanceLog($rec) {
	    array_multisort(array_column($rec, 'expend'), SORT_DESC, SORT_NUMERIC, $rec);
	    return $rec;
	}
}