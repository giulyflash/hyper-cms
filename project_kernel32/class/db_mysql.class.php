<?php

class db_mysql_config extends module_config{
	protected $type = 'mysql';
	protected $host = 'localhost';
	protected $user = 'root';
	protected $password = '';
	protected $name = 'cms';
	protected $charset = 'utf8';
	protected $prefix = '';
}

class db_mysql extends module implements db_interface{
	protected $config_class_name = 'db_mysql_config';
	private $link;
	
	public function __construct(&$parent=NULL){
		parent::__construct($parent);
		$this->connect();
	}
	
	public function __destruct(){
		if($this->link)
			mysql_close($this->link);
	}
	
	public function connect(){
		if(!($this->link = mysql_connect($this->_config('host'), $this->_config('user'), $this->_config('password'))))
			throw new my_exception('can\'t connect to database');
		if (!mysql_select_db($this->_config('name'),$this->link))
			throw new my_exception('can\'t select database',$this->_config('name'));
		if(!mysql_set_charset($this->_config('charset'),$this->link))
			throw new my_exception('can\'t set charset',$this->_config('charset'));
	}
	
	public function query($query, $error_lvl = 4){
		/*if($this->parent->_config('debug')){
			$date_start = new DateTime();
		}*/
		if(!$this->link)
			$this->connect();
		$queryResult = mysql_query($query, $this->link);
		if($this->parent->_config('debug')){
			$db_arr = array();
			$db_arr['query'] = $query;
			//$date_query_end = new DateTime();
			//$date = $date_query_end->diff($date_start);
			//$db_arr['date'] = $date_query_end->format('H:i:s');
			$this->parent->_debug['db'][] = &$db_arr;
		}
		if($error = mysql_error()){
			//TODO process error, dont output row
			$this->_message('sql: '.$query/*,array('sql'=>$query)*/);
			throw new my_exception('mysql error', $error);
		}
		else{
			$result = array();
			if(is_bool($queryResult)){
				if($queryResult)
					return $queryResult;
				else
					return $result;
			}
			elseif(is_resource($queryResult)){
				if($numRows = mysql_num_rows($queryResult))
					for ($num=0;$num<$numRows;$num++)
						$result[$num] = mysql_fetch_array($queryResult,MYSQL_ASSOC);
				return $result;
			}
			else
				throw new my_exception('query result is not valid resource', $error, $error_lvl);
		}
	}
	
	public static function escstr($string){
		if (function_exists('get_magic_quotes_gpc'))
			if (get_magic_quotes_gpc())
				$string = stripslashes($string);
		return mysql_real_escape_string($string);
	}
	
	public function insert_id(){
		if(!$this->link)
			$this->connect();
		if($id = mysql_insert_id($this->link))
			return $id;
	}
}
?>