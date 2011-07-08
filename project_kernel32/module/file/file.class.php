<?php
class file_config extends module_config{
	protected $img_ext=array('jpeg','jpg','png','gif','bmp');
	protected $video_ext=array('flv','mpeg','mpg','avi','wmv');
	protected $doc_ext=array('doc','docx','xls','xlsx');
	protected $invalid_ext=array(
		"php",
		"php5",
		"php4",
		"phtml",
		"pl",
		"perl"
	);
	protected $default_thumb = 'template/admin/images/_document.png';
	protected $include = array(
		'get_list'=>'<script type="text/javascript" src="/extensions/SWFUpload/swfupload.js"></script>
		<script type="text/javascript" src="/extensions/SWFUpload/plugins/swfupload.queue.js"></script>
		<script type="text/javascript" src="/extensions/SWFUpload/plugins/fileprogress.js"></script>
		<script type="text/javascript" src="/extensions/SWFUpload/plugins/handlers.js"></script>
		<link rel="stylesheet" type="text/css" href="/extensions/SWFUpload/default.css"/>
		<script type="text/javascript" src="/module/file/load_file.js"></script>
		<link rel="stylesheet" type="text/css" href="/module/file/admin.css"/>',
	);
	protected $file_path='file/';
	protected $thumb_path='thumb/';
	protected $thumb_width=180;
	protected $thumb_height=180;
	protected $overwrite_if_exist = false;
	
	protected $callable_method=array(
		'get'=>array(
			self::object_name=>array(__CLASS__,'menu_item'),
			self::role_name=>array(self::role_read,self::role_read),
		),
		'get_list'=>array(
			self::object_name=>array(__CLASS__,'menu_item'),
			self::role_name=>array(self::role_read,self::role_read),
		),
		'upload'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
		/*'edit'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
		'save'=>array(
			'text'=>FILTER_UNSAFE_RAW,
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),*/
		'admin'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
		'remove'=>array(
			self::object_name=>__CLASS__,
			self::role_name=>self::role_write,
		),
	);
}

class file extends module{
	protected $config_class_name = 'file_config';
	
	public function _admin(){
	}
	
	public function get($module='file'/*,$page=1,$count=10*/){
		$this->_result = $this->_query->select()->from('file')->where(($module)?'module':'',($module)?$module:'')->_and('language',array('*',$this->_config('language')),'in')->query();
	}
	
	public function get_list($module='file'/*,$page=1,$count=10*/){
		$this->_result = $this->_query->select()->from('file')->where(($module)?'module':'',($module)?$module:'')->_and('language',array('*',$this->_config('language')),'in')->order('create_date desc')->query();
	}
	
	public function upload($data=NULL, $module='file'){
		$this->parent->config->set('template','module/file/file.xhtml.xsl');
		$this->_result = $this->get_files($module);
	}
	
	public function get_files($module='file'){
		$result = array();
		foreach($_FILES as $file_order=>$file){
			if(is_array($file['name'])){
				foreach($file['name'] as $arg_order=>$useless){
					$args=array(
						'name'=>$file[$name][$arg_order],
						'tmp_name'=>$file[$tmp_name][$arg_order],
						'error'=>$file[$error][$arg_order],
						'size'=>$file[$size][$arg_order],
						'type'=>$file[$type][$arg_order],
						'module'=>$module,
					);
					$result[] = self::save_file($args);
				}
			}
			else{
				$file['module'] = $module;
				$result[] = self::save_file($file);
			}
		}
		return $result;
	}
	
	private function save_file($file){
		//TODO edit
		//TODO error reporting
		if(!$file['name'])
			throw new my_exception('file name not found');
		if(!preg_match('%^(.+?)\.?([^.]+)$%',$file['name'],$re))
			throw new my_exception('wrong file name',$file['name']);
		$file['name'] = $re[1];
		$file['extension'] = strtolower($re[2]);
		if(in_array($file['extension'], $this->_config('invalid_ext')))
			throw new my_exception('unallowed extension',$file['extension']);
		$file['translit_name'] = translit::transliterate($file['name']);
		$file['path'] = $this->_config('file_path').$file['translit_name'];
		if(file_exists($file['path'])){
			if($this->_config('overwrite_if_exist')){
				//TODO remove file
			}
			else
				$file['path'].= '_'.md5(rand(1,1000).date('Y-m-d H:i:s'));
		}
		$file['path'].= '.'.$file['extension'];
		if(!move_uploaded_file($file['tmp_name'], $file['path']))
			throw new my_exception('move file fail');
		$date = new DateTime();
		$file['create_date'] = $date->format('Y-m-d H:i:s');
		$file['thumb_path'] = $this->create_thumb($file);
		//echo "\n".$this->_query->insert('file')->values($file)->get_sql()."\n";
		if($id=$this->_query->select('id')->from('file')->where('path',$file['path'])->query1('id'))
			$this->_query->update('file')->set($file)->where('id',$id);
		else
			$this->_query->insert('file')->values($file)->execute();
		$file['id'] = $this->parent->db->insert_id();
		return $file;
	}
	
	public function remove($id, $die=true){
		$path = $this->_query->select('path,thumb_path')->from('file')->where('id',$id)->query1();
		if(!$path)
			throw new my_exception('file not found','id='.$id);
		if(!unlink($path['path']) || $path['thumb_path']!=$this->_config('default_thumb') && !unlink($path['thumb_path']))
			throw new my_exception('remove file fail');
		$this->_query->delete()->from('file')->where('id',$id)->query1();
		if($die)
			die;
		//TODO output JSON
	}
	
	private function create_thumb(&$file){
		if(!in_array($file['extension'], $this->_config('img_ext'))){
			if(in_array($file['extension'], $this->_config('doc_ext'))){
				$file['internal_type'] = 'document';
				return $this->_config('default_thumb');//TODO icons
			}
			$file['internal_type'] = 'file';
			return $this->_config('default_thumb');
		}
		$file['internal_type'] = 'image';
		$img = new my_image($file['path'],$this->_config('thumb_path'),$this->_config('thumb_width'),$this->_config('thumb_height'));
		return $img->thumb();
	}
}
?>