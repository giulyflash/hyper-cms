﻿<?php

function transliterate($str){
	$cyr=array(
		"Щ",  "Ш", "Ч", "Ц","Ю", "Я", "Ж", "А","Б","В","Г","Д","Е","Ё","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х", "Ь","Ы","Ъ","Э","Є","Ї",
    	"щ",  "ш", "ч", "ц","ю", "я", "ж", "а","б","в","г","д","е","ё","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х", "ь","ы","ъ","э","є","ї");
	$lat=array(
		"Shh","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","","Y","","E","Je","Ji",
		"shh","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","","y","","e","je","ji"
	);
	for($i=0; $i<count($cyr); $i++){
		$c_cyr = $cyr[$i];
		$c_lat = $lat[$i];
		$str = str_replace($c_cyr, $c_lat, $str);
	}
	$str = str_replace(' ','-',$str);
	$str = preg_replace("/[^qwrtpsdfghjklzxcvbnmQWRTPSDFGHJKLZXCVBNMeyuioaEYUIOA1234567890\-\.]/", "", $str);
	$str = preg_replace("/([qwrtpsdfghjklzxcvbnmQWRTPSDFGHJKLZXCVBNM]+)[jJ]e/", "\${1}e", $str);
	$str = preg_replace("/([qwrtpsdfghjklzxcvbnmQWRTPSDFGHJKLZXCVBNM]+)[jJ]/", "\${1}j", $str);
	$str = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $str);
	$str = preg_replace("/^kh/", "h", $str);
	$str = preg_replace("/^Kh/", "H", $str);
	return $str;
}

$input = array(
	//'Список домов'
);



foreach($input as $item)
	echo transliterate($item).'<br/>';
foreach($_REQUEST as $item){
	echo transliterate(iconv("cp1251", "utf-8", $item)).'<br/>';
}
?>