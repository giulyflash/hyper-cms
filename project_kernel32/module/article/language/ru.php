<?php
$language = array(
	'get'=>array(
		'__title'=>'выбрать статью',
		'__param'=>array(
			'title' => 'заголовок',			
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
	'edited seccessfuly' => 'Статья "$title" успешно отредактирована',
	'added seccessfuly' => 'Статья "$title" успешно добавлена',
	'deleted seccessfuly' => 'Статья "$title" успешно удалена',
	'article not found' => 'Статья "$title" не найдена',
	'id is empty' => 'Указан пустой идентификатор статьи',
	'category name must not be empty'=>'Имя категории не должно быть пустым',
	'not found by id' => 'Статья не найдена по номеру "$id"',
	'title not found' => 'Заголовок статьи не найден',
	'item moved' => 'Статья &#171;$name&#187; перемещена',
);


?>