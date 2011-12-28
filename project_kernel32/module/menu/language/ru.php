<?php
/*<option value="edit_item">edit_item</option>
<option value="move_item">move_item</option>
<option value="remove_item">remove_item</option>
<option value="save_item">save_item</option>
<option value="get_category">выбрать список категорий</option>
<option value="get">выбрать список объектов</option>
<option value="edit_category">редактировать категорию</option>
<option value="edit">редактировать объект</option>
<option value="remove_category">удалить категорию</option>
<option value="remove">удалить объект</option>*/
$language = array(
	'__title'=>'меню',
	'_admin'=>array(
		'__title'=>'Меню',
		'add_menu'=>'Добавить меню',
		'add_category'=>'Добавить пункт меню'
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
	)
);

$error = array(
	'menu_id not found'=>'Идентификатор меню не найден',
	'menu name must not be empty'=>'Имя пункта меню не должно быть пустым',
	'menu list is empty'=>'Список меню пуст',
	//
	'object not found' => 'Меню "$name" не найдено',
	'object not found by id' => 'Меню не найдено по идентивикатору "$id"',
	'deleted successfully' => 'Меню "$name" успешно удалено',
	'id is empty' => 'Идентивикатор объекта пуст',
	'parent not found' => 'Родитель id="$id" не найден',
	'new category add' => 'Новый пункт меню "$title" добавлен',
	'insert_type not found' => 'Тип вставки пункта меню не найден',
	'wrong insert type' => 'Неверный тип вставки пункта меню "$type"',
	'not found category to movу' => 'Не найден пункта меню для перемещения с id="$id"',
	'insert place not found' => 'Не найден пункт меню для вставки с id="$id"',
	'may not to move category into itselve' => 'Нельзя переместить пункт меню внутрь самого себя',
	'category moved successfully' => 'Пункт меню перемещен',
	
	'menu item name must not be empty'=>'Имя пункта меню не должно быть пустым',
	
	'category id is empty' => 'Указано пустое имя категории',
	'category not found' => 'Пункт меню  &#171;$id&#187; не найден',
	'category edited successfully' => 'Пункт меню &#171;$title&#187; отредактирован',
	'category added successfully' => 'Пункт меню  &#171;$title&#187; добавлен',
	'category deleted successfully' => 'Пункт меню &#171;$title&#187; удален',
	'category moved successfully' => 'Пункт меню &#171;$title&#187; перемещен',
	
	'object edited successfully' => 'Меню &#171;$title&#187; отредактировано',
	'object added successfully' => 'Меню &#171;$title&#187; добавлено',
	'object deleted successfully' => 'Меню &#171;$title&#187; удалено',
)
?>