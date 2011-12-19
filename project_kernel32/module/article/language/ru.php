<?php
$language = array(
	'get'=>array(
		'__title'=>'выбрать статью',
		'__param'=>array(
			'id' => 'заголовок',			
			'show_title' => 'показывать заголовок',
		),
	),
	'edit'=>array(
		'__title'=>'редактировать статью',
		'__param'=>array(
			'id' => 'заголовок',
		),
	),
	'remove'=>array(
		'__title'=>'удалить статью',
		'__param'=>array(
			'id' => 'заголовок',
		),
	),
	'_admin'=>array(
		'__title'=>'Статьи',
		'add_item' => 'Добавить статью',
	),
	'__title'=>'статьи',
	'__object'=>array(
		'article'=>'статья',
		'article_category'=>'категория статей',
	)
);

$error = array(
	'category name must not be empty'=>'Имя категории не должно быть пустым',
	'title must not be empty'=>'Заголовок статьи не может быть пустым',
	
	'object id is empty' => 'Указано пустое имя статьи',
	'object not found' => 'Статья &#171;$id&#187; не найдена',
	'object edited successfully' => 'Статья &#171;$title&#187; отредактирована',
	'object added successfully' => 'Статья &#171;$title&#187; добавлена',
	'object deleted successfully' => 'Статья &#171;$title&#187; удалена',
	'object moved successfully' => 'Статья &#171;$title&#187; перемещена',
);


?>