<?php

class site_nbk extends module{
	protected $config_class_name = 'site_nbk_config';
	
	public function _admin($order='num',$page=1,$count=NULL){
		if(!$count)
			$count = $this->_config('page_count');
		$tablename = $this->_config('table');
		$this->_result = $this->_query->select()->from($tablename)->order($order)->query_page($page,$count);
		$this->_result['__page_size'] = $count;
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
				'account_comment'  => $sheet->getCell('H'.$line)->getValue(),
				'debt'  => $sheet->getCell('I'.$line)->getValue(),
				'balance'  => $sheet->getCell('J'.$line)->getValue(),
				'charges'  => $sheet->getCell('K'.$line)->getValue(),
				'control_summ'  => $sheet->getCell('R'.$line)->getValue(),
				'debt_date'  => self::convert_date($sheet->getCell('S'.$line)->getFormattedValue()),
				'pay_date'  => self::convert_date($sheet->getCell('T'.$line)->getFormattedValue()),
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
	
	private static function convert_date($str){
		if(!preg_match('%^(.*?)-(.*?)-(.*?)$%',$str,$re))
			throw new my_exception('wrong data format');
		if($re[1] && $re[2] && $re[3]){
			$date_str = '20'.$re[3].'-'.$re[1].'-'.$re[2];
			//var_dump($str, $date_str);
			$date = new DateTime($date_str);
			$output = $date->format('Y-m-d H:i:s');
			//var_dump($str, $date_str, $output); die;
		}
		else
			$output = "0000-00-00 00:00:00";
		return $output;
	}
	
	/*private function save_article($article_title,$article_text, $replace=NULL, $do_not_create_new = false){
		$article_name = translit::transliterate($article_title);
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		$value = array();
		//$this->_query->echo_sql=1;
		if($text=$this->_query->select('text')->from('article')->where('translit_title',$article_name)->query1('text')){
			$value['edit_date'] = $date;
			if($replace){
				if(preg_match($replace,$text))
					$value['text'] = preg_replace($replace,$article_text,$text);
				else
					$value['text'] = $text.$article_text;
			}
			else
				$value['text'] = $article_text;
			$this->_query->update('article')->set($value)->where('translit_title',$article_name)->limit(1)->execute();
		}
		elseif($do_not_create_new){
			$this->_message('Не найдена статья "'.$article_title.'"');
			//echo 'Не найдена статья "'.$article_title.'"<br/>';
		}
		else{
			$value['text'] = $article_text;
			$value['create_date'] = $date;
			$value['translit_title'] = $article_name;
			$value['draft'] = 0;
			$value['category_id'] = 1000;
			$value['title'] = $article_title;
			$this->_query->insert('article')->values($value)->execute();
		}
	}
	
	public function generate_works($is_default=false){
		if($is_default)
			return;
		if(empty($_FILES["path"]["name"])){
			$this->_message('Файл не был загружен.');
			return;
		}
		$file = new file($this->parent);
		$file->config->set('overwrite_if_exist',true);
		$file_list = $file->get_files('article');
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
		$sheets=$objPHPExcel->getAllSheets();
		foreach($sheets as $sheet){
			//$sheet = $objPHPExcel->getActiveSheet();
			$column = 0;
			while($text = $sheet->getCellByColumnAndRow($column,1)->getValue()){
				$text = "<div class=\"__class_name__\"><p>$text<br/>";
				$text.= $sheet->getCellByColumnAndRow($column,2)->getValue().'<br/>';
				//
				$street = trim($sheet->getCellByColumnAndRow($column+2,3)->getFormattedValue());
				if(strpos('.',$street)==false)
					$street = ucfirst($street);
				$house = trim($sheet->getCellByColumnAndRow($column+4,3)->getFormattedValue());
				$date_from = $sheet->getCellByColumnAndRow($column+2,4)->getFormattedValue();
				if(preg_match('%^([0-9]+)\-([0-9]+)\-([0-9]+)$%',$date_from,$re))
					$date_from = $re[2].'.'.$re[1].'.20'.$re[3];
				$date_to = $sheet->getCellByColumnAndRow($column+4,4)->getFormattedValue();
				if(preg_match('%^([0-9]+)\-([0-9]+)\-([0-9]+)$%',$date_to,$re))
					$date_to = $re[2].'.'.$re[1].'.20'.$re[3];
				$text.= $sheet->getCellByColumnAndRow($column,3)->getValue().$street.
					$sheet->getCellByColumnAndRow($column+3,3)->getFormattedValue().' '.$house.' <br/>'.
					$sheet->getCellByColumnAndRow($column,4)->getValue().' '.$date_from.' '.
					$sheet->getCellByColumnAndRow($column+3,4)->getFormattedValue().' '.$date_to.' </p>';
				//
				$class_name = translit::transliterate($street)."_{$house}_{$date_from}_{$date_to}";
				$text = str_replace('__class_name__',$class_name,$text);
				$text.='<table class="jkomfort_house">';
				$col_count = 0;
				$col_is_set = false;
				for($row=6;$row<13;$row++){
					$text.='<tr>';
					if(!$col_is_set){
						while($value = $sheet->getCellByColumnAndRow($column+$col_count,$row)->getCalculatedValue()){
							$text.= "<th>$value</th>";
							$col_count+=1;
						}
						$col_is_set = true;
					}
					else
						for($col=$column;$col<$column+$col_count;$col++){
							$tag = ($col==$column || $row==6) ? 'th' : 'td';
							$value = $sheet->getCellByColumnAndRow($col,$row)->getCalculatedValue();
							if($tag!='th' && (($value_type = gettype($value))=='double' || $value_type=='float'))
								$value = number_format($value,2,',','');
							$text.= "<$tag>$value</$tag>";
						}
					$text.='</tr>';
				}
				
				$text.='</table><table class="jkomfort_house">';
				//
				$row=14;
				while($value = $sheet->getCellByColumnAndRow($column,$row)->getCalculatedValue()){
					$tag = ($row==14)?'th':'td';
					$text.="<tr><th>".$sheet->getCellByColumnAndRow($column,$row)->getCalculatedValue().
						"</th><$tag>".$sheet->getCellByColumnAndRow($column+$col_count-1,$row)->getCalculatedValue()."</$tag></tr>";
					$row++;
				}
				$text.='</table></div>';
				//
				$article_name = $street.$this->street_separator.$house;
				$this->save_article(
					$article_name,
					$text,
					"%<div class=\"$class_name\">.*</div>%",
					true
				);
				//
				$column+=$col_count+1;
			}
		}
		$this->_message("Список домов сгенерирован, <a href='/Primernaja-rasshifrovka-platy' target='_blank'>посмотреть</a>");
		$output_index_error = true;
	}*/
}

class site_nbk_config extends module_config{
	protected $callable_method=array(
		'generate,generate_works'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
		'_admin'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
	
	protected $default_method = '_admin';
	protected $page_count = 20;
	protected $table = 'debtor_list';
}
?>