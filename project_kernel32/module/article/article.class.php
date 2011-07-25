<?php
class article extends base_module{
	protected $config_class_name = 'article_config';

	public function get($field = 'id', $value='1',$show_title=false){
		$select = array('text');
		if($show_title === false)
			$show_title = $this->_config('default_show_title');
		if($show_title)
			$select[] = 'title';
		if(parent::get($field,$value,$select,array('draft',1,'!=')) && $show_title)
			$this->_title = $this->_result['title'];
	}
	
	public function get_by_title($title = NULL, $show_title=false){
		if(!$title)
			$this->_message('title not found');
		else
			$this->get('translit_title',$title,$show_title);
	}
	
	public function get_category($field = 'translit_title', $value=NULL, $need_item=true){
		$this->_get_category($field, $value, $need_item, 'id,title,translit_title,text,preview,category_id');
	}
	
	public function save($id=NULL, $title=NULL, $translit_title=NULL, $text=NULL, $keyword=NULL, $description=NULL, $draft=NULL){
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
	}
	
	public function _admin($page=null, $count=null, $show='all'/*objects-categories-all*/){
		parent::_admin($page, $count, $show, 'depth,title,id', 'category_id,title,id', 'create_date');
	}
	
	public function remove($id=NULL){
		$param = array();
		if($id)
			$param['title'] = $this->_query->select('title')->from($this->module_name)->where('id',$id)->query1('title');
		parent::remove($id,$param);
	}
	
	//category
	public function save_category($id=NULL,$title=NULL,$insert_place=NULL,$condition = array()){
		if(!$title){
			$this->_message('category name must not be empty');
			$this->parent->redirect('admin.php?call='.$this->module_name.'.admin');
			return;
		}
		$link = $input_type=='article'?$link_article:$link;
		$value = array('title'=>$title);
		parent::save_category($id,$value,$insert_place);
	}
	
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
						return $this->_query->select('id,title')->from($this->module_name)->query2assoc_array('id','title');
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
						return $this->_query->select('title,translit_title')->from($this->module_name)->where('draft',1,'!=')->query2assoc_array('translit_title','title');
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

class article_config extends base_module_config{
	protected $callable_method = array(
		'get_by_title' =>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
	
	protected $link = array(
		'admin_mode.edit'=>array(
			'right'=>'file.get_list&module=article'
		),
	);
	
	protected $include = array(
		'edit'=>
			'<link href="module/article/admin.css" rel="stylesheet" type="text/css"/>
			<script type="text/javascript" src="extensions/ckeditor/ckeditor.js"></script>
			<script type="text/javascript" src="module/article/admin.js"></script>'
	);
	
	public $has_item = true;
	public $has_category = true;
	public $close_nested_folder = 1;
	public $default_show_title = true;
}
?>