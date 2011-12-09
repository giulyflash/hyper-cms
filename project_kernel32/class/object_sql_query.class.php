<?php
class object_sql_query{
	/*
	 * prototype of instant PHP-MYSQL interface
	 * Kulakov Sergey 2011
	 * mailto: kulakov.serg@gmail.com
	 */
	private $sql_query_class;
	private $parent;
	private $sql_query_method;
	
	//TODO check called methods existence and their order
	
	public function __construct($sql_query_class=NULL, &$parent=NULL, $sql_query_method='query'){
		if(!$parent){
			$parent = $this;
			if(!($sql_query_class && $sql_query_method))
				throw new my_exception('sql class and method not found');
		}
		$this->parent = $parent;
		$this->sql_query_class = $sql_query_class;
		$this->sql_query_method = $sql_query_method;
	}
	
	public function add_sql($sql){
		$this->parent->sql.= $sql;
	}
	
	public function get_sql(){
		$sql = trim($this->parent->sql);
		$this->set_sql();
		return $sql;
	}
	
	public function get_sql1(){
		return trim($this->parent->sql);
	}
	
	public function set_sql($sql=NULL){
		$this->parent->sql = $sql;
	}
	
	public function select($name='*',$quot='`'){
		$sql = '';
		if(!is_array($name)){
			if(strpos($name,',')!==false)
				$name = explode(',',$name);
			else
				$name = array($name);
		}
		foreach($name as $item){
			if($item){
				$item = trim($item);
				if($item == '*')
					$sql.= $item.',';
				elseif(preg_match('%^[a-zA-Z0-9_\-\.]+$%', $item))
					$sql.= $quot.$item.$quot.',';
				else
					throw new my_exception('wrong field');
			}
		}
		if($sql){
			$sql = ' SELECT '.substr($sql, 0, strlen($sql)-1);
			$this->parent->add_sql($sql);
		}
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function from($name=NULL, $quot='`'){
		//TODO join in table name
		$this->check_table_name($name);
		$this->parent->add_sql(' FROM '.$quot.$name.$quot);
		return new object_sql_query(NULL, $this->parent);
	}
	
	private function check_table_name($name){
		if(!preg_match('%^[ a-zA-Z_\-\.]+$%', $name))
			throw new my_exception('wrong table name',$name);
	}
	
	public function where($name=NULL,$value=NULL,$operand='=',$open_bracket=false){
		return $this->clause('WHERE',$name,$value,$operand,$open_bracket);
	}
	
	public function _and($name=NULL,$value=NULL,$operand='=',$quot = '`',$open_bracket=false){
		return $this->clause('AND',$name,$value,$operand,$open_bracket, $quot);
	}
	
	public function _or($name=NULL,$value=NULL,$operand='=',$quot = '`',$open_bracket=false){
		return $this->clause('OR',$name,$value,$operand,$open_bracket, $quot);
	}
	
	public function clause($clause='AND',$name=NULL,$value=NULL,$operand='=',$open_bracket=false,$quot = '`'){
		$sql = '';
		$operand = strtolower(trim($operand));
		$clause_name = strtoupper(trim($clause));
		switch($operand){
			case '=':
			case '>':
			case '<':
			case '>=':
			case '<=':
			case '!=':
			case 'like':
			{
				$value_type = gettype($value);
				if(in_array($value_type,array('object','resource','unknown type')) || $value_type=='array' && (!in_array($operand,array('like','in'))))
					throw new my_exception('wrong value type',array('operation'=>$clause,'name'=>$name,'value'=>$value));
				if($operand == '=' && $value === NULL)
					$sql = $quot.self::escstr($name).$quot.' IS NULL';
				elseif($operand == 'like'){
					if($value_type=='array')
						$value = implode('%',$value);
					$percent = strpos('%',$value)===false?'%':'';
					$sql = $quot.self::escstr($name).$quot.' LIKE "'.$percent.self::escstr($value).$percent.'"';
				}
				else
					$sql = $quot.self::escstr($name).$quot.$operand.'"'.self::escstr($value).'"';
				break;
			}
			case 'in':{
				if(!is_array($value)){
					if(false!==strpos($value,','))
						$value = explode(',', $value);
					else
						$value = array($value);
				}
				if(!$value)
					throw new my_exception('"IN" clause must have not-empty array');
				else{
					foreach($value as $item)
						$sql.=($item===NULL?'NULL':('"'.self::escstr($item).'"')).',';
					$sql = $quot.self::escstr($name).$quot.' IN ('.substr($sql, 0, strlen($sql)-1).')';
				}
				break;
			}
			case 'between':{
				if(!is_array($value)){
					if(false!==strpos($value,','))
						$value = explode(',', $value);
					else
						throw new my_exception('value for between must be an array');
				}
				if(count($value)!=2)
					throw new my_exception('count of values array for between must be 2', array('operation'=>$clause,'name'=>$name,'value'=>$value));
				$keys = array_keys($value);
				$sql = $quot.self::escstr($name).$quot.' BETWEEN \''.$this->escstr($value[$keys[0]]).'\' AND \''.$this->escstr($value[$keys[1]]).'\'';
				break;
			}
			default:{
				throw new my_exception('unsupported operator',$operand);
			}
		}
		if($sql)
			$sql = ' '.$clause.' '.($open_bracket?'(':'').$sql;
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function order($name=NULL, $quot='`'){
		$sql = '';
		if($name){
			if(!is_array($name)){
				if(false!==strpos($name,','))
					$name = explode(',', $name);
				else
					$name = array($name);
			}
			foreach($name as $item){
				if($item){
					$item = trim($item);
					if(preg_match('%^([a-z_\-\.]+)( desc)?( asc)?$%i', $item, $re)){
						$sql.= $quot.$re[1].$quot;
						if(!empty($re[2]))
							$sql.=$re[2];
						elseif(!empty($re[3]))
							$sql.=$re[3];
						$sql.= ',';
					}
					else
						$this->_error('wrong order', var_export($order,true), NULL, $reciever, $error_lvl+1);
				}
			}
		}
		if($sql){
			$sql = ' ORDER BY '.substr($sql, 0, strlen($sql)-1);
			$this->parent->add_sql($sql);
		}
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function limit($from=NULL,$to=NULL){
		$sql = $this->check_limit($from, 'FROM').$this->check_limit($to, 'TO', ',');
		$this->parent->add_sql($sql?(' LIMIT '.$sql):'');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function limit_page($page=NULL,$count=NULL){
		if($count){
			if($page)
				$sql = ' LIMIT '.(($page-1)*$count).', '.($page*$count);
			else
				$sql = ' LIMIT '.((int)$count);
		}
		else
			$limit = '';
		return new object_sql_query(NULL, $this->parent);
	}
	
	private function check_limit($limit,$name,$quot=''){
		if($limit){
			if($int_limit = (int)$limit)
				return $quot.$int_limit;
			else
				throw new my_exception('wrong '.$name.' value for LIMIT clause', $limit);
		}
	}
	
	public function update($name, $quot='`'){
		$this->check_table_name($name);
		$this->parent->add_sql(' UPDATE '.$quot.$name.$quot);
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function set($set=NULL, $quot='`'){
		$sql='';
		if(!is_array($set))
			throw new my_exception('SET must be an array');
		foreach($set as $name=>$value){
			if($value === NULL)
				$sql.=$quot.self::escstr($name).$quot.'=NULL,';
			else
				$sql.=$quot.self::escstr($name).$quot.'="'.self::escstr($value).'",';
		}
		if(!$sql)
			throw new my_exception('SET array must not be empty');
		$sql = ' SET '.substr($sql, 0, strlen($sql)-1);
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function insert($table=NULL, $field_name=NULL, $quot='`'){
		$sql='';
		$this->check_table_name($table);
		$sql = ' INSERT INTO '.$quot.$table.$quot.' ';
		if($field_name){
			$sql.='(';
			if(!is_array($field_name)){
				if(false!==strpos($field_name,','))
					$field_name = explode(',', $field_name);
				else
					$field_name = array($field_name);
			}
			foreach($field_name as $item)
				$sql.=$quot.$this->escstr($item).$quot.',';
			$sql = substr($sql, 0, strlen($sql)-1).')';
		}
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function values($values=NULL, $quot='`'){
		$sql='';
		if(!$values)
			throw new my_exception('values must not be empty');
		if(!is_array($values)){
			if(false!==strpos($values,','))
				$values = explode(',', $values);
			else
				throw new my_exception('values must be an array');
		}
		$vars_declared = substr($parent_sql = $this->parent->get_sql1(),strlen($parent_sql)-1,1)==')';
		$var_sql = '';
		foreach($values as $name=>$value){
			if(is_array($value)){
				if(!$vars_declared)
					throw new my_exception('field list for multidimentional array of VALUES must be declared in INSERT');
				$sql.= '(';
				foreach($value as $val){
					$sql.=$this->value_insert_check($val);
				}
				$sql = substr($sql, 0, strlen($sql)-1).'),';
			}
			elseif($vars_declared)
				$sql.=$this->value_insert_check($value);
			else{
				$var_sql.= $quot.$this->escstr($name).$quot.',';
				$sql.=$this->value_insert_check($value);
			}
		}
		$sql = substr($sql, 0, strlen($sql)-1);
		if(substr($sql,0,1)!='(')
			$sql = '('.$sql;
		if(substr($sql,strlen($sql)-1,1)!=')')
			$sql.= ')';
		if($var_sql){
			$var_sql = '('.substr($var_sql, 0, strlen($var_sql)-1).')';
			$sql = $var_sql.' VALUES '.$sql;
		}
		else
			$sql = ' VALUES '.$sql;
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	private function value_insert_check($value){
		if($value===NULL)
			return 'NULL,';
		else
			return '"'.$this->escstr($value).'",';
	}
	
	public function delete(){
		$this->parent->add_sql(' DELETE ');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function join($table, $left='left', $inner='inner'){
		if(!in_array(strtolower($left), array('left','right','')))
			throw new my_exception('Wrong join',$left);
		if(!in_array(strtolower($inner), array('inner','outer')))
			throw new my_exception('Wrong join',$inner);
		$this->check_table_name($table);
		$sql = ' '.strtoupper($left).' '.strtoupper($inner).' JOIN '.$table;
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function _on($field1,$field2){
		if(preg_match('%^[a-zA-Z0-9_\-\.]+$%', field1))
			throw new my_exception('Wrong field', field1);
		if(preg_match('%^[a-zA-Z0-9_\-\.]+$%', field2))
			throw new my_exception('Wrong field', field2);
		$sql = ' ON '.$this->escstr($field1).' = '.$this->escstr($field2);
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function lock($name=NULL, $mode='WRITE', $quot='`'){
		//TODO lock a lot of tables
		if($name){
			$this->check_table_name($name);
			$mode = $this->escstr(strtoupper(trim($mode)));
			$this->parent->add_sql(' LOCK TABLE '.$quot.$name.$quot.' '.$mode);
		}
		else
			$this->parent->add_sql(' FLUSH TABLES WITH READ LOCK ');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function unlock(){
		$this->parent->add_sql(' UNLOCK TABLES ');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function truncate($table){
		$this->check_table_name($table);
		$this->parent->add_sql('TRUNCATE TABLE `'.$table.'`');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public static function escstr($string){
		if($string===NULL)
			return $string;
		if (function_exists('get_magic_quotes_gpc'))
			if (get_magic_quotes_gpc())
				$string = stripslashes($string);
		return mysql_real_escape_string($string);
	}
	
	public function open_bracket(){
		$this->parent->add_sql('(');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function close_bracket(){
		$this->parent->add_sql(')');
		return new object_sql_query(NULL, $this->parent);
	}
	
	public function injection($sql){
		$this->parent->add_sql($sql);
		return new object_sql_query(NULL, $this->parent);
	}
	
	//output 
	
	public function execute($need_num_rows = false){
		if($this->parent->sql){
			if(!empty($this->parent->echo_sql))
				echo $this->parent->sql.'<br/>';
			$sql_query_class = &$this->parent->sql_query_class;
			$sql_query_method = $this->parent->sql_query_method;
			if(!method_exists($sql_query_class, $sql_query_method))
				throw new my_exception('method not found for this class',($sql_query_class->module_name).'.'.$sql_query_method);
			if($need_num_rows)
				$this->parent->sql = str_replace('SELECT','SELECT SQL_CALC_FOUND_ROWS', $this->parent->sql, $_num=1);
			$this->parent->query_result = $sql_query_class->$sql_query_method($this->parent->sql);
			if($need_num_rows){
				$rows = $sql_query_class->$sql_query_method('SELECT FOUND_ROWS()');
				if(isset($rows[0]['FOUND_ROWS()']))
					$this->parent->query_result['__num_rows'] = $rows[0]['FOUND_ROWS()'];
			}
			$this->set_sql();
			return new object_sql_query(NULL, $this->parent);
		}
		else
			throw new my_exception('sql not found');
	}
	
	public function query(){
		$this->parent->execute();
		return $this->parent->query_result;
	}
	
	public function insert_id(){
		return $this->sql_query_class->insert_id();
	}
	
	public function query1($field=NULL,$limit=true, $remove_existing_limit=false){
		if($remove_existing_limit)
			$this->parent->set_sql(preg_replace('%^(.+) LIMIT[0-9 ]+,?[0-9 ]*$%', '\1',$this->parent->sql));
		if($limit)
			$this->parent->set_sql($this->parent->sql.' LIMIT 1');
		$this->parent->execute();
		if(isset($this->parent->query_result[0])){
			if($field){
				if(isset($this->parent->query_result[0][$field]))
					return $this->parent->query_result[0][$field];
				return NULL;
			}
			return $this->parent->query_result[0];
		}
		return $this->parent->query_result;
	}
	
	public function query_page($page=1,$count=NULL){
		$page = (int)$page;
		$count = (int)$count;
		$sql = $this->parent->get_sql1();
		$sql = preg_replace('%^(.+) LIMIT[0-9 ]+,?[0-9 ]*$%', '\1',$sql);
		if($page && $count)
			$this->parent->set_sql($sql.' LIMIT '.($page-1)*$count.','.$count);
		$this->parent->execute(true);
		$this->parent->query_result['__page_size'] = $count;
		$this->parent->query_result['__page'] = $page;
		$this->parent->query_result['__max_page'] = (int)ceil($this->parent->query_result['__num_rows']/$count);
		return $this->parent->query_result;
	}
	
	public function query2assoc_array($name_column, $value_column=NULL, $unset=true){
		$this->parent->execute();
		if(!$this->parent->query_result)
			return;
		if(isset($this->parent->query_result[0][$name_column])){
			$new_result = array();
			foreach($this->parent->query_result as &$result){
				if($value_column){
					$new_result[$result[$name_column]] = $result[$value_column];
				}
				else{
					$new_result[$result[$name_column]] = $result;
					if($unset)
						unset($new_result[$result[$name_column]][$name_column]);
				}
			}
			return $new_result;
		}
		else
			throw new my_exception('column not found in database table',$column); 
	}
	
	public function fetch_field($table){
		$this->check_table_name($table);
		$resource = mysql_query('select * from `'.$table.'` where 1=0');
		$fields = array();
		for($i=0; $i< mysql_num_fields($resource); $i++)
			$fields[] = mysql_fetch_field($resource)->name;
		return $fields;
	}
	
	public function fetch_field_assoc($table){
		$field = $this->fetch_field($table);
		foreach($field as $name=>$value){
			$field[$value] = $value;
			unset($field[$name]);
		}
		return $field;
	}
}