<?php
class file_config extends module_config{
	protected $img_ext=array('jpeg','jpg','png','gif','bmp');
	protected $video_ext=array('flv','mpeg','mpg','avi','wmv');
	protected $doc_ext=array('doc','docx','xls','xlsx');
	protected $invalid_ext=array('php','php5','php4','phtml','pl','perl');
	protected $allowed_ext=array('jpeg','jpg','png','gif','bmp');
	protected $default_thumb = 'template/admin/images/_document.png';
	protected $include = array(
		'get_list'=>'<script src="/extensions/SWFUpload/swfupload.js" type="text/javascript"></script>
		<script src="/extensions/SWFUpload/plugins/swfupload.queue.js" type="text/javascript"></script>
		<script src="/extensions/SWFUpload/plugins/fileprogress.js" type="text/javascript"></script>
		<script src="/extensions/SWFUpload/plugins/handlers.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="/extensions/SWFUpload/default.css"/>
		<script src="/module/file/load_file.js" type="text/javascript"></script>
		<link href="/module/file/admin.css" type="text/css" rel="stylesheet"/>',
	);
	protected $file_path='file/';
	protected $thumb_path='thumb/';
	protected $thumb_width=180;
	protected $thumb_height=180;
	protected $overwrite_if_exist = false;
	
	protected $callable_method=array(
		'get,get_list,'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'upload,admin,remove,edit,save'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
	);
}

class file extends module{
	protected $config_class_name = 'file_config';
	
	public function _admin(){
	}
	
	public function get($module='file'/*,$page=1,$count=10*/){
		$this->_result = $this->_query->select()->from('file')->where(($module)?'module':'',($module)?$module:'')
			//->_and('language',array('*',$this->_config('language')),'in')
		->query();
	}
	
	public function get_list($module='file'/*,$page=1,$count=10*/){
		$this->_result = $this->_query->select()->from('file')->where(($module)?'module':'',($module)?$module:'')
			//->_and('language',array('*',$this->_config('language')),'in')->order('create_date desc')
		->query();
	}
	
	public function upload($data=NULL, $module='file'){
		$this->parent->config->set('template','module/file/file.xhtml.xsl');
		$this->_result = $this->get_files($module);
	}
	
	public function get_files($module='file', $id=NULL, $title=NULL, $category_id=NULL){
		$result = array();
		if(!$category_id)
			$category_id = NULL;
		foreach($_FILES as $file_order=>$file){
			if(is_array($file['name'])){
				if($id && count($file['name'])>1)
					throw new my_exception('denied to edit a lot of files');
				foreach(array_keys($file['name']) as $arg_order){
					if(!(
						$file['name'][$arg_order]=='' &&
						$file['type'][$arg_order]=='' &&
						$file['tmp_name'][$arg_order]=='' &&
						($file['error'][$arg_order]=='' || $file['error'][$arg_order]==4) &&
						$file['size'][$arg_order]==''
					)){
						$args=array(
							'title'=>$file['name'][$arg_order],
							'tmp_name'=>$file['tmp_name'][$arg_order],
							'error'=>$file['error'][$arg_order],
							'size'=>$file['size'][$arg_order],
							'type'=>$file['type'][$arg_order],
							'module'=>$module,
							'category_id'=>$category_id,
						);
						if($id && count($file['name'])==1){
							$args['id'] = $id;
							$args['user_title'] = $title;
						}
						$result[] = self::save_file($args);
					}
				}
			}
			else{
				$file['module'] = $module;
				$file['title'] = $file['name'];
				unset($file['name']);
				$file['id'] = $id;
				$file['user_title'] = $title;
				$file['category_id'] = $category_id;
				$result[] = self::save_file($file);
			}
		}
		return $result;
	}
	
	private function save_file($file){
		//TODO edit
		//TODO error reporting
		if(!$file['title'])
			throw new my_exception('file name not found');
		if(!preg_match('%^(.+?)\.?([^.]+)$%',$file['title'],$re))
			throw new my_exception('wrong file name',$file['title']);
		if(array_key_exists('user_title',$file)){
			if($file['user_title'])
				$file['title'] = $file['user_title'];
			else
				$file['title'] = $re[1];
			unset($file['user_title']);
		}
		else
			$file['title'] = $re[1];
		$file['extension'] = strtolower($re[2]);
		if(in_array($file['extension'], $this->_config('invalid_ext')))
			throw new my_exception('unallowed extension',$file['extension']);
		$file['translit_title'] = translit::transliterate($file['title']);
		$file['path'] = $this->_config('file_path').$file['translit_title'].'.'.$file['extension'];
		if(!empty($file['id']))
			$this->remove($file['id'], false, false, false);
		while(file_exists($file['path'])){
			if(!empty($file['id']) && $this->_config('overwrite_if_exist'))
				$this->remove($file['id'], false, false, false);
			else
				$file['path'] = $this->_config('file_path').$file['translit_title'].'_'.md5(rand(1,1000).date('Y-m-d H:i:s')).'.'.$file['extension'];
		}
		//$file['path'] = getcwd().'/'.$file['path'];
		//TODO need 7 permission to write, wtf?
		if(!move_uploaded_file($file['tmp_name'], $file['path']))
			throw new my_exception('move file fail');
		$date = new DateTime();
		$file['create_date'] = $date->format('Y-m-d H:i:s');
		$file['thumb_path'] = $this->create_thumb($file);
		if(!empty($file['id']))
			$this->_query->update('file')->set($file)->where('id',$file['id'])->execute();
		else{
			$this->_query->insert('file')->values($file)->execute();
			$file['id'] = $this->parent->db->insert_id();
		}
		return $file;
	}
	
	public function remove($id, $die=true, $delete_sql=true, $throw_error = false){
		$path = $this->_query->select('path,thumb_path')->from('file')->where('id',$id)->query1();
		if(!$path)
			throw new my_exception('file not found','id='.$id);
		if($delete_sql)
			$this->_query->delete()->from('file')->where('id',$id)->query1();
		if($throw_error && (!@unlink($path['path']) || $path['thumb_path']!=$this->_config('default_thumb') && !@unlink($path['thumb_path'])) )
			throw new my_exception('remove file fail','id='.$id);
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