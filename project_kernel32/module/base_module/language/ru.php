<?php
$language = array(	
	'get' => array(
		'__title' => 'выбрать список объектов',
		'__param' => array(
			'field' => 'имя поля',
			'value' => 'значение поля',
		),
	),
	'get_category' => array(
		'__title' => 'выбрать список категорий',
		'__param' => array(
			'title' => 'заголовок',
			'need_item' => 'выводить объекты'
		),
		'no_obj_msg'=>'Объектов не найдено.',
	),
	'edit' => array(
		'__title' => 'редактировать объект',
		'__param' => array(
			'title' => 'порядковый номер',
		),
	),
	'remove' => array(
		'__title' => 'удалить объект',
		'__param' => array(
			'id' => 'заголовок',
		),
	),
	'_admin' => array(
		'add_item' => 'Добавить объект',
		'add_category' => 'Добавить категорию'
	),
	'edit_category' => array(
		'__title' => 'редактировать категорию',
		'__param' => array(
			'id' => 'заголовок',
			'insert_place' => 'вставить в категорию',
		),
	),
	'remove_category' => array(
		'__title' => 'удалить категорию',
		'__param' => array(
			'id' => 'заголовок',
		),
	),
);

$error = array(
	//exception
	'condition is not an array' =>  'условие "$condition" не является массивом',
	'condition field and value must be set' => 'поле и значение условия должны быть заданы',
	'parent not found' => 'Родитель &#171;$id&#187; не найден',
	'can not move by empty id'=>'Невозможно переместить по пустому идентификатору',
	'insert_type not found' => 'Тип вставки категории не найден',
	'wrong insert type' => 'Неверный тип вставки категории "$type"',
	'not found category to move' => 'Не найдена категория &#171;$id&#187; для перемещения',
	'can not move category into itselve' => 'Невозможно переместить категорию внутрь самой себя',
	'insert place not found' => 'Не найдена категория-родитель &#171;$id&#187; для вставки',

	//error
	'module has not items' => 'Модуль "$name" не имеет объектов',
	'database unlocked' => 'База разблокирована',
	'table ids updated' => 'Идентификаторы таблицы "$table" обновлены',
	
	'category id is empty' => 'Указано пустое имя категории',
	'category not found' => 'Категория &#171;$id&#187; не найдена',
	'category edited successfully' => 'Категория &#171;$title&#187; отредактирована',
	'category added successfully' => 'Категория &#171;$title&#187; добавлена',
	'category deleted successfully' => 'Категория &#171;$title&#187; удалена',
	'category moved successfully' => 'Категория &#171;$title&#187; перемещена',
	
	'object id is empty' => 'Указано пустое имя объекта',
	'object not found' => 'Объект &#171;$id&#187; не найден',
	'object edited successfully' => 'Объект &#171;$title&#187; отредактирован',
	'object added successfully' => 'Объект &#171;$title&#187; добавлен',
	'object deleted successfully' => 'Объект &#171;$title&#187; удален',
	'object moved successfully' => 'Объект &#171;$title&#187; перемещен',
);


?>