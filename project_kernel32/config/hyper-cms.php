<?php
$config = array(
	'app' => array(
		'template' => 'template/jkomfort/index.xhtml.xsl',
		'default_page_title' => 'ООО "Апатиты-Комфорт"',
	),
	'db' => array(
		'type' => 'mysql',
	)
);

//link to call modules with each other
//if _language check language
//if _exclude do not load
$link = array(
	'*.*'=>array(
		'right'=>'article.get&field=translit_title&value=Kontaktnaja-informacija&show_title=0',
	),
);
$admin_exclude = array();
$unlink = array();
$include = array();
?>