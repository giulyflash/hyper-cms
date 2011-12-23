<?php

class site_jkomfort extends module{
	protected $config_class_name = 'site_jkomfort_config';
	
	/*public $callable_method=array(
			'generate,_admin,admin,generate_works'=>array(
		self::object_name=>__CLASS__,
		self::role_name=>self::role_write,
		),
	);
	public function admin(){
	}
	*/
	
	private $street_separator = ' ';
	
	public function _admin(){}
	
	public function mb_ucfirst($str, $enc = null){
	  if($enc === null) $enc = 'utf8';
	  return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}

	public function generate($main_article_title=NULL,$is_default=false){
		if($is_default)
			return;
		$line0 = 7;
		$column_count = 3;
		if(!$main_article_title){
			$this->_message('Имя статьи не найдено');
			return;
		}
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
		$line = $line0;
		$sheet = $objPHPExcel->getActiveSheet();
		$house_list_title = '<p>'.$sheet->getCell('A1')->getValue().'</p><p>'.$sheet->getCell('A2')->getValue().'</p><br/>';
		$house_list = array();
		//
		$column_default = 68;
		$column = $column_default;
		$row_table_start = '<table class="jkomfort_house">';
		$row_table = $row_table_start;
		$temp_th1 = $sheet->getCell(chr($column).'4')->getValue();
		$temp_th2 = $sheet->getCell(chr($column).'5')->getValue();
		$rowspan = 0;
		$value_names = array();
		while($temp_th1 || $temp_th2){
			$row_table.='<tr>';
			if($temp_th1){
				if(!$temp_th2)
					$row_table.="<th colspan='2'>$temp_th1</th>";
				else{
					$rowspan+=1;
					$row_table.="<th rowspan='__rowspan__'>$temp_th1</th><th>$temp_th2</th>";
				}
			}
			else{
				$row_table.="<th>$temp_th2</th>";
				$rowspan+=1;
			}
			$value_name = '__value_'.chr($column).'__';
			$row_table.="<td>$value_name</td></tr>";
			$value_names[] = $value_name;
			$column++;
			$temp_th1 = $sheet->getCell(chr($column).'4')->getValue();
			$temp_th2 = $sheet->getCell(chr($column).'5')->getValue();
		}
		$row_table = str_replace("__rowspan__",$rowspan,$row_table).'</table>';
		//echo $row_table.'<style>table{border-collapse: collapse; padding: 0; margin: 0} tr,th{border: 1px solid black; padding: 0; margin: 0}</style>';die;
		$street0 = '';
		$street_list = array();
		while($sheet->getCell('A'.$line)->getValue()){
			$street = trim($sheet->getCell('B'.$line)->getValue());
			if(strpos('.',$street)===false)
				$street = $this->mb_ucfirst($street);
			if(!$street0 || $street0 != $street){
				$street0 = $street;
				$street_list[$street] = array();
			}
			$house = trim($sheet->getCell('C'.$line)->getValue());
			$article_title = $street.$this->street_separator.$house;
			$street_list[$street][] = $house;
			$values = array();
			for($col=$column_default; $col<=$column; $col++){
				$value = $sheet->getCell(chr($col).$line)->getCalculatedValue();
				$value_formated = $sheet->getCell(chr($col).$line)->getFormattedValue();
				if(preg_match('%^([0-9]+)\-([0-9]+)\-([0-9]+)$%',$value_formated,$re))
					$value = $re[2].'.'.$re[1].'.20'.$re[3];
				elseif( ($value_type = gettype($value))=='double' || $value_type=='float')
					$value = number_format($value,2,',','');
				$values[] = $value;
			}
			$article_text = str_replace($value_names,$values,$row_table);
			$this->save_article($article_title, $article_text,"%$row_table_start.*?</table>%");
			//
			//$house_list[]="<li><a href='$article_name'>$article_title</a></li>";
			$line++;
		}
		if(!$street_list){
			$this->_message('Неверное содержимое файла.');
			return;
		}
		//
		$line_count = $line-$line0 + 3*count($street_list);
		$street_list_half = $line_count/$column_count;
		$house_list_text=$house_list_title.'<table class="house_list"><tr><td>';
		$street_list_temp = array();
		foreach(array_keys($street_list) as $num=>$key)
			 $street_list_temp[$key] = $num+1;
		$line = 0;
		$line0 = 0;
		foreach($street_list as $street_name=>$street){
			for($i=1; $i<$column_count; $i++){
				if($line>$street_list_half*$i && $line0<$street_list_half*$i)
					$house_list_text.='</td><td>';
			}
			$line0 = $line;
			$house_list_text.="<p>$street_name</p><ul>";
			$line+=3;
			foreach($street as $house){
				$house_list_text.= '<li><a href="'.translit::transliterate($street_name.$this->street_separator.$house).'">'.$house.'</a></li>';
				$line+=1;
			}
			$house_list_text.='</ul>';
		}
		$house_list_text.='</td></tr></table>';
		$this->save_article($main_article_title, $house_list_text);
		$this->_message("Список домов сгенерирован, <a href='/".translit::transliterate($main_article_title)."' target='_blank'>посмотреть</a>");
		$output_index_error = true;
	}
	
	private function save_article($article_title,$article_text, $replace=NULL, $do_not_create_new = false){
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
				if(!$street)
					throw new my_exception('Неверный формат таблиц: название улицы не найдено');
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
				while(!$sheet->getCellByColumnAndRow($column,$row) && $row<100)
					$row++;
				$row00 = $row;
				while($value = $sheet->getCellByColumnAndRow($column,$row)->getCalculatedValue()){
					$tag = ($row==$row00)?'th':'td';
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
	}
}

class site_jkomfort_config extends module_config{
	protected $callable_method=array(
		'generate,_admin,generate_works'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
	);
}
?>