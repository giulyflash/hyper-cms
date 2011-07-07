<?php
class output_json_all extends output implements output_interface{
	public function get(){
		return json_encode($this->parent->output);
	}
}
?>