<?php
class output_xsl extends output implements output_interface{
	protected $content_type = 'text/html';
	private $loaded_templates = array();
	
	public function get(){
		$xsl_filename = $this->parent->_config('template');
		$this->loaded_templates[$xsl_filename] = true;
		if(!file_exists($xsl_filename))
			throw new my_exception('template not found',$xsl_filename);
		$xsl = new DOMDocument();
		$xsl->load($xsl_filename);
		$stylesheetNode = $xsl->firstChild;
		foreach($this->parent->template_include as $include)
			$this->xsl_include($xsl, $stylesheetNode, NULL, $include);
		foreach($this->parent->output['module'] as $module){
			if(!isset($module['_module_name'])){
				throw new my_exception('module name not found',NULL,0);
			}
			$this->xsl_include($xsl, $stylesheetNode, $module['_module_name'], isset($module['_template'])?$module['_template']:NULL);
		}
		//var_dump($this->loaded_templates);
		$proc = new XSLTProcessor();
		try{
			$proc->importStyleSheet($xsl);
		}
		catch(Exception $e){
			throw new my_exception('import XSL error',$e->getMessage());
		}
		$xml = new DOMDocument("1.0",$this->charset);
		$xml_root = $xml->appendChild(new DOMElement('root'));
		try{
			output_xml::transform_array2DOM($this->parent->output, $xml_root);
		}
		catch(Exception $e){
			throw new my_exception('array 2 DOM transformation error ',$e->getMessage(),0);
		}
		return $proc->transformToXML($xml);
	}
	
	private function xsl_include($xsl,$stylesheet_node,$class_name,$template){
		if($template)
			$file_name = $template;
		else
			$file_name = _module_path.$class_name.'/'.$class_name.$this->parent->_config('module_template_ext');
		if(!isset($this->loaded_templates[$file_name])){
			if(!file_exists($file_name))
				throw new my_exception('template not found', $file_name);
			else{
				$import_xsl=$xsl->createElementNS('http://www.w3.org/1999/XSL/Transform','xsl:include');
				$import_xsl->setAttribute('href','../../'.$file_name);
				$stylesheet_node->insertBefore($import_xsl,$stylesheet_node->firstChild);
			}
			$this->loaded_templates[$file_name] = true;
		}
	}
}
?>