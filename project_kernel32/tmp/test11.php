<?php
$language0 = array(	
	'get'=>array(
		'_method_name'=>'список объектов',
		'_params'=>array(
			'name'=>'имя параметра',
			'value'=>'значение параметра'
		)
	),
	'get_category'=>array(
		'_method_name'=>'категория',
	),
	'admin'=>array(
		'_method_name'=>'администрирование',
	),
	'edit'=>array(
		'_method_name'=>'редактирование объекта',
	),
	'save'=>array(
		'_method_name'=>'сохранение объекта',
	),
	'remove'=>array(
		'_method_name'=>'удалить объект',
	),
	'edit_category'=>array(
		'_method_name'=>'редактирование категолрии',
	),
	'save_category'=>array(
		'_method_name'=>'сохранение категории',
	),
	'move_category'=>array(
		'_method_name'=>'перемещение категории',
	),
	'remove_category'=>array(
		'_method_name'=>'удаление категории',
	),
	'unlock_database'=>array(
		'_method_name'=>'разблокирование БД',
	),
);

$language1 = array(
	'get'=>array(
		'_method_name'=>'список статей',
	),
	'edit'=>array(
		'_method_name'=>'редактирование статьи',
	),
	'save'=>array(
		'_method_name'=>'сохранение статьи',
	),
	'remove'=>array(
		'_method_name'=>'удаление статьи',
	),
); 

header('Content-Type: text/plain; charset=utf-8');
//var_dump(array_merge($language0,$language1));
//var_dump($language0+$language1);
var_dump(my_array_merge_recursive($language0,$language1));

function my_array_merge_recursive(&$ar1, &$ar2){
		//ugly method, but native array_merge_recursive create sub-array when names intersect
		$result = $ar1;
		foreach($ar2 as $name=>$value)
			if(isset($ar1[$name]) && is_array($value))
				$result[$name] = my_array_merge_recursive($ar1[$name],$ar2[$name]);
			else
				$result[$name]=$value;
		return $result;
	}
?>