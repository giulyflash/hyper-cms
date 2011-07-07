<?php
/*
 * Kulakov Sergey
 * mailto: kulakov.serg@gmail.com
 */

//TODO проставить в базе индексы, протестировать скорость выдачи до и после

class app_config extends config{
	public $default_page_title = 'cms';
	public $page_title;
	protected $title_delimiter = ' - ';
	public $error_delimiter = ' - ';
	public $content_type = 'xsl';
	public $language = 'ru';
	public $default_language = 'en';
	protected $default_module = 'article';
	public $charset = 'utf-8';
	protected $position = '__app__';
	public $main_position_name = 'center';
	protected $module_link_from_db = true;
	protected $module_link_from_config = true;
	protected $module_link_from_module_config = true;
	protected $include_from_config = true;
	protected $include_from_module_config = true;
	protected $include_from_db = true;
	protected $config_from_db = true;
	protected $php_filter_eneble = true;
	protected $default_argument_filter = FILTER_SANITIZE_STRING;
	protected $output_new_argument = true;//output only values, different of default
	protected $output_all_argument = true;//output all argument values: default and new
	protected $output_config = true;
	protected $admin_method;
	protected $output_session = true;
	protected $exclude_method_from_link_list = '_exclude';//exclude given methods or param from list in module_link admin
	//path
	protected $module_language_path = 'language';
	protected $module_language_ext = '.php';
	protected $template_path = 'template/';
	protected $output_class_prefix = 'output_';
	protected $module_template_ext = '.xhtml.xsl';
	protected $admin_template = 'template/admin/index.xhtml.xsl';
	protected $db_connector_prefix = 'db_';
	protected $get_module_template_include = true;
	public $language_data_for_all_methods = '_for_all';
	protected $language_title_name = '__title';
	protected $language_param_name = '__param';

	protected $link = array(
		'admin_mode.*'=>array('head'=>'user',),
	); //link for all admin modules
	
	protected $include = array(
		'*'=>'<script type="text/javascript" src="extensions/jquery/jquery-1.5.2.min.js"></script>',
		'admin_mode.*'=>'<script type="text/javascript" src="extensions/jquery/jquery-1.5.2.min.js"></script>',
	); //include for all modules
	
	protected $admin_exclude = array(
		'file','user','base_module'
	); //modules excluded from admin panel list
	protected $not_found_article_name = '404';//if isset output this article when something not found and it is not fatal
	protected $not_found_text = 'Страница не найдена';//TODO replace Russian
	
	const role_read = 'read';
	const role_write = 'write';
	const role_delete = 'delete';
	const role_name = '__role__';
	const object_name = '__object__';
}

class app extends module{
	public $admin_mode;
	public $db;
	public $call_string;
	public $domain;
	public $config_cache = array();
	public $link_cache = array();
	public $link_cache_row = array();
	public $center_module;
	public $center_method;
	public $call_list = array(); //list of modules to call
	public $module = array(); //list of called modules
	public $include = array();//text to output in <HEAD> for specified module and method
	public $include_raw = array();
	public $error = array();//error cache
	public $message = array();//message cache
	public $template_include = array('module/app/app.xhtml.xsl');
	public $echo_error = false;
	private $calleble_method_cache = array();
	public $language_cache = array();
	public $error_cache = array();
	public $output = array();
	
	public function __construct($admin_mode = NULL){
		//set_error_handler("app::error_handler");
		//set_exception_handler("app::exception_handler");
		$this->check_session();
		$this->admin_mode = $admin_mode;
		$this->parent = $this;
		//$this->module[] = &$this;
		$this->module_name = get_class($this);//get_called_class();
		$this->config = new app_config();
		try{
			$this->_set_call_time();
			$this->get_domain_config();
			$this->config->set_vars($this->get_module_config());
			$this->db_connect();
			$this->_query = new object_sql_query($this->db);
			$this->get_call();
			$this->get_request_params();
			$this->get_module_link();
			$this->get_db_config();
			$this->config->set_vars($this->get_module_config());
			$this->check_admin_mode();
			$this->get_module_config_include($this);
			$call_count = count($this->call_list);
			for($num=0; $num<$call_count; $num++)
				$this->try_call($this->call_list[$num], $num);
			//TODO recursive link call, get db params for each iteration
			if(($new_call_count = count($this->call_list))>$call_count)
				for($num=$call_count; $num<$new_call_count; $num++)
					$this->try_call($this->call_list[$num], $num);
			$this->get_include();
		}
		catch(Exception $exception){
			$this->_exception($exception);
		}
		if($this->error && $this->echo_error){
			header('Content-Type: text/plain; charset='.$this->_config('charset'));
			return $this->echo_errors("\n");
		}
		try{
			$this->output();
		}
		catch(Exception $exception){
			$this->echo_errors();
		}
	}
	
	public function check_session(){
		if(isset($_REQUEST['PHPSESSID']))
			session_id($_REQUEST['PHPSESSID']);
		if(!isset($_SESSION))
			session_start();
		if(!empty($_SESSION['error'])){
			$this->error = $_SESSION['error'];
			unset($_SESSION['error']);
		}
		if(!empty($_SESSION['message'])){
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		}
	}
	
	//error section
	
	/*public function _error($errno, $errstr, $errfile, $errline){
		$error = array();
		$time = new date_microtime();
		$backtrace = debug_backtrace();
		//TODO full backtrace;
		$lvl = 2;
		$error['method'] = $backtrace[$lvl]['function'];
		$error['class'] = $backtrace[$lvl]['class'];
		//$error['line'] = $backtrace[$lvl-1]['line'];
		$error['line'] = $errline;
		//TODO language
		$error['text'] = $errstr;
		$error['err_type'] = $errno;
		$error['time'] = $time->format();
		$this->error[$error['class']][$error['method']][] = $error;
	}*/
	
	public function _exception($exception,$lvl=2){
		$error = array();
		$time = new date_microtime();
		$backtrace = $exception->getTrace();
		//foreach($backtrace as $num=>$item) echo "$num: ".(isset($item['class'])?$item['class']:'undefined_class')."->{$item['function']}<br/>";
		if(isset($exception->lvl) && $exception->lvl!==NULL)
			$lvl = $exception->lvl;
		while($lvl && !isset($backtrace[$lvl]))
			$lvl-=1;
		$error['method'] = $backtrace[$lvl]['function'];
		if(isset($backtrace[$lvl]['class']))
			$error['class'] = $backtrace[$lvl]['class'];
		else
			$error['class'] = 'undefined_class';
		if($lvl && isset($backtrace[$lvl-1]['line']))
			$error['line'] = $backtrace[$lvl-1]['line'];
		if(!empty($exception->need_backtrace))
			$error['backtrace'] = $backtrace;
		//TODO language
		$error['text'] = $exception->getMessage();
		if(!empty($this->error_cache[$error['method']][$error['text']]))
			$error['text'] = $this->error_cache[$error['method']][$error['text']];
		if(isset($exception->text))
			$error['text'].= ' - '.$exception->text;
		$error['err_type'] = $exception->getCode();
		$error['time'] = $time->format();
		$this->error[$error['class']][$error['method']][] = $error;
	}
	
	public function message($text){
		//internal message collecting method
		if($text)
			$this->message[] = $text;
	}
	
	public function get_error($class=NULL, $method=NULL, $need_array=true){
		//return array or flag of existing errors of specified class.method
		if(!$class)
			throw new my_exception('class name not found');
		elseif(isset($this->error[$class])){
			if($method){
				if(isset($this->error[$class][$method]))
					return ($need_array ? $this->error[$class][$method] : true);
			}
			else
				return ($need_array ? $this->error[$class] : true);
		}
		return array();
	}
	
	public function echo_errors($br='<br/>'){
		foreach($this->error as $module)
			foreach($module as $method)
				foreach($method as $error)
					$this->echo_error($error,$br);
	}
	
	public function echo_error($error,$br){
		if ($error['err_type']=='error'){
			echo 'Error - '.$error['class'].'.'.$error['method'];
			if(isset($error['line']))
				echo ', line '.$error['line'];
			echo ' - ';
		}
		else
			echo 'Message - ';
		echo $error['text'].$br;
	}
	
	//service section
	
	public function check_admin_mode(){
		if($this->admin_mode){
			$this->config->set('template',$this->_config('admin_template'));
			$this->config->set('default_module','admin');
		}
	}
	
	public function get_module_config($module=NULL){
		if(!$module)
			$module = $this->module_name;
		if(isset($this->config_cache[$module])){
			//var_dump($module, is_array($this->config_cache[$module]), $this->config_cache[$module]); echo '!!!<br/>';
			return $this->config_cache[$module];
		}
		else
			return array();
	}
	
	private function get_domain_config(){
		//get config from domain config file
		if(empty($_SERVER['HTTP_HOST']))
			throw new my_exception('server host not found');
		else{
			$this->domain = $_SERVER['HTTP_HOST'];
			$domain_file = _config_path.$this->domain._config_ext;
			if(!file_exists($domain_file))
				throw new my_exception('domain config not found', $domain_file);
			else{
				require_once($domain_file);
				if(empty($config))
					throw new my_exception('config array not found', $domain_file);
				else
					$this->config_cache = $config;
				if(!empty($link))
					$this->link_cache_row = $link;
				//TODO include from module config
				if(!empty($include))
					$this->include_raw = $include;
				if(!empty($admin_exclude))
					$this->admin_exclude = $admin_exclude;
			}
		}
	}
	
	private function db_connect(){
		//select the database to connect and create its instance
		if(empty($this->config_cache['db']['type']))
			throw new my_exception('db type not found in config');
		else{
			$type = strtolower($this->config_cache['db']['type']);
			$class_name = $this->_config('db_connector_prefix').$type;
			if(!file_exists($file_name = _kernel_path.$class_name._class_ext))
				throw new my_exception('db connector class not found', $file_name);
			$this->config_cache[$class_name] = $this->config_cache['db'];
			$this->db = new $class_name($this);
		}
	}
	
	private function get_call(){
		//get 'call' param from $_REQUEST
		$this->check_admin_mode();
		if(!empty($_REQUEST['call'])){
			$call_str = $_REQUEST['call'];
			if(!preg_match('%^([0-9a-zA-Z_\-]*)\.?([0-9a-zA-Z_\-]*)$%', $_REQUEST['call'], $call))
				throw new my_exception('wrong call',$call_str);
			else{
				$this->center_module = $call[1];
				$this->center_method = $call[2];
			}
		}
		elseif($default_module = $this->_config('default_module'))
			$this->center_module = $default_module;
		else
			throw new my_exception('default module not found');
	}
	
	private function get_module_link(){
		//TODO unset links
		$this->check_admin_mode();
		if($this->_config('module_link_from_module_config') && $links = $this->_config('link'))
			$this->get_config_link($links);
		if($this->_config('module_link_from_config'))
			$this->get_config_link($this->link_cache_row, $this->center_module, $this->center_method);
		if($this->_config('module_link_from_db'))
			$this->get_db_link();
		array_unshift($this->call_list,array(
			'module_name' => $this->center_module,
			'method_name' => $this->center_method, 
			'position' => $this->_config('main_position_name'),
			'params' => NULL,
		));
	}
	
	private function get_config_link(&$link_cache, $module=NULL, $method=NULL){
		//!module -> parse link from module config, not global config
		$link_list = $this->get_module_link_list($link_cache,$module,$method);
		foreach($link_list as $call){
			foreach ($call as $position=>$call_data){
				if(!is_array($call_data))
					$call_data = array($call_data);
				foreach($call_data as $call_str){
					if(!preg_match('%([a-zA-Z0-9_]+)\.?([a-zA-Z0-9_]*)(.*)%', $call_str, $call_re))
						throw new my_exception('wrong format',$call_str);
					else{
						$params = array();
						if($re_res = preg_match_all('%&([^&]+)=([^&]+)%', $call_re[3], $params_row))
							foreach ($params_row[1] as $num=>$param_name)
								$params[$param_name] = $params_row[2][$num]; 
						$this->call_list[] = array(
							'module_name'=>$call_re[1],
							'method_name'=>$call_re[2],
							'position'=>$position,
							'params'=>$params,
						);
					}
				}
			}
		}
		//var_dump($this->call_list);
	}
	
	private function get_module_link_list(&$link_cache, $module=NULL, $method=NULL){
		//TODO cache
		$link_list = array();
		$admin_mode = $this->admin_mode;
		if($link_cache){
			$this->check_array_comma($link_cache);
			if($module){
				if(!$admin_mode){ 
					if(isset($link_cache['*.*']))
						$link_list[] = $link_cache['*.*'];
					if(isset($link_cache[$module.'.*']))
						$link_list[] = $link_cache[$module.'.*'];
				}
				else{
					if(isset($link_cache['admin_mode.*']))
						$link_list[] = $link_cache['admin_mode.*'];
					if(isset($link_cache['admin_mode.*.*']))
						$link_list[] = $link_cache['admin_mode.*.*'];
					if(isset($link_cache['admin_mode.'.$module.'.*']))
						$link_list[] = $link_cache['admin_mode.'.$module.'.*'];
					if(isset($link_cache['_admin_mode.'.$module.'.'.$method]))
						$link_list[] = $link_cache['admin_mode.'.$module.'.'.$method];
				}
				if(isset($link_cache[$module.'.'.$method]))
					$link_list[] = $link_cache[$module.'.'.$method];
			}
			else{
				if(!$admin_mode){ 
					if(isset($link_cache['*']))
						$link_list[] = $link_cache['*'];
				}
				else{
					if(isset($link_cache['admin_mode.*']))
						$link_list[] = $link_cache['admin_mode.*'];
					if($method && isset($link_cache['admin_mode.'.$method]))
						$link_list[] = $link_cache['admin_mode.'.$method];
				}
				if($method && isset($link_cache[$method]))
					$link_list[] = $link_cache[$method];
			}
		}
		return $link_list;
	}
	
	public function check_array_comma(&$include_src){
		//TODO cache
		foreach(array_keys($include_src) as $name)
			if(strpos($name, ',')!==false){
				$includes = explode(',',$name);
				foreach($includes as $new_name){
					if(is_array($include_src[$name])){
						//TODO recursive merge
						if(isset($include_src[$new_name]))
							$include_src[$new_name] = array_merge($include_src[$new_name], $include_src[$name]);
						else
						//if(!isset($include_src[$new_name]))
							$include_src[$new_name] = $include_src[$name];
					}
					else
						$include_src[$new_name] = (isset($include_src[$new_name])?$include_src[$new_name]:'').$include_src[$name];
				}
				unset($include_src[$name]);
			}
	}
	
	private function get_db_link(){
		//TODO join there, remove cicles
		//get links
		$center_module = array($this->center_module, ($this->admin_mode?'admin_mode.':'').'*');
		$center_method = array('*');
		if($this->center_method)
			$center_method[] = $this->center_method;//TODO nedd to get center method from call
		//TODO not only central link
		//$this->_query->echo_sql = true;
		$call_db_list = $this->_query->select('module_name, method_name, position, link_id')->from('module_link')->
			where('exclude',1,'!=')->_and('center_module',$center_module,'in')->_and('center_method',$center_method,'in')->_and('admin_mode',$this->admin_mode)->
			order('order')->query();
		//create array of link_id for query
		$link_list = array();
		//var_dump($this->admin_mode, $call_db_list);
		if($call_db_list){
			foreach($call_db_list as $call)
				if(!isset($link_list[$call['link_id']]))
					$link_list[$call['link_id']] = true;
			//var_dump($link_list);
			$link_param = $this->_query->select()->from('module_link_param')->where('link_id',array_keys($link_list),'in')->query();
			//grouping params by link_id
			$param_group = array();
			foreach($link_param as $param){
				if(!isset($param_group[$param['link_id']]))
					$param_group[$param['link_id']] = array('condition'=>array(),'param'=>array());
				if($param['type'] == 'param')
					$param_group[$param['link_id']]['param'][$param['param_name']] = $param['value'];
				elseif($param['type'] == 'condition')
					$param_group[$param['link_id']]['condition'][$param['param_name']] = $param['value'];
			}
			//applying params
			//var_dump($param_group, $this->_call_list);
			foreach($call_db_list as $call){
				if(!empty($param_group[$call['link_id']])){
					$call['params'] = $param_group[$call['link_id']]['param'];
					$call['condition'] = $param_group[$call['link_id']]['condition'];
				}
				$this->call_list[] = $call;
			}
		}
	}
	
	private function get_db_config(){
		//get config for modules from db and put them to cache
		$this->get_module_list();
		if($this->_config('config_from_db')){
			//var_dump($this->_query);
			$db_params = $this->_query->select('module_name,param_name,value')->from('module_param')->where('module_name',$this->module_list,'in')->query();
			foreach($db_params as $param)
				$this->config_cache[$param['module_name']][$param['param_name']] = $param['value'];
		}
	}
	
	private function get_module_list(){
		$module_list = array();
		foreach($this->call_list as $call)
			if(!isset($module_list[$call['module_name']]))
				$module_list[$call['module_name']] = true;
		$this->module_list = array_keys($module_list);
	}
	
	private function get_include(){
		if($this->_config('include_from_config'))
			$this->get_config_include();
		if($this->_config('include_from_db'))
			$this->get_db_include();
	}
	
	private function get_config_include(){
		$this->check_array_comma($this->include_raw);
		$this->loaded_include = array();
		$admin_mode = $this->admin_mode?'admin_mode.':'';
		foreach($this->call_list as $call){
			$this->add_module_include($admin_mode.'*');
			$this->add_module_include($admin_mode.'*.*');
			$this->add_module_include($admin_mode.$call['module_name'].'.*');
			$this->add_module_include($admin_mode.$call['module_name'].'.'.$call['method_name']);
			if($admin_mode){
				$this->add_module_include($call['module_name'].'.*');
				$this->add_module_include($call['module_name'].'.'.$call['method_name']);
			}
		}
		if(empty($_SERVER["REQUEST_URI"]) || $_SERVER["REQUEST_URI"]=='/')
			$this->add_module_include('/');
	}
	
	private function get_module_config_include(&$module){
		if($this->_config('include_from_module_config') && $include_cache = $module->_config('include')){
			$this->check_array_comma($include_cache);
			//var_dump($include_cache);echo "<br/>\n<br/>\n";
			if(!isset($this->loaded_module_include))
				$this->loaded_module_include = array();
			$admin_mode = $this->admin_mode?'admin_mode.':'';
			$this->add_module_include($admin_mode.'*', $include_cache, $module->module_name, $this->loaded_module_include);
			$this->add_module_include($admin_mode.'*.*', $include_cache, $module->module_name, $this->loaded_module_include);
			$this->add_module_include($admin_mode.($module->module_name).'.*', $include_cache, $module->module_name, $this->loaded_module_include);
			if($module->method_name){
				$this->add_module_include($admin_mode.$module->module_name.$module->method_name, $include_cache, $module->module_name, $this->loaded_module_include);
				if($this->admin_mode)
					$this->add_module_include($module->method_name, $include_cache, $module->module_name, $this->loaded_module_include);
			}
		}
	}
	
	private function add_module_include($link_call, &$include_cache=NULL, $module_name=NULL, &$loaded_include=NULL){
		if(!$include_cache)
			$include_cache = &$this->include_raw;
		if(!$loaded_include)
			$loaded_include = &$this->loaded_include;
		$link_full_name = ($module_name?($module_name.'.'):'').$link_call;
		if(!isset($loaded_include[$link_full_name])){
			$this->loaded_include[$link_full_name] = true;
			if(isset($include_cache[$link_call])){
				if(is_array($include_cache[$link_call]))
					foreach($include_cache[$link_call] as $link)
						$this->include[] = $link;
				else
					$this->include[] = $include_cache[$link_call];
			}
		}
	}
	
	private function get_module_template_include(&$module){
		if($this->_config('get_module_template_include'))
			if($include = $module->_config('template_include')){
				if(!is_array($include)){
					if(strpos($include,',')!==false)
						$name = explode(',',$include);
					else
						$name = array($include);
				}
				foreach($include as $item){
					$this->template_include[] = $item;
				}
			}
	}
	
	private function get_db_include(){
		$this->_query->select('include')->from('module_include')->where('module_name',$this->call_list[0]['module_name']);
		$this->loaded_module_include = array();
		for($i=1; $i<count($this->call_list); $i++){
			$this->db_include_condition($this->call_list[$i]['module_name'],'*');
			$this->db_include_condition($this->call_list[$i]['module_name'],$this->call_list[$i]['method_name']);
		}
		if(empty($_SERVER["REQUEST_URI"]) || $_SERVER["REQUEST_URI"]=='/')
			$this->_query->_or('module_name','/');
		$db_include = $this->_query->query();
		if($db_include)
			foreach($db_include as $include)
				$this->include[] = $include['include'];
	}
	
	private function db_include_condition($module_name, $method_name){
		$link_str = $module_name.'.'.$method_name;
		if(!isset($this->loaded_module_include[$link_str])){
			$this->_query->_or('module_name',$module_name)->_and('method_name',$method_name);
			$this->loaded_module_include[$link_str] = true;
		}
	}
	
	private function get_request_params(){
		//get special params from $_REQUEST
		if(!empty($_REQUEST['_content']))
			//if(!in_array($output, $this->_output_type_allowed))
				//$this->_error('wrong output data type', $output);
			//else
				$this->config->set('content_type',$_REQUEST['_content']);
		if(!empty($_REQUEST['_echo_error']))
			$this->echo_error = true;
		if(!empty($_REQUEST['language']))
			$this->config->set('language',$_REQUEST['language']);
		if(!empty($_REQUEST['_no_link'])){
			$this->config->set('module_link_from_config','');
			$this->config->set('module_link_from_db','');
		}
		elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) &&
			preg_match('%^(.+?)-(.+?),(.+?);q=([0-9\.]+)%', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_arr)){
			$this->config->set('language',$lang_arr[1]);
		}
	}
	
	//call section
	
	private function try_call(&$call, $num){
		try{
			$this->call_module($call, $num);
		}
		catch(Exception $e){
			$this->_exception($e);
		}
	}
	
	private function call_module(&$call, $num){
		if(!empty($call['condition']))
			foreach($call['condition'] as $cond_name=>$cond_value)
				if(!isset($this->module[0]->argument_all[$cond_name]) || $this->module[0]->argument_all[$cond_name]!=$cond_value)
					return;
		$module_name = $call['module_name'];
		$this->check_module_exists($module_name);
		$module = new $module_name($this);
		$this->module[] = &$module;
		$this->set_main_template($module);
		//if(!empty($call['method_name']))
		$method_name = $this->get_call_method($module,$call);
		if(!$call['method_name'])
			$call['method_name'] = $method_name;
		if($this->check_method_access($module, $method_name)){
			$this->call_module_method($module, $method_name, $call);
			//TODO module inner title like $this->_title;
			if($num==0){
				$title = isset($module->_title)?$module->_title:$module->_config('title');
				if($title)
					$title.= $this->_config('title_delimiter');
				$this->config->set('page_title',$title.$this->_config('default_page_title'));
				if($this->_config('module_link_from_module_config') && $links = $module->_config('link'))
					$this->get_config_link($links,NULL,$method_name,$this->admin_mode);
			}
		}
	}
	
	private function check_module_exists($module_name){
		if($module_name == 'app')
			throw new my_exception('app is not callable');
		if(!class_exists($module_name, false)){
			if(!file_exists($file_name = _module_path.$module_name.'/'.$module_name._class_ext))
				throw new my_exception('module file not found', $file_name);
			else{
				require_once $file_name;
				if(!class_exists($module_name, false))
					throw new my_exception('module class not found', $file_name);
			}
		}
	}
	
	private function set_main_template(&$module){
		if($module_main_template = $module->_config('main_template'))
			$this->config->set('template', $module_main_template);
	}
	
	private function get_call_method(&$module, &$call){
		if(!empty($call['method_name']))
			return $call['method_name'];
		if($default_method_name = $module->_config('default_method'))
			return $default_method_name;
		$reflecton = new ReflectionClass($module_name);
		$method_list = $reflecton->getMethods();
		foreach($method_list as $class_method){
			$default_method_name = $class_method->name;
			if(substr($default_method_name,0,1)!='_' && $class_method->isPublic())
				break;
		}
		if(substr($default_method_name,0,1)=='_' || !$class_method->isPublic())
			throw new my_exception('default method name not found for this module', $module_name);
		else
			return $default_method_name;
	}
	
	private function check_method_access(&$module, $method_name){
		$module_name = $module->module_name;
		$this->prepare_callable_method($module);
		$callable_method = $module->_config('callable_method',true);
		//var_dump($module_name, $callable_method);
		if(!isset($callable_method[$method_name]))
			throw new my_exception('access rule not found',$module_name.'.'.$method_name,0);
		//TODO access rules for users and groups
		//FIXME temp access rules
		$access = true;
		$module_config_class_name = $module->_get_config_name();
		$module_object_name = constant($module_config_class_name.'::object_name');
		$module_role_name = constant($module_config_class_name.'::role_name');
		$module_role_read = constant($module_config_class_name.'::role_read');
		$module_role_write = constant($module_config_class_name.'::role_write');
		$module_role_delete = constant($module_config_class_name.'::role_delete');
		if($method_name == 'admin' || $module_name == 'admin')
			$access = false;
		elseif(!isset($callable_method[$method_name][$module_object_name], $callable_method[$method_name][$module_role_name]))
			throw new my_exception('not found roles for called method', $module_name.'.'.$method_name);
		else{
			if(is_array($callable_method[$method_name][$module_role_name])){
				foreach($callable_method[$method_name][$module_role_name] as $role_type)
					if($role_type==$module_role_write || $role_type==$module_role_delete){
						$access = false;
						break;
					}
			}
			elseif($callable_method[$method_name][$module_role_name]==$module_role_write || $callable_method[$method_name][$module_role_name]==$module_role_delete)
				$access = false;
		}
		if(!$access && !isset($_SESSION['user_info'])){
			$message_str = '';
			if(is_array($callable_method[$method_name][$module_role_name])){
				foreach($callable_method[$method_name][$module_object_name] as $num=>$obj){
					$message_str = $obj.':'.$callable_method[$method_name][$module_role_name][$num].', ';
					//TODO language for object names
				}
				$message_str = substr($message_str, 0, strlen($message_str)-2);
			}
			else
				$message_str = $callable_method[$method_name][$module_object_name].':'.$callable_method[$method_name][$module_role_name];
			//$this->message('access denied', $message_str);
			$this->message('Для использования '.$module_name.'.'.$method_name.' необходимо указать логин и пароль.');
			unset($this->module[count($this->module)-1]);
			$this->add_user_module();
			return;
		}
		return true;
	}
	
	private function prepare_callable_method(&$module){
		$temp_rule = array();
		$callable_method = $module->config->get('callable_method',true);
		if(!isset($this->calleble_method_cache[$module->module_name])){
			$this->check_array_comma($callable_method);
			$this->calleble_method_cache[$module->module_name] = &$callable_method;
			$module->config->set('callable_method',$callable_method);
		}
		else
			$callable_method = $this->calleble_method_cache[$module->module_name];
			//TODO not copy, get from cache
	}
	
	private function add_user_module(){
		$user_method = 'form';
		if(!$this->user_module_exists($user_method)){
			$user_module = new user($this);
			$user_module->method_name = $user_method;
			$user_module->position = $this->_config('main_position_name');
			if($result = $user_module->$user_method())
				$user_module->_result = $result;
			$this->module[] = &$user_module;
		}
	}
	
	private function user_module_exists($user_default_method){
		foreach($this->call_list as $call)
			if($call['module_name'] == 'user' && ($call['method_name'] == 'form' || !$call['method_name']))
				return true;
	}
	
	private function call_module_method(&$module, $method_name, &$call){
		$module_name = $module->module_name;
		if(!method_exists($module, $method_name))
			throw new my_exception('method name not found for this module', $module_name.'.'.$method_name);
		$args_new = array();
		$args = $this->get_args($module,$method_name, $call['params'], $args_new);
		$module->argument_all = &$args;
		$module->argument_new = &$args_new;//list of arguments different from defaults
		$module->method_name = $method_name;
		$module->position = (!empty($call['position']))?$call['position']:$this->main_position_name;
		if($result = call_user_func_array(array($module,$method_name),$args))
			$module->_result = $result;
		$this->get_module_config_include($module);
		$this->get_module_template_include($module);
	}
	
	private function get_args($obj,$method,$args=NULL, &$args_new=array()){
		//php-reflection allow to miss method arguments
		//if $args is array $_REQUEST params will be ignored
		$method_reflection = new ReflectionMethod($obj,$method);
		$params_reflection = $method_reflection->getParameters();
		$params = array();
		foreach($params_reflection as &$param_reflection){
			$param_name = $param_reflection->name;
			$value = false;
			if(is_array($args)){
				if(isset($args[$param_name]))
					$value = $args[$param_name];
			}
			elseif(isset($_REQUEST[$param_name])){
				$value = $_REQUEST[$param_name];
				if($this->_config('php_filter_eneble')){
					$callable_method = $obj->_config('callable_method',true);
					if(isset($callable_method[$method][$param_name]) && $callable_method[$method][$param_name]!=$this->_config('exclude_method_from_link_list'))
						$filter = $callable_method[$method][$param_name];
					//TODO array of filters for param: abiblity to add php-filter for admin_list invisible params 
					elseif(!($default_argument_filter = $this->_config('default_argument_filter')))
						throw new my_exception('php filter enabled but default filter is undefined');
					else
						$filter = $default_argument_filter;
					if(!$filter)
						$value = false;
					else 
						$value = filter_var($value, $filter);
				}
			}
			if($value===false){
				if($param_reflection->isDefaultValueAvailable())
					$value = $param_reflection->getDefaultValue();
				else
					$value = NULL;
			}
			else
				$args_new[$param_name] = $value;
			$params[$param_name] = $value;
		}
		return $params;
	}
	
	//output section
	
	private function output(){
		//call output engine class for content_type
		$this->form_output();
		$class_name = $this->_config('output_class_prefix').$this->_config('content_type');
		try{
			if(!file_exists($file_name = _kernel_path.$class_name._class_ext))
				throw new my_exception('output class not found',$file_name);
			else{
				$output = new $class_name($this);
				$output->header();
				echo $output->get();
			}
		}
		catch(Exception $e){
			$this->_exception($e);
			//TODO output internal html template
			$this->echo_errors();
		}
	}
	
	private function form_output(){
		//prepare $this->output array for template engine
		$this->output['module'] = array();
		foreach ($this->module as $module){
			if(empty($this->output['language'][$module->module_name][$module->method_name]) && 
				!empty($this->language_cache[$module->module_name][$module->method_name])){
					$this->output['language'][$module->module_name][$module->method_name] =
						$this->language_cache[$module->module_name][$module->method_name];
					unset($this->output['language'][$module->module_name][$module->method_name]['_method_name']);
					unset($this->output['language'][$module->module_name][$module->method_name][$this->_config('language_param_name')]);
					unset($this->output['language'][$module->module_name][$module->method_name]['_admin']);
					//TODO params for admin mode ...['_admin']
				}
			if(empty($this->output['language'][$module->module_name][$for_all = $this->_config('language_data_for_all_methods')]) &&
				!empty($this->language_cache[$module->module_name][$for_all])){
					$this->output['language'][$module->module_name][$for_all] =
						$this->language_cache[$module->module_name][$for_all];
					unset($this->output['language'][$module->module_name][$for_all]['_method_name']);
					unset($this->output['language'][$module->module_name][$for_all]['_param']);
					unset($this->output['language'][$module->module_name][$for_all]['_admin']);
				}
			$result = isset($module->_result) ? $module->_result : array();
			if(!is_array($result))
				$result = array($result);
			if($module->_config('output_config'))
				$result['_config'] = get_object_vars($module->config);
			$result['_module_name'] = $module->module_name;
			if($module->method_name)
				$result['_method_name'] = $module->method_name;
			if($module->position)
				$result['_position'] = $module->position;
			if(($template = $module->_config('template')) && $result['_module_name']!='app')
				$result['_template'] = $template;
			if($module->_config('output_all_argument') && !empty($module->argument_all)){
				$result['argument'] = &$module->argument_all;
				if($module->_config('output_new_argument') && !empty($module->argument_new))
					$result['argument_new'] = $module->argument_new;
			}
			elseif($module->_config('output_new_argument') && !empty($module->argument_new))
				$result['argument'] = $module->argument_new;
			$this->output['module'][] = $result;
		}
		if($this->error)
			$this->output['error'] = $this->error;
		if($this->message)
			$this->output['message'] = $this->message;
		if($this->include)
			$this->output['include'] = $this->include;
		if($this->domain)
			$this->output['meta']['domain'] = $this->domain;
		$this->output['meta']['request'] = $_SERVER["REQUEST_URI"];
		if($this->call_string)
			$this->output['meta']['call_string'] = $this->call_string;
		if($main_language = $this->_config('language'))
			$this->output['meta']['language'] = $main_language;
		if($this->admin_mode)
			$this->output['meta']['admin_mode'] = $this->admin_mode;
		if($this->_config('output_session') && !empty($_SESSION))
			$this->output['session'] = $_SESSION;
		if($this->_config('output_config'))
			$this->output['meta']['app_config'] = get_object_vars($this->config);
	}
	
	public function redirect($location){
		if($this->error)
			$_SESSION['error'] = $this->error;
		if($this->message)
			$_SESSION['message'] = $this->message;
		header("Location: ".$location);
	}
}
?>