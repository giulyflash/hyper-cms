<?php
class my_image{
	public $path;
	public $quality = 90;//0 - 100 (the best), 0 - 9 for .PNG
	public $output_path = '';
	public $output_width = 100;
	public $output_height = 75;
	
	public function __construct($path=NULL, $output_path=NULL, $output_width=NULL, $output_height=NULL, $quality=NULL){
		if(!$path)
			throw new my_exception('image path not found');
		$this->path = $path;
		if($output_path)
			$this->output_path = $output_path;
		if($output_width)
			$this->output_width = $output_width;
		if($output_height)
			$this->output_height = $output_height;
		if($quality)
			$this->quality = $quality;
		$this->check_support();
		if(preg_match('%^(.*?)([^/]+?(\.([a-z]+))?)$%i',$path,$ext_re)){
			$this->file_path = $ext_re[1];
			$this->file_name = $ext_re[2];
			$this->extension = (!empty($ext_re[4]))?strtolower($ext_re[4]):'';
		}
		else{
			throw new my_exception('wrong file name',$path);
		}
		if(!in_array($this->extension, $this->supported_extension))
			throw new my_exception('unsupported image type',$this->extension);
		if(!file_exists($path))
			throw new my_exception('file not found',$path);
		$this->load();
	}
	
	private function check_support(){
		if (imagetypes() & IMG_JPG) {
			$this->supported_extension[]='jpg';
			$this->supported_extension[]='jpeg';
		}
		if (imagetypes() & IMG_PNG)
			$this->supported_extension[]='png';
		if (imagetypes() & IMG_GIF)
			$this->supported_extension[]='gif';
		if (imagetypes() & IMG_WBMP)
			$this->supported_extension[]='bmp';
		//$this->supported_extension = array('jpg','jpeg','png','gif','bmp');
	}
	
	private function load(){
		switch ($this->extension){
			case 'jpg':
			case 'jpeg':
				$this->source = imagecreatefromjpeg($this->path);
				break;
			case 'gif':
				$this->source = imagecreatefromgif($this->path);
				break;
			case 'png':
				$this->source = imagecreatefrompng($this->path);
				break;
			case 'bmp':
				$this->source = imagecreatefromwbmp($this->path);
				break;
			case '':
				throw new my_exception('extension not found',$this->path);
				break;
			default:
				throw new my_exception('unsupported image type',$this->extension);
		}
		if(!$this->source)
			throw new my_exception('error while reading file',$src_path);
	}
	
	public function thumb($output_path=NULL, $output_width=NULL, $output_height=NULL, $quality=NULL){
		if($output_path)
			$this->output_path = $output_path;
		if($output_width)
			$this->output_width = $output_width;
		if($output_height)
			$this->output_height = $output_height;
		if($quality)
			$this->quality = $quality;
		$this->create_thumb();
		return $this->output_path;
	}
	
	public function create_thumb(){
		$this->output_path = $this->file_path.$this->output_path.$this->output_width.'x'.$this->output_height.'_'.$this->file_name;
		$this->load();
		$src_x=imagesx($this->source);
		$src_y=imagesy($this->source);
		$thumb_y = $this->output_height;
		$thumb_x = $this->output_width;
		if(!$thumb_y or ($src_x/$src_y >= $thumb_x/$thumb_y)){
    		$dst_x=$thumb_x;
    		$dst_y=round($src_y*$thumb_x/$src_x);
    	}
    	else{
    		$dst_x=round($src_x*$thumb_y/$src_y);
    		$dst_y=$thumb_y;
    	}
    	if(($dst_x>=$src_x || $dst_y>=$src_y)){
    		$dst_x = $src_x;
    		$dst_y = $src_y;
    	}
    	$this->result = imagecreatetruecolor($dst_x, $dst_y);
    	imagecopyresampled($this->result, $this->source, 0,0,0,0, $dst_x, $dst_y, $src_x, $src_y);
    	$this->write_file();
    	imagedestroy($this->source);
    	imagedestroy($this->result);
    	if(!file_exists($this->output_path))
    		throw new my_exception('unable save file to HDD. No permition?',$this->output_path);		
	}
	
	public function write_file(){
		if(!$this->result)
			$this->result = &$this->source;
		if($this->quality<0 || $this->quality>100)
			throw new my_exception('wrong image quality value'.$quality);
		switch ($this->extension){
			case 'jpg':
			case 'jpeg':
				$res = imagejpeg($this->result, $this->output_path, $this->quality);
				break;
			case 'gif':
				$res = imagegif($this->result, $this->output_path);
				break;
			case 'png':
				$res = imagepng($this->result, $this->output_path, round($this->quality/10));
				break;
			case 'bmp':
				$res = imagewbmp($this->result, $this->output_path);
				break;
			default:
				trigger_error('unsupported img type',$this->extension);
		}
		if(!$res)
			throw new my_exception('save file error', $this->output_path);
	}
}
?>