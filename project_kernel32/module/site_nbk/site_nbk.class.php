<?php

class site_nbk extends module{
	protected $config_class_name = 'site_nbk_config';
	
	public function _admin(){}
	
	public function get($order='num',$page=1,$count=NULL,$search=NULL,$filter=NULL, $column=NULL, $export=NULL){
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
				if((int)substr($column,$i,1)){
					$select_str.= ', '.(isset($field[$field_name]['field'])?$field[$field_name]['field']:$field_name);
					$field[$field_name]['selected'] = 1;
				}
				$i++;
			}
		}
		//$this->_query->echo_sql = true;
		$this->_query->injection($select_str)->from($tablename);
		$this->_query->injection(' WHERE 1=1');
		if($search){
			if(isset($_POST['search']))
				//$redirect_params['search'] = iconv("cp1251", "utf-8", $search);//$search;
				$redirect_params['search'] = $search;
			else{
				$i=0;
				foreach($field as $field_name=>&$field_value){
					if(!$i)
						$this->_query->_and($field_name,$search,'like','`',true);
					elseif(isset($field_value['type']) && $field_value['type']=='date'){}
					else
						$this->_query->_or($field_name,$search,'like');
					$i++;
				}
				$this->_query->close_bracket();
			}
		}
		if($filter && !$redirect_params){
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
				$field_conf = $this->_config('field');
				foreach(array_keys($filter) as $field_name){
					if(!isset($field_conf[$field_name]))
						throw new my_exception('undefined field',array('name'=>$field_name));
					switch($field_conf[$field_name]['type']){
						case 'string':
						case 'string_parted':
						case 'text':{
							if(strlen($filter[$field_name])){
								$this->_query->_and($field_conf[$field_name]['type']=='string_parted'?"CONCAT($field_name,{$field_name}_str)":$field_name,$filter[$field_name],'like','');
								$field[$field_name]['filter'] = $filter[$field_name];
							}
							else
								unset($filter[$field_name]);
							break;
						}
						case 'bool':{
							if($filter[$field_name]!=''){
								$this->_query->_and($field_name,$filter[$field_name]);
								$field[$field_name]['filter'] = $filter[$field_name];
							}
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
									$field[$field_name]['filter']['type'] = 1;
									$field[$field_name]['filter']['min'] = $filter[$field_name]['min'];
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
										$field[$field_name]['filter']['max'] = $filter[$field_name]['max'];
										$this->_query->_and($field_name,array($filter[$field_name]['min'],$filter[$field_name]['max']),'between');
									}
									else{
										$this->_query->_and($field_name,$filter[$field_name]['min'],'>=');
										unset($filter[$field_name]['max']);
									}
									$field[$field_name]['filter']['min'] = $filter[$field_name]['min'];
								}
								else{
									if(isset($filter[$field_name]['max']) && $filter[$field_name]['max']!==''){
										if($field_conf[$field_name]['type']=='date')
											$filter[$field_name]['max'] = preg_replace($date_pattern, $replace_patterd, $filter[$field_name]['max']);
										$this->_query->_and($field_name,$filter[$field_name]['max'],'<=');
										$field[$field_name]['filter']['max'] = $filter[$field_name]['max'];
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
		if($redirect_params)
			$this->parent->redirect('/?call='.$this->module_name,$this->get_redirect_params($order,$page,$count,$search,$filter,$column,$export,$redirect_params));
		$this->_query->set_sql(str_replace('WHERE 1=1 AND', 'WHERE ', $this->_query->get_sql()));
		$this->_query->order($order);
		if($export){
			$result = $this->_query->query();
			require_once ('extensions/PHPExcel/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$sheet=$objPHPExcel->getActiveSheet();
			//
			$col = 0;
			$cellStyle = new PHPExcel_Style();
			$cellStyle = array('borders' => array(	
				'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'bottom'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
			));
			$headStyle = array(
				'borders' => array(
					'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'bottom'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
				),
				'font' => array(
					'italic' => true,
					'bold' => true,
				)
			);
			foreach($field as &$item)
				if(isset($item['selected'])){
					$sheet->setCellValueByColumnAndRow($col, 1, $item['title']);
					//$sheet->getStyleByColumnAndRow($col,1)->applyFromArray($headStyle);
					$col++;
				}
			foreach($result as $num=>&$item){
				$col = 0;
				foreach($item as $field_name=>&$value)
					if($field_name!='id' && substr($field_name,-5)!='__src'){
						$sheet->setCellValueByColumnAndRow($col, $num+2, $value);
						//$sheet->getStyleByColumnAndRow($col,$num+2)->applyFromArray($cellStyle);
						$col++;
					}
			}
			//
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			ob_end_clean();
			$cur_date = new DateTime();
			$file_name='Report_'.(($export==1)?$cur_date->format('Y-m-d_H-i-s'):translit::transliterate($export)).'.xls';
			header('Content-Type: application/ms-excel');
			header("Content-Disposition: attachment;filename=$file_name");
			$objWriter->save('php://output');
			$objPHPExcel->disconnectWorksheets();
			unset($sheet);
			unset($objPHPExcel);
			die;
		}
		else
			$this->_result = $this->_query->query_page($page,$count);
		//impossible to do mor than 700 turns for loop in XSLT, have to do it there
		$this->_result['__max_page'] = ceil($this->_result['__num_rows']/$count);
		$page_select_html = '';
		for($i=1; $i<=$this->_result['__max_page']; $i++)
			$page_select_html.='<option value="'.$i.'" '.($i==$page?'selected="1"':'').'>'.$i.'</option>';
		if($page_select_html)
			$this->_result['_page_select_html'] = &$page_select_html;
		$this->_result['_default_page_count'] = &$this->_config('page_count');
		
		foreach($field as $field_name=>&$field_value){
			if($filter && isset($filter[$field_name]))
				$field_value['value'] = $filter[$field_name];
			if(isset($field_value['selected']))
				if(!$this->set_sort($field_name, $field_value, $order, true))
					$this->set_sort($field_name, $field_value, $order, false);
		}
		$this->_result['_field'] = $field;
	}
	
	private function set_sort($field_name, &$field_value, $order, $desc=false){
		$result = false;
		$search_name = 'order_'.($desc?'desc':'asc');
		$search = (isset($field_value[$search_name])) ? $field_value[$search_name] : ($field_name.($desc?' desc':''));
		if(strpos($order,$search)!==false){
			if(strpos($order,$search.',')!==false)
				$search.= ',';
			$field_value['order'] = trim(str_replace($search, '', $order));
			$field_value['desc'] = $desc?'desc':'asc';
			$result = true;
		}
		else
			$field_value['order'] = $order;
		$replacement_name = 'order_'.($desc?'asc':'desc');
		$replacement = (isset($field_value[$replacement_name])) ? $field_value[$replacement_name] : ($field_name.(($desc || !$result)?'':' desc'));
		if($result || !$desc)
			$field_value['order'] = $replacement.($field_value['order']?',':'').$field_value['order'];
		return $result;
	}
	
	private function get_redirect_params($order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL, $redirect_params=array()){
		if($order && $order!='num')
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
			$redirect_params['column'] = $column;
		if($export)
			$redirect_params['export'] = $export;
		return $redirect_params;
	}
	
	public function mb_ucfirst($str, $enc = null){
		if($enc === null) $enc = 'utf8';
		return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}

	
	public function import($is_default=true){
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
			$house = $this->num_str2array($sheet->getCell('D'.$line)->getValue());
			$flat = $this->num_str2array($sheet->getCell('E'.$line)->getValue());
			$value = array(
				'num' => $num+$default_num,
				'account'  => $sheet->getCell('B'.$line)->getValue(),
				'street'  => $sheet->getCell('C'.$line)->getValue(),
				'house'  => $house[1],
				'house_str'  => $house[2],
				'flat'  => $flat[1],
				'flat_str'  => $flat[2],
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
	
	private function num_str2array($str){
		if(preg_match('%([0-9]*)(.*)%', $str, $result))
			return $result;
		else
			throw new my_exception('wrong number format',array('number'=>$str));
	}
	
	private function convert_date($str, $format='%([0-9]+)-([0-9]+)-([0-9]+)$%',$output_format='20$3-$1-$2'){
		if(!preg_match($format,$str,$re) || !($re[1] && $re[2] && $re[3])){
			$output = "0000-00-00 00:00:00";
		}
		else{
			$date_str = preg_replace($format, $output_format, $str);
			$date = new DateTime($date_str);
			$output = $date->format($this->_config('db_date_format'));
		}
		return $output;
	}
	
	public function edit($id=NULL, $order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL){
		$this->_result['_field'] = $this->_config('field');
		if($id){
			$select_str = 'SELECT `id`';
			foreach($this->_result['_field'] as $field_name=>&$field)
				$select_str.= ', '.(isset($field['field'])?$field['field']:('`'.$field_name.'`'));
			$field_value = $this->_query->injection($select_str)->from($tablename = $this->_config('table'))->where('id',$id)->query1();
			if(!$field_value)
				$this->_message('record not found by id',array('id'=>$id));
			else
				foreach($field_value as $name=>&$value)
					$this->_result['_field'][$name]['value'] = $value;
		}
	}
	
	public function save($value=NULL, $id=NULL, $order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL, $redirect=true){
		//var_dump($value);die;
		$date_pattern = $this->_config('date_pattern');
		$replace_pattern = $this->_config('replace_pattern');
		$field = $this->_config('field');
		foreach($value as $name=>&$val)
			if(isset($field[$name]['type'])){
				if($field[$name]['type']=='date')
					$val=$this->convert_date($val,$date_pattern,$replace_pattern);
				elseif($field[$name]['type']=='string_parted'){
					$val_temp = $this->num_str2array($val);
					$val = $val_temp[1];
					$value[$name.'_str'] = $val_temp[2];
				}
			}
		$tablename = $this->_config('table');
		if($id){
			$this->_query->update($tablename)->set($value)->limit(1)->execute();
			$this->_message('record edited successfully');
		}
		else{
			$this->_query->insert($tablename)->values($value)->execute();
			$this->_message('record added successfully');
		}
		if($redirect)
			$this->parent->redirect('/?call='.$this->module_name,$this->get_redirect_params($order,$page,$count,$search,$filter,$column,$export));
	}
}

class site_nbk_config extends module_config{
	protected $callable_method=array(
		'import,_admin,edit,remove,get,save'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
		'get,edit,save'=>array(
			'filter'=>'_disable_filter',
			'column'=>'_disable_filter',
			'value'=>'_disable_filter',
		),
	);
	
	protected $include=array(
		'get,filter,edit,column'=>'<link href="/module/site_nbk/index.css" rel="stylesheet" type="text/css"/>',
		'get,filter,edit'=>'<link href="/module/site_nbk/index.css" rel="stylesheet" type="text/css"/>
			<script src="/module/site_nbk/list.js"></script>',
		'get,edit'=>'<link rel="stylesheet" href="/extensions/datapicker/jquery.ui.all.css">
			<script src="/extensions/datapicker/jquery.ui.core.js"></script>
			<script src="/extensions/datapicker/jquery.ui.widget.js"></script>
			<script src="/extensions/datapicker/jquery.ui.datepicker.js"></script>
			<script src="/extensions/datapicker/jquery.ui.datepicker-ru.js"></script>',
		'edit'=>'<script type="text/javascript" src="extensions/ckeditor/ckeditor.js"></script>
			<script src="/module/site_nbk/edit.js"></script>'
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
			'type'=>'string_parted',
			'field'=>'CONCAT(house,house_str) as house, house as house__src, house_str as house_str__src',
			'order_asc'=>'house__src,house_str__src',
			'order_desc'=>'house__src desc,house_str__src desc',
		),
		'flat'=>array(
			'title'=>'Квартира',
			'type'=>'string_parted',
			'field'=>'CONCAT(flat,flat_str) as flat, flat as flat__src, flat_str as flat_str__src',
			'order_asc'=>'flat__src,flat_str__src',
			'order_desc'=>'flat__src desc,flat_str__src desc',
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
			'field'=>'DATE_FORMAT(debt_date,"%d.%m.%Y") as debt_date',
		),
		'pay_date'=>array(
			'title'=>'Дата платежа',
			'type'=>'date',
			'field'=>'DATE_FORMAT(pay_date,"%d.%m.%Y") as pay_date',
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