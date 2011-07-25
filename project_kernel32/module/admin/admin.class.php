<?php
class admin_config extends module_config{
	protected $include = array(
		'_admin.get' => '<script type="text/javascript" src="/module/admin/admin.js"></script>',
	);
	
	protected $callable_method=array(
		'get'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
}

class admin extends module{
	protected $config_class_name = 'admin_config';
	
	public function get(){
		$exclude = array();
		if($app_exclude = $this->parent->_config('admin_exclude'))
			$exclude = &$app_exclude;
		//throw new my_exception('module directory not found',_module_path);
		//$link = new module_link($this->parent);
		$this->_result = $this->get_module($exclude, true);
	}
	
	//TODO optimize this 3 copy-pasted methods below
	
	public function &get_module($exclude = array(), $adnin_method_only=false){
		//TODO check access for $module->admin_method
		$exclude = array_merge($exclude, array('admin','app','base_module'));
		$class_list = array();
		$dir = _module_path;
		if ($handle = opendir($dir)){
			$module_root=scandir($dir);
			foreach($module_root as $class_dir)
			if(preg_match("%^([a-zA-Z\-_]+)$%",$class_dir,$re) && !in_array($re[1],$exclude)){
				$module_name = $re[1];
				$module = new $module_name($this->parent);
				$callable_method = $module->_config('callable_method',true);
				if($adnin_method_only){
					$admin_method = $module->_config('admin_method');
					if($admin_method && isset($callable_method[$admin_method])){
						$this->add_method($module, $admin_method, $class_list);
					}
				}
				else{
					$class_list[$module_name]['title'] =
					(!empty($this->parent->language_cache[$module_name][$title_str = $this->parent->_config('language_title_name')]))?
					$this->parent->language_cache[$module->module_name][$title_str]:$module_name;
					$params_title=$this->parent->_config('language_param_name');
					$exclude_name = $this->parent->_config('exclude_method_from_link_list');
					foreach(array_keys($callable_method) as $callable_name)
					$this->add_method($module, $callable_name, $class_list, true, $callable_method, $title_str, $params_title, $exclude_name);
				}
			}
		}
		else
		throw new exception('module directory not found',_module_path);
		return $class_list;
	}
	
	public function add_method(&$module, &$method_name, &$class_list, $need_params = false, &$callable_method=NULL, &$title_str=NULL, &$params_title=NULL, &$exclude_name=NULL){
		if($method_name && !$this->check_method_exclude($method_name, $callable_method) && method_exists($module, $method_name)){
			$class_list[$module->module_name]['method'][$method_name]['title'] =
			(!empty($this->parent->language_cache[$module->module_name][$method_name][$title = $this->parent->_config('language_title_name')]))?
			($this->parent->language_cache[$module->module_name][$method_name][$title]):$method_name;
			if($need_params){
				$method_reflection = new ReflectionMethod($module,$method_name);
				$params_reflection = $method_reflection->getParameters();
				$params = &$class_list[$module->module_name]['method'][$method_name]['params'];
				foreach($params_reflection as &$param_reflection){
					$param_name = $param_reflection->name;
					if(!isset($callable_method[$method_name][$param_name]) || $callable_method[$method_name][$param_name] && $callable_method[$method_name][$param_name]!=$exclude_name){
						$params[$param_name] = !empty($this->parent->language_cache[$module->module_name][$method_name][$params_title][$param_name])?
						$params[$param_name] = $this->parent->language_cache[$module->module_name][$method_name][$params_title][$param_name] : $param_name;
					}
				}
			}
		}
	}
	
	private function check_method_exclude($method, &$callable_method){
		if(!empty($callable_method[$method][$this->parent->_config('exclude_method_from_link_list')]))
		return true;
	}
}
?>