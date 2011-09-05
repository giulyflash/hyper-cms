<?php

class debtor_list extends module{
	protected $config_class_name = 'debtor_list_config';
	
	public function _admin(){}
	
	public function get($order=NULL, $page=1, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $zero_debt=NULL, $show_sum=NULL, $export=NULL, $table_name=NULL){
		//$this->_query->echo_sql = true;
		if($table_name=='debtor_log' && $_SESSION['user_info']['login']!='admin')
			throw new my_exception('only for admin');
		$redirect_params = $this->check_table($table_name, $field, $count, $order, $column, true);
		$parted_string_src_posfix = $this->_config('parted_string_src_posfix');
		$parted_string_str_posfix = $this->_config('parted_string_str_posfix');
		$sum_select = '';
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
					$select_str.= $this->_config('order_separator');
					if(isset($field[$field_name]['field']))
						$select_str.=$field[$field_name]['field'];
					elseif(isset($field[$field_name]['type'])){
						if($field[$field_name]['type']=='string_parted')
							$select_str.= "CONCAT($field_name,{$field_name}{$parted_string_str_posfix}) as $field_name, $field_name as {$field_name}{$parted_string_src_posfix}, {$field_name}{$parted_string_str_posfix} as {$field_name}{$parted_string_str_posfix}{$parted_string_src_posfix}";
						elseif($field[$field_name]['type']=='int')
							$select_str.= "CONCAT('<span class=\"{$field[$field_name]['type']}\">', FORMAT($field_name,0,'ru_RU'), '</span>') as $field_name";
						elseif($field[$field_name]['type']=='float')
							$select_str.= "CONCAT('<span class=\"{$field[$field_name]['type']}\">', FORMAT($field_name,2,'ru_RU'), '</span>') as $field_name";
						else
							$select_str.=$field_name;
					}
					else
						$select_str.=$field_name;
					if($show_sum && $field_name!='num' && isset($field[$field_name]['type']) && in_array($field[$field_name]['type'],array('int','float')))
						$sum_select.= $this->_config('order_separator').' sum(`'.$field_name.'`) as '.$field_name;
					$field[$field_name]['selected'] = 1;
				}
				$i++;
			}
		}
		$this->_query->injection($select_str)->from($table_name);
		$this->_query->injection(' WHERE 1=1');
		if($table_name==$this->_config('default_table') && !$zero_debt)
			$this->_query->_and('debt',0,'>');
		if($search){
			if(isset($_POST['search']))
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
				$field_conf = $field;//$this->_config('field');
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
						case 'enum':{
							if(!empty($filter[$field_name]) && is_array($filter[$field_name])){
								foreach($field[$field_name]['val'] as &$enum_value){
									$enum_value = array('value' => $enum_value);
									foreach($filter[$field_name] as &$filter_temp_value)
										if($enum_value['value']==$filter_temp_value)
											$enum_value['selected'] = 1;
								}
								$field[$field_name]['filter']=implode($this->_config('enum_separator'), $filter[$field_name]);
								$this->_query->_and($field_name,$filter[$field_name],'like');
							}
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
			$this->parent->redirect('/?call='.$this->module_name,$this->get_redirect_params($page,$search,$filter,$export,$zero_debt,$show_sum,$redirect_params));
		$this->_query->set_sql(str_replace(array('WHERE 1=1 AND', 'WHERE 1=1'), array('WHERE',''), $this->_query->get_sql()));
		$temp_sql = $this->_query->sql;
		if($sum_select){
			$this->_query->set_sql();
			$sum_value = $this->_query->injection(preg_replace('%SELECT .* FROM%', 'SELECT '.substr($sum_select, 2).' FROM', $temp_sql))->query();
			foreach($sum_value[0] as $field_name=>$sum)
				$field[$field_name]['sum'] = $field[$field_name]['type']=='float'?number_format($sum, 2, ',', ' '):number_format($sum, 0, ',', ' ');
		}
		$this->_query->set_sql($temp_sql);
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
			foreach($field as &$item)
				if(isset($item['selected'])){
					$sheet->setCellValueByColumnAndRow($col, 1, $item['title']);
					$col++;
				}
			foreach($result as $num=>&$item){
				$col = 0;
				foreach($item as $field_name=>&$value)
					if($field_name!='id' && substr($field_name,-5)!=$parted_string_src_posfix){
						$sheet->setCellValueByColumnAndRow($col, $num+2, strip_tags($value));
						$col++;
					}
			}
			$cellStyle = array('borders' => array(
				'allborders'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
			));
			$headStyle = array(
				'font' => array(
					'italic' => true,
					'bold' => true,
				)
			);
			$sumStyle = array(
				'font' => array(
					'bold' => true,
				)
			);
			$sheet->getStyle('A1:'.chr(64+$col).'1')->applyFromArray($headStyle);
			$sheet->getStyle('A1:'.chr(64+$col).($num+=2))->applyFromArray($cellStyle);
			if($show_sum){
				$col_sum = 0;
				foreach(array_keys($result[0]) as $key)
					if(!empty($field[$key]['selected'])){
						if(isset($field[$key]['sum']))
							$sheet->setCellValueByColumnAndRow($col_sum, $num+1, $field[$key]['sum']);
						$col_sum++;
					}
				$sheet->getStyle("A$num:".chr(64+$col).(++$num))->applyFromArray($sumStyle);
			}
			$cur_date = new DateTime();
			$sheet->setCellValueByColumnAndRow(0, $num+2, $cur_date->format('d.m.Y H:i'));
			//
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			ob_end_clean();
			$file_name='Report_'.(($export==1)?$cur_date->format('Y-m-d_H-i-s'):translit::transliterate($export)).'.xls';
			header('Content-Type: application/ms-excel');
			header("Content-Disposition: attachment;filename=$file_name");
			$objWriter->save('php://output');
			$objPHPExcel->disconnectWorksheets();
			unset($sheet, $objPHPExcel);
			$this->log_event('export');
			die;
		}
		else
			$this->_result = $this->_query->query_page($page,$count);
		if($this->_result['__max_page'] && $page>$this->_result['__max_page']){
			$redirect_params = $this->check_table($table_name, $field, $count, $order, $column);
			$page=$this->_result['__max_page'];
			//var_dump($this->get_redirect_params($page,$search,$filter,$export,$zero_debt,$redirect_params));die;
			$this->parent->redirect('/?call='.$this->module_name,$this->get_redirect_params($page,$search,$filter,$export,$zero_debt,$show_sum,$redirect_params));
		}
		//impossible to do mor than 700 turns for loop in XSLT, have to do it there
		$this->_result['_default_count'] = $this->_default_count;
		$this->_result['_count'] = $count;
		$page_select_html = '';
		for($i=1; $i<=$this->_result['__max_page']; $i++)
			$page_select_html.='<option value="'.$i.'" '.($i==$page?'selected="1"':'').'>'.$i.'</option>';
		if($page_select_html)
			$this->_result['_page_select_html'] = &$page_select_html;
		
		$enum_separator = $this->_config('enum_separator');
		
		foreach($field as $field_name=>&$field_value){
			if($filter && isset($filter[$field_name])){
				$field_value['value'] = $filter[$field_name];
				if(isset($field_value['type'])){
					if($field_value['type']=='int')
						$field_value['value'] = number_format($field_value['value'], 0, '.', '');
					elseif($field_value['type']=='float')
						$field_value['value'] = number_format($field_value['value'], 2, '.', '');
				}
			}
			if(isset($field_value['selected']))
				if(!$this->set_sort($field_name, $field_value, $order, true))
					$this->set_sort($field_name, $field_value, $order, false);
			if(isset($field_value['type']) && $field_value['type']=='enum')
				foreach($field_value['val'] as &$enum_value)
					if(!is_array($enum_value))
						$enum_value = array('value' => $enum_value);
		}
		$this->_result['_field'] = $field;
	}

	private function check_table(&$table_name, &$field=NULL, &$count=NULL, &$order=NULL, &$column=NULL, $is_get = false){
		$redirect_params=array();
		//table_name
		if($table_name && $table_name != $this->_config('default_table')){
			if(!$is_get)
				$redirect_params['table_name'] = $table_name;
		}
		else
			$table_name = $this->_config('default_table');
		//field
		$field = $field?$field:$this->_config($table_name.'_field');
		if(!$field)
			throw new my_exception('unallowed table',array('table',$table_name));
		//order
		$default_order = $this->_config($table_name.'_order');
		if(!$default_order)
			$default_order = 'id';
		if($order && $order!=$default_order){
			if(!$is_get)
				$redirect_params['order'] = $order;
		}
		else
			$order = $default_order;
		//column
		$default_column = $this->_config($table_name.'_column');
		if(!$default_column){
			$default_column = '';
			for($i = 0; $i<count($field); $i++)
				$default_column.=1;
		}
		if($column && $column!=$default_column){
			if(!$is_get)
				$redirect_params['column'] = $column;
		}
		else
			$column = $default_column;
		//count
		$this->_default_count = $this->_config($table_name.'_page_count');
		if(!$this->_default_count)
			$this->_default_count = $this->_config('default_page_count');
		if($count && $count!=$this->_default_count){
			if(!$is_get || isset($_POST['count']))
				$redirect_params['count'] = $count;
		}
		else
			$count = $this->_default_count;
		//var_dump($redirect_params); die;
		return $redirect_params;
	}
	
	private function set_sort($field_name, &$field_value, $order, $desc=false){
		$result = false;
		$desc_str = $desc?' desc':'';
		$parted_string_src_posfix = $this->_config('parted_string_src_posfix');
		$parted_string_str_posfix = $this->_config('parted_string_str_posfix');
		$order_separator = $this->_config('order_separator');
		$search = (isset($field_value['type']) && $field_value['type']=='string_parted') ?
			($field_name.$parted_string_src_posfix.$desc_str.$order_separator.$field_name.$parted_string_str_posfix.$parted_string_src_posfix.$desc_str):
			($field_name.$desc_str);
		if(strpos($order,$search)!==false){
			if(strpos($order,$search.$order_separator)!==false)
				$search.= $order_separator;
			$field_value['order'] = trim(str_replace($search, '', $order));
			$field_value['desc'] = $desc?'desc':'asc';
			$result = true;
		}
		else
			$field_value['order'] = $order;
		$replacement_name = 'order_'.($desc?'asc':'desc');
		$desc_str = ($desc || !$result)?'':' desc';
		$replacement = (isset($field_value['type']) && $field_value['type']=='string_parted') ?
			($field_name.$parted_string_src_posfix.$desc_str.$order_separator.$field_name.$parted_string_str_posfix.$parted_string_src_posfix.$desc_str):
			($field_name.$desc_str); 
		if($result || !$desc)
			$field_value['order'] = $replacement.($field_value['order']?$order_separator:'').$field_value['order'];
		return $result;
	}
	
	private function get_redirect_params($page=NULL, $search=NULL, $filter=NULL, $export=NULL, $zero_debt=NULL, $show_sum=NULL, $redirect_params=array()){
		if($page && $page!=1)
			$redirect_params['page'] = $page;
		if($search && ($this->method_name!='get' && !isset($redirect_params['search']) || $this->method_name=='get' && isset($_POST['search'])))
			$redirect_params['search'] = $search;
		if($filter && !isset($redirect_params['filter']))
			$redirect_params['filter'] = $filter;
		if($export)
			$redirect_params['export'] = $export;
		if($zero_debt)
			$redirect_params['zero_debt'] = $zero_debt;
		if($show_sum)
			$redirect_params['show_sum'] = $show_sum;
		return $redirect_params;
	}
	
	public function mb_ucfirst($str, $enc = null){
		if($enc === null) $enc = 'utf8';
		return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}

	
	public function import($is_default=true, $redirect=true, $order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL, $zero_debt=NULL, $show_sum=NULL,  $table_name=NULL){
		if($is_default)
			return;
		if(empty($_FILES["path"]["name"])){
			$this->_message('file not uploaded');
			return;
		}
		//$redirect_params = $this->check_table($table_name,$field,$count,$order,$column);
		$this->check_table($table_name);
		//var_dump($table_name);
		$file = new file($this->parent);
		$file->config->set('overwrite_if_exist',true);
		$file_list = $file->get_files($this->module_name);
		if(!$file_list){
			$this->_message('file not uploaded');
			return;
		}
		if($file_list[0]['extension']!='xls'){
			$file->remove($file_list[0]['id'],false);
			$this->_message('wrong file type',array('ext'=>$file_list[0]['extension']));
			return;
		}
		$doc_path = $file_list[0]['path'];
		global $output_index_error;
		require_once ('extensions/PHPExcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		//
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize ' => '1024MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings))
			die('CACHEING ERROR');
		//
		$objPHPExcel = PHPExcel_IOFactory::load($doc_path);
		$sheet = $objPHPExcel->getActiveSheet();
		$default_num = $this->_query->injection('select max(num) as number from `'.$table_name.'`')->query();
		$default_num = $default_num[0]['number'];
		$line = 2;
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
			if($this->_query->select('id')->from($table_name)->where('account',$value['account'])->query1()){
				unset($value['num'], $value['comment']);
				$this->_query->update($table_name)->set($value)->where('account',$value['account'])->query1();
				$default_num--;
			}
			else{
				$this->_query->insert($table_name)->values($value)->query();
			}
			$line++;
		}
		$this->_message('debtor list generated');
		$objPHPExcel->disconnectWorksheets();
		unset($sheet, $objPHPExcel);
		$this->log_event($this->method_name);
		if($this->parent->_config('debug'))
			$this->_message('<a href="/?call='.$this->module_name.($this->parent->_config('debug')?'&_debug=1':'').'">Назад</a>');
		elseif($redirect)
			$this->parent->redirect('/?call='.$this->module_name);
		$this->_title = $this->parent->_config('default_page_title').' - импорт';
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
	
	public function edit($id=NULL, $order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL, $zero_debt=NULL, $show_sum=NULL,  $table_name=NULL){
		$field=NULL;
		$this->check_table($table_name, $field);
		$parted_string_src_posfix = $this->_config('parted_string_src_posfix');
		$parted_string_str_posfix = $this->_config('parted_string_str_posfix');
		if($id){
			$select_str = 'SELECT `id`';
			//FIXME parted fields there
			foreach($field as $field_name=>&$field_select){
				$select_str.= ', ';
				if(isset($field_select['field']))
					$select_str.= $field_select['field'];
				elseif(isset($field_select['type']) && $field_select['type']=='string_parted')
					$select_str.= "CONCAT($field_name,{$field_name}{$parted_string_str_posfix}) as $field_name, $field_name as {$field_name}{$parted_string_src_posfix}, {$field_name}{$parted_string_str_posfix} as {$field_name}{$parted_string_str_posfix}{$parted_string_src_posfix}";
				else
					$select_str.= '`'.$field_name.'`';
			}
			$field_value = $this->_query->injection($select_str)->from($table_name)->where('id',$id)->query1();
			if(!$field_value)
				$this->_message('record not found by id',array('id'=>$id));
			else
				foreach($field_value as $name=>&$value){
					if($name=='id')
						$field[$name] = $value;
					else
						$field[$name]['value'] = $value;
					if(isset($field[$name]['type']) && $field[$name]['type']=='enum'){
						$enum_separator = $this->_config('enum_separator');
						foreach($field[$name]['val'] as &$enum_value){
							$enum_value = array('value' => $enum_value);
							if($value){
								$enum_temp = explode($enum_separator, $value);
								foreach($enum_temp as &$filter_temp_value)
									if($enum_value['value']==$filter_temp_value)
										$enum_value['selected'] = 1;
							}
						}
					}
				}
		}
		else{
			foreach($field as $name=>&$value)
				if(isset($field[$name]['type']) && $field[$name]['type']=='enum'){
					$enum_separator = $this->_config('enum_separator');
					foreach($field[$name]['val'] as &$enum_value)
						$enum_value = array('value' => $enum_value);
				}
		}
		$this->_result['_field'] = &$field;
		$this->_title = $this->parent->_config('default_page_title').' - редактирование';
	}
	
	public function save($value=NULL, $id=NULL, $order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL, $zero_debt=NULL, $show_sum=NULL,  $table_name=NULL, $redirect=true){
		$date_pattern = $this->_config('date_pattern');
		$replace_pattern = $this->_config('replace_pattern');
		$field=NULL;
		$redirect_params = $this->check_table($table_name,$field,$count,$order,$column);
		foreach($value as $name=>&$val)
			if(isset($field[$name]['type'])){
				if($field[$name]['type']=='date')
					$val=$this->convert_date($val,$date_pattern,$replace_pattern);
				elseif($field[$name]['type']=='string_parted'){
					$val_temp = $this->num_str2array($val);
					$val = (int)$val_temp[1];
					if(!$val)
						unset($val);
					else
						$value[$name.'_str'] = $val_temp[2];
				}
				elseif($field[$name]['type']=='int')
					$val = (int)$val;
				elseif($field[$name]['type']=='float')
					$val = (float)$val;
				elseif($field[$name]['type']=='enum' && $val)
					$val = implode($this->_config('enum_separator'), $val);
				elseif($field[$name]['type']=='bool')
					$val = $val?1:0;
			}
		if(empty($value['num']))
			$value['num'] = $this->_query->injection('SELECT MAX(num) as num_max ')->from($table_name)->query1('num_max')+1;
		if($id){
			$this->_query->update($table_name)->set($value)->where('id',$id)->limit(1)->execute();
			$this->_message('edited successfuly',array('num'=>$value['num']));
			$this->log_event('edit', $id);
		}
		else{
			$this->_query->insert($table_name)->values($value)->execute();
			$this->_message('added successfully',array('num'=>$value['num']));
			$this->log_event('add',$this->_query->insert_id());
		}
		if($redirect)
			$this->parent->redirect('/?call='.$this->module_name,$this->get_redirect_params($page,$search,$filter,$export,$zero_debt,$show_sum,$redirect_params));
	}
	
	public function remove($id=NULL, $order=NULL, $page=NULL, $count=NULL, $search=NULL, $filter=NULL, $column=NULL, $export=NULL, $zero_debt=NULL, $show_sum=NULL,  $table_name=NULL, $redirect=true){
		$redirect_params = $this->check_table($table_name);
		if($id && $rec = $this->_query->select()->from($table_name)->where('id',$id)->query1()){
			$this->_query->delete()->from($table_name)->where('id',$id)->query1();
			if($table_name==$this->_config('log_table'))
				$this->_message('deleted successfully',array('num'=>$id));
			else{
				$this->_query->update($table_name)->injection(' SET `num`=`num`-1 ')->where('num',$rec['num'],'>')->_and('num',1,'>')->query();
				$this->_message('deleted successfully',array('num'=>$rec['num']));
				$this->log_event($this->method_name, $id);
			}
		}
		elseif($id)
			$this->_message('record not found by id',array('id'=>$id));
		if($redirect)
			$this->parent->redirect('/?call='.$this->module_name,$this->get_redirect_params($page,$search,$filter,$export,$zero_debt,$show_sum,$redirect_params));
	}
	
	public function clear($table_name=NULL){
		$this->check_table($table_name);
		if($_SESSION['user_info']['login']=='admin'){
			$this->_query->truncate($table_name)->execute();
			$this->_message('table clear');
		}
		else
			$this->_message('access denied');
		$redirect_params = array();
		if($table_name)
			$redirect_params['table_name'] = $table_name;
		$this->parent->redirect('/?call='.$this->module_name, $redirect_params);
	}
	
	private function log_event($event,$id=NULL){
		$date = new DateTime();
		$this->_query->insert($this->_config('log_table'))->values(array(
			'time'=>$date->format($this->_config('db_date_format')),
			'event'=>$event,
			'record_id'=>$id,
			'user'=>$_SESSION['user_info']['login'],
		))->execute();
	}
	
	public function statistics($event,$id=NULL){}
}

class debtor_list_config extends module_config{
	protected $output_config = true;
	
	protected $callable_method=array(
		'import,_admin,edit,remove,get,save,statistics,clear'=>array(
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
		'get,filter,edit,column'=>'<link href="/module/debtor_list/index.css" rel="stylesheet" type="text/css"/>',
		'get,filter,edit'=>'<link href="/module/debtor_list/index.css" rel="stylesheet" type="text/css"/>
			<script src="/module/debtor_list/list.js"></script>',
		'get,edit'=>'<link rel="stylesheet" href="/extensions/datapicker/jquery.ui.all.css">
			<script src="/extensions/datapicker/jquery.ui.core.js"></script>
			<script src="/extensions/datapicker/jquery.ui.widget.js"></script>
			<script src="/extensions/datapicker/jquery.ui.datepicker.js"></script>
			<script src="/extensions/datapicker/jquery.ui.datepicker-ru.js"></script>',
		'edit'=>'<script type="text/javascript" src="extensions/ckeditor/ckeditor.js"></script>
			<script src="/module/debtor_list/edit.js"></script>'
	);
	
	protected $debtor_list_field = array(
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
		),
		'flat'=>array(
			'title'=>'Квартира',
			'type'=>'string_parted',
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
		'type'=>array(
			'title'=>'Тип должнника',
			'type'=>'enum',
			'val'=>array(
				'алкоголик',
				'наркоман',
				'безработный',
			)
		),
		'comment'=>array(
			'title'=>'Комментарий',
			'type'=>'text',
		),
	);
	
	protected $debtor_log_field = array(
		'time'=>array(
			'title'=>'Время',
			'type'=>'date',
		),
		'event'=>array(
			'title'=>'Событие',
			'type'=>'string',
		),
		'record_id'=>array(
			'title'=>'Номер записи',
			'type'=>'id',
		),
		'user'=>array(
			'title'=>'Пользователь',
			'type'=>'float',
		)
	);
	protected $debtor_list_column = '111110101';
	public $default_page_count = 25;
	protected $debtor_log_page_count = 100;
	protected $debtor_list_order = 'num';
	
	public $parted_string_src_posfix = '__src';
	public $parted_string_str_posfix = '_str';
	
	protected $enum_separator = ', ';
	protected $order_separator = ', ';
	public $default_table = 'debtor_list';
	public $log_table = 'debtor_log';
	protected $db_date_format = 'Y-m-d H:i:s';
	
	protected $date_pattern = '%([0-9]+)\.([0-9]+)\.([0-9]+)$%';
	protected $replace_patterd = '$3-$2-$1';
	protected $reverse_date_pattern = '%([0-9]+)\-([0-9]+)\-([0-9]+).*%';
	protected $reverse_replace_pattern = '$3.$2.$1';
}
?>