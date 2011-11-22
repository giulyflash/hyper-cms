<?php
class breadcrumbs extends module{
	protected $config_class_name = 'breadcrumbs_config';

	public function get($manual=NULL){
		if(!$manual && !$this->parent->manual_path && $this->parent->get_path_count()==$this->parent->default_path_count)
			$this->parent->get_method_path();
		return $this->parent->get_path();
	}
}

class breadcrumbs_config extends module_config{
	protected $output_config = true;
	public $delimiter = '&#9658;';
	protected $callable_method=array(
		'get'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
	
	protected $include = array(
		'*,admin_mode.*' => '<link href="/module/breadcrumbs/breadcrumbs.css" rel="stylesheet" type="text/css"/>',
	);
}

?>