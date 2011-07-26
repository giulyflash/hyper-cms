<?php
$language = array(
	'get_by_title'=>array(
		'__title'=>'выбрать статью по заголовку',
		'__param'=>array(
			'title' => 'заголовок',			
			'show_title' => 'показывать заголовок',
		),
	),
	'get'=>array(
		'__title'=>'выбрать статью',
		'__param'=>array(
			'field' => 'имя поля',
			'value' => 'значение поля',			
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
	),
	'__title'=>'статьи',
	'__object'=>array(
		'article'=>'статья',
		'article_category'=>'категория статей',
	)
);

$error = array(
	'edit successfooly' => 'Статья "$title" успешно отредактирована',
	'add successfooly' => 'Статья "$title" успешно добавлена',
	'delete successfooly' => 'Статья "$title" успешно удалена',
	'article not found' => 'Статья "$title" не найдена',
	'id is empty' => 'Указан пустой идентификатор статьи',
	'category name must not be empty'=>'Имя категории не должно быть пустым',
	'not found by id' => 'Статья не найдена по номеру "$id"',
	'title not found' => 'Заголовок статьи не найден',
);


?>