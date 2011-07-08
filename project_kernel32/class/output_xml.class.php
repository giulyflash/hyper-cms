<?php
class output_xml extends output implements output_interface{
	protected $content_type = 'text/xml';
	
	public function get(){
		$xml = new DOMDocument("1.0",$this->charset);
		$xml_root = $xml->appendChild(new DOMElement('root'));
		try{
			$this->transform_array2DOM($this->parent->output, $xml_root);
		}
		catch(Exception $e){
			throw new my_exception('array 2 DOM transformation error ',$e->getMessage());
		}
		return $xml->saveXML();
	}

	/* TODO test this:
function assocArrayToXML($root_element_name,$ar)
{
//if (is_numeric($key)) $key = "unknownNode_".(string) $key;
//$key = preg_replace('/[^a-z]/i', '', $key);
    $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$root_element_name}></{$root_element_name}>");
    $f = create_function('$f,$c,$a','
            foreach($a as $k=>$v) {
                if(is_array($v)) {
                    $ch=$c->addChild($k);
                    $f($f,$ch,$v);
                } else {
                    $c->addChild($k,$v);
                }
            }');
    $f($f,$xml,$ar);
    return $xml->asXML();
} 
*/
	//TODO create text from array and parse it without DomDocument class
	
	public function transform_array2DOM($array, DOMElement &$container=NULL, $nodeName=NULL, $useNodeNameForChildren = TRUE) {
		if (!is_array($array) && (!is_object($array))) $array=array($array);
		if (!$container) $container=new DOMDocument("1.0",$this->_charset);
		foreach ($array as $key=>$value) {
			if (is_numeric($key))
				$key=($nodeName)?$nodeName:"item";
			$key=str_replace('$','',$key);
			if($key == '*')
				$key = '_';
			if (is_array($value) || is_object($value)){
				output_xml::transform_array2DOM($value, $container->appendChild(
				new DOMElement($key)), (($useNodeNameForChildren)?$nodeName:NULL));
			}
			else {
				if (strstr($key,'Url')) $escapeAmp = FALSE;
				$container->appendChild(new DOMElement($key))->appendChild(new DOMCDATASection($value));
			}
		}
	}
}
?>