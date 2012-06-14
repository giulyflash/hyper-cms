<?php
class question extends base_module{
	protected $config_class_name = 'question_config';
	protected $_field = 'text,id,date,username,parent';
	protected $_field_admin = '*';
	protected $_id_type = 'int';//TODO id type
	protected $_id_field = 'id';
	protected $_draft = false;
	protected $_show_module_path = false;
	protected $_need_path = true;
	protected $_page_count = 10;
	protected $_message = true;
	const count = 100;
	
	
	protected $_default_username = 'Аноним';
	
	/*data operations*/
	
	public function get($id=NULL, $select=NULL){
		$where = false;
		if($id){
			$this->_query->where($this->_id_field,$id);
			$where = true;
			if(!$this->admin_mode && $this->_draft){
				if($where)
					$this->_query->_and('draft','1','!=');
				else
					$this->_query->where('draft','1','!=');
			}
			$this->_result = $this->_query->query1();
		}
		//condition?
		if(!$this->_result)
			$this->_message('object not found',array($this->_id_field=>$id));
		$this->add_item_path();
		if($this->position==$this->parent->_config('main_position_name') && !empty($this->_result['title']))
			$this->_title = $this->_result['title'];
		//TODO automatical get title from result;
	}
	
	public function save($id=NULL, $value = "", $parent=NULL, $username ="", $redirect = 'get_list', $redirect_params=array()){
		//fixme эксплойт - редактирование любой записи
		//$this->check_title($value);
		//$params = array('title'=>$value['title']);
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		$value = array('text'=>$value, 'username'=>(!$username?$this->_default_username:$username), 'date'=>$date, 'parent'=>($parent?$parent:$id));
		if($id){
			if(!isset($_SESSION[user_info]))
				throw new my_exception('access denied');
			$this->_query->update($this->_table_name)->set($value)->where($this->_id_field,$id)->query1();
			$this->_message('object edited successfully',$params);
		}
		else{
			//this->get_unique_title($value, $id);
			//if(empty($value['order']))
				//$value['order'] = $this->get_order(isset($value['category_id'])?$value['category_id']:NULL);
			$this->_query->insert($this->_table_name)->values($value)->execute();
			if(!$parent){
				$id = $this->_query->insert_id();
				$this->_query->update($this->_table_name)->set(array('parent'=>$id))->where('id',$id)->query1();
			}
			$this->_message('object added successfully',$params);
		}
		//$redirect_params['id'] = $value[$this->category_id_field];
		$this->redirect($redirect_params, $redirect);
	}
	
	public function remove($id=NULL,$redirect='get_list',$redirect_params = array()){
		if($id){
			$this->_query->delete()->from($this->_table_name)->where($this->_id_field,$id)->_or('parent',$id)->query1();
			$this->_message('object deleted successfully');
		}
		else
			$this->_message('object id is empty');
		$this->redirect($redirect_params,$redirect);
	}
	
	public function _admin($page=NULL,$count=self::count){
		return $this->get_list($page,$count);
	}
	
	public function edit($id=NULL, $category_id=NULL, $parent=NULL, $select='*'){
		if($parent)
			$this->_result['parent'] = $this->_query->select($this->_field)->from($this->_table_name)->where($this->_id_field,$parent)->query1();
		if($id){
			$this->_result = $this->_query->select($this->_field)->from($this->_table_name)->where($this->_id_field,$id)->query1();
			if(!$this->_result)
				throw new my_exception('object not found', array($this->_id_field=>$id));
		}
	}
	
	public function get_list($page=NULL,$count=NULL){
		if(!$count)
			$count = $this->_page_count;
		$this->_query->select($this->_field)->from($this->_table_name);
		//TODO rename 'draft' to 'show';
		if($this->_draft)
			$this->_query->where('draft','1','!=');
		$this->_result = $this->_query->order('parent,date')->limit_page($page,$count)->query();
	}
	
	public function edit_comment(){}
	
	public function save_comment(){}
}

class question_config extends module_config{
	protected $default_method = 'get_list';
	protected $callable_method=array(
		'get,get_list,save,edit'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'_admin,remove'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
	);
	protected $include = array(
		'*'=>'<link href="/module/question/question.css" rel="stylesheet" type="text/css"/>',
	);
}
?>