<?php
class base_module_config extends module_config{
	protected $callable_method=array(
		//TODO check array merge works properly for child modules
		'get,get_category'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'get_category'=>array(
			'item_fields'=>false,
			'category_fields'=>false,
			'category_condition'=>false,
			'item_condition'=>false,
		),
		'get,edit'=>array(
			'select'=>false,
		),
		'get'=>array(
			'condition'=>false,
		),
		'_admin,edit,save,remove,unlock_database,_get_param_value,edit_category,save_category,move_category,remove_category,move_item,remove_item'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
		'edit_category,save_category,move_category,remove_category'=>array(
			'__access__' => array(
				"base_module_config_category" => self::role_write,
			),
		),
		'remove_category' => array(
			'condition' =>false,
			'redirect' =>false,
		),
		'remove'=>array(
			'condition' =>false,
			'redirect' =>false,
		),
		'save,save_category,move_category,unlock_database,_get_param_value,_admin'=>array(
			'_exclude'=>true
		),
		'save'=>array(
			'text'=>FILTER_UNSAFE_RAW,
		),
		'edit_category'=>array(
			'insert_place'=>'_exclude',
		)
	);
	
	/*protected $template_include = array(
		'module/base_module/base_module.xhtml.xsl',
	);*/
	
	protected $template_include = array(
		'module/module_link/link_wizard.xhtml.xsl',
		'module/base_module/base_module.xhtml.xsl',
	);
	
	protected $include = array(
		'admin_mode.*' =>
			'<link href="/module/base_module/admin.css" rel="stylesheet" type="text/css"/>',
		'*,admin_mode.*' =>
			'<link href="/module/base_module/base_module.css" rel="stylesheet" type="text/css"/>
			<script src="/module/base_module/base_module.js" type="text/javascript"></script>',
		'get_category,_admin' =>
			'<script src="/module/base_module/category.js" type="text/javascript"></script>',
	);
	
	//<script type="text/javascript" src="/module/base_module/category.js"></script>
	
	protected $repair_hole = false;
	
	protected $category_posfix = '_category';
	public $has_item = true;
	public $has_category = false;
	protected $output_config = true;
	public $close_nested_folder = 0;
	private $article_not_found_name = "404";//TODO move this to main template/config
	protected $parent_module = array('base_module');
	
	protected $exclude_method_from_link_list = array('save','save_category','move_category');
	
	protected $category_field='id,title,left,right,depth,draft,link';
	protected $item_field='id,title,category_id,link';
	protected $item_single_field='id,title,category_id';
	protected $item_order='order';
	protected $need_path = true;
	public $category_type = 'dropdown';
	
	public $default_thumb = 'template/admin/images/_document.png';
	public $simple_category_style = 0;
}

abstract class base_module extends module{
	protected $config_class_name = 'base_module_config';
	public $id_field = 'id';
	public $category_id_field = 'id';
	//protected $_draft = true;
	protected $_need_message = true;
	protected $_show_module_path = true;
	
	protected $_field = '*';
	protected $_field_admin = '*';
	//protected $_id_type = 'int';//TODO id type
	protected $_draft = true;
	protected $_need_path = true;
	protected $_page_count = 10;
	protected $_message = true;
	
	public function __construct(&$parent=NULL){
		parent::__construct($parent);
		if($this->admin_mode)
			$this->config->set('simple_category_style',0);
		$this->_inherit_language('base_module');
	}
	
	public function _message($name=NULL, $params=array()){
		if($this->_message)
			parent::_message($name, $params);
	}
	
	public function _get_param_values(){
		$values = array();
		$values['edit'] = 1;
		return $values;
	}
	
	public function _admin($id=NULL){
		$this->_get_category($this->category_id_field, $id, true, 'auto');
		//parent::get_category('translit_title', $id, true, 'auto', NULL, array(array('module',$this->module_name), array('internal_type','image')));
		//FIXME wtf it do not load category by title?
	}
	
	public function get_category($id=false, $show='auto'){
		$this->_get_category($this->category_id_field, $id, true, $show);
	}
	
	public function _get_category($field=NULL, $value=false, $need_item=true, $show='auto', $category_condition=array(),$item_condition=array(),$page=NULL,$count=NULL){
		if(!$field)
			$field = $this->category_id_field;
		//TODO pages?
		//$show: all (all categories and subcategories), category (0lvl categories + tree to current category), current (current category content only), auto ('current' for json, 'category' for others)
		if($value==='')
			$value = false;
		if($show=='auto')
			$show = in_array($this->parent->_config('content_type'), array('json','json_html'/*,'xml'*/))?'current':'category';
		if($this->_config('has_category')){
			if(!is_array($bound = $this->get_bound($field,$value)))
				return;
			$this->_query->select($this->_config('category_field'));
			$this->_query->injection(',"'.$this->module_name.'" as _module_name, "'.$this->admin_mode.'" as _admin, "'.$show.'" as _show');//hack for xslt
			switch($show){
				case 'category':{
					$this->get_category_tree($field, $value,$category_condition, $bound);
					$this->add_category_path($bound?$bound['left']:NULL);
					break;
				}
				case 'all':{
					$this->get_category_all($field, $value, $category_condition, $bound);
					break;
				}
				default:{
					$this->get_category_current($field, $value, $category_condition, $bound);
					$this->add_category_path($bound?$bound['left']:NULL);
				}
			}
			//items
			if($need_item)
				$this->get_category_items($show,$item_condition,$value,$bound);
			$this->create_tree();
		}
		elseif($need_item && $this->_config('has_item')){
			//$this->get_category_items($show,$item_condition,$value,$bound);
			$this->_query->select($this->_config('item_field'))->from($this->_table_name);
			if($value!==false){
				$category_id = NULL;
				$bound_query = new object_sql_query($this->parent->db);
				$category_id = $field==$this->category_id_field?$value:
					$bound_query->select($this->category_id_field)->from($this->_category_table_name)->where($field,$value)->query1($this->category_id_field);
				$this->_query->where('category_id',$category_id);
			}
			if($item_order)
				$this->_query->order($item_order);
			$this->_result = $this->_query->limit_page($page,$count)->query();
		}
		//TODO remove this temp code below
		if($this->admin_mode){
			$this->category_list($category_condition);
		}
	}
	
	private function get_category_all($field=false, $value=false, $category_condition=array(), &$bound=array()){
		$where = false;
		if($value!==false && $bound){
			$this->get_category_sql_active($bound);
			if(!$this->admin_mode && $this->_draft){
				$this->_query->where('draft',0);
				$where = true;
			}
			$this->parse_condition($category_condition,true);
			$this->_result = $this->_query->order('left')->query2assoc_array('left',NULL,false);
		}
		else
			$this->get_category_sql_default($category_condition, true);
	}
	
	private function get_category_sql_active($bound){
		$this->_query->injection(', (`left` <= '.$bound['left'].' AND `right`>='.$bound['right'].') as `active`')->from($this->_category_table_name);
	}
	
	private function get_category_sql_default($category_condition=NULL, $query = NULL){
		$where = false;
		$this->_query->from($this->_category_table_name);
		if(!$this->admin_mode && $this->_draft){
			$this->_query->where('draft',0);
			$where = true;
		}
		if($query){
			$this->parse_condition($category_condition,$where);
			$this->_result = $this->_query->order('left')->query2assoc_array('left',NULL,false);
		}
		return $where;
	}
	
	
	private function get_category_current($field=false, $value=false, $category_condition=array(), &$bound=array()){
		$where = $this->get_category_sql_default();
		if($bound)
			$this->_query->clause($where?'AND':'WHERE','left', $bound['left'], '>')->_and('right', $bound['right'],'<')->_and('depth',$bound['depth']+1);
		else
			$this->_query->clause($where?'AND':'WHERE','depth',0);
		$this->parse_condition($category_condition,true);
		$this->_result = $this->_query->order('left')->query2assoc_array('left',NULL,false);
		//TODO limit page query
	}
	
	private function get_category_tree($field=false, $value=false, $category_condition=array(), &$bound=array()){
		$where = false;
		if($value!==false && $bound){
			$this->get_category_sql_active($bound);
			if(!$this->admin_mode && $this->_draft){
				$this->_query->where('draft',0);
				$where = true;
			}
			if($where)
				$this->_query->_and('depth',0,'=','`',true);
			else
				$this->_query->where('depth',0,'=',true);
			if($bound){
				if($bound['depth']){
					$bound_query = new object_sql_query($this->parent->db);
					$bound0 = $bound_query->select('left,right,depth')->from($this->_category_table_name)->where('left',$bound['left'],'<=')->_and('right',$bound['right'],'>=')->query();
					foreach($bound0 as $level)
						if($level['right']==$level['left']+1)
							$this->_query->_or('left',$level['left']);
						else
							$this->_query->_or('left',array($level['left'],$level['right']),'between')->_and('depth',$level['depth']+1,'=');
				}
				else
					$this->_query->_or('left',array($bound['left'],$bound['right']),'between')->_and('depth',1);
			}
			$this->_query->close_bracket();
		}
		else{
			$where = $this->get_category_sql_default();
			if($where)
				$this->_query->_and('depth',0,'=','`',true);
			else{
				$this->_query->where('depth',0,'=',true);
				$where = true;
			}
			$this->_query->close_bracket();
		}
		$this->parse_condition($category_condition,$where);
		//$this->_query->echo_sql = 1;
		$this->_result = $this->_query->order('left')->query2assoc_array('left',NULL,false);
		//$this->_query->echo_sql = 0;
	}
	
	private function get_bound($field=NULL,$value=false){
		$bound = array();
		if($value!==false){
			$bound_select = 'left,right,depth,'.$this->category_id_field;
			$bound_query = new object_sql_query($this->parent->db);
			if($this->module_name=='article'){
				$bound = $bound_query->select($bound_select.',title,article_redirect,'.$this->category_id_field)->from($this->_category_table_name)->where($field,$value)->query1();
				if(!$this->admin_mode && $this->parent->_config('content_type')=='xsl' && !empty($bound['article_redirect'])){
					$this->_query->set_sql();
					$this->add_category_path($bound['left'],$bound['title']);
					$this->config->set('need_path',false);
					$this->get($bound['article_redirect']);
					$this->_result['article_redirect'] = $bound['article_redirect'];
					return;
				}
			}else
				$bound = $bound_query->select($bound_select)->from($this->_category_table_name)->where($field,$value)->query1();
		}
		return $bound;
	}
	
	//$show: all (all categories and subcategories), all_sub (all subcategories), category (0lvl categories + tree to current category), current (current category content only), auto ('current' for json, 'category' for others)
	
	public function get_category_items($show,$item_condition,$value,$bound){
		if($this->_config('has_item')){
			$item_field = $this->_config('item_field');
			if($this->admin_mode && $this->_draft)
				$item_field.= ',draft';
			//',"'.$this->module_name.'" as _module_name, "'.$this->admin_mode.'" as _admin,
			$this->_query->select($item_field)->injection(',"'.$this->module_name.'" as _module_name, "'.$this->admin_mode.'" as _admin')->from($this->_table_name);//temp hack for xsl-tree builder
			$categories = array();
			$null_categories = false;
			if($show!='current' || $show=='all_sub' && !$value)
				$null_categories = true;
			elseif($bound)
				$categories[$bound[$this->category_id_field]] = $bound[$this->category_id_field];
			if($this->_result && $bound){
				foreach($this->_result as $cat_num=>&$category){
					if(!empty($category['active']))
						$categories[$category[$this->category_id_field]] = $cat_num;
					if($category[$this->category_id_field]==$bound[$this->category_id_field]){
						$category['is_current'] = 1;
					}
				}
			}
			$where = false;
			if($categories){
				$this->_query->where('category_id',array_keys($categories),'in',true);
				if($null_categories)
					$this->_query->_or('category_id',NULL);
				$this->_query->close_bracket();
				$where = true;
			}
			elseif($null_categories){
				$this->_query->where('category_id',NULL);
				$where = true;
			}
			//TODO draft check there
			$this->parse_condition($item_condition,$where);
			if($item_order = $this->_config('item_order'))
				$this->_query->order($item_order);
			if($items = $this->_query->query()){
				foreach(array_keys($items) as $item_num)
					if($items[$item_num]['category_id'] && isset($this->_result[$num = $categories[$items[$item_num]['category_id']] ])){
						$this->_result[$num]['items'][] = $items[$item_num];
						unset($items[$item_num]);
					}
				if($items){
					$this->_result['items'] = $items;
				}
			}
		}
	}
	
	public function category_list($category_condition=NULL){
		$this->_query->select('left,title,depth,'.$this->category_id_field)->from($this->_category_table_name);
		if($this->_draft)
			$this->_query->where('draft',1,'!=');
		$this->parse_condition($category_condition,true);
		$this->_result['_category_list'] = $this->_query->order('left')->query();
	}
	
	private function create_tree(&$parent=NULL, &$prev=NULL){
		if(!$this->_result)
			return;
		$prev = NULL;
		$parents = array();
		$result = array();
		foreach($this->_result as $key=>&$curr){
			if($key=='items')
				$result['items'] = &$curr;
			else{
				if($prev){
					if($curr['depth'] > $prev['depth']){
						$prev[$key] = &$curr;
						$parents[] = &$prev;
						$prev = &$prev[$key];
					}
					else{
						if($curr['depth'] != $prev['depth']){
							for($i=($prev['depth']-$curr['depth']);$i>0; $i--)
								array_pop($parents);
						}
						$parent_key_last = end(array_keys($parents));
						if($parent_key_last!==false && $parent = &$parents[$parent_key_last]){
							$parent[$key] = &$curr;
							$prev = &$parent[$key];
						}
						else{
							$result[$key] = &$curr;
							$prev = &$result[$key];
						}
					}
				}
				else{
					$result[$key] = &$curr;
					$prev = &$result[$key];
				}
			}
		}
		$this->_result = &$result;
	}
	
	private function parse_condition(&$condition,$where=false){
		if(!$condition)
			return;
		if(!is_array($condition))
			throw new my_exception('condition is not an array',array('condition'=>$condition));
		if(!is_array($condition[0]))
			$condition = array($condition);
		foreach($condition as $cond){
			if(empty($cond[2]))
				$cond[2] = '=';
			if(empty($cond[0]) || !isset($cond[1]))
				throw new my_exception('condition field and value must be set');
			if($where)
				$this->_query->_and($cond[0],$cond[1],$cond[2]);
			else{
				$this->_query->where($cond[0],$cond[1],$cond[2]);
				$where = true;
			}
		}
	}
	
	public function get($id=NULL, $select=NULL){
		$this->_get(NULL, $id, $select);
	}
	
	public function _get($field=NULL, $value=NULL, $select=NULL, $condition=array()){
		if(!$select)
			$select = $this->_config('item_single_field');
		if(!$field)
			$field = $this->id_field;
		$this->_result = $this->_query->select($select)->from($this->_table_name);
		$where = false;
		//TODO draft check there
		if($field){
			$this->_query->where($field,$value);
			$where = true;
		}
		if(!$this->admin_mode && $this->_draft){
			if($where)
				$this->_query->_and('draft','1','!=');
			else
				$this->_query->where('draft','1','!=');
		}
		$this->parse_condition($condition,$where);
		$this->_result = $this->_query->query1();
		if(!$this->_result)
			$this->_message('object not found',array('id'=>$value));
		$this->add_item_path();
		if($this->position==$this->parent->_config('main_position_name') && !empty($this->_result['title']))
			$this->_title = $this->_result['title'];
	}
	
	protected function add_item_path(){
		if($this->_config('need_path')){
			if($this->_show_module_path)
				$this->parent->add_module_path();
			else
				$this->parent->check_default_path();
			if(!empty($this->_result['category_id'])){
				$left = $this->_query->select('left')->from($this->_category_table_name)->where($this->category_id_field,$this->_result['category_id'])->query1('left');
				if($left && (empty($this->_result['draft']) || $this->_result['draft']=='0') && $this->_config('has_category'))
					$this->get_path($left);
			}
			if(!empty($this->_result['title']))
				$this->parent->add_path($this->_result['title']);
		}
	}
	
	protected function add_category_path($left=NULL,$title=NULL,$path_from_result=true){
		if($this->_config('need_path')){
			if($this->_show_module_path)
				$this->parent->add_module_path();
			else
				$this->parent->check_default_path();
			if($left){
				if($title){
					$this->get_path($left);
					$this->parent->add_path($this->_result['title']);
				}
				else
					$this->get_path($left,$path_from_result);
			}
		}
	}
	
	protected function get_path($left=NULL,$path_from_result=NULL){
		if($left && !in_array($this->parent->_config('content_type'), array('json','json_html')) && (empty($this->_result['draft']) || $this->_result['draft']=='0' || $this->admin_mode) && $this->_config('has_category')){
			$admin_mode = $this->admin_mode?'admin.php':'';
			$method = $admin_mode?$this->_config('admin_method'):'get_category';
			$this->module_path_count = $this->parent->get_path_count()-1;
			if($path_from_result){
				foreach($this->_result as $name=>&$value)
					if($value['left']<=$left && $value['right']>$left){
						if(!($this->admin_mode || empty($match['draft'])))
							$this->parent->unset_path($this->module_path_count);
						$this->parent->add_path(array(
							'title'=>$value['title'],
							'href'=>'/'.$admin_mode.'?call='.$this->module_name.'.'.$method.'&title='.$value[$this->category_id_field]
						));
					}
			}
			else{
				$matches = $this->_query->select('title,draft,'.$this->category_id_field)->from($this->_category_table_name)->where('left',$left,'<=')->_and('right',$left,'>=')->order('left')->query();
				//TODO href
				foreach($matches as &$match){
					if(!($this->admin_mode || empty($match['draft'])))
						$this->parent->unset_path($this->module_path_count);
					$this->parent->add_path(array('title'=>$match['title'],'href'=>'/'.$admin_mode.'?call='.$this->module_name.'.'.$method.'&title='.$match[$this->category_id_field]));
				}
			}
		}
	}
	
	public function edit($id=NULL, $category_id=NULL, $select='*'){
		if(!empty($_SESSION['_user_input'][$this->module_name])){
			//_user_input - save user input values when error
			$this->_result = $_SESSION['_user_input'][$this->module_name];
			unset($_SESSION['_user_input'][$this->module_name]);
		}
		elseif($id){
			$this->_result = $this->_query->select($select)->from($this->_table_name)->where($this->id_field,$id)->query1();
			if(!$this->_result)
				throw new my_exception('object not found', array($this->id_field=>$id));
			$this->add_category_path($this->_result['category_id'],$this->_result['title']);
		}
		$this->category_list();
		if(!isset($this->_result['create_date'])){
			$date = new DateTime();
			$this->_result['create_date'] = $date->format('Y-m-d H:i:s');
		}
	}
	
	public function save($id=NULL, $value = array()){
		$this->_save($id,$value);
	}
	
	public function _save($id=NULL, $value = array(), $redirect = 'edit', $redirect_params=array()){
		$this->check_title($value);
		$params = array('title'=>$value['title']);
		if($id){
			$this->_query->update($this->_table_name)->set($value)->where($this->id_field,$id)->query1();
			if($this->_need_message)
				$this->_message('object edited successfully',$params);
		}
		else{
			$this->get_unique_title($value, $id);
			if(empty($value['order']))
				$value['order'] = $this->get_order(isset($value['category_id'])?$value['category_id']:NULL);
			$this->_query->insert($this->_table_name)->values($value)->execute();
			if($this->_need_message)
				$this->_message('object added successfully',$params);
		}
		$redirect_params['id'] = $value[$this->category_id_field];
		$this->redirect($redirect_params, $redirect);
	}
	
	private function &get_order($category=NULL){
		$order = $this->_query->select(NULL)->max('order','o')->from($this->_table_name)->where('category_id',$category)->query1('o',false) +1;
		return $order;
	}
	
	public function remove($id=NULL,$redirect=false,$redirect_params = array()){
		if($id){
			$data = $this->_query->select('title,category_id')->from($this->_table_name)->where($this->id_field,$id)->query1();
			$this->_query->delete()->from($this->_table_name)->where($this->id_field,$id)->query1();
			if($this->_need_message)
				$this->_message('object deleted successfully',array('title'=>$data['title']));
			if($data['category_id'])
				$redirect_params['id'] = $data['category_id'];
		}
		else
			$this->_message('object id is empty');
		$this->redirect($redirect_params,$redirect);
	}
	
	protected function redirect($redirect_params,$redirect=false){
		if($redirect === false)
			$redirect = $this->_config('admin_method');
		elseif(!$redirect)
			$return;
		$this->parent->redirect('/'.($this->admin_mode?'admin.php':'').'?call='.$this->module_name.'.'.$redirect,$redirect_params);
	}
	
	public function edit_category($id=NULL, $insert_place=NULL){
		if(!empty($_SESSION['_user_input'][$this->module_name])){
			//_user_input - save user input values when error
			$this->_result = $_SESSION['_user_input'][$this->module_name];
			unset($_SESSION['_user_input'][$this->module_name]);
		}
		elseif($id){
			$this->_result = $this->_query->select()->from($this->_category_table_name)->where($this->category_id_field,$id)->query1();
			if($this->_config('need_path'))
				$this->parent->add_method_path($this->_result['title']);
		}
	}
	
	public function save_category($id=NULL,$title=NULL,$insert_place=NULL,$draft=NULL,$new_id=NULL){
		$this->_save_category($id,
			array('title'=>$title,'draft'=>$draft,'id'=>$new_id),
			$insert_place
		);
	}
	
	public function check_title(&$value,$category=false){
		if(empty($value['title'])){
			$this->_message('title must not be empty');
			if(!isset($_SESSION['_user_input']))
				$_SESSION['_user_input'] = array();
			$_SESSION['_user_input'][$this->module_name] = $value;
			$redirect_params = array();
			if(!empty($value['id']))
				$redirect_params['id'] = $value['id'];
			$this->redirect($redirect_params, 'edit'.$category?'_category':'');
		}
	}

	public function _save_category($id=NULL,$value=array(),$insert_place=NULL,$condition = array(),$redirect = false,$redirect_params = array()){
		$this->check_title($value,true);
		if(isset($value['draft']))
			$value['draft'] = (int)$value['draft'];
		if(empty($value[$this->category_id_field]) || !$value[$this->category_id_field])
			$this->get_unique_title($value, $id, true);
		if($id){
			if(!$item = $this->_query->select('title,left')->from($this->_category_table_name)->where($this->category_id_field,$id)->query1())
				new my_exception('category not found',array($this->category_id_field=>$id));
			$this->_query->update($this->_category_table_name)->set($value)->where($this->category_id_field,$id)->query1();
			if($this->_need_message)
				$this->_message('category edited successfully',array('title'=>$item['title']));
			if($parent_title = $this->_query->select($this->category_id_field)->from($this->_category_table_name)->where('left',$item['left'],'<')->_and('right',$item['left'],'>')->order('left desc')->query1($this->category_id_field))
				$redirect_params['id'] = $parent_title;
		}
		else{
			if($insert_place){
				$destination = $this->_query->select('right,depth,'.$this->category_id_field)->from($this->_category_table_name)->where($this->category_id_field,$insert_place)->query1();
				if(!$destination)
					throw new my_exception('parent not found',array($this->category_id_field=>$insert_place));
				$redirect_params['id'] = $destination[$this->category_id_field];
				$value['left'] = $destination['right'];
				$this->_query->lock($this->_category_table_name)->execute();
				$this->_query->update($this->_category_table_name)->increment('right',2)->where('right',$value['left'],'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();
				if($this->_config('has_item'))
					$this->_query->update($this->_category_table_name)->increment('category_count')->where($this->category_id_field,$insert_place)->query1();
				$this->_query->update($this->_category_table_name)->increment('left',2)->where('left',$value['left'],'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();
				$this->_query->unlock()->execute();
				$value['depth'] = $destination['depth']+1;
			}
			else{
				$this->_query->select(NULL)->max('right','right')->from($this->_category_table_name);
				$this->parse_condition($condition);
				$value['left'] = $this->_query->query1('right')+1;
			}
			$value['right'] = $value['left'] + 1;
			$this->_query->insert($this->_category_table_name)->values($value)->execute();
			if($this->_need_message)
				$this->_message('category added successfully',array('title'=>$value['title'])); 
		}
		$this->redirect($redirect_params,$redirect);
	}

	public function move_category($id=NULL, $insert_type=NULL, $insert_place=NULL, $condition = array(),$redirect = false, $redirect_params = array()){
		if(!$id)
			throw new my_exception('can not move by empty id');
		if(!$insert_type)
			throw new my_exception('insert_type not found');
		elseif(!in_array($insert_type, array('before', 'inside')))
			throw new my_exception('wrong insert type', array('type'=>$insert_type));
		if(!$target = $this->_query->select('left,right,depth,title')->from($this->_category_table_name)->where($this->category_id_field,$id)->query1())
			throw new my_exception('not found category to move',array($this->category_id_field=>$id));
		if($id==$insert_place){
			$this->_message('can not move category into itselve');
			$redirect_params['id'] = $id;
		}
		else{
			$width = $target['right'] - $target['left'] + 1;
			$this->_query->lock($this->_category_table_name)->execute();
			if($insert_place=='last'){
				$this->_query->select(NULL)->max('right','right')->from($this->_category_table_name);
				$this->parse_condition($condition);
				$place = $this->_query->query1('right')+1;
				$difference =  $place - $target['left'];
				$difference_depth = -1*$target['depth'];
				$new_width = $width;
				$target['left'] = $target['left']-$width;
				$target['right'] = $target['right']-$width;
			}
			else{
				$destination = $this->_query->select('left,right,depth,'.$this->category_id_field)->from($this->_category_table_name)->where($this->category_id_field,$insert_place)->query1();
				if(!$destination){
					$this->_query->unlock();
					throw new my_exception('insert place not found',array($this->category_id_field=>$insert_place));
				}
				$redirect_params['id'] = $destination[$this->category_id_field];
				if($destination['left'] > $target['left'] && $destination['left'] < $target['right'])
					$this->_message('can not move category into itselve');
				else{
					//prepare place to put target in
					$place = $insert_type=='before' ? $destination['left'] : $destination['right'];
					$this->_query->update($this->_category_table_name)->increment('left',$width)->
						where('left',$place,'>=');
					$this->parse_condition($condition,true);
					$this->_query->execute();
					$this->_query->update($this->_category_table_name)->increment('right',$width)->
						where('right',$place,'>=');
					$this->parse_condition($condition,true);
					$this->_query->execute();
					//move target
					$new_width = ($destination['left']<$target['left'])?$width:0;
					$difference = $place - $target['left'] - $new_width;
					if($insert_type=='before')
						$difference_depth = $destination['depth'] - $target['depth'];
					else
						$difference_depth = $destination['depth'] - $target['depth'] + 1;
				}
			}
			$this->_query->update($this->_category_table_name)->
				increment('left',$difference,true,false)->increment('right',$difference,false,!$difference_depth);
			if($difference_depth)
				$this->_query->increment('depth',$difference_depth,false);
			$this->_query->where('left',array($target['left']+$new_width,$target['right']+$new_width),'between');
			$this->parse_condition($condition,true);
			$this->_query->execute();
			if($this->_config('repair_hole')){
				$this->_query->update($this->_category_table_name)->
					decrement('left',$width)->
					where('left',$target['left']+$width,'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();
				$this->_query->update($this->_category_table_name)->
					decrement('right',$width)->
					where('right',$target['left']+$width,'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();;
			}
			$this->_query->unlock()->execute();
			if($this->_need_message)
				$this->_message('category moved successfully',array('title'=>$target['title']));
		}
		$this->redirect($redirect_params,$redirect);
	}

	public function remove_category($id=NULL,$condition = array(), $redirect = false, $redirect_params = array()){
		if(!$id)
			throw new my_exception('category id is empty');
		$target = $this->_query->select('left,right,title')->from($this->_category_table_name)->where($this->category_id_field,$id)->query1();
		if(!$target)
			throw new my_exception('category not found',array($this->category_id_field=>$id));
		if($parent_title = $this->_query->select($this->category_id_field)->from($this->_category_table_name)->where('left',$target['left'],'<')->_and('right',$target['left'],'>')->order('left desc')->query1($this->category_id_field) )
			$redirect_params['id'] = $parent_title;
		if($this->_config('has_item')){
			$category = $this->_query->select($this->category_id_field)->from($this->_category_table_name)->where('left',array($target['left'],$target['right']),'between')->query1($this->category_id_field);
			$this->_query->delete()->from($this->_category_table_name)->where($this->category_id_field,$category,'in')->query();
			$this->_query->delete()->from($this->_table_name)->where('category_id',$category,'in')->query();
		}
		else
			$this->_query->delete()->from($this->_category_table_name)->where('left',array($target['left'],$target['right']),'between')->query();
		if($this->_config('repair_hole')){
			$width = $target['right'] - $target['left'] + 1;
			$this->_query->update($this->_category_table_name)->injection(' SET `left`=`left`-'.$width)->where('left',$target['left'],'>=');
			$this->parse_condition($condition,true);
			$this->_query->execute();
			$this->_query->update($this->_category_table_name)->injection(' SET `right`=`right`-'.$width)->where('right',$target['right'],'>=');
			$this->parse_condition($condition,true);
			$this->_query->execute();
		}
		$this->_query->unlock()->execute();
		if($this->_need_message)
			$this->_message('category deleted successfully',array('title'=>$target['title']));
		$this->redirect($redirect_params, $redirect);
	}
	
	public function unlock_database(){
		$this->_query->unlock()->execute();
		$this->_message('database unlocked');
	}
	
	public function move_item($id=NULL, $insert_after=NULL, $insert_category=NULL, $insert_item=NULL, $redirect=false, $redirect_params = array()){
		//echo "id:'$id' insert_after:'$insert_after' insert_category:'$insert_category' insert_item:'$insert_item'";die;
		if($id){
			$match = $this->_query->select('id,category_id,title')->from($this->_table_name)->where($this->id_field,$id)->query1();
			if(!$match)
				throw new my_exception('object not found',array($this->id_field=>$id));
			if($insert_after!=$insert_item){
				$place = NULL;
				if($insert_item)
					$place = $this->_query->select('order,category_id')->from($this->_table_name)->where($this->id_field,$insert_item)->query1();
				if(!$place)
					$place = array('order'=>0, 'category_id'=>$match['category_id']);
				$this->_query->update($this->_table_name)->increment('order')->where('order',$place['order'],'>')->_and('category_id',$place['category_id'])->query();
				$this->_query->update($this->_table_name)->set(array('order'=>$place['order']+1))->where($this->id_field,$id)->query1();
				$order = $place['order'];
				if($place['category_id'])
					$redirect_params['id'] = $place['category_id'];
			}else{
				if(!$insert_category)
					$insert_category = NULL;
				$order = $this->get_order($insert_category);
				$this->_query->update($this->_table_name)->set(array('category_id'=>$insert_category, 'order'=>$order))->where($this->id_field,$id)->query1();
				if($insert_category)
					$redirect_params['id'] = $insert_category;
			}
			if($this->_need_message)
				$this->_message('object moved successfully',array('title'=>$match['title']));
		}
		else
			throw new my_exception('can not move by empty id');
		$this->redirect($redirect_params,$redirect);
	}
	
	public function backtrace(){
		$backtrace = debug_backtrace();
		foreach($backtrace as $item){
			foreach(array_keys($item['args']) as $key)
				if("object"==gettype($item['args'][$key]))
					unset($item['args'][$key]);
			$name = '';
			if(isset($item['class']))
				$name.=$item['class'];
			if(isset($item['class']))
				$name.=$item['type'];
			if(isset($item['class']))
				$name.=$item['function'];
			var_dump($name, $item['args']);
		}
		die;
	}
	
	public function set_translit_title($category=NULL){
		if(!$category){
			$table = $this->_table_name;
			$column = $this->id_field;
		}
		else{
			$table = $this->_category_table_name;
			$column = $this->category_id_field;
		}
		$category = $this->_query->select('title,id')->from($table)->query();
		foreach($category as &$item){
			$this->get_unique_title($item,NULL,$category);
			$this->_query->update($table)->set(array('title'=>$item['title'], 'id'=>$item['id']))->where('id',$item['id'])->query1();
		}
		$this->_message('table ids updated',array('table'=>$table));
	}
	
	public function set_translit_title_category(){
		$this->set_translit_title($this->_category_table_name);
	}
	
	private function get_unique_title(&$value=array(), $id=NULL, $category = false){
		if(!empty($value['id'])){
			if($id!=$value['id'])
				$value['id'] = $this->check_unique_title($value['id'], $category);
		}
		else{
			if(!$id){
				if(!empty($value['title']))
					$value['id'] = $this->check_unique_title(translit::transliterate($value['title']), $category);
				else
					$value['id'] = $this->check_unique_title(NULL, $category);
			}	
		}
		//TODO trigger
	}
	
	public function mb_strrev(&$text, $encoding = null)
	{
		$funcParams = array($text);
		if ($encoding !== null)
			$funcParams[] = $encoding;
		$length = call_user_func_array('mb_strlen', $funcParams);
		$output = '';
		$funcParams = array($text, $length, 1);
		if ($encoding !== null)
			$funcParams[] = $encoding;
		while ($funcParams[1]--) {
			$output .= call_user_func_array('mb_substr', $funcParams);
		}
		return $output;
	}
	
	private function check_unique_title($value = NULL, $category = false){
		if(!$value)
			$value = 1;
		if($count = $this->_query->injection('SELECT count(*) as c')->
			from($category?$this->_category_table_name:$this->_table_name)->
			where($category?$this->category_id_field:$this->id_field,$value.'%','like')->
			query1('c',false)
		)
			$value.=$count;
		return $value;
	}
	
	public function _get_param_value($method_name,$param_name){
		switch($method_name){
			case 'get':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('title,'.$this->id_field)->from($this->_table_name)->query2assoc_array($this->id_field,'title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get_category':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('title,'.$this->category_id_field)->from($this->_category_table_name)->query2assoc_array($this->category_id_field,'title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select($this->id_field.',title')->from($this->_table_name)->query2assoc_array($this->id_field,'title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'remove':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select($this->id_field.',title')->from($this->_table_name)->query2assoc_array($this->id_field,'title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit_category':{
				switch($param_name){
					case 'id':
					case 'insert_place':{
						$this->_result = $this->_query->select($this->category_id_field.',title')->from($this->_category_table_name)->query2assoc_array($this->id_field,'title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'remove_category':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select($this->category_id_field.',title')->from($this->_category_table_name)->query2assoc_array($this->category_id_field,'title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			default: parent::_get_param_value($method_name,$param_name);
		}
	}
}
?>