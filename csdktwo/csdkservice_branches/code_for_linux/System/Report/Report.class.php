<?php
/**
* Report 基础类库
*/


namespace System\Report;

/**
 * Report base class.
 * @author 郭尔基 <guoerji@vacn.com.cn>
 */
class Report {
	
	public function __construct() {}
	
	//abstract function export();
	
	//abstract function import();
	
	/**
	 * @returns CSV Instance
	 */
	public function getCSVWriter() {
		return new CSV();
	}
	
	public function formatToJson($data) {
		return json_encode($data);
	}
}

class CSV {
	
	protected $data;
	
	/**
	 * @params array $columns
	 * @returns void
	 */
	public function __construct($data='') {
		$this->data = $data;
	}
	
	/**
	 * @param array $columns
	 * @returns void
	 */
	public function addColums($columns) {
		$this->data .= '"' . implode('","', $columns) . '"' . "\n";
	}
	
	/**
	 * @param array $row
	 * @returns void
	 */
	public function addRows($row) {
		foreach ($row as $val) {
			$this->addRow($val);
		}
	}
	
	/**
	 * @params string $row
	 * @returns void
	 */
	public function addRow($row) {
		$this->data .= '"' . implode ('","', $row) . '"' . "\n";
	}
	
	/**
	 * @returns void
	 */
	public function addNewLine() {
		$this->data .= "\n";
	}
	
	/**
	 * @param string $filename
	 * @returns void
	 */
	public function exportToFile($filename) {
	    $fullPath = APP_PATH . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . $filename .'.csv';
		$flag = file_put_contents($fullPath, $this->data, LOCK_EX);
		return (bool) $flag;
	}
	
	public function export($filename) {
		header('Content-type: application/csv');
		header("Content-Disposition: attachment; filename='". $filename .".csv'");
		echo $this->data;
	}
	
	public function __toString() {
		return $this->data;
	}
	
}
