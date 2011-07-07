<?php
class output_config extends module_config{
	protected $need_language = false;
}

class output extends module{
	protected $content_type = 'text/plain';
	const config_class_name = 'output_config';
	
	public function header(){
		header('Content-Type: '.$this->content_type.'; charset='.$this->charset);
	}
}
?>