<?php
/*<option value="edit_item">edit_item</option>
<option value="move_item">move_item</option>
<option value="remove_item">remove_item</option>
<option value="save_item">save_item</option>
<option value="get_category">выбрать список категорий</option>
<option value="get_category_by_title">выбрать список категорий по имени</option>
<option value="get">выбрать список объектов</option>
<option value="edit_category">редактировать категорию</option>
<option value="edit">редактировать объект</option>
<option value="remove_category">удалить категорию</option>
<option value="remove">удалить объект</option>*/
$language = array(
	'__title'=>'меню',
	'_admin'=>array(
		'__title'=>'Меню',
	),
	'edit_item'=>array(
		'__title'=>'редактировать пункт меню',
		'__param'=>array(
			'id' => 'имя пункта',
		),
	),
	'remove_item'=>array(
		'__title'=>'удалить пункт меню',
		'__param'=>array(
			'id' => 'имя пункта',
		),		
	),
	'remove'=>array(
		'__title'=>'удалить меню',
		'__param'=>array(
			'id' => 'имя меню',
		),
	),
	'edit'=>array(
		'__title'=>'редактировать меню',
		'__param'=>array(
			'id' => 'имя меню',
		),
	),
	'get'=>array(
		'__title'=>'выбрать меню',
		'__param'=>array(
			'id' => 'имя меню',			
			'show_title' => 'показывать заголовок',
		),
	),
	'__object'=>array(
		'menu'=>'меню',
		'menu_item'=>'пункт меню',
	)
);

$error = array(
	'menu_id not found'=>'Идентификатор меню не найден',
	'menu name must not be empty'=>'Имя пункта меню не должно быть пустым',
	'menu list is empty'=>'Список меню пуст',
	//
	'category not found' => 'Пункт меню "$title" не найден',
	'object not found' => 'Меню "$name" не найдено',
	'object not found by id' => 'Меню не найдено по идентивикатору "$id"',
	'edit successfool' => 'Редактирование меню "$name" прошло успешно',
	'add successfool' => 'Меню "$name" успешно добавлено',
	'delete successfooly' => 'Меню "$name" успешно удалено',
	'id is empty' => 'Идентивикатор объекта пуст',
	'category edited successfooly' => 'Пункт меню "$title" успешно отредактирован',
	'parent not found' => 'Родитель id="$id" не найден',
	'new category add' => 'Новый пункт меню "$title" добавлен',
	'insert_type not found' => 'Тип вставки пункта меню не найден',
	'wrong insert type' => 'Неверный тип вставки пункта меню "$type"',
	'not found category to movу' => 'Не найден пункта меню для перемещения с id="$id"',
	'insert place not found' => 'Не найден пункт меню для вставки с id="$id"',
	'may not to move category into itselve' => 'Нельзя переместить пункт меню внутрь самого себя',
	'category moved successfooly' => 'Пункт меню перемещен',
	'category not found by id' => 'Пункт меню не найден по идентификатору id="$id"',
	'category deleted successfooly' => 'Пункт меню "$name" уcпешно удален',
	'database unlocked' => 'База разблокирована',
)
?>