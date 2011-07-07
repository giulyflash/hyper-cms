<?php
class admin_config extends module_config{
	protected $include = array(
		'_admin.get' => '<script type="text/javascript" src="/module/admin/admin.js"></script>',
	);
	
	protected $callable_method=array(
		'get'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_read,
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
		$link = new module_link($this->parent);
		$this->_result = $link->get_module($exclude, true);
	}
	
	public function get_module($exclude = array(), $adnin_method_only=false){
		//TODO check access for $module->admin_method
		$exclude = array_merge($exclude, array('admin','app'));
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
						if(isset($callable_method[$callable_name=$module->_config('admin_method')]))
							$this->add_method($module, $callable_name, $class_list, false);
					}
					else{
						$class_list[$module_name]['title'] = 
							(!empty($this->parent->language_cache[$module_name][$title_str = $this->parent->_config('language_title_name')]))?
								$this->parent->language_cache[$module->module_name][$title_str]:$module_name;
						foreach($callable_method as $callable_name=>$callable_value)
							$this->add_method($module, $callable_name, $class_list);
						
					}
				}
		}
		else
			throw new exception('module directory not found',_module_path);
		return $class_list;
	}
	
	public function add_method(&$module, &$method_name, &$class_list, $need_params = true){
		if($method_name && method_exists($module, $method_name)){
			$class_list[$module->module_name]['method'][$method_name]['title'] =
				(!empty($this->parent->language_cache[$module_name][$method_name]['_method_name']))?
					$this->parent->language_cache[$module->module_name][$this->parent->_config('language_title_name')]:$method_name;
		}
	}
}
?>