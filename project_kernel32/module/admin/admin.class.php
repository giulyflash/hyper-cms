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
	
	public function add_method(&$module, &$method_name, &$class_list, $need_params = true){
		if($method_name && method_exists($module, $method_name)){
			$class_list[$module->module_name]['method'][$method_name]['title'] =
				(!empty($this->parent->language_cache[$module_name][$method_name]['_method_name']))?
					$this->parent->language_cache[$module->module_name][$this->parent->_config('language_title_name')]:$method_name;
		}
	}
}
?>