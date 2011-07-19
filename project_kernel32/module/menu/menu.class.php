<?php
class menu extends base_module{
	protected $config_class_name = 'menu_config';
	
	public function _admin($page=null, $count=null, $show='all'){
		$this->_result = $this->_query->select()->from('menu')->query();
		$this->_result['lt'] = '<';
		$this->_result['gt'] = '>';
		if(!$this->_result)
			$this->_message('menu list is empty');
	}
	
	public function get($id = 1, $show_title=NULL){
		parent::get_category(NULL,NULL,NULL,NULL,'id,title,depth,link',array('menu_id',$id));
		if($show_title)
			$this->_result['title'] = $this->_query->select('title')->from('menu')->where('id',$id)->query1('title');
	}
	
	/*function build_menu(&$src, &$result_node, $parent_id=NULL){
		foreach($src as $num=>$item){
			if($item['parent_id']==$parent_id){
				$pos = count($result_node);
				$result_node[$pos] = $item;
				self::build_menu($src, $result_node[$pos], $item['id']);
			}
		}
	}*/
	
	public function edit($id=NULL){
		parent::edit($id);
		if($id)
			$this->_result = array_merge($this->_result,$this->_query->select()->from($this->module_name.$this->_config('category_posfix'))->where('menu_id',$id)->order('left')->query());
		$this->_result['lt'] = '<';
		$this->_result['gt'] = '>';
	}
	
	public function remove($id=NULL){
		parent::remove($id, NULL, true, array('name'=>$this->_query->select('title')->from($this->module_name)->where('id',$id)->query1('title')));
		$this->_query->delete()->from($this->module_name.$this->_config('category_posfix'))->where('menu_id',$id)->query();
		$this->parent->redirect('/admin.php?call='.$this->module_name.'.admin');
	}
	
	public function edit_item($id=NULL, $insert_place=NULL){
		if(!$menu_id = $this->_query->select('menu_id')->from($this->module_name.$this->_config('category_posfix'))->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		parent::edit_category($id);
		//$this->_result['article'] = $this->_query->select('title,translit_title')->from('article')->order('create_date')->query();
		$module = new module_link($this->parent);
		$module_list = $module->get_module($module->_config('exclude_from_admin_list'));
		foreach($module_list as $module_name=>&$module)
			$this->_result['module_list'][$module_name] = $module['title'];
		$this->_result['data'] = json_encode($module_list);
		//TODO edit
	}

	public function save_item($id=NULL,$title=NULL,$link=NULL,$insert_place=NULL,$input_type=NULL,$link_article=NULL){
		if(!$menu_id = $this->_query->select('menu_id')->from($this->module_name.$this->_config('category_posfix'))->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		if(!$title){
			$this->_message('menu name must not be empty');
			return;
		}
		$link = $input_type=='article'?$link_article:$link;
		$value = array(
			'title'=>$title,
			'link'=>($link?$link:$this->_config('default_link')),
			'menu_id'=>$menu_id,
		);
		parent::save_category($id,$value,$insert_place,array('menu_id',$menu_id),NULL);
		$this->parent->redirect('admin.php?call=menu.edit&id='.$menu_id);
	}

	public function move_item($id=NULL, $insert_type=NULL, $insert_place=NULL){
		if(!$menu_id = $this->_query->select('menu_id')->from($this->module_name.$this->_config('category_posfix'))->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		parent::move_category($id,$insert_type,$insert_place,array('menu_id',$menu_id),NULL);
		$this->parent->redirect('admin.php?call=menu.edit&id='.$menu_id);
	}

	public function remove_item($id=NULL){
		if(!$menu_id = $this->_query->select('menu_id')->from($this->module_name.$this->_config('category_posfix'))->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		parent::remove_category($id, array(), NULL);
		$this->parent->redirect('admin.php?call=menu.edit&id='.$menu_id);
	}
	
	public function save($id, $title){
		$value = array('title'=>$title);
		parent::save($id, $value, 'edit', true, array('title'=>$title));
		$this->parent->redirect('/admin.php?call=menu.admin');
	}
	
	public function _get_param_value($method_name,$param_name){
		switch($method_name){
			case 'get':{
				switch($param_name){
					case 'id':{
						return $this->_query->select('id,title')->from($this->module_name)->query2assoc_array('id','title');
						break;
					}
					case 'show_title':{
						return array('true'=>'+','false'=>'-');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit':{
				switch($param_name){
					case 'id':{
						return $this->_query->select('id,title')->from($this->module_name)->query2assoc_array('id','title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				
				break;
			};
			case 'remove_item':
			case 'edit_item':{
				switch($param_name){
					case 'id':{
						return $this->_query->select('id,title')->from($this->module_name.$this->_config('category_posfix'))->query2assoc_array('id','title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			//edit_item
			default: parent::_get_param_value($method_name,$param_name);
		}
	}
}

class menu_config extends base_module_config{
	protected $callable_method=array(
		'get'=>array(
			self::object_name=>array('menu','menu_item'),
			self::role_name=>array(self::role_read,self::role_read),
		),
		'_admin,edit,save,remove,edit_item,save_item,move_item,remove_item,unlock_database'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
		'move_item,save_item,remove_category,edit_category,get_category,get_category_by_title,save_category'=>array(
			'_exclude'=>true
		),
		'edit_item'=>array(
			'insert_place'=>'_exclude',
		)
	);
	
	protected $include = array(
		'edit_item'=>
			'<link href="module/module_link/wizard.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="module/module_link/wizard.js"></script>
			<script type="text/javascript" src="module/menu/admin.js"></script>',
	);
	
	protected $template_include = array(
		'module/module_link/link_wizard.xhtml.xsl',
		'module/base_module/base_module.xhtml.xsl',
	);
	
	protected $output_new_argument = true;
	protected $default_link = '#';
	protected $category_posfix = '_item';
	
	public $has_item = false;
	public $has_category = true;
}
?>