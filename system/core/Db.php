<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class DbBase {
	static protected $conns      = array();
	static protected $activeConn = '';
	
	final function __construct() {
		Sys::errorHandler("Db instantiation not allowed");
	}
	
	static public function init($configs) {
		$connTypes = array();
		foreach ( $configs AS $configName => $config ) {
			$connTypes[] = $config['type'];
		}
		foreach ( $connTypes AS $connType ) {
			require_once Locator::pathSysDir() . "/core/db-drivers/db-{$connType}.php";
		}
		
		foreach ( $configs AS $configName => $config ) {
			if ( $config['type'] == 'mysql' ) {
				Db::$conns[$configName] = new dbMysql(
					$config['host'],
					$config['username'],
					$config['password'],
					$config['dbname']
				);
			}
			else if ( $config['type'] == 'pgsql' ) {
				Db::$conns[$configName] = new dbPgsql(
					$config['host'],
					$config['username'],
					$config['password'],
					$config['dbname']
				);
			}
		}
		if ( count($configs) ) {
			$arr              = array_keys($configs);
			Db::$activeConn = $arr[0];
		}
	}
	
	static public function setActiveConn($conn) {
		Db::$activeConn = $conn;
	}
	
	static public function getActiveConn() {
		return Db::$activeConn;
	}
	
	
    static public function getLastError($conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->getLastError();    	 
    }
    
    static public function isError($conn='') {
    	return Db::$conns[($conn ? $conn : Db::$activeConn)]->isError();
    }
    
	
	static public function getAll($qry, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->getAll($qry);
	}
	
	static public function getAssoc($qry, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->getAssoc($qry);
	}
	
	static public function getRow($qry, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->getRow($qry);
	}
	
	static public function getOne($qry, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->getOne($qry);
	}
	
	static public function query($qry, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->query($qry);
	}

	/*
	static public function runQuery($qry, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->runQuery($qry);
	}
	*/
	
	static public function insert($table, $data, $noEscape=array(), $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->insert($table, $data, $noEscape);
	}
	
	static public function update($table, $data, $where, $noEscape=array(), $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->update($table, $data, $where, $noEscape);
	}
	
	# fields, comma separated values
	# return type:one, row, array, assoc, default = array
	static public function getFrom($table, $fields='', $where='', $returnType='', $order='', $limit = '', $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->getFrom($table, $fields, $where, $returnType, $order, $limit);
	}
	
	static public function escape($str, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->escape($str);
	}
	
	static public function escapeInput($str, $conn='') {
		return Db::$conns[($conn ? $conn : Db::$activeConn)]->escapeInput($str);
	}
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Db.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Db') ) {
	class Db extends DbBase {
	}
}

?>