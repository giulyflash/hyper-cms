<?php
class module_link extends module{
	protected $config_class_name = 'module_link_config';
	
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
					app::check_array_comma($callable_method);
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

	public function edit($id=NULL, $module=NULL, $method=NULL, $params=array()){
		//module, method, params - to made link from
		if($id){
			$link = $this->_query->select()->from('module_link ml')->where('id',$id)->query1();
			/*foreach($params as $name=>$value){
				$this->_query->join('module_link_param mlp','left','inner')->_on('ml.link_id','ml.link_id')->where();
			}*/
		}
		//var_dump($this->_config('exclude_from_admin_list'));
		$module_list = $this->get_module($this->_config('exclude_from_admin_list'));
		foreach($module_list as $module_name=>&$module)
			$this->_result['module_list'][$module_name] = $module['title'];
		$this->_result['data'] = json_encode($module_list);
		$this->_result['test'] = $module_list; 
		//header('Content-Type: text/plain; charset=utf-8');
		//var_dump($module_list);die;
	}
	
	public function _admin(){
		$result = $this->_query->select()->from('module_link')->order('order')->query2assoc_array('link_id',NULL,false);
		$params = $this->_query->select()->from('module_link_param')->where('link_id',array_keys($result),'in')->_and('type','condition')->query();
		foreach($params as $param)
			$result[$param['link_id']]['params'][] = $param;
		$this->_result = &$result;
	}
}

class module_link_config extends module_config{
	public $callable_method=array(
		'_admin,edit,remove'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
		'save'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
			//TODO check overwrite callable_method params
			'params'=>FILTER_UNSAFE_RAW,
			'_exclude'=>true
		),
	);
	
	protected $exclude_from_admin_list = array();
	
	protected $include = array(
		'_admin,edit'=>
			'<link href="module/module_link/admin.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="module/module_link/admin.js"></script>
			<script type="text/javascript" src="module/module_link/wizard.js"></script>',
	);
}
?>