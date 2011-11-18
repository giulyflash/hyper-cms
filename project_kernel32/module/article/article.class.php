<?php
class article extends base_module{
	protected $config_class_name = 'article_config';
	protected $preview_default_count = 200;
	
	private $more_tag = '<!--more-->';

	public function get($field = 'id', $value='1',$show_title=false){
		$select = array('text','id');
		if($show_title === false)
			$show_title = $this->_config('default_show_title');
		if($show_title)
			$select[] = 'title';
		if(parent::get($field,$value,$select,array('draft',1,'!=')) && $show_title)
			$this->_title = $this->_result['title'];
	}
	
	public function get_by_title($title = NULL, $show_title=false){
		if(!$title)
			$this->_message('title not found');
		else
			$this->get('translit_title',$title,$show_title);
	}
	
	/*public function get_category($field = 'translit_title', $value=NULL, $need_item=true){
		parent::get_category($field, $value, $need_item, false, 'id,title,translit_title,text,preview,category_id');
	}*/
	
	public function save($id=NULL, $title=NULL, $translit_title=NULL, $text=NULL, $keyword=NULL, $description=NULL, $draft=NULL, $category_id=NULL, $create_date=array()){
		if(!$title)
			throw new my_exception('title must not be empty');
		if(!$category_id)
			$category_id = NULL;
		$value = array(
			'title'=>$title,
			'translit_title'=>($translit_title?$translit_title:translit::transliterate($title)),
			'text'=>&$text,
			'preview'=>$this->get_preview($text),
			'keyword'=>$keyword,
			'description'=>$description,
			'draft'=>($draft)?1:0,
			'category_id'=>$category_id,
		);
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		if($user_date = $this->get_date($create_date))
			$value['create_date'] = $user_date;
		else
			$value['create_date'] = $date;
		parent::save($id, $value, 'edit',true,array('name'=>$title));
	}
	
	/*public function _admin($page=null, $count=null, $show='all'){
		parent::_admin($page, $count, $show, 'depth,title,id', 'category_id,title,id', 'create_date');
	}*/
	
	private function get_date($date){
		$user_date = '';
		if(isset($create_date['y']) && $create_date['y']!=='')
			$user_date.=$create_date['y'].'-';
		else return;
		if(isset($create_date['m']) && $create_date['m']!=='')
			$user_date.=$create_date['m'].'-';
		else return;
		if(isset($create_date['d']) && $create_date['d']!=='')
			$user_date.=$create_date['d'].' ';
		else return;
		if(isset($create_date['h']) && $create_date['h']!=='')
			$user_date.=$create_date['h'].':';
		else return;
		if(isset($create_date['i']) && $create_date['i']!=='')
			$user_date.=$create_date['i'].':';
		else return;
		if(isset($create_date['s']) && $create_date['s']!=='')
			$user_date.=$create_date['s'];
		else return;
		return $user_date;
	}
	
	public function &get_preview(&$text){
		$preview = '';
		if($text){
			if( ($pos = mb_strpos($text,$this->more_tag ))!==false)
				$preview = mb_substr($text, 0, $pos);
			elseif($this->preview_default_count){
				$strip_text = trim(strip_tags($text));
				$strip_text = str_replace(array("\n","\t","\r"), array(" "," "), $strip_text);
				$strip_text = preg_replace('%( +)|(&[a-z#0-9]+;)%', ' ', $strip_text);
				$insert_pos = $this->preview_default_count-mb_strpos($this->mb_strrev(mb_substr($strip_text,0,$this->preview_default_count),'utf8'),' ')-1;
				if($insert_pos){
					$preview = mb_substr($strip_text,0, $insert_pos);
					//$text = substr_replace($text, $this->more_tag, $insert_pos, 0);
				}
				else
					$preview = &$text;
			}
		}
		return $preview;
	}
	
	private function mb_strrev(&$text, $encoding = null)
	{
	    $funcParams = array($text);
	    if ($encoding !== null)
	        $funcParams[] = $encoding;
	    $length = call_user_func_array('mb_strlen', $funcParams);
	
	    $output = '';
	    $funcParams = array($text, $length, 1);
	    if ($encoding !== null)
	        $funcParams[] = $encoding;
	    while ($funcParams[1]--) {
	         $output .= call_user_func_array('mb_substr', $funcParams);
	    }
	    return $output;
	}
	
	public function remove($id=NULL){
		$param = array();
		if($id)
			$param['title'] = $this->_query->select('title')->from($this->module_name)->where('id',$id)->query1('title');
		parent::remove($id,$param);
	}
	
	//category
	public function save_category($id=NULL,$title=NULL,$insert_place=NULL,$condition = array()){
		if(!$title){
			$this->_message('category name must not be empty');
			$this->parent->redirect('admin.php?call='.$this->module_name.'.edit_category&id='.$id.'&insert_place='.$insert_place);
			return;
		}
		$value = array('title'=>$title,'translit_title'=>translit::transliterate($title));
		parent::save_category($id,$value,$insert_place);
	}
	
	public function get_news($title=false, $show='auto'){
		if(!$news = $this->_query->select('left,right')->from($this->_category_table_name)->where('translit_title',$this->_config('news_trans_title'))->query1())
			throw new my_exception('news category not found',array('translit_title'=>$this->_config('news_trans_title'))); 
		$this->get_category_base('translit_title', $title, true, $show, array('left',$news,'between'));
	}
	
	public function fill_empty_preview($all=false){
		$this->_query->select('id,text')->from($this->_table_name);
		if(!$all)
			$this->_query->where('preview',NULL)->_or('preview','');
		$items = $this->_query->query();
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		foreach($items as &$item){
			$this->_query->update($this->_table_name)->set(array(
				'preview' => $this->get_preview($item['text']),
				'edit_date' => $date
			))->where('id',$item['id'])->query1();
		}
	}
	
	public function _get_param_value($method_name,$param_name){
		switch($method_name){
			case 'get':{
				switch($param_name){
					case 'show_title':{
						return array('true'=>'+','false'=>'-');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'edit':{
				switch($param_name){
					case 'id':{
						return $this->_query->select('id,title')->from($this->module_name)->query2assoc_array('id','title');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get_by_title':{
				switch($param_name){
					case 'title':{
						return $this->_query->select('title,translit_title')->from($this->module_name)->where('draft',1,'!=')->query2assoc_array('translit_title','title');
						break;
					}
					case 'show_title':{
						return array('true'=>'+','false'=>'-');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			default:
				parent::_get_param_value($method_name,$param_name);
		}
	}
}

class article_config extends base_module_config{
	protected $callable_method = array(
		'get_by_title' =>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'fill_empty_preview,set_translit_title,set_translit_title_category' =>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
		'save' =>array(
			'create_date'=>FILTER_UNSAFE_RAW,
		)
	);
	
	protected $object = array(
		'article'=>array(
			'method'=>'get_by_title',
			'param'=>'title'
		),
		'article_category'=>array(
			'method'=>'get_category_by_title',
			'param'=>'title'
		),
	);
	
	protected $link = array(
		'admin_mode.edit'=>array(
			'right'=>'file.get_list&module=article'
		),
	);
	
	protected $include = array(
		'edit'=>
			'<link href="/module/article/admin.css" rel="stylesheet" type="text/css"/>
			<script src="/extensions/ckeditor/ckeditor.js" type="text/javascript"></script>
			<script src="/module/article/admin.js" type="text/javascript"></script>',
		'*'=>'<link href="/module/article/article.css" rel="stylesheet" type="text/css"/>',
	);
	
	public $has_item = true;
	public $has_category = true;
	public $close_nested_folder = 1;
	public $default_show_title = true;
	
	private $news_trans_title = 'Novosti';
	private $more_tag = '<!--more-->';
	protected $item_field = 'id,translit_title,title,category_id,preview';
}
?>