<?php
class menu extends base_module{
	protected $config_class_name = 'menu_config';
	protected $alias_data = array();
	
	public function __construct(&$parent=NULL){
		parent::__construct($parent);
		if(!$this->parent->menu_module)
			$this->parent->menu_module = $this;
	}
	
	public function _admin($title=NULL,$menu_id=NULL){
		if(!$menu_id){
			$this->_result = $this->_query->select()->from($this->module_name)->query();
		}else{
			$this->get_category_base('translit_title',$title,false,'all_sub',array('menu_id',$menu_id));
		}
	}
	
	public function get($id = 1, $show_title=NULL, $type=NULL){
		$this->get_category_base('link', $link = ($_SESSION['call'][0]=='/'?false:$_SESSION['call'][0]), NULL, 'all', array('menu_id',$id));
		if($show_title)
			$this->_result['title'] = $this->_query->select('title')->from($this->module_name)->where('id',$id)->query1('title');
		if($type)
			$this->parent->include[] = '<link href="/module/menu/'.$type.'.css" rel="stylesheet" type="text/css"/>';
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
			$this->_result = array_merge($this->_result,$this->_query->select()->from($this->_category_table_name)->where('menu_id',$id)->order('left')->query());
	}
	
	public function remove($id=NULL){
		parent::remove($id, NULL, true, array('name'=>$this->_query->select('title')->from($this->module_name)->where('id',$id)->query1('title')));
		$this->_query->delete()->from($this->_category_table_name)->where('menu_id',$id)->query();
		$this->parent->redirect('/admin.php?call='.$this->module_name.'.'.$this->_config('admin_method'));
	}
	
	public function edit_item($id=NULL, $insert_place=NULL, $menu_id=NULL, $link=NULL){
		if(!$menu_id && !$menu_id = $this->_query->select('menu_id')->from($this->_category_table_name)->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		parent::edit_category($id);
		$module_link = new module_link($this->parent);
		$module_link->edit(isset($this->_result['link_id'])?$this->_result['link_id']:NULL, $id?NULL:$link);
		$this->_result['data'] = $module_link->_result['data'];
		if(isset($module_link->_result['link']))
			$this->_result['link_data'] = $module_link->_result['link'];
		//TODO draft
		//TODO alias editor
		//TODO drag-n-drop edit alias order of aliases
	}

	public function save_item($id=NULL,$title=NULL,$insert_place=NULL,$input_type=NULL,$link_text=NULL,$link=array(),$menu_id){
		//var_dump($link);die;
		if(!$menu_id && !($menu_id = $this->_query->select('menu_id')->from($this->_category_table_name)->where('id',$id)->query1('menu_id')))
			throw new my_exception('menu_id not found');
		if(!$title){
			if(isset($link[0]['module'], $link[0]['method'], $link[0]['param'], $link[0]['param']['name'], $link[0]['param']['value']) && count($link[0]['param'])==1){
				$temp_module_name = $link[0]['module'];
				$temp_module = new $temp_module_name($this->parent);
				$temp_params = $temp_module->_get_param_value($link[0]['method'], $link[0]['param']['name']);
				if($temp_params && isset($temp_params[$link[0]['param']['value']]))
					$title = $temp_params[$link[0]['param']['value']];
			}
			if(!$title){
				$this->_message('menu item name must not be empty');
				//array(1) { [0]=> array(3) { ["module"]=> string(7) "article" ["method"]=> string(12) "get" ["param"]=> array(1) { [0]=> array(2) { ["name"]=> string(5) "title" ["value"]=> string(7) "Servera" } } } } 
				//{"center_module":"*","center_method":"*","module_name":"article","method_name":"get","param":[{"param_name":"title","value":"Server","type":"param"}]}
				if(!empty($link[0]))
					$link = '&link='.json_encode($link[0]);
				$this->parent->redirect('/admin.php?call=menu.edit_item&menu_id='.$menu_id.$link);
			}
		}
		$link_id = $id?($this->_query->select('link_id')->from($this->_category_table_name)->where('id',$id)->query1('link_id')):NULL;
		if($input_type=='wizard'){
			if($link){
				$link = $link[0];
				if(!$link['module'])
					throw new my_exception('module name not found');
				$module_link = new module_link($this->parent);
				$link_id = $module_link->save($link_id,array(0=>$link),false,1);
				$link_text = '/?call='.$link['module'].'.'.$link['method'];
				if(isset($link['param']))
					foreach($link['param'] as &$param)
						$link_text.= '&'.$param['name'].'='.$param['value'];
				$link_text = $this->check_alias($link_text);
			}
		}elseif($link_id){
			$module_link = new module_link($this->parent);
			$module_link->remove($link_id,false);
		}
		$value = array(
			'title'=>$title,
			'translit_title'=>translit::transliterate($title),
			'link'=>($link_text?$link_text:$this->_config('default_link')),
			'link_id'=>$link_id,
			'menu_id'=>$menu_id,
		);
		parent::save_category($id,$value,$insert_place,array('menu_id',$menu_id),NULL);
		$this->parent->redirect('/admin.php?call=menu._admin&menu_id='.$menu_id);
	}

	public function move_category($id=NULL, $insert_type=NULL, $insert_place=NULL){
		if(!$menu_id = $this->_query->select('menu_id')->from($this->_category_table_name)->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		parent::move_category($id,$insert_type,$insert_place,array('menu_id',$menu_id),NULL);
		$this->parent->redirect('/admin.php?call=menu._admin&menu_id='.$menu_id);
	}

	public function remove_category($id=NULL){
		if(!$menu_id = $this->_query->select('menu_id')->from($this->_category_table_name)->where('id',$id)->query1('menu_id'))
			throw new my_exception('menu_id not found');
		if($link_id = $this->_query->select('link_id')->from($this->_category_table_name)->where('id',$id)->query1('link_id')){
			$module_link = new module_link($this->parent);
			$module_link->remove($link_id,false);
		}
		parent::remove_category($id, array(), NULL);
		$this->parent->redirect('/admin.php?call=menu._admin&menu_id='.$menu_id);
	}
	
	public function save($id, $title){
		$value = array('title'=>$title, 'translit_title'=>translit::transliterate($title));
		parent::save($id, $value, 'edit', true, array('name'=>$title));
	}
	
	public function check_alias($link){
		if(!$this->alias_data) 
			$this->alias_data = $this->_query->select('link_template,alias_template')->from($this->_config('alias_table'))->order('order')->query();
		foreach($this->alias_data as &$alias){
			if(preg_match('%'.$alias['link_template'].'%', $link)){
				$link = preg_replace('%'.$alias['link_template'].'%', $alias['alias_template'], $link);
				break;
			}
		}
		return $link;
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
						return $this->_query->select('id,title')->from($this->_category_table_name)->query2assoc_array('id','title');
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
	/*protected $template_include = array(
		'module/module_link/link_wizard.xhtml.xsl',
		TODO wtf the bug???
	);*/
	
	protected $callable_method=array(
		'get'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
				'menu_item' => self::role_read
			),
		),
		'_admin,edit,save,remove,edit_item,save_item,move_item,remove_item,unlock_database,set_translit_title,set_translit_title_category'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
				'menu_item' => self::role_write
			),
		),
		'move_item,save_item,remove_category,edit_category,get_category,save_category'=>array(
			'_exclude'=>true
		),
		'edit_item'=>array(
			'insert_place'=>'_exclude',
		),
		'save_item'=>array(
			'link'=>'_disable_filter',
		),
	);
	
	protected $object = array(
		'menu'=>array(
			'method'=>'get',
			'param'=>'id'
		),
	);
	
	protected $object_exclude = array(
		'menu_item'
	);
	
	protected $include = array(
		'edit_item'=>
			'<link href="/module/module_link/wizard.css" rel="stylesheet" type="text/css"/>
			<script src="/module/module_link/wizard.js" type="text/javascript"></script>
			<script src="/module/menu/admin.js" type="text/javascript"></script>',
		'_admin,edit_item,edit_category'=>'<link href="/module/menu/admin.css" rel="stylesheet" type="text/css"/>',
	);
	
	protected $alias_table = 'menu_link_alias';
	
	protected $output_new_argument = true;
	protected $default_link = '#';
	protected $category_table = 'menu_item';
	protected $category_field='id,title,translit_title,left,right,depth,link,menu_id';
	
	public $has_item = false;
	public $has_category = true;
}
?>