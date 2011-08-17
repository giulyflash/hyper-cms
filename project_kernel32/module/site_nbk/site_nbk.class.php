<?php

class site_nbk extends module{
	protected $config_class_name = 'site_nbk_config';
	
	public function _admin(){}
	
	public function get($order='num',$page=1,$count=NULL,$search=NULL,$filter=NULL, $column=NULL){
		if(!$column)
			$column = $this->_config('column');
		if(!$count)
			$count = $this->_config('page_count');
		$tablename = $this->_config('table');
		$field = $this->_config('field');
		$redirect_params = array();
		if($column && is_array($column)){
			$column_str = '';
			foreach($field as $field_name=>&$field_value)
				if(isset($column[$field_name]))
					$column_str.='1';
				else
					$column_str.='0';
			while(substr($column_str,-1,1)=='0')
				$column_str = substr($column_str,0,-1);
			$redirect_params['column'] = $column_str;
		}
		else{
			$select_str = 'SELECT `id`';
			$i = 0;
			foreach(array_keys($field) as $field_name){
				if($chr = substr($column,$i,1))
					$select_str.= ', '.(isset($field[$field_name]['field'])?$field[$field_name]['field']:$field_name);
				else
					unset($field[$field_name]);
				$i++;
			}
		}
		$this->_query->injection($select_str)->from($tablename);
		if($search){
			$this->_query->where('num',$search,'like');
			$this->_query->_or('account',$search,'like');
			$this->_query->_or('street',$search,'like');
			$this->_query->_or('house',$search,'like');
			$this->_query->_or('flat',$search,'like');
			$this->_query->_or('owner',$search,'like');
			$this->_query->_or('acc_comm',$search,'like');
			$this->_query->_or('debt',$search,'like');
			$this->_query->_or('balance',$search,'like');
			$this->_query->_or('charges',$search,'like');
			$this->_query->_or('control_summ',$search,'like');
			$this->_query->_or('comment',$search,'like');
		}
		if($filter){
			if(is_array($filter))
				$filter_is_array = true;
			else{
				$filter=json_decode($filter,true);
				$filter_is_array = false;
				if(!$filter)
					$this->_message('wrong filter');
			}
			if($filter){
				$date_pattern = $this->_config('date_pattern');
				$replace_patterd = $this->_config('replace_patterd');
				//
				$this->_query->injection(' WHERE 1=1 ');
				$field_conf = $this->_config('field');
				foreach(array_keys($filter) as $field_name){
					if(!isset($field_conf[$field_name]))
						throw new my_exception('undefined field',array('name'=>$field_name));
					switch($field_conf[$field_name]['type']){
						case 'string':
						case 'text':{
							if(strlen($filter[$field_name]))
								$this->_query->_and($field_name,$filter[$field_name],'like');
							else
								unset($filter[$field_name]);
							break;
						}
						case 'bool':{
							if($filter[$field_name]!='')
								$this->_query->_and($field_name,$filter[$field_name]);
							else
								unset($filter[$field_name]);
							break;
						}
						default:{
							if(isset($filter[$field_name]['type']) && $filter[$field_name]['type']==1){
								if(isset($filter[$field_name]['min'])){
									if($field_conf[$field_name]['type']=='date')
										$filter[$field_name]['min'] = preg_replace($date_pattern, $replace_patterd, $filter[$field_name]['min']);
									$this->_query->_and($field_name,$filter[$field_name]['min']);
									unset($filter[$field_name]['max']);
								}
								else
									unset($filter[$field_name]);
							}
							else{
								unset($filter[$field_name]['type']);
								if(isset($filter[$field_name]['min']) && $filter[$field_name]['min']!==''){
									if($field_conf[$field_name]['type']=='date')
										$filter[$field_name]['min'] = preg_replace($date_pattern, $replace_patterd, $filter[$field_name]['min']);
									if(isset($filter[$field_name]['max']) && $filter[$field_name]['max']!==''){
										if($field_conf[$field_name]['type']=='date')
											$filter[$field_name]['max'] = preg_replace($date_pattern, $replace_patterd, $filter[$field_name]['max']);
										$this->_query->_and($field_name,array($filter[$field_name]['min'],$filter[$field_name]['max']),'between');
									}
									else{
										$this->_query->_and($field_name,$filter[$field_name]['min'],'>=');
										unset($filter[$field_name]['max']);
									}
								}
								else{
									if(isset($filter[$field_name]['max']) && $filter[$field_name]['max']!==''){
										if($field_conf[$field_name]['type']=='date')
											$filter[$field_name]['max'] = preg_replace($date_pattern, $replace_patterd, $filter[$field_name]['max']);
										$this->_query->_and($field_name,$filter[$field_name]['max'],'<=');
										unset($filter[$field_name]['min']);
									}
									else
										unset($filter[$field_name]);
								}
							}
						}
					}
				}
				if($filter_is_array)
					$redirect_params['filter'] = json_encode($filter);
			}
		}
		if($redirect_params){
			if($order)
				$redirect_params['order'] = $order;
			if($page && $page!=1)
				$redirect_params['page'] = $page;
			if($count && $count!=$this->_config('page_count'))
				$redirect_params['count'] = $count;
			if($search)
				$redirect_params['search'] = $search;
			if($filter && !isset($redirect_params['filter']))
				$redirect_params['filter'] = $filter;
			if($column && !isset($redirect_params['column']) && $column!=$this->_config('column'))
				$redirect_params['column'] = $column_str;
			$this->parent->redirect('/?call='.$this->module_name,$redirect_params);
		}
		//$this->_query->echo_sql = true;
		$this->_result = $this->_query->order($order)->query_page($page,$count);
		//impossible to do mor than 700 turns for loop in XSLT, have to do it there
		$this->_result['__max_page'] = ceil($this->_result['__num_rows']/$count);
		$page_select_html = '';
		for($i=1; $i<=$this->_result['__max_page']; $i++)
			$page_select_html.='<option value="'.$i.'" '.($i==$page?'selected="1"':'').'>'.$i.'</option>';
		if($page_select_html)
			$this->_result['_page_select_html'] = &$page_select_html;
		$this->_result['_default_page_count'] = &$this->_config('page_count');
		//$field = $this->_config('field');
		foreach($field as $field_name=>&$field_value){
			if(strpos($order,$field_name)!==false){
				if(strpos($order,$field_name.' desc')!==false){
					$to_replace = (strpos($order,$field_name.' desc,')!==false)?($field_name.' desc,'):($field_name.' desc');
					$field_value['order'] = trim(str_replace($to_replace, '', $order));
					$field_value['order'] = $field_name.($field_value['order']?',':'').$field_value['order'];
					$field_value['desc'] = 'desc';
				}
				else{
					$to_replace = (strpos($order,$field_name.',')!==false)?($field_name.','):$field_name;
					$field_value['order'] = trim(str_replace($to_replace, '', $order));
					$field_value['order'] = $field_name.' desc'.($field_value['order']?',':'').$field_value['order'];
					$field_value['desc'] = 'asc';
				}
				if(substr($field_value['order'],-1)==',')
					$field_value['order'] = substr($field_value['order'],0,-1);
			}
			else
				$field_value['order'] = $field_name.($order?',':'').$order;
		}
		$this->_result['_field'] = $field;
		$this->_result['field_raw'] = $this->_config('field');
		//
		if($filter = json_decode($filter,true))
			foreach($filter as $field=>&$value)
				$this->_result['field_raw'][$field]['value'] = $value;
	}
	
	public function mb_ucfirst($str, $enc = null){
		if($enc === null) $enc = 'utf8';
		return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}

	
	public function generate($is_default=false){
		if($is_default)
			return;
		if(empty($_FILES["path"]["name"])){
			$this->_message('Файл не был загружен.');
			return;
		}
		$file = new file($this->parent);
		$file->config->set('overwrite_if_exist',true);
		$file_list = $file->get_files($this->module_name);
		if(!$file_list){
			$this->_message('Файл н был загружен.');
			return;
		}
		if($file_list[0]['extension']!='xls'){
			$file->remove($file_list[0]['id'],false);
			$this->_message('Неверный тип файла: .'.$file_list[0]['extension']);
			return;
		}
		$doc_path = $file_list[0]['path'];
		global $output_index_error;
		$output_index_error = false;
		require_once ('extensions/PHPExcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load($doc_path);
		$sheet = $objPHPExcel->getActiveSheet();
		$default_num = $this->_query->injection('select max(num) as number from `'.$this->_config('table').'`')->query();
		$default_num = $default_num[0]['number'];
		$line = 2;
		//$this->_query->echo_sql = true;
		while($num = $sheet->getCell('A'.$line)->getValue()){
			$value = array(
				'num' => $num+$default_num,
				'account'  => $sheet->getCell('B'.$line)->getValue(),
				'street'  => $sheet->getCell('C'.$line)->getValue(),
				'house'  => $sheet->getCell('D'.$line)->getValue(),
				'flat'  => $sheet->getCell('E'.$line)->getValue(),
				'privatizated'  => ($sheet->getCell('F'.$line)->getValue()?1:0),
				'owner'  => $sheet->getCell('G'.$line)->getValue(),
				'acc_comm'  => $sheet->getCell('H'.$line)->getValue(),
				'debt'  => $sheet->getCell('I'.$line)->getValue(),
				'balance'  => $sheet->getCell('J'.$line)->getValue(),
				'charges'  => $sheet->getCell('K'.$line)->getValue(),
				'control_summ'  => $sheet->getCell('R'.$line)->getValue(),
				'debt_date'  => $this->convert_date($sheet->getCell('S'.$line)->getFormattedValue()),
				'pay_date'  => $this->convert_date($sheet->getCell('T'.$line)->getFormattedValue()),
				'comment' => ''
			);
			if($this->_query->select('id')->from($this->_config('table'))->where('account',$value['account'])->query1()){
				unset($value['num'], $value['comment']);
				$this->_query->update($this->_config('table'))->set($value)->where('account',$value['account'])->query1();
				$default_num--;
			}
			else{
				
				$this->_query->insert($this->_config('table'))->values($value)->query();
			}
			$line++;
		}
		
		$this->_message("Список должников сгенерирован, <a href='/?call=".$this->module_name."'>посмотреть</a>");
		$output_index_error = true;
	}
	
	private function convert_date($str, $format='%([0-9]+)-([0-9]+)-([0-9]+)$%',$output_format='20$3-$1-$2'){
		var_dump($str,$format,$output_format);
		if(!preg_match($format,$str,$re)){
			throw new my_exception('wrong data format',array('format'=>$format, 'date'=>$str));
		}
		if($re[1] && $re[2] && $re[3]){
			$date_str = preg_replace($format, $output_format, $str);
			$date = new DateTime($date_str);
			$output = $date->format($this->_config('db_date_format'));
		}
		else
			$output = "0000-00-00 00:00:00";
		return $output;
	}
	
	public function filter($filter=NULL,$order=NULL,$count=NULL,$column=NULL){
		$reverse_date_pattern = $this->_config('reverse_date_pattern');
		$reverse_replace_pattern = $this->_config('reverse_replace_pattern');
		$this->_result['field'] = $this->_config('field');
		if($filter = json_decode($filter,true))
			foreach($filter as $field=>&$value){
				if($this->_result['field'][$field]['type']=='date'){
					if(isset($value['min']))
						$value['min'] = preg_replace($reverse_date_pattern, $reverse_replace_pattern, $value['min']);
					if(isset($value['max']))
						$value['max'] = preg_replace($reverse_date_pattern, $reverse_replace_pattern, $value['max']);
				}
				$this->_result['field'][$field]['value'] = $value;
			}
	}
	
	public function edit($id=NULL, $filter=NULL,$order=NULL,$count=NULL,$column=NULL){
		$reverse_date_pattern = $this->_config('reverse_date_pattern');
		$reverse_replace_pattern = $this->_config('reverse_replace_pattern');
		$this->_result['field_raw'] = $this->_config('field');
		if($id){
			$field_value = $this->_query->select()->injection(',DATE_FORMAT(debt_date,"%d.%m.%Y") as debt_date_formatted, DATE_FORMAT(pay_date,"%d.%m.%Y") as pay_date_formatted ')->from($tablename = $this->_config('table'))->where('id',$id)->query1();
			//var_dump($field_value);
			if(!$field_value)
				$this->_message('record not found by id',array('id'=>$id));
			else
				foreach($field_value as $name=>&$value){
					if(isset($this->_result['field_raw'][$name])){
						if($this->_result['field_raw'][$name]['type']=='date')
							$value = preg_replace($reverse_date_pattern,$reverse_replace_pattern,$value);
						$this->_result['field_raw'][$name]['value'] = $value;
					}
				}
		}
	}
	
	public function save($id=NULL, $filter=NULL,$order=NULL,$count=NULL, $column=NULL){
		
	}
	
	public function column($filter=NULL,$order=NULL,$count=NULL, $column=NULL){
		$this->_result['field'] = $this->_config('field');
		$i=0;
		foreach($this->_result['field'] as $field_name=>&$field){
			if(substr($column,$i,1))
				$field['active'] = 1;
			$i++;
		}
	}
}

class site_nbk_config extends module_config{
	protected $callable_method=array(
		'generate,generate_works,_admin,edit,edit_comment,edit_account_comment,remove,get,filter,column'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
		'get,filter,column'=>array(
			'filter'=>'_disable_filter',
			'column'=>'_disable_filter',
		),
	);
	
	protected $include=array(
		'get,filter,edit,column'=>'<link href="/module/site_nbk/index.css" rel="stylesheet" type="text/css"/>',
		'get,filter,edit'=>'<link href="/module/site_nbk/index.css" rel="stylesheet" type="text/css"/>
			<script src="/module/site_nbk/list.js"></script>',
		'filter,edit'=>'<link rel="stylesheet" href="/extensions/datapicker/jquery.ui.all.css">
			<script src="/extensions/datapicker/jquery.ui.core.js"></script>
			<script src="/extensions/datapicker/jquery.ui.widget.js"></script>
			<script src="/extensions/datapicker/jquery.ui.datepicker.js"></script>
			<script src="/extensions/datapicker/jquery.ui.datepicker-ru.js"></script>',
	);
	
	protected $field = array(
		'num'=>array(
			'title'=>'№ п/п',
			'type'=>'int',
			'field'=>'num',
		),
		'account'=>array(
			'title'=>'Лицевой счет',
			'type'=>'string',
		),
		'street'=>array(
			'title'=>'Улица',
			'type'=>'string',
		),
		'house'=>array(
			'title'=>'Дом',
			'type'=>'string',
		),
		'flat'=>array(
			'title'=>'Квартира',
			'type'=>'string',
		),
		'privatizated'=>array(
			'title'=>'Приватизация',
			'type'=>'bool',
		),
		'owner'=>array(
			'title'=>'Владелец/Квартиросъемщик',
			'type'=>'string',
		),
		'acc_comm'=>array(
			'title'=>'Комментарий к лицевому счету',
			'type'=>'text',
		),
		'debt'=>array(
			'title'=>'Долг на момент контроля',
			'type'=>'float',
		),
		'balance'=>array(
			'title'=>'Остаток на кон.мес. на момент контроля',
			'type'=>'float',
		),
		'charges'=>array(
			'title'=>'Начисления',
			'type'=>'float',
		),
		'control_summ'=>array(
			'title'=>'Оплата<=суммы контроля',
			'type'=>'float',
		),
		'debt_date'=>array(
			'title'=>'Месяц начала задолженности',
			'type'=>'date',
			'field'=>'DATE_FORMAT(debt_date,"%m.%Y") as debt_date',
		),
		'pay_date'=>array(
			'title'=>'Дата платежа',
			'type'=>'date',
			'field'=>'DATE_FORMAT(pay_date,"%m.%Y") as pay_date',
		),
		'comment'=>array(
			'title'=>'Комментарий',
			'type'=>'text',
		),
	);
	
	protected $column = '111110101';
	
	//protected $default_method = '_admin';
	protected $page_count = 25;
	protected $table = 'debtor_list';
	protected $db_date_format = 'Y-m-d H:i:s';
	
	protected $date_pattern = '%([0-9]+)\.([0-9]+)\.([0-9]+)$%';
	protected $replace_patterd = '$3-$2-$1';
	protected $reverse_date_pattern = '%([0-9]+)\-([0-9]+)\-([0-9]+).*%';
	protected $reverse_replace_pattern = '$3.$2.$1';
}
?>