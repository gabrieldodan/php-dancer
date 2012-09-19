<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class dbPgsql {
    private $host       = NULL;
    private $user       = NULL;
    private $pass       = NULL;
    private $dbName     = NULL;  
    private $link       = NULL;
    private $connected  = NULL;
    private $persistant = NULL;
	private $error      = NULL;
	private $isError    = FALSE;
    

	function dbPgsql($host, $user, $pass, $dbName, $persistant = FALSE) { 
        $this->host       = $host;
        $this->user       = $user;
        $this->pass       = $pass;
        $this->dbName     = $dbName;
        $this->persistant = $persistant;
        
        $connFunct       = ($this->persistant ? "pg_pconnect" : "pg_connect");
        $this->link      = @$connFunct("host={$this->host} dbname={$this->dbName} user={$this->user} password={$this->pass}");
		if ( $this->link === FALSE ) {
			Sys::errorHandler("Error connecting to database");
		}
        $this->connected = TRUE;
        
		return $this->link;
    }
    public function getLastError() {
		return pg_last_error($this->link);    	 
    }
    public function isError() {
		return $this->isError;    	 
    }
    
	public function getAll($qry) {
		$dbResult = $this->runQuery($qry);
		if ( $dbResult === FALSE ) {
			return FALSE;
		}
		if ( pg_num_rows($dbResult) ) {
			return pg_fetch_all($dbResult);
		}
		return array();
	}
	public function getAssoc($qry) {
		$result   = array();
		$dbResult = $this->runQuery($qry);
		if ( $dbResult === FALSE ) {
			return FALSE;
		}
		
		if ( !pg_num_rows($dbResult) ) {
			return $result;
		}
		if ( pg_num_rows($dbResult) ) {
			$aRow = pg_fetch_assoc($dbResult);
			$keys = array_keys($aRow);
			$key  = $keys[0];
		}
		pg_result_seek($dbResult, 0);
		while ( $row = pg_fetch_assoc($dbResult) ) {
			$result[$row[$key]] = (count($keys) <= 2 ? $row[$keys[count($keys)-1]] : $row);	
		}
		return $result;
	}
	
	public function getRow($qry) {
		$dbResult = $this->runQuery($qry);
		if ( $dbResult === FALSE ) {
			return FALSE;
		}
		if ( pg_num_rows($dbResult) ) {
			return pg_fetch_assoc($dbResult);
		}
		else {
			return array();
		}
	}
	public function getOne($qry) {
		$dbResult = $this->runQuery($qry);
		if ( $dbResult === FALSE ) {
			return FALSE;
		}
		if ( pg_num_rows($dbResult) ) {
			$row = pg_fetch_row($dbResult);
			return $row[0];
		}
		else {
			return NULL;
		}
	}
	public function query($qry) {
		return $this->runQuery($qry);
	}
	
	private function runQuery($qry) {
		$res = pg_query($this->link, $qry);
		if ( $res === FALSE ) {
			$this->isError = TRUE;
		}
		else {
			$this->isError = FALSE;
		}
		return $res;
	}
	
	
	public function insert($table, $data, $noEscape=array(), $returnLastId=FALSE) {
		$fields = array();
		$values = array();
		foreach ($data AS $field => $val) {
			if ( !in_array($field, $noEscape) ) {
				$val = "'" . $this->escape($val) . "'";
			}
			$fields[] = "\"{$field}\"";
			$values[] = $val;
		}
		$res = $this->runQuery("INSERT INTO \"{$table}\"(" . implode(",", $fields) . ") VALUES(" . implode(",", $values) . ")");
		if ( $res ) {
			if ( $returnLastId  ) {
				return $this->getOne("SELECT lastval()");
			}
			else {
				return TRUE;
			}
			
		}
		else {
			return FALSE;	
		}
		
	}
	
	public function update($table, $data, $where, $noEscape=array()) {
		$set = array();
		foreach ($data AS $field => $val) {
			if ( !in_array($field, $noEscape) ) {
				$val = "'" . $this->escape($val) . "'";
			}
			$set[] = "\"{$field}\" = {$val}";
		}
		return $this->runQuery("UPDATE \"{$table}\" SET " . implode(",", $set) . " WHERE $where");
	}
	
	# fields, comma separated values
	# return type:one, row, array, assoc, default = array
	function getFrom($table, $fields='', $where='', $returnType='', $order='', $limit = '' ) {
		$table = "{$table}";
		if ( $fields ) {
			$arrFields = explode(",", $fields);
			$newFields = array();
			foreach ($arrFields AS $f) {
				$newFields[] = '"' . trim($f) . '"';
			}
			$fields = implode(", ", $newFields);
		}
		else {
			$fields = "*";
		}
		
		$qry = "SELECT $fields FROM \"{$table}\" " . ($where ? "WHERE $where" : "") . " " . ($order ? "ORDER BY $order" : "") . " " . ($limit ? "LIMIT $limit" : "");
		$returnType = ($returnType ? $returnType : "array");
		if ( $returnType == 'one') {
			return $this->getOne($qry);
		}
		if ( $returnType == 'row') {
			return $this->getRow($qry);
		}
		if ( $returnType == 'array') {
			return $this->getAll($qry);
		}
		if ( $returnType == 'assoc') {
			return $this->getAssoc($qry);
		}
	}
	
	public function escape($str) {
		return pg_escape_string($str);
	}
	public function escapeInput($str) {
		if ( get_magic_quotes_gpc() ) {
			return pg_escape_string( stripslashes($str) );
		}
		return pg_escape_string($str);
	}
}
?>