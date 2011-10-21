<?php
class base_module_config extends module_config{
	protected $callable_method=array(
		//TODO check array merge works properly for child modules
		'get,get_category,get_category_by_title'=>array(
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
			'<link href="module/base_module/admin.css" rel="stylesheet" type="text/css"/>',
		'*,admin_mode.*' =>
			'<link href="module/base_module/base_module.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="/module/base_module/base_module.js"></script>',
		'get_category,_admin' =>
			'<script type="text/javascript" src="/module/base_module/category.js"></script>',
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
	
	protected $category_field='id,title,translit_title,left,right,depth';
	protected $item_field='id,title,category_id';
	protected $item_order='order';
	public $category_type = 'dropdown';
	
	public $default_thumb = 'template/admin/images/_document.png';
}

abstract class base_module extends module{
	protected $config_class_name = 'base_module_config';
	
	public function __construct(&$parent=NULL){
		parent::__construct($parent);
		$this->_inherit_language('base_module');
	}
	
	public function _get_param_values(){
		$values = array();
		$values['edit'] = 1;
		return $values;
	}
	
	public function _admin($title=NULL){
		$this->get_category($title);
		//parent::get_category('translit_title', $title, true, 'auto', NULL, array(array('module',$this->module_name), array('internal_type','image')));
		//FIXME wtf it do not lod category by title?
	}
	
	public function get_category($title=false, $show='auto'){
		$this->get_category_base('translit_title', $title, true, $show);
	}
	
	public function get_category_base($field = 'translit_title', $value=false, $need_item=true, $show='auto', $category_condition=array(),$item_condition=array()){
		//$this->_query->echo_sql=1;
		//TODO pages?
		//$show: all (all categories and subcategories), category (0lvl categories + tree to current category), current (current category content only), auto ('current' for json, 'category' for others)
		if($value=='')
			$value = false;
		if($show=='auto')
			$show = in_array($this->parent->_config('content_type'), array('json','json_html'/*,'xml'*/))?'current':'category';
		$item_field = $this->_config('item_field');
		$category_field = $this->_config('category_field');
		$item_order = $this->_config('item_order');
		$category_table = $this->_category_table_name;
		if($this->_config('has_category')){
			$this->_query->select($category_field);
			if($value!==false){
				$bound_query = new object_sql_query($this->parent->db);
				$bound = $bound_query->select('left,right,id,depth')->from($category_table)->where($field,$value)->query1();
			}
			else
				$bound = array();
			if($show=='category' || $show=='all'){
				if($value!==false && $bound){
					$this->_query->injection(', (`left` <= '.$bound['left'].' AND `right`>='.$bound['right'].') as `active`')->from($category_table);
					if($show=='category'){
						$this->_query->where('depth',0,'=',true)->_or('left',array($bound['left'], $bound['right']),'between')->_and('depth',$bound['depth']+2,'<');
						if($bound['depth']!=0)
							$this->_query->_or('left',$bound['left'],'<')->_and('right',$bound['left'],'>');
						$this->_query->close_bracket();
					}
				}
				else{
					$this->_query->from($category_table);
					if($show=='category'){
						$this->_query->where('depth',0,'=',true);
						if($value!==false && $bound)
							$this->_query->_or('left', $bound, 'between');
						$this->_query->close_bracket();
					}
				}
			}
			elseif($show=='current'){
				$this->_query->from($category_table);
				if($bound)
					$this->_query->where('left', $bound['left'], '>')->_and('right', $bound['right'],'<');
			}
			if($bound || $show!='current'){
				$this->parse_condition($category_condition,false);
				$this->_result = $this->_query->order('left')->query2assoc_array('id',NULL,false);
			}
			else{
				$this->_query->set_sql();
				$this->_result = array();
			}
			//items
			if($need_item && $this->_config('has_item')){
				$this->_query->select($item_field)->from($this->_table_name);
				$categories = array();
				$null_categories = false;
				if($show!='current')
					$null_categories = true;
				elseif($value)
					$categories[] = $bound['id'];
				if($this->_result){
					if($bound)
 						foreach($this->_result as &$category){
							if(!empty($category['active']))
								$categories[] = $category['id'];
							if($category['id']==$bound['id']){
								/*if($show=='current')
									$category['uncategorized'] = 1;
								else*/
									$category['is_current'] = 1;
							}
						}
				}
				$where = false;
				if($categories){
					$this->_query->where('category_id',$categories,'in',true);
					if($null_categories)
						$this->_query->_or('category_id',NULL);
					$this->_query->close_bracket();
					$where = true;
				}
				elseif($null_categories){
					$this->_query->where('category_id',NULL);
					$where = true;
				}
				$this->parse_condition($item_condition,$where);
				if($item_order)
					$this->_query->order($item_order);
				if($items = $this->_query->query()){
					foreach(array_keys($items) as $item_num)
						if($items[$item_num]['category_id'] && isset($this->_result[$items[$item_num]['category_id']])){
							$this->_result[$items[$item_num]['category_id']]['items'][] = $items[$item_num];
							unset($items[$item_num]);
						}
					if($items){
						$this->_result['items'] = $items;
					}
				}
			}
			$this->create_tree();
		}
		elseif($need_item && $this->_config('has_item')){
			$this->_query->select($item_field)->from($this->_table_name);
			if($value!==false){
				$category_id = NULL;
				if($field!='id'){
					$bound_query = new object_sql_query($this->parent->db);
					$category_id = $bound_query->select('id')->from($category_table)->where($field,$value)->query1('id');
				}
				$this->_query->where('category_id',$category_id);
			}
			if($item_order)
				$this->_query->order($item_order);
			$this->_result = $this->limit_page($page,$count)->_query->query();
		}
		//TODO remove this temp code below
		//var_dump($_SERVER['REQUEST_URI'],$this->parent->admin_mode); die;
		if($this->parent->admin_mode){
			$this->category_list($category_condition);
		}
	}
	
	public function category_list($category_table=NULL, $category_condition=NULL){
		if(!$category_table)
			$category_table = ($category_table = $this->_config('category_table'))?$category_table:($this->_table_name.'_category');
		$this->_query->select('id,title,translit_title,depth')->from($category_table);
		$this->parse_condition($category_condition,false);
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
						//echo "$key.{$curr['title']} - <b>1</b> - {$prev['title']};<br/>";
						$prev[$key] = &$curr;
						$parents[] = &$prev;
						$prev = &$prev[$key];
					}
					else{
						if($curr['depth'] != $prev['depth']){
							for($i=($prev['depth']-$curr['depth']);$i>0; $i--)
								array_pop($parents);
							//echo "$key.{$curr['title']} - <b>3";
						}
						//else
							//echo "$key.{$curr['title']} - <b>2";
						$parent_key_last = end(array_keys($parents));
						if($parent_key_last!==false && $parent = &$parents[$parent_key_last]){
							//echo ".1</b> - {$prev['title']};<br/>";
							$parent[$key] = &$curr;
							$prev = &$parent[$key];
						}
						else{
							//echo ".2</b> - {$prev['title']};<br/>";
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
	
	public function get_category_by_title($title){
		$this->get_category('translit_title', $title);
	}
	
	private function parse_condition(&$condition,$where=false){
		//$condition = array( array('field1','value1'),  array('field2','value2','!=') );
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
	
	public function get($field = NULL, $value=NULL,$select = '*',$condition=array()){

		$this->_result = $this->_query->select($select)->from($this->_table_name);
		if($field){
			$this->_query->where($field,$value);
			$where = true;
		}
		else
			$where = false;
		$this->parse_condition($condition,$where);
		$this->_result = $this->_query->query1();
		if(!$this->_result)
			$this->_message('object not found',array('name'=>$value));
	}
	
	public function edit($id=NULL, $category_id=NULL, $select='*'){
		if($id){
			$result = $this->_query->select($select)->from($this->_table_name)->where('id',$id)->query1();
			if(!$result)
				throw new my_exception('object not found by id', array('id'=>$id));
			$this->_result = $result;
		}
		$this->category_list();
		if(!isset($this->_result['create_date'])){
			$date = new DateTime();
			$this->_result['create_date'] = $date->format('Y-m-d H:i:s');
		}
	}
	
	public function save($id=NULL, $value = array(), $redirect = 'edit', $output_message = true, $params=array()){
		//TODO unique translit_title
		if($id){
			$this->_query->update($this->_table_name)->set($value)->where('id',$id)->limit(1)->execute();
			if($output_message)
				$this->_message('edited seccessfully',$params);
		}
		else{
			$this->_query->insert($this->_table_name)->values($value)->execute();
			$id = $this->parent->db->insert_id();
			if($output_message)
				$this->_message('added seccessfully',$params);
		}
		//$this->edit($id);
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect, array('id'=>$id));
	}
	
	public function remove($id=NULL,$redirect='_admin',$message=true,$param = array()){
		$redirect_params = array();
		if($id){
			$data = $this->_query->select('title,category_id')->from($this->_table_name)->where('id',$id)->query1();
			$this->_query->delete()->from($this->_table_name)->where('id',$id)->query1();
			if($message)
				$this->_message('deleted seccessfully',array('title'=>$data['title']));
			if($data['category_id'])
				$redirect_params['title'] = $this->_query->select('translit_title')->from($this->_category_table_name)->where('id',$data['category_id'])->query1('translit_title');
		}
		else
			$this->_message('id is empty');
		$this->parent->redirect('/'.($this->parent->admin_mode?'admin.php':'').'?call='.$this->module_name.($this->parent->admin_mode?'._admin':''),$redirect_params);
	}
	
	public function edit_category($id=NULL, $insert_place=NULL){
		if($id)
			$this->_result = $this->_query->select()->from($this->_table_name.$this->_config('category_posfix'))->where('id',$id)->query1();
		$this->_result['lt'] = '<';
		$this->_result['gt'] = '>';
	}

	public function save_category($id=NULL,$value=array(),$insert_place=NULL,$condition = array(),$redirect = '_admin'){
		$table_name = $this->_table_name.$this->_config('category_posfix');
		if($id){
			$title = $this->_query->select('title')->from($table_name)->where('id',$id)->query1('title');
			$this->_query->update($table_name)->set($value)->where('id',$id)->limit(1)->execute();
			$this->_message('category edited successfully',array('title'=>$title));
		}
		else{
			if($insert_place){
				$destination = $this->_query->select('right,depth')->from($table_name)->where('id',$insert_place)->query1();
				if(!$destination)
					throw new my_exception('parent not found',array('id'=>$insert_place));
				$value['left'] = $destination['right'];
				$this->_query->lock($table_name)->execute();
				$this->_query->update($table_name)->injection(' SET `right`=`right`+2')->where('right',$value['left'],'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();
				$this->_query->update($table_name)->injection(' SET `left`=`left`+2')->where('left',$value['left'],'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();
				$this->_query->unlock()->execute();
				$value['depth'] = $destination['depth']+1;
			}
			else{
				$this->_query->injection('SELECT max(`right`) as `right`')->from($table_name);
				$this->parse_condition($condition);
				$value['left'] = $this->_query->query1('right')+1;
			}
			$value['right'] = $value['left'] + 1;
			$this->_query->insert($table_name)->values($value)->execute();
			$id = $this->parent->db->insert_id();
			$this->_message('new category add',array('title'=>$value['title']));
		}
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect);
	}

	public function move_category($id=NULL, $insert_type=NULL, $insert_place=NULL, $condition = array(),$redirect = '_admin'){
		$table_name = $this->_table_name.$this->_config('category_posfix');
		if(!$id)
			throw new my_exception('id is empty');
		if(!$insert_type)
			throw new my_exception('insert_type not found');
		elseif(!in_array($insert_type, array('before', 'inside')))
			throw new my_exception('wrong insert type', array('type'=>$insert_type));
		$target = $this->_query->select('left,right,depth,title')->from($table_name)->where('id',$id)->query1();
		if(!$target)
			throw new my_exception('not found category to movу',array('id'=>$id));
		elseif($id==$insert_place)
			throw new my_exception('can not paste category into itselve',array('id'=>$id));
		$width = $target['right'] - $target['left'] + 1;
		$this->_query->lock($table_name)->execute();
		$redirect_params = array();
		if($insert_place=='last'){
			$this->_query->injection('SELECT max(`right`) as `right`')->from($table_name);
			$this->parse_condition($condition);
			$place = $this->_query->query1('right')+1;
			$difference =  $place - $target['left'];
			$difference_depth = -1*$target['depth'];
			$new_width = $width;
			$target['left'] = $target['left']-$width;
			$target['right'] = $target['right']-$width;
		}
		else{
			$destination = $this->_query->select('left,right,depth,translit_title')->from($table_name)->where('id',$insert_place)->query1();
			if(!$destination)
				throw new my_exception('insert place not found',array('id'=>$insert_place));
			$redirect_params['title'] = $destination['translit_title'];
			if($destination['left'] > $target['left'] && $destination['left'] < $target['right'])
				$this->_message('may not to move category into itselve');
			else{
				//prepare place to put target in
				$place = $insert_type=='before' ? $destination['left'] : $destination['right'];
				$this->_query->update($table_name)->injection(' SET `left`=`left`+'.$width)->
					where('left',$place,'>=');
				$this->parse_condition($condition,true);
				$this->_query->execute();
				$this->_query->update($table_name)->injection(' SET `right`=`right`+'.$width)->
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
		$this->_query->update($table_name)->
			injection(' SET `left`=`left`+'.$difference.', `right`=`right`+'.$difference.', `depth`=`depth`+'.$difference_depth)->
			where('left',array($target['left']+$new_width,$target['right']+$new_width),'between');
		$this->parse_condition($condition,true);
		$this->_query->execute();
		if($this->_config('repair_hole')){
			$this->_query->update($table_name)->
				injection(' SET `left`= `left`-'.$width)->
				where('left',$target['left']+$width,'>=');
			$this->parse_condition($condition,true);
			$this->_query->execute();
			$this->_query->update($table_name)->
				injection(' SET `right`=`right`-'.$width)->
				where('right',$target['left']+$width,'>=');
			$this->parse_condition($condition,true);
			$this->_query->execute();;
		}
		$this->_query->unlock()->execute();
		$this->_message('category moved successfully',array('title'=>$target['title']));
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect, $redirect_params);
	}

	public function remove_category($id=NULL,$condition = array(), $redirect = '_admin'){
		$table_name = $this->_table_name.$this->_config('category_posfix');
		if(!$id)
			throw new my_exception('id is empty');
		$target = $this->_query->select('left,right,title')->from($table_name)->where('id',$id)->query1();
		if(!$target)
			throw new my_exception('category not found by id',array('id'=>$id));
		$this->_query->lock($table_name)->execute();
		$this->_query->delete()->from($table_name)->where('left',array($target['left'],$target['right']),'between')->query();
		if($this->_config('repair_hole')){
			$width = $target['right'] - $target['left'] + 1;
			$this->_query->update($table_name)->injection(' SET `left`=`left`-'.$width)->where('left',$target['left'],'>=');
			$this->parse_condition($condition,true);
			$this->_query->execute();
			$this->_query->update($table_name)->injection(' SET `right`=`right`-'.$width)->where('right',$target['right'],'>=');
			$this->parse_condition($condition,true);
			$this->_query->execute();
		}
		$this->_query->unlock()->execute();
		$this->_message('category deleted successfully',array('name'=>$target['title']));
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect);
	}
	
	public function unlock_database(){
		$this->_query->unlock()->execute();
		$this->_message('database unlocked');
	}
	
	public function move_item($id=NULL, $insert_after=NULL, $insert_category=NULL, $insert_item=NULL, $redirect='get_category'){
		$redirect_params = array();
		if($id){
			$match = $this->_query->select()->from($this->_table_name)->where('id',$id)->query1();
			if(!$match)
				throw new my_exception('item not found by id',array('id'=>$id));
			if($insert_after!=$insert_item){
				$place = NULL;
				if($insert_item)
					$place = $this->_query->select('order,category_id')->from($this->_table_name)->where('id',$insert_item)->query1();
				if(!$place)
					$place = array('order'=>'1', 'category_id'=>$match['category_id']);
					//throw new my_exception('item not found by id',array('id'=>$insert_item));
				$this->_query->update($this->_table_name)->injection(' SET `order`=`order`+1 ')->where('order',$place['order'],'>')->_and('category_id',$place['category_id'])->query();
				$this->_query->update($this->_table_name)->set(array('order'=>$place['order']+1))->where('id',$id)->query1();
				$order = $place['order'];
				if($place['category_id'])
					$redirect_params['title'] = $this->_query->select('translit_title')->from('file_category')->where('id',$place['category_id'])->query1('translit_title');
			}else{
				if(!$insert_category)
					$insert_category = NULL;
				$order = $this->_query->select('order')->from($this->_table_name)->where('category_id',$insert_category)->order('order desc')->query1('order');
				if(!$order)
					$order = 1;
				else
					$order+=1;
				$this->_query->update($this->_table_name)->set(array('category_id'=>$insert_category, 'order'=>($order?$order:1)))->where('id',$id)->query1();
				if($insert_category)
					$redirect_params['title'] = $this->_query->select('translit_title')->from('file_category')->where('id',$insert_category)->query1('translit_title');
			}
			$this->_message('item moved',array('name'=>$match['title']));
		}
		if($redirect){
			if($this->parent->admin_mode)
				$redirect = $this->_config('admin_method');
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect, $redirect_params);
		}
	}
	
	public function backtrace(){
		$backtrace = debug_backtrace();
		foreach($backtrace as $item){
			foreach(array_keys($item['args']) as $key)
				if("object"==gettype($item['args'][$key]))
					unset($item['args'][$key]);
				//$item['args'][$key] = gettype($item['args'][$key]);
			$name = '';
			if(isset($item['class']))
				$name.=$item['class'];
			if(isset($item['class']))
				$name.=$item['type'];
			if(isset($item['class']))
				$name.=$item['function'];
			var_dump($name, $item['args']);
			//unset($item['object']);
			//var_dump($item);
		}
		die;
	}
	
	public function _get_param_value($method_name,$param_name){
		switch($method_name){
			case 'get':{
				switch($param_name){
					case 'field':{
						$this->_result = $this->_query->fetch_field_assoc($this->_table_name);
						break;
					}
					case 'value':break;
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get_category':{
				switch($param_name){
					case 'field':{
						$this->_result = $this->_query->fetch_field($this->_table_name.$this->_config('category_posfix'));
						break;
					}
					case 'value':break;
					case 'need_item':{
						return array('true'=>'+','false'=>'-');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get_category_by_title':{
				switch($param_name){
					case 'title':{
						$this->_result = $this->_query->select('title,translit_title')->from($this->_table_name.$this->_config('category_posfix'))->query2assoc_array('translit_title','title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('id')->from($this->_table_name)->query2assoc_array('id','id');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'remove':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('id,title')->from($this->_table_name)->query2assoc_array('id','title');
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
						$this->_result = $this->_query->select('id,title')->from($this->_table_name.$this->_config('category_posfix'))->query2assoc_array('id','title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'remove_category':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('id,title')->from($this->_table_name.$this->_config('category_posfix'))->query2assoc_array('id','title');
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