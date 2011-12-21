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
	public $default_module = 'article';
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
	protected $debug = false;
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
	protected $disable_php_filter = '_disable_filter';//disable filter for method parameter 
	protected $language_obj_name = '__object';
	protected $path = array();
	protected $main_page_title = 'Главная';//FIXME translation in language file
	protected $admin_page_title = 'Панель управления';
	protected $path_link_alias = true;

	protected $link = array(
		'admin_mode.*'=>array('head'=>'user',),
	); //link for all admin modules
	
	protected $include = array(
		'*'=>'<script src="/extensions/jquery/jquery-1.5.2.min.js" type="text/javascript"></script>
			<script src="/module/app/app.js" type="text/javascript"></script>',
		'admin_mode.*'=>'<script src="/extensions/jquery/jquery-1.5.2.min.js" type="text/javascript"></script>
			<script src="/module/app/app.js" type="text/javascript"></script>',
	); //include for all modules
	
	protected $admin_exclude = array(
		'file','user','base_module'
	); //modules excluded from admin panel list
	protected $not_found_article_name = '404';//if isset output this article when something not found and it is not fatal
	protected $not_found_text = 'Страница не найдена';//TODO replace Russian
	
	const role_read = 'read';
	const role_write = 'write';
	const role_delete = 'delete';
	
	protected $role_read = 'read';
	protected $role_write = 'write';
	protected $role_delete = 'delete';
	
	protected $access_name = '__access__';
	//$file_name = _module_path.$class_name.'/'.$class_name.$this->parent->_config('module_template_ext');
	protected $json_html_template = 'json_html.xhtml.xsl';
	protected $ucfirst_path = true;
}

class app extends module{
	const call_list_count = 5;//max count of calls, reembered in $_SESSION
	const debug_memory_real_usage = true;
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
	public $compiled_config_cache = array();
	private $_path = array();
	public $_debug = array();
	public $default_path_count = 1;
	public $manual_path = false;
	public $menu_module = NULL;
	
	public function __construct($admin_mode = NULL){
		//set_error_handler("app::error_handler");
		//set_exception_handler("app::exception_handler");
		$this->check_session();
		$this->admin_mode = $admin_mode;
		$this->parent = $this;
		//$this->module[] = &$this;
		$this->module_name = get_class($this);//get_called_class();
		$this->config = new app_config($temp=NULL,$this);
		$this->_path[] = array('href'=>'/', 'title'=>$this->_config('main_page_title'));
		if($this->admin_mode)
			$this->_path[] = array('href'=>'/admin.php', 'title'=>$this->_config('admin_page_title'));
		$this->default_path_count = count($this->_path);
		try{
			$this->_set_call_time();
			$this->get_domain_config();
			$this->config->set_vars($this->get_module_config());
			$this->db_connect();
			$this->_query = new object_sql_query($this->db);
			$this->get_call();
			$this->get_request_params();
			if($this->_config('debug'))
				$this->_debug['memory']['before'] = number_format(memory_get_usage(self::debug_memory_real_usage),0 ,',',' ');
			$this->get_module_link();
			$this->get_db_config();
			$this->config->set_vars($this->get_module_config());
			$this->check_admin_mode();
			$this->set_module_config_include($this);
			$call_count = count($this->call_list);
			for($num=0; $num<$call_count; $num++)
				$this->try_call($this->call_list[$num], $num);
			//TODO recursive link call, get db params for each iteration
			if(($new_call_count = count($this->call_list))>$call_count)
				for($num=$call_count; $num<$new_call_count; $num++)
					$this->try_call($this->call_list[$num], $num);
			$this->get_include();
			//var_dump($this->call_list);
		}
		catch(Exception $exception){
			$this->_exception($exception);
		}
		if($this->error && $this->echo_error){
			header('Content-Type: text/plain; charset='.$this->_config('charset'));
			return $this->echo_errors("\n");
		}
		if($this->_config('debug')){
			$this->_debug['memory']['peak'] = number_format(memory_get_peak_usage(self::debug_memory_real_usage),0 ,',',' ');
			$this->_debug['memory']['after'] = number_format(memory_get_usage(self::debug_memory_real_usage),0 ,',',' ');
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
		//
		if(!isset($_SESSION['call']))
			$_SESSION['call'] = array();
		if(!(isset($_REQUEST['_content']) && (($content_type = strtolower(substr($_REQUEST['_content'],0,4)) )=='json' || $content_type=='xml') || 
			!empty($_SERVER['REQUEST_URI']) && preg_match('%.*\.[a-zA-Z]+$%', $_SERVER['REQUEST_URI']) ) ){
			$request_uri = (!empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!='/favicon.ico')?$_SERVER['REQUEST_URI']:'/';
			if(!isset($_SESSION['call'][0]) || $request_uri!=$_SESSION['call'][0])
				array_unshift($_SESSION['call'],$request_uri);
			if(count($_SESSION['call'])>self::call_list_count)
				array_splice($_SESSION['call'],-1);
		}
		//var_dump($_SESSION['call']);
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
	
	public function _exception($exception,$lvl=0){
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
		if(!empty($this->error_cache[$error['method']][$error['text']])){
			$error['text'] = $this->error_cache[$error['method']][$error['text']];
		}
		if(isset($exception->text))
			$error['text'].= ' - '.((is_array($exception->text))?var_export($exception->text,true):$exception->text);
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
		elseif(!$this->admin_mode && $this->_config('config_from_db') &&
			$db_call = $this->_query->select(array('name','value'))->from('module_param')->where('module_name','app')->_and('name',array('default_module','default_method','default_param'),'in')->query()
		){
			foreach(array_keys($db_call) as $key)
				$db_call[$db_call[$key]['name']] = $db_call[$key]['value'];
			if(empty($db_call['default_module']))
				throw new my_exception('default call module not found in db');
			else
				$this->center_module = $db_call['default_module'];
			if(!empty($db_call['default_method']))
				$this->center_method = $db_call['default_method'];
			if(!empty($db_call['default_param']))
				$this->center_params = $this->parse_center_params($db_call['default_param']);
		}
		elseif($default_module = $this->_config('default_module'))
			$this->center_module = $default_module;
		else
			throw new my_exception('default module not found');
	}
	
	private function parse_center_params($params){
		$result = array();
		preg_match_all('%(.*?)=(.*?)(;|$)%', $params, $temp_params);
		foreach($temp_params as $key=>&$param_name)
			$result[$param_name] = $temp_params[2][$key];
		return $result;
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
		//var_dump($this->_config('module_link_from_module_config'), $this->_config('module_link_from_db'), $this->call_list );
		array_unshift($this->call_list,array(
			'module_name' => $this->center_module,
			'method_name' => $this->center_method, 
			'position' => $this->_config('main_position_name'),
			'params' => (!empty($this->center_params))?$this->center_params:NULL,
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
				foreach($includes as &$new_name){
					if(is_array($include_src[$name])){
						//TODO recursive merge
						if(isset($include_src[$new_name])){
							foreach($include_src[$name] as $param_name=>&$value)
								if(isset($include_src[$new_name][$param_name]) && is_array($include_src[$new_name][$param_name]))
									$include_src[$new_name][$param_name] = array_merge($include_src[$new_name][$param_name],$value);
								else
									$include_src[$new_name][$param_name] = $value;
						}
						else
							$include_src[$new_name] = $include_src[$name];
					}
					else
						$include_src[$new_name] = (isset($include_src[$new_name])?($include_src[$new_name]."\n"):'').$include_src[$name];
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
		//$this->_query->echo_sql=1;
		$call_db_list = $this->_query->select('module_name, method_name, position, id')->from('module_link')->
			where('exclude',1,'!=')->_and('inactive',0)->_and('center_module',$center_module,'in')->_and('center_method',$center_method,'in')->_and('admin_mode',$this->admin_mode)->
			order('order,id')->query();
		//$this->_query->echo_sql=0;
		//create array of link_id for query
		//var_dump($call_db_list);die;
		$link_list = array();
		if($call_db_list){
			foreach($call_db_list as &$call)
				if(!isset($link_list[$call['id']]))
					$link_list[$call['id']] = true;
			$call['params'] = array();
			$link_param = $this->_query->select()->from('module_link_param')->where('link_id',array_keys($link_list),'in')->query();
			//var_dump($link_param); die;
			//grouping params by link_id
			$param_group = array();
			foreach($link_param as $param){
				if(!isset($param_group[$param['link_id']]))
					$param_group[$param['link_id']] = array('condition'=>array(),'param'=>array());
				if($param['type'] == 'param')
					$param_group[$param['link_id']]['param'][$param['name']] = $param['value'];
				elseif($param['type'] == 'condition')
					$param_group[$param['link_id']]['condition'][$param['name']] = $param['value'];
			}
			//applying params
			foreach($call_db_list as &$call){
				if(!empty($param_group[$call['id']])){
					$call['params'] = $param_group[$call['id']]['param'];
					$call['condition'] = $param_group[$call['id']]['condition'];
				}
				$this->call_list[] = $call;
			}
		}
		//var_dump($this->call_list);die;
	}
	
	private function get_db_config(){
		//get config for modules from db and put them to cache
		$this->get_module_list();
		if($this->_config('config_from_db')){
			$db_params = $this->_query->select('module_name,name,value')->from('module_param')->where('module_name',array_merge(array('app'),$this->module_list),'in')->query();
			foreach($db_params as $param)
				$this->config_cache[$param['module_name']][$param['name']] = $param['value'];
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
	
	public function set_module_config_include(&$module){
		if($this->_config('include_from_module_config') && $include_cache = $module->_config('include')){
			//var_dump($module->module_name,$include_cache);
			$this->check_array_comma($include_cache);
			if(!isset($this->loaded_module_include))
				$this->loaded_module_include = array();
			$admin_mode = $this->admin_mode?'admin_mode.':'';
			$this->add_module_include($admin_mode.'*', $include_cache, $module->module_name, $this->loaded_module_include);
			$this->add_module_include($admin_mode.'*.*', $include_cache, $module->module_name, $this->loaded_module_include);
			$this->add_module_include($admin_mode.($module->module_name).'.*', $include_cache, $module->module_name, $this->loaded_module_include);
			if($module->method_name){
				//var_dump($module->module_name.'->'.$module->method_name, $include_cache);
				$this->add_module_include($admin_mode.$module->module_name.$module->method_name, $include_cache, $module->module_name, $this->loaded_module_include);
				//if($this->admin_mode)
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
	
	public function set_module_template_include(&$module){
		if($this->_config('get_module_template_include'))
			if($include = $module->_config('template_include')){
				if(!is_array($include)){
					if(strpos($include,',')!==false)
						$include = explode(',',$include);
					else
						$include = array($include);
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
		elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) &&
			preg_match('%^(.+?)-(.+?),(.+?);q=([0-9\.]+)%', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_arr)){
			$this->config->set('language',$lang_arr[1]);
		}
		if(!empty($_REQUEST['_no_link']) || $this->_config('content_type')=='json_html'){
			$this->config->set('module_link_from_module_config',NULL); //need or need not?
			$this->config->set('module_link_from_config',NULL);
			$this->config->set('module_link_from_db',NULL);
		}
		if(!empty($_REQUEST['_debug'])){
			$this->config->set('debug',true);
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
		//!!!var_dump($call);
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
		if(!empty($call['method_name']) && $call['method_name']!='*')
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
		//$this->prepare_callable_method($module);
		$callable_method = $module->_config('callable_method');
		if(!isset($callable_method[$method_name]))
			throw new my_exception('access rule not found',$module_name.'.'.$method_name,0);
		//TODO access rules for users and groups
		//FIXME temp access rules
		$access = true;
		$module_config_class_name = $module->_get_config_name();
		$module_role_read = constant($module_config_class_name.'::role_read');
		$module_role_write = constant($module_config_class_name.'::role_write');
		$module_role_delete = constant($module_config_class_name.'::role_delete');
		$access_name = $this->_config('access_name');
		if($method_name == '_admin' || $module_name == 'admin')
			$access = false;
		elseif(empty($callable_method[$method_name][$access_name]))
			throw new my_exception('not found roles for called method', $module_name.'.'.$method_name);
		else{
			foreach($callable_method[$method_name][$access_name] as $obj_name=>&$access_rule){
				if($access_rule!=$module_role_read)
					$access = false;
			}
		}
		if(!$access && !isset($_SESSION['user_info'])){
			$message_str = '';
			$this->message($module_name.'.'.$method_name.': недостаточно прав доступа. Укажите логин и пароль');
			unset($this->module[count($this->module)-1]);
			$this->add_user_module();
		}
		else
			$access = true;
		//var_dump($module->module_name, $method_name, $access);
		return $access;
	}
	
	/*private function prepare_callable_method(&$module){
		$temp_rule = array();
		$callable_method = $module->config->get('callable_method',true);
		if(!isset($this->calleble_method_cache[$module->module_name])){
			//$this->check_array_comma($callable_method);
			$this->calleble_method_cache[$module->module_name] = &$callable_method;
			$module->config->set('callable_method',$callable_method);
		}
		else
			$callable_method = $this->calleble_method_cache[$module->module_name];
			//TODO not copy, get from cache
	}*/
	
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
	
	private function user_module_exists($user_default_method='form'){
		foreach($this->call_list as $call)
			if($call['module_name'] == 'user' && ($call['method_name'] == $user_default_method || !$call['method_name'] || $call['method_name']=='*'))
				return true;
	}
	
	private function call_module_method(&$module, $method_name, &$call){
		$module_name = $module->module_name;
		if(!method_exists($module, $method_name))
			throw new my_exception('method name not found for this module', $module_name.'.'.$method_name);
		$args_new = array();
		if(!isset($call['params']))
			$call['params'] = NULL;
		$args = $this->get_args($module,$method_name, $call['params'], $args_new);
		$module->argument_all = &$args;
		$module->argument_new = &$args_new;//list of arguments different from defaults
		$module->method_name = $method_name;
		$module->position = (!empty($call['position']))?$call['position']:$this->main_position_name;
		$this->set_module_config_include($module);
		$this->set_module_template_include($module);
		//var_dump($this->include);
		if($result = call_user_func_array(array($module,$method_name),$args))
			$module->_result = $result;
		//$this->set_module_config_include($module);
	}
	
	private function get_args($obj,$method,$args=NULL, &$args_new=array()){
		//php-reflection allow to miss method arguments
		//if $args is array $_REQUEST params will be ignored
		$method_reflection = new ReflectionMethod($obj,$method);
		$params_reflection = $method_reflection->getParameters();
		$disable_php_filter = $this->_config('disable_php_filter');
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
					$callable_method = $obj->_config('callable_method');
					if(isset($callable_method[$method][$param_name]) && $callable_method[$method][$param_name]!=$this->_config('exclude_method_from_link_list'))
						$filter = $callable_method[$method][$param_name]; 
					elseif(!($default_argument_filter = $this->_config('default_argument_filter')))
						throw new my_exception('php filter enabled but default filter is undefined');
					else
						$filter = $default_argument_filter;
					if(!$filter)
						$value = false;
					elseif($filter!=$disable_php_filter){
						$value = filter_var($value, $filter);
					}
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
		$center_position = $this->_config('main_position_name');
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
			//echo "{$module->module_name}:{$module->_config('output_config')};";
			if($module->_config('output_config'))
				$result['_config'] = get_object_vars($module->config);
			$result['_module_name'] = $module->module_name;
			if($module->method_name)
				$result['_method_name'] = $module->method_name;
			if($module->position)
				$result['_position'] = $module->position;
			if(($template = $module->_config('template')) && $result['_module_name']!='app')
				$result['_template'] = $template;
			if(($module->_config('output_all_argument') || $module->position==$center_position) && !empty($module->argument_all)){
				$result['_argument'] = &$module->argument_all;
				if($module->_config('output_new_argument') && !empty($module->argument_new) && $module->method_name!='_get_param_value')
					$result['argument_new'] = $module->argument_new;
			}
			elseif($module->_config('output_new_argument') && !empty($module->argument_new))
				$result['_argument'] = $module->argument_new;
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
		$this->output['meta']['content_type'] = $this->_config('content_type');
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
		if($this->_config('debug')){
			$this->output['debug'] = $this->_debug;
		}
	}
	
	public function redirect($location,$params=array()/*, $auto_base = false*/){
		if(!$location)
			return;
		if(!isset($_SESSION))
			session_start();
		if($this->error)
			$_SESSION['error'] = $this->error;
		if($this->message)
			$_SESSION['message'] = $this->message;
		foreach($params as $name=>$value)
			$location.= '&'.$name.'='.$value;
		//if($auto_base) TODO get module-method
		//var_dump($location); return;
		header("Location: ".$location);
		die;
	}
	
	//path
	
	public function add_path($item){
		if($item){
			if(!is_array($item))
				$item = array('title'=>$item);
			if(!isset($item['href']))
				$item['href'] = $_SERVER['REQUEST_URI'];
			if($this->_config('path_link_alias')){
				if(!$this->menu_module)
					$this->menu_module = new menu($this);
				$item['href'] = $this->menu_module->check_alias($item['href']);
			}
			$item['title'] = $this->_config('ucfirst_path')?$this->mb_ucfirst($item['title']):$item['title'];
			$this->_path[] = $item;
		}
	}
	
	public function add_module_path($item=NULL){
		if($this->get_path_count()==$this->default_path_count)
			$this->get_module_path();
		$this->add_path($item);
	}
	
	public function add_method_path($item){
		if($this->get_path_count()==$this->default_path_count)
			$this->get_method_path();
		$this->add_path($item);
	}
	
	public function &get_path(){
		return $this->_path;
	}
	
	public function get_path_count(){
		return count($this->_path);
	}
	
	public function unset_path($start = 0, $length = 0){
		if($start==0 and $length==0)
			$this->_path = array();
		else{
			if(!$length)
				$length = $this->get_path_count();
			for($i=$start;$i<$length;$i++)
				unset($this->_path[$i]);
		}
	}
	
	public function get_lang_title($module=NULL,$method=NULL,$param=NULL){
		if(!$module)
			$module = $this->center_module;
		if($method===NULL)
			$method = $this->center_method;
		if(!$param)
			$param = $this->parent->_config('language_title_name');
		$admin_mode = $this->admin_mode?'admin.php':'';
		if($method)
			$name = isset($this->parent->language_cache[$module][$method][$param])?$this->parent->language_cache[$module][$method][$param]:$method;
		else
			$name = isset($this->parent->language_cache[$module][$param])?$this->parent->language_cache[$module][$param]:$module;
		return $name;
	}
	
	public function get_module_path(){
		if($this->center_module == $this->_config('default_module') && !$this->center_method){
			if(!$this->admin_mode)
				$this->_path = array();
		}
		else{
			$module_name = $this->get_lang_title(NULL,false);
			$admin_mode = $this->admin_mode?'admin.php':'';
			if(isset($this->module[0]))
				$this->add_path(array('href'=>'/'.$admin_mode.'?call='.$this->center_module.'.'.$this->module[0]->get_default_method(),'title'=>$module_name));
		}
	}
	
	public function get_method_path($method=NULL){
		if(!$method)
			$method = $this->center_method;
		$this->get_module_path();
		if($method && (!isset($this->module[0]) || $method!=$this->module[0]->get_default_method() )){
			$admin_mode = $this->admin_mode?'admin.php':'';
			$method_name = $this->get_lang_title();
			$this->add_path(array('href'=>'/'.$admin_mode.'?call='.$this->center_module.'.'.$method,'title'=>$method_name));
		}
	}
	
	public function mb_ucfirst($str, $enc = 'utf8'){
		return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}
}
?>