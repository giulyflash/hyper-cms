<?php
class output_array extends output implements output_interface{
	public function get(){
		return var_export($this->parent->output,true);
	}
}
?>