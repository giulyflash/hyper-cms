<?php
class output_json extends output implements output_interface{
	public function get(){
		unset(
			$this->parent->output['module'][0]['_module_name'],
			$this->parent->output['module'][0]['_method_name'],
			$this->parent->output['module'][0]['_config'],
			$this->parent->output['module'][0]['_position'],
			$this->parent->output['module'][0]['_argument']
		);
		if($this->parent->error)
			$this->parent->output['module'][0]['__error'] = $this->parent->error;
		return json_encode($this->parent->output['module'][0]);
	}
}
?>