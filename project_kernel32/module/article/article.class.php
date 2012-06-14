<?php
class article extends base_module{
	protected $config_class_name = 'article_config';
	protected $preview_default_count = 200;
	protected $show_module_path = false;
	
	private $more_tag = '<!--more-->';

	public function get($id = NULL, $show_title=false){
		if(!$id)
			$id = $this->_config('default_article_name');
		$select = $this->_config('item_single_field');
		if($show_title === false)
			$show_title = $this->_config('default_show_title');
		$this->_get($this->id_field, $id, $select);
	}
	
	/*public function get_category($id=false, $show='auto'){
		$this->get_category('translit_title',$id,true,'auto',NULL,array('draft',0));
	}*/
	
	public function save($id=NULL, $title=NULL, $text=NULL, $keyword=NULL, $description=NULL, $draft=NULL, $insert_place=NULL, $create_date=array()){
		$date = new DateTime();
		$value = array(
			'id'=>$id,
			'title'=>$title,
			'text'=>&$text,
			'preview'=>$this->get_preview($text),
			'keyword'=>$keyword,
			'description'=>$description,
			'draft'=>($draft)?$draft:0,
			'category_id'=>$insert_place?$insert_place:NULL,
			'create_date'=>($user_date = $this->get_date($create_date))?$user_date:$date->format('Y-m-d H:i:s'),
		);
		$this->_save($id, $value);
	}

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
	
	//category
	public function save_category($id=NULL,$title=NULL,$article_redirect=NULL,$insert_place=NULL,$draft=0){
		$this->_save_category($id,array('title'=>$title,'article_redirect'=>($article_redirect?$article_redirect:NULL),'draft'=>$draft),$insert_place);
	}
	
	public function edit_category($id=NULL, $insert_place=NULL){
		parent::edit_category($id,$insert_place);
		$this->_result['article'] = $this->_get_param_value('get','title','title');
	}
	
	public function get_news($title=false, $show='auto'){
		//TODO
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
	
	public function _get_param_value($method_name,$param_name,$order=NULL){
		switch($method_name){
			case 'edit':{
				switch($param_name){
					case 'id':{
						return $this->_query->select($this->id_field.',title')->from($this->module_name)->query2assoc_array($this->id_field,'title');
						break;
					}
					default:
						parent::_get_param_value($method_name,$param_name);
				}
				break;
			};
			case 'get':{
				switch($param_name){
					case 'id':{
						$this->_query->select($this->id_field.',title')->from($this->module_name)->where('draft',1,'!=');
						if($order)
							$this->_query->order($order);
						return $this->_query->query2assoc_array($this->id_field,'title');
						break;
					}
					case 'show_title':{
						return array('1'=>'+','0'=>'-');
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
	
	public function create_empty_menu(){
		
	}
}

class article_config extends base_module_config{
	protected $callable_method = array(
		'get' =>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
		'fill_empty_preview,set_translit_title,set_translit_title_category,create_empty_menu' =>array(
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
			'method'=>'get',
			'param'=>'id',
			//'db_param'=>'translit_title',
		),
		'article_category'=>array(
			'method'=>'get_category',
			'type'=>'category',
			'param'=>'id',
			//'db_param'=>'translit_title',
		),
	);
	
	protected $link = array(
		'admin_mode.edit'=>array(
			'right'=>'file.get_list&module=article'
		),
	);
	
	protected $include = array(
		'edit'=>
			'<script src="/extensions/ckeditor/ckeditor.js" type="text/javascript"></script>
			<script src="/module/article/admin.js" type="text/javascript"></script>',
		'*'=>'<link href="/module/article/article.css" rel="stylesheet" type="text/css"/>',
		'admin_mode.*'=>
			'<link href="/module/article/admin.css" rel="stylesheet" type="text/css"/>'
	);
	
	public $has_item = true;
	public $has_category = true;
	public $close_nested_folder = 1;
	public $default_show_title = true;
	
	private $news_trans_title = 'Novosti';
	private $more_tag = '<!--more-->';
	protected $item_field = 'id,title,category_id,preview,link';
	protected $item_single_field='id,category_id,text,link';
	protected $category_field = 'id,title,left,right,depth,article_redirect,draft,link';
}
?>