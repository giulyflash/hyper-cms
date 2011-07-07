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
			'field' => 'имя поля',
			'value' => 'значение поля',
			'need_item' => 'выводить объекты'
		),
	),
	'get_category_by_title' => array(
		'__title' => 'выбрать список категорий по имени',
		'__param' => array(
			'title' => 'заголовок',
		),
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
	'try to use not existing category' => 'попытка использовать несуществующую категорию: "$title"',
	'category not found' => 'Категория "$title" не найдена',
	'module has not items' => 'Модуль "$name" не имеет объектов',
	'condition is not an array' =>  'условие "$condition" не является массивом',
	'condition field and value must be set' => 'поле и значение условия должны быть заданы',
	'object not found' => 'Объект "$name" не найден',
	'object not found by id' => 'Объект не найден по идентивикатору "$id"',
	'edit successfool' => 'Редактирование объекта прошло успешно',
	'add successfool' => 'Объект успешно добавлен',
	'delete successfooly' => 'Объект успешно удален',
	'id is empty' => 'Идентивикатор объекта пуст',
	'category edited successfooly' => 'Категория "$title" успешно отредактирована',
	'parent not found' => 'Родитель id="$id" не найден',
	'new category add' => 'Новая категория "$title" добавлена',
	'insert_type not found' => 'Тип вставки категории не найден',
	'wrong insert type' => 'Неверный тип вставки категории "$type"',
	'not found category to movу' => 'Не найдена категория для перемещения с id="$id"',
	'insert place not found' => 'Не найдена категория-родитель для вставки с id="$id"',
	'may not to move category into itselve' => 'Нельзя переместить элемент внутрь самого себя',
	'category moved successfooly' => 'Категория перемещена',
	'category not found by id' => 'Категория не найдена по идентификатору id="$id"',
	'category deleted successfooly' => 'Категория "$name" уcпешно удалена',
	'database unlocked' => 'База разблокирована',
);


?>