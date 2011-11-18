<?php
class gallery extends base_module{
	protected $config_class_name = 'gallery_config';

	public function get_category($title = false){ 
		//sleep(100500);
		parent::get_category_base('translit_title', $title, true, 'auto', NULL, array(array('module',$this->module_name), array('internal_type','image')));
	}
	
	public function remove($id=NULL){
		$redirect_params = array();
		if($id){
			$data = $this->_query->select('title,category_id')->from($this->_table_name)->where('id',$id)->query1();
			file::remove($id,false);
			$this->_message('deleted seccessfully',array('title'=>$data['title']));
			if($data['category_id'])
				$redirect_params['title'] = $this->_query->select('translit_title')->from($this->_category_table_name)->where('id',$data['category_id'])->query1('translit_title');
		}
		$this->parent->redirect('/'.($this->parent->admin_mode?'admin.php':'').'?call='.$this->module_name.($this->parent->admin_mode?'._admin':''),$redirect_params);
	}
	
	public function _admin($title=NULL){
		$this->get_category($title);
	}
	
	public function save($id=NULL, $title=NULL, $category_id=NULL){
		$file = new file($this->parent);
		if($files = $file->get_files($this->module_name, $id, $title, $category_id)){
			$count = count($files);
			$file_name = '';
			foreach($files as $num=>&$match)
				$file_name.='"'.$match['title'].'"'.(($num+1)!=$count?', ':'');
			if(!$id)
				$this->_message('file edited successfully', array('file'=>&$file_name));
			else{
				if($count==1)
					$this->_message('file loaded successfully', array('file'=>&$file_name));
				else
					$this->_message('files loaded successfully', array('files'=>&$file_name));
			}
		}
		elseif($id){
			$file_info = array('category_id' => $category_id?$category_id:NULL);
			if($title)
				$file_info['title'] = $title;
			$this->_query->update('file')->set($file_info)->where('id',$id)->execute();
			$this->_message('file edited successfully', array('file'=>'"'.$this->_query->select('title')->from('file')->where('id',$id)->query1('title').'"'));
		}
		if($id)
		$redirect_params = array();
		if($category_id)
			$redirect_params['title'] = $this->_query->select('translit_title')->from($this->_category_table_name)->where('id',$category_id)->query1('translit_title');
		$this->parent->redirect('/'.($this->parent->admin_mode?'admin.php':'').'?call='.$this->module_name.($this->parent->admin_mode?'._admin':''),$redirect_params);
	}
	
	/*
array(1) {
  ["file"]=>
  array(5) {
    ["name"]=>
    array(3) {
      [0]=>
      string(12) "gradient.png"
      [1]=>
      string(13) "Forum Fit.xls"
      [2]=>
      string(13) "controlsv.xls"
    }
    ["type"]=>
    array(3) {
      [0]=>
      string(9) "image/png"
      [1]=>
      string(20) "application/ms-excel"
      [2]=>
      string(20) "application/ms-excel"
    }
    ["tmp_name"]=>
    array(3) {
      [0]=>
      string(27) "C:\Windows\Temp\php2DBC.tmp"
      [1]=>
      string(27) "C:\Windows\Temp\php2DBD.tmp"
      [2]=>
      string(27) "C:\Windows\Temp\php2DBE.tmp"
    }
    ["error"]=>
    array(3) {
      [0]=>
      int(0)
      [1]=>
      int(0)
      [2]=>
      int(0)
    }
    ["size"]=>
    array(3) {
      [0]=>
      int(14853)
      [1]=>
      int(11776)
      [2]=>
      int(149504)
    }
  }
}
	 */
	
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
	
	protected $include = array(
		'get_category,edit,_admin,edit'=>
			'<link href="/extensions/jquery_lightbox/jquery_lightbox.css" rel="stylesheet" type="text/css"/>
			<script src="/extensions/jquery_lightbox/jquery_lightbox.min.js" type="text/javascript"></script>
			<link href="/module/gallery/gallery.css" rel="stylesheet" type="text/css"/>',
	);
	
	public $has_item = true;
	public $has_category = true;
	public $close_nested_folder = 1;
	public $default_show_title = true;
	
	protected $table_name = 'file';
	protected $item_field = 'id,translit_title,path,thumb_path,thumb2_path,title,category_id';
	protected $default_method = 'get_category';
}
?>