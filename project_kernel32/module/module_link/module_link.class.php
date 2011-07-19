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
			$this->_result['link'] = $this->_query->select()->from('module_link')->where('id',$id)->query1();
			$this->_result['link']['param'] = $this->_query->select()->from('module_link_param')->where('link_id',$id)->query();
			$this->_result['link_position'] = $this->_result['link']['position']; 
			$this->_result['link'] = json_encode($this->_result['link']);
		}
		$module_list = $this->get_module($this->_config('exclude_from_admin_list'));
		$this->_result['data'] = json_encode($module_list);
		$this->_result['position'] = $this->_query->select('translit_title,title')->from('position')->query2assoc_array('translit_title','title');
	}
	
	public function save($id=NULL,$link=NULL,$position=NULL,$order=NULL){
		//var_dump($link);
		if(empty($link[1]['module']))
			throw new my_exception('module name not found');
		$link_value = array('module_name'=>$link[1]['module']);
		if(!empty($link[1]['method']))
			$link_value['method_name'] = $link[1]['method'];
		if(!empty($link[2]['module']))
			$link_value['center_module'] = $link[2]['module'];
		if(!empty($link[2]['module']))
			$link_value['center_method'] = $link[2]['method'];
		$link_value['position'] = $position?$position:$this->parent->_config('main_position_name');
		$link_value['order'] = $order?$order:1;
		if($id){
			$this->_query->update($this->module_name)->set($link_value)->where('id',$id)->limit(1)->execute();
			$message = 'edit successfool';
		}
		else{
			$this->_query->insert($this->module_name)->values($link_value)->execute();
			if(!$id = $this->_query->insert_id())
				throw new my_exception('id not found');
			$message = 'add successfool';
		}
		foreach($link as $link_id=>&$link_item){
			$this->_query->delete()->from($this->module_name.'_param')->where('link_id',$id)->query();
			if(isset($link_item['param']))
				foreach($link_item['param'] as &$param){
					$type = ($link_id-1)?'condition':'param';
					//var_dump($link,$link_id,$type);
					$param_value = array('param_name'=>$param['name'], 'type'=>$type, 'link_id'=>$id);
					if(isset($param['value']))
						$param_value['value'] = $param['value'];
					$this->_query->insert($this->module_name.'_param')->values($param_value)->execute();
				}
		}
		$this->_message($message);
		$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$this->_config('admin_method'));
	}
	
	public function _admin(){
		$this->_result = $this->_query->select()->from('module_link')->where('menu',0)->order('order')->query2assoc_array('id',NULL,false);
		$params = $this->_query->select()->from('module_link_param')->where('link_id',array_keys($this->_result),'in')->query();
		foreach($params as &$param)
			$this->_result[$param['link_id']]['params'][] = $param;
		$title_field = $this->parent->_config('language_title_name');
		$param_field =  $this->parent->_config('language_param_name');
		$position = $this->_query->select('translit_title,title')->from('position')->query2assoc_array('translit_title','title');
		foreach($this->_result as &$link){
			$link['position_title'] = (!empty($position[$link['position']]))?$position[$link['position']]:$link['position'];
			$this->get_link_translation($link,$title_field,$param_field);
			$this->get_link_translation($link,$title_field,$param_field,1);
		}
	}
	
	private function get_link_translation(&$link,$title_field,$param_field,$canter_module = NULL){
		if($canter_module){
			$module_param = 'center_module';
			$method_param = 'center_method';
			$type_param = 'condition';
			$module_trans_title = 'center_module_title';
			$method_trans_title = 'center_method_title';
		}
		else{
			$module_param = 'module_name';
			$method_param = 'method_name';
			$type_param = 'param';
			$module_trans_title = 'module_title';
			$method_trans_title = 'method_title';
		}
		$module_name = $link[$module_param];
		$module = new $module_name($this->parent);
		$link[$module_trans_title] = (!empty($this->parent->language_cache[$link[$module_param]][$title_field]))?
		$this->parent->language_cache[$link[$module_param]][$title_field]:$link[$module_param];
		$link[$method_trans_title] = (!empty($this->parent->language_cache[$link[$module_param]][$link[$method_param]][$title_field]))?
		$this->parent->language_cache[$link[$module_param]][$link[$method_param]][$title_field]:$link[$method_param];
		if(isset($link['params']))
		foreach($link['params'] as &$param)
			if($param['type']==$type_param){
				$param['title'] = (!empty($this->parent->language_cache[$link[$module_param]][$link[$method_param]][$param_field][$param['param_name']]))?
				$this->parent->language_cache[$link[$module_param]][$link[$method_param]][$param_field][$param['param_name']]:$param['param_name'];
				$module->_get_param_value($link[$method_param],$param['param_name']);
				if($module->_result)
				if(!empty($module->_result[$param['value']]))
				$param['value'] = $module->_result[$param['value']];
			}
	}
	
	public function remove($id=NULL){
		if(!$id)
			throw new my_exception('id not found');
		$this->_query->delete()->from($this->module_name)->where('id',$id)->limit(1)->execute();
		$this->_query->delete()->from($this->module_name.'_param')->where('link_id',$id)->execute();
		$this->_message('delete successfool');
		$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$this->_config('admin_method'));
	}
}

class module_link_config extends module_config{
	public $callable_method=array(
		'_admin,edit,remove,save'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
		'save'=>array(
			'link'=>'_disable_filter',
			'_exclude'=>true
		),
	);
	
	protected $template_include = array(
		'module/module_link/link_wizard.xhtml.xsl',
	);
	
	protected $exclude_from_admin_list = array();
	
	protected $include = array(
		'_admin,edit'=>
			'<link href="module/module_link/admin.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="module/module_link/admin.js"></script>',
		'edit'=>
			'<link href="module/module_link/wizard.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="module/module_link/wizard.js"></script>',
	);
}
?>