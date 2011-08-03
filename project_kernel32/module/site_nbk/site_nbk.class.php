<?php

class site_nbk extends module{
	protected $config_class_name = 'site_nbk_config';
	
	public function _admin(){}
	
	public function get($order='num',$page=1,$count=NULL){
		if(!$count)
			$count = $this->_config('page_count');
		$tablename = $this->_config('table');
		$this->_result = $this->_query->select()->from($tablename)->order($order)->query_page($page,$count);
		foreach($this->_result as &$item){
			if(is_array($item)){
				$debt_date = new DateTime();
				$debt_date->createFromFormat($this->_config('db_date_format'), $item['debt_date']);
				$item['debt_date'] = $debt_date->format('m.Y');
				$pay_date = new DateTime();
				$pay_date->createFromFormat($this->_config('db_date_format'), $item['pay_date']);
				$item['pay_date'] = $debt_date->format('m.Y');
			}
		}
		$page_count = ceil($this->_result['__num_rows']/$count);
		$page_select_html = '';
		for($i=1; $i<=$page_count; $i++)
			$page_select_html.='<option value="'.$i.'" '.($i==$page?'selected="1"':'').'>'.$i.'</option>';
		$this->_result['_page_select_html'] = &$page_select_html;
		$field = array(
			'num'=>array('title'=>'№ п/п'),
			'account'=>array('title'=>'Лицевой счет'),
			'street'=>array('title'=>'Улица'),
			'house'=>array('title'=>'Дом'),
			'flat'=>array('title'=>'Квартира'),
			'privatizated'=>array('title'=>'Приватизация'),
			'owner'=>array('title'=>'Владелец/Квартиросъемщик'),
			'account_comment'=>array('title'=>'Комментарий к лицевому счету'),
			'debt'=>array('title'=>'Долг на момент контроля'),
			'balance'=>array('title'=>'Остаток на кон.мес. на момент контроля'),
			'charges'=>array('title'=>'Начисления'),
			'control_summ'=>array('title'=>'Оплата<=суммы контроля'),
			'debt_date'=>array('title'=>'Месяц начала задолженности'),
			'pay_date'=>array('title'=>'Дата платежа'),
			'comment'=>array('title'=>'Комментарий'),
		);
		foreach($field as $field_name=>&$field_value){
			if(strpos($order,$field_name)!==false){
				if(strpos($order,$field_name.' desc')!==false){
					$to_replace = (strpos($order,$field_name.' desc,')!==false)?($field_name.' desc,'):($field_name.' desc');
					$field_value['order'] = trim(str_replace($to_replace, '', $order));
					$field_value['order'] = $field_name.($field_value['order']?',':'').$field_value['order'];
				}
				else{
					$to_replace = (strpos($order,$field_name.',')!==false)?($field_name.','):$field_name;
					$field_value['order'] = trim(str_replace($to_replace, '', $order));
					$field_value['order'] = $field_name.' desc'.($field_value['order']?',':'').$field_value['order'];
				}
				if(substr($field_value['order'],-1)==',')
					$field_value['order'] = substr($field_value['order'],0,-1);
			}
			else
				$field_value['order'] = $field_name.($order?',':'').$order;
		}
		$this->_result['_field'] = $field;
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
			$output = $date->format($this->_config('db_date_format'));
			//var_dump($str, $date_str, $output); die;
		}
		else
			$output = "0000-00-00 00:00:00";
		return $output;
	}
}

class site_nbk_config extends module_config{
	protected $callable_method=array(
		'generate,generate_works,_admin'=>array(
			'__access__' => array(
				__CLASS__ => self::role_write,
			),
		),
		'get'=>array(
			'__access__' => array(
				__CLASS__ => self::role_read,
			),
		),
	);
	
	protected $include=array(
		'get'=>'<script type="text/javascript" src="module/site_nbk/list.js"></script>',
	);
	
	//protected $default_method = '_admin';
	protected $page_count = 20;
	protected $table = 'debtor_list';
	protected $db_date_format = 'Y-m-d H:i:s';
}
?>