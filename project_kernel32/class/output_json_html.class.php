<?php
class output_json_html extends output implements output_interface{
	//protected $content_type = 'text/json';
	//TODO include
	public function get(){
		$file_name = _module_path.'app/'.$this->parent->_config('json_html_template');
		$this->parent->config->set('template',$file_name);
		if(!empty($this->parent->output['error']))
			$error = $this->parent->output['error'];
		else
			$error = NULL;
		//unset($this->parent->output['include'], $this->parent->output['error']);
		$output = new output_xsl($this->parent);
		$this->parent->output['html'] = $output->get();
		if($error)
			$this->parent->output['error'] = $error;
		unset($this->parent->output['module'], $this->parent->output['language'], $this->parent->output['meta']);
		return json_encode($this->parent->output);
	}
}
?>