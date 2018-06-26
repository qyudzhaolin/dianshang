<?php
//require_once(APP_PATH."/php/conf/conf_db.php") ;

//define(LOG4PHP_DIR, APP_PATH."/php/libs/log4php");  
//require_once(LOG4PHP_DIR . '/Logger.php');  
//Logger::configure(APP_PATH."/php/log4php.properties") ;

class PdbcTemplate {
    public static function query($sql, $params=null, $fetch_style=null, $is_one=0) {
        Global $db_host, $db_port,$db_name, $db_username, $db_password ;
        if (is_null($sql)) {
            return ;
        }  
        $dbh = null ;
        $result = null ;
        try {
            $dbh = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_username, $db_password, array( PDO::ATTR_PERSISTENT => false));
          
            $stmt = $dbh->prepare($sql) ;
            if (is_null($params)) {
                $stmt->execute() ;
            } else {
                $stmt->execute($params);
            }
            if (is_null($fetch_style)) {
				if ($is_one) {
					$result = $stmt->fetch(PDO::FETCH_OBJ) ;
				} else {
					$result = $stmt->fetchAll(PDO::FETCH_OBJ) ;
				}
            } else {
				if ($is_one) {
					$result = $stmt->fetch($fetch_style) ;
				} else {
					$result = $stmt->fetchAll($fetch_style) ;
				}
            }
        } catch (Exception $e) {
           echo $e->getMessage() ;
        }  
        return $result ;
    }
    public static function execute_batch($arr_sql, $sql_params=null ) {
		//$logger = Logger::getRootLogger() ;
        Global $db_host, $db_port, $db_name, $db_username, $db_password ;
        if (is_null($arr_sql)) {
            return ;
        }  
        $dbh = null ;
        $num_rows = 0;
		$lastInsertId = 0 ;
       	$dbh = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_username, $db_password, array( PDO::ATTR_PERSISTENT => false));
		$dbh->beginTransaction() ;
        try {
			for ($i = 0 ; $i < count($arr_sql) ; $i++) {
				$sql = $arr_sql[$i] ;
        	    $stmt = $dbh->prepare($sql) ;
				//$logger->debug("*****($sql)") ;
				//$logger->debug("*****".json_encode($sql_params)) ;
        	    if (is_null($sql_params)) {
        	        $num_rows = $stmt->execute() ;
        	    } else {
					$params =  $sql_params[$i] ;
        	        $num_rows = $stmt->execute($params) ;
        	    }
        	    $lastInsertId = $dbh->lastInsertId() ;
			}
        	$dbh->commit() ;
        } catch (PDOException $e) {
            //echo $e->getMessage() ;
			if (null != $dbh) {
                $dbh->rollback() ;
            } 
        }  
		$res = array($num_rows, $lastInsertId) ;
        return $res ;
    }
    public static function execute($sql, $params=null, $use_transaction=0 ) {
		//$logger = Logger::getRootLogger() ;
		
        Global $db_host, $db_port, $db_name, $db_username, $db_password ;
        if (is_null($sql)) {
            return ;
        }  
        $dbh = null ;
        $num_rows = 0;
		$lastInsertId = 0 ;
        try {
            $dbh = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_username, $db_password, array( PDO::ATTR_PERSISTENT => false));
            $stmt = $dbh->prepare($sql) ;
			if ($use_transaction) {
				$dbh->beginTransaction() ;
			}
			//echo "sql = $sql\n" ;
            if (is_null($params)) {
                $num_rows = $stmt->execute() ;
            } else {
                $num_rows = $stmt->execute($params) ;
            }
            $lastInsertId = $dbh->lastInsertId() ;
			//echo "num_rows = $num_rows" ;
			
			
			if ($use_transaction) {
            	$dbh->commit() ;
			}
        } catch (PDOException $e) {
            //echo $e->getMessage() ;
			if (null != $dbh && $use_transaction) {
                $dbh->rollback() ;
            } 
        }  
		//echo $num_rows;
		$res = array($num_rows, $lastInsertId) ;	
		return $res ;
    }
}
?>
