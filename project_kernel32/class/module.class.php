<?php
class module_config extends config{
	protected $default_method = 'get';//method to call when method was not specified in HTTP/link call
	protected $language;//language for this module
	protected $template;//path to template of this module; if false will be got template form module directory 
	protected $charset;//charset for this module TODO convert while output
	protected $main_template;//main template path
	protected $admin_method = '_admin';//method for to output data in admin mode and flag for output this class in admin module list
	protected $get_params_method = '_params';//method name for output list of params values for callable methods in link editor
	protected $param_values_method = '_get_param_values';//method name to output values of params for all methods
	protected $output_config = false;//if true public variables of this config will be send to output array  
	protected $output_all_argument = false;//if true all (default and new) arguments of called method will be sent to output array
	protected $output_new_argument = false;//if true new arguments of called method will be sent to output array
	protected $title;//if isset can be used as title for page
	protected $include = array();
	protected $link = array();
	protected $template_include = array();
	protected $need_language = true;//if true language file will be loaded from external file
	protected $parent_module = array();//if true language file will be merged from each parent
	protected $callable_method = array();//list of callable from HTTP-request with filter vars
	
	const role_read = 'read';
	const role_write = 'write';
	const role_delete = 'delete';
	const role_name = '__role__';
	const object_name = '__object__';
}

abstract class module{
	public $parent;//FIXME can't see properties from private; this var must be private
	public $config;//object containing configuration
	public $module_name;//name of this class
	public $method_name;//called method name
	public $position;//position of module instance
	public $_result;//result of called method execution; can be returned by called method or set manualy;
	public $header;//page header string(for central module)
	public $_query;//object allow to do sql query easy
	public $argument = array();//call args
	protected $config_class_name = 'module_config';//if defined config will be created from this class
	
	public function __construct(&$parent=NULL){
		$this->_set_call_time();
		$this->module_name = get_class($this);//get_called_class();
		if(!$parent)
			throw new my_exception('app object not found',$this->module_name);
		$this->parent = $parent;//TODO parent to _parent
		$config_class_name = $this->config_class_name;
		$this->config = new $config_class_name($this->parent->get_module_config($this->module_name));
		$this->_inherit_config();
		if(!$this->_config('language'))
			$this->config->set('language', $this->parent->_config('language'));
		$charset = $this->_config('charset');
		if(!$charset && !empty($this->parent->charset))
			$charset = $this->parent->charset;
		$this->charset = $charset;
		if(isset($this->parent->db))
			$this->_query = new object_sql_query($this->parent->db);
		if($this->_config('need_language'))
			$this->_get_module_language($this->module_name);
	}
	
	private function _get_module_language($module){
		if(!isset($this->parent->language_cache[$module])){
			$this->_get_module_language_data($module);
			if($inherit = $this->_config('parent_module'))
				$this->_inherit_language($inherit);
		}
	}
	
	public function _inherit_language($module){
		if(!is_array($module))
			$module = array($module);
		foreach($module as $module_name){
			$this->_get_module_language_data($module_name);
			$this->parent->language_cache[$this->module_name] = self::array_merge_recursive(
				$this->parent->language_cache[$module_name],
				$this->parent->language_cache[$this->module_name]
			);
			$this->parent->error_cache[$this->module_name] = array_merge(
				$this->parent->error_cache[$module_name],
				$this->parent->error_cache[$this->module_name]
			);
		}
	}
	
	private function _get_module_language_data($module_name){
		if(!isset($this->parent->language_cache[$module_name])){
			$file_name = _module_path.($module_name).'/'._language_path.$this->_config('language')._language_ext;
			@include $file_name;
			if(!empty($language))
				$this->parent->language_cache[$module_name] = $language;
			else
				$this->parent->language_cache[$module_name] = array();
			if(!empty($error))
				$this->parent->error_cache[$module_name] = $error;
			else
				$this->parent->error_cache[$module_name] = array();	
		}
	}
	
	public function array_merge_recursive(&$ar1, &$ar2){
		//ugly method, but native array_merge_recursive create sub-array when names intersect
		$result = $ar1;
		foreach($ar2 as $name=>$value)
			if(isset($ar1[$name]) && is_array($value))
				$result[$name] = &self::array_merge_recursive($ar1[$name],$ar2[$name]);
			else
				$result[$name]=$value;
		return $result;
	}
	
	public function _set_call_time(){
		$this->call_time = new date_microtime();
	}
	
	public function _get_call_time(){
		return $this->call_time->format();
	}
	
	public function _message($name=NULL, $params=array()){
		foreach($params as $param_name=>$value)
			if(substr($param_name,0,1)!='$'){
				$params['$'.$param_name]=$value;
				unset($params[$param_name]);
			}
		if(!empty($this->parent->error_cache[$this->module_name][$name]))
			$name = $this->parent->error_cache[$this->module_name][$name];
		$text = str_replace(array_keys($params),array_values($params), $name);
		$this->parent->message($text);
	}
	
	public function _db_query($query){
		//alias of database query
		//TODO own database for module, automaticaly connect
		return $this->parent->db->query($query, $this);
	}
	
	public function _config($name){
		return $this->config->get($name);
	}
	
	public function _add_include($module_include){
		if(!$module_include)
			return;
		if(!is_array($module_include))
			throw new my_exception('include is not array', $module_include);
		if($src_include = $this->_config('include')){
			foreach($module_include as $name=>$value)
				$src_include[$name] = (isset($src_include[$name])?$src_include[$name]:'').$value;
			$this->config->set('include',$src_include);
		}
		else
			$this->config->set('include',$module_include);
	}
	
	public function _params($method){
		$params = array();
		$method_reflection = new ReflectionMethod($this,$method);
		$params_reflection = $method_reflection->getParameters();
		$params = array();
		foreach($params_reflection as &$param_reflection){
			$param_name = $param_reflection->name;
			$callable_method = $this->_config('callable_method',true);
			if(!isset($callable_method[$method][$param_name]) || $callable_method[$method][$param_name] && $callable_method[$method][$param_name]!=$this->parent->_config('exclude_method_from_link_list')){
				if(!empty($this->parent->language_cache[$this->module_name][$method][$params_title=$this->parent->_config('language_param_name')][$param_name]))
					$params[$param_name]['title'] =  $this->parent->language_cache[$this->module_name][$method][$params_title][$param_name];
				else
					$params[$param_name]['title'] =  $param_name;
			}
		}
		return $params;
	}
	
	private function _inherit_config(){
		if($parent_module = $this->_config('parent_module'))
			for($i = count($parent_module)-1; $i>=0; $i--)
				$this->_inherit_module_config($parent_module[$i]);
	}
	
	private function _inherit_module_config(&$module){
		$config_class_name = $module.'_config';
		if(!class_exists($config_class_name)){
			if(file_exists($file_name = _module_path.$module.'/'.$module._class_ext));
			require_once($file_name);
		}
		if(class_exists($config_class_name)){
			$parent_config = new $config_class_name($this->parent->get_module_config($module));
			$this->_merge_config_array('include', $parent_config);
			$this->_merge_config_array('link', $parent_config);
			$this->_merge_config_array('tempalte_include', $parent_config, 'merge');
			$this->_merge_config_array('callable_method', $parent_config, 'inherit');
		}
	}
	
	private function _merge_config_array($param_name, &$parent_config,$mode = 'add'){
		if($parent_value = $parent_config->get_ref($param_name)){
			if($src_include = $this->config->get_ref($param_name)){
				if($mode=='add')
					foreach($parent_value as $name=>$value)
						$src_include[$name] = (isset($src_include[$name])?$src_include[$name]:'').$value;
				elseif($mode=='inherit'){
					foreach($parent_value as $name=>$value)
						if(isset($src_include[$name]))
							$src_include[$name] = array_merge($value, $src_include[$name]);
						else
							$src_include[$name] = $value;
				}
				elseif($mode=='merge')
					$src_include = array_merge($parent_value,$src_include);
				else
					throw new my_exception('wrong mode');
				//FIXME $src_include must be a reference and must not be need to do $src_include_ref
				$src_include_ref = &$src_include;
				$this->config->set_ref($param_name,$src_include_ref);
			}
			else
				$this->config->set_ref($param_name,$parent_value);
		}
	}
	
	public function _get_config_name(){
		return $this->config_class_name;
	}
	
	public function _get_param_value($method_name,$param_name){
		throw new my_exception('under construction');
	}
}
?>