<?php
class gallery extends base_module{
	protected $config_class_name = 'gallery_config';

/*	public function get($field = 'id', $value='1'){
		if(parent::get($field,$value,$this->_config('field_list'),array('module',$this->module_name)) )
			$this->_title = $this->_result['title'];
	}
	
	public function get_by_title($title = NULL, $show_title=false){
		if(!$title)
			$this->_message('title not found');
		else
			$this->get('translit_title',$title,$show_title);
	}*/
	
	public function get_category($title = false){
		sleep(1);
		parent::get_category('translit_title', $title, true, false, $this->_config('field_list'), 'id,title,translit_title,depth',array(),array(array('module',$this->module_name), array('internal_type','image')));
	}
	
	/*public function save($id=NULL, $title=NULL, $translit_title=NULL, $text=NULL, $keyword=NULL, $description=NULL, $draft=NULL){
		if(!$title)
			throw new my_exception('title must not be empty');
		$value = array(
			'title'=>$title,
			'translit_title'=>($translit_title?$translit_title:translit::transliterate($title)),
			'text'=>$text,
			'keyword'=>$keyword,
			'description'=>$description,
			'draft'=>($draft)?1:0,
		);
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		if($id)
			$value['edit_date'] = $date;
		else
			$value['create_date'] = $date;
		parent::save($id, $value, 'edit',true,array('name'=>$title));
	}*/
	
	public function _admin($page=null, $count=null, $show='all'){
		parent::_admin($page, $count, $show, 'id,title,translit_title,depth', $this->_config('field_list'),'order',array(),array(array('module',$this->module_name), array('internal_type','image')));
	}

	public function remove($id=NULL){
		if($id){
			$param['title'] = $this->_query->select('name')->from($this->_table_name)->where('id',$id)->query1('name');
			file::remove($id,false);
		}
		$this->parent->redirect('/?call='.$this->module_name);	
	}
	
	/*
	//category
	public function save_category($id=NULL,$title=NULL,$insert_place=NULL,$condition = array()){
		if(!$title){
			$this->_message('category name must not be empty');
			$this->parent->redirect('admin.php?call='.$this->module_name.'.edit_category&id='.$id.'&insert_place='.$insert_place);
			return;
		}
		$value = array('title'=>$title);
		parent::save_category($id,$value,$insert_place);
	}*/
	
	public function _get_param_value($method_name,$param_name){
		switch($method_name){
			case 'get':{
				switch($param_name){
					case 'show_title':{
						return array('true'=>'+','false'=>'-');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit':{
				switch($param_name){
					case 'id':{
						return $this->_query->select('id,title')->from($this->_table_name)->query2assoc_array('id','title');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get_by_title':{
				switch($param_name){
					case 'title':{
						return $this->_query->select('title,translit_title')->from($this->_table_name)->where('draft',1,'!=')->query2assoc_array('translit_title','title');
						break;
					}
					case 'show_title':{
						return array('true'=>'+','false'=>'-');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			default:
				parent::_get_param_value($method_name,$param_name);
		}
	}
}

class gallery_config extends base_module_config{
	protected $callable_method = array(
		'get_by_title' =>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
	
	protected $object = array(
		'article'=>array(
			'method'=>'get_by_title',
			'param'=>'title'
		),
		'article_category'=>array(
			'method'=>'get_category_by_title',
			'param'=>'title'
		),
	);
	
	protected $link = array(
		'admin_mode.edit'=>array(
			'right'=>'file.get_list&module=article'
		),
	);
	
	protected $include = array(
		'get_category,edit'=>
			'<link href="/extensions/jquery_lightbox/jquery_lightbox.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="/extensions/jquery_lightbox/jquery_lightbox.min.js"></script>
			<script type="text/javascript" src="/module/gallery/gallery.js"></script>',
		'get_category,_admin'=>
			'<link href="/module/gallery/gallery.css" rel="stylesheet" type="text/css"/>',
	);
	
	public $has_item = true;
	public $has_category = true;
	public $close_nested_folder = 1;
	public $default_show_title = true;
	
	protected $table_name = 'file';
	protected $field_list = 'id,translit_name,path,thumb_path,thumb2_path,name,category_id,';
	protected $default_method = 'get_category';
}
?>