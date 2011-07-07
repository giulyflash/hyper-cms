<?php
class base_module_config extends module_config{
	protected $callable_method=array(
		//TODO check array merge works properly for child modules
		'get,get_category,get_category_by_title'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_read,
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
		'_admin,edit,save,remove,edit_category,save_category,move_category,remove_category,unlock_database,_get_param_value'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
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
	);
	
	protected $template_include = array(
		'module/base_module/base_module.xhtml.xsl',
	);
	
	protected $include = array(
		'admin_mode.*' =>
			'<link href="module/base_module/admin.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="/module/base_module/admin.js"></script>',
		'*,admin_mode.*' =>
			'<link href="module/base_module/base_module.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="/module/base_module/base_module.js"></script>',
	);
	
	protected $repair_hole = false;
	
	protected $category_posfix = '_category';
	public $has_item = true;
	public $has_category = false;
	protected $output_config = true;
	public $close_nested_folder = 0;
	private $article_not_found_name = "404";//TODO move this to main template/config
	protected $parent_module = array('base_module');
	
	protected $exclude_method_from_link_list = array('save','save_category','move_category');
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
	
	public function _admin($page=null, $count=null, $show='all', $category_field='*', $item_field = '*', $item_order = 'order'){
		//TODO $show: item, category
		if($this->_config('has_category')){
			$this->_result = $this->_query->select($category_field)->from($this->module_name.$this->_config('category_posfix'))->order('left')->query();
			if($this->_config('has_item'))
				if($items = $this->_query->select($item_field)->from($this->module_name)->order($item_order)->query()){
					foreach($this->_result as &$category)
						foreach(array_keys($items) as $item_num)
							if($category['id']==$items[$item_num]['category_id']){
								if(!isset($category['items']))
									$category['items'] = array();
								$category['items'][] = $items[$item_num];
								unset($items[$item_num]);
							}
					if($items)
						$this->_result['items'] = $items;
				}
		}
		elseif($this->_config('has_item'))
			$this->_result = $this->_query->select($item_field)->from($this->module_name)->order($item_order)->limit_page($page,$count)->query();
		$this->_result['lt'] = '<';
		$this->_result['gt'] = '>';
	}
	
	public function get_category($field = 'title', $value=NULL, $need_item=true, $item_fields = '*', $category_fields='id,title,translit_title,depth',$category_condition=array(),$item_condition=array()){
		//TODO common nested items: http://dev.mysql.com/tech-resources/articles/hierarchical-data.html
		if(!$this->_config('has_category'))
			throw new my_exception('try to use not existing category',array('title'=>$value)); 
		$this->_query->select($category_fields)->from($this->module_name.$this->_config('category_posfix'));
		$where = false;
		if($field){
			$where = true;
			$this->_query->where($field,$value);
		}
		$this->parse_condition($category_condition,$where);
		$this->_result = $this->_query->order('left')->query();
		if(!$this->_result && $field)
			$this->_message('category not found',array('title'=>$value));
		if($need_item){
			if(!$this->_config('has_item'))
				throw new my_exception('module has not items',array('name'=>$this->module_name));
			else{
				$this->_query->select($item_fields)->from($this->module_name);
				if($field){
					foreach ($this->_result as $category){
						if(is_array($category))
							$category_list[] = $category['id'];
					}
					$this->_query->where('category_id',$category_list,'in',true)->_or('category_id',NULL)->close_bracket();
					$where = true;
				}
				else
					$where = false;
				$this->parse_condition($item_condition,$where);
				$this->_result['items'] = $this->_query->query();
			}
		}
		$this->_result['lt'] = '<';
		$this->_result['gt'] = '>';
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
		$this->_result = $this->_query->select($select)->from($this->module_name);
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
	
	public function edit($id=NULL,$select='*'){
		if($id){
			$result = $this->_query->select($select)->from($this->module_name)->where('id',$id)->query1();
			if(!$result)
				throw new my_exception('object not found by id', array('id'=>$id));
			$this->_result = $result;
		}
	}
	
	public function save($id=NULL, $value = array(), $redirect = 'edit', $output_message = true, $params=array()){
		if($id){
			$this->_query->update($this->module_name)->set($value)->where('id',$id)->limit(1)->execute();
			if($output_message)
				$this->_message('edit successfool',$params);
		}
		else{
			$this->_query->insert($this->module_name)->values($value)->execute();
			$id = $this->parent->db->insert_id();
			if($output_message)
				$this->_message('add successfool',$params);
		}
		//$this->edit($id);
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect.'&id='.$id);
	}
	
	public function remove($id=NULL,$redirect='admin',$message=true,$param = array()){
		if($id){
			if($title)
				$this->_message('delete successfooly',$param);
			else
				$this->_query->delete()->from($this->module_name)->where('id',$id)->query1();
		}
		else
			$this->_message('id is empty');
	}
	
	public function edit_category($id=NULL, $insert_place=NULL){
		if($id)
			$this->_result = $this->_query->select()->from($this->module_name.$this->_config('category_posfix'))->where('id',$id)->query1();
		$this->_result['lt'] = '<';
		$this->_result['gt'] = '>';
	}

	public function save_category($id=NULL,$value=array(),$insert_place=NULL,$condition = array(),$redirect = 'admin'){
		$table_name = $this->module_name.$this->_config('category_posfix');
		if($id){
			$title = $this->_query->select('title')->from($table_name)->where('id',$id)->query1('title');
			$this->_query->update($table_name)->set($value)->where('id',$id)->limit(1)->execute();
			$this->_message('category edited successfooly',array('title'=>$title));
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
			//var_dump($insert_place,$value);die;
			$this->_query->insert($table_name)->values($value)->execute();
			$id = $this->parent->db->insert_id();
			$this->_message('new category add',array('title'=>$value['title']));
		}
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect);
	}

	public function move_category($id=NULL, $insert_type=NULL, $insert_place=NULL, $condition = array(),$redirect = 'admin'){
		//var_dump($id, $insert_type, $insert_place, $condition, $redirect);die();
		$table_name = $this->module_name.$this->_config('category_posfix');
		if(!$id)
			throw new my_exception('id is empty');
		if(!$insert_type)
			throw new my_exception('insert_type not found');
		elseif(!in_array($insert_type, array('before', 'inside')))
			throw new my_exception('wrong insert type', array('type'=>$insert_type));
		$target = $this->_query->select('left,right,depth,title')->from($table_name)->where('id',$id)->query1();
		if(!$target)
			throw new my_exception('not found category to movу',array('id'=>$id));
		$width = $target['right'] - $target['left'] + 1;
		$this->_query->lock($table_name)->execute();
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
			$destination = $this->_query->select('left,right,depth')->from($table_name)->where('id',$insert_place)->query1();
			if(!$destination)
				throw new my_exception('insert place not found',array('id'=>$insert_place));
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
		$this->_message('category moved successfooly',array('title'=>$target['title']));
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect);
	}

	public function remove_category($id=NULL,$condition = array(), $redirect = 'admin'){
		$table_name = $this->module_name.$this->_config('category_posfix');
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
		$this->_message('category deleted successfooly',array('name'=>$target['title']));
		if($redirect)
			$this->parent->redirect('admin.php?call='.$this->module_name.'.'.$redirect);
	}
	
	public function unlock_database(){
		$this->_query->unlock()->execute();
		$this->_message('database unlocked');
	}
	
	public function _get_param_value($method_name,$param_name){
		switch($method_name){
			case 'get':{
				switch($param_name){
					case 'field':{
						$this->_result = $this->_query->fetch_field_assoc($this->module_name);
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
						$this->_result = $this->_query->fetch_field($this->module_name.$this->_config('category_posfix'));
						break;
					}
					case 'value':break;
					case 'need_item':{
						$this->_result = array(1=>'+',''=>'-');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get_category_by_title':{
				switch($param_name){
					case 'title':{
						$this->_result = $this->_query->select('title,translit_title')->from($this->module_name.$this->_config('category_posfix'))->query2assoc_array('translit_title','title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('id')->from($this->module_name)->query2assoc_array('id','id');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'remove':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('id,title')->from($this->module_name)->query2assoc_array('id','title');
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
						$this->_result = $this->_query->select('id,title')->from($this->module_name.$this->_config('category_posfix'))->query2assoc_array('id','title');
						break;
					}
					default: parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'remove_category':{
				switch($param_name){
					case 'id':{
						$this->_result = $this->_query->select('id,title')->from($this->module_name.$this->_config('category_posfix'))->query2assoc_array('id','title');
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