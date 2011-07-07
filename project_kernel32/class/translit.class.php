<?php
class translit{
	private static $cyr=array(
    " ",".","_","Щ",  "Ш", "Ч", "Ц","Ю", "Я", "Ж", "А","Б","В","Г","Д","Е","Ё","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х", "Ь","Ы","Ъ","Э","Є","Ї",
    "щ",  "ш", "ч", "ц","ю", "я", "ж", "а","б","в","г","д","е","ё","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х", "ь","ы","ъ","э","є","ї");
	private static $lat=array(
    "-","-","-","Shh","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","","Y","","E","Je","Ji",
    "shh","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","","y","","e","je","ji"
    );

    public function transliterate($str, $encIn='utf-8', $encOut='utf-8'){
    	$str = iconv($encIn, "utf-8", $str);
    	$str = str_replace(self::$cyr, self::$lat, $str);
    	$str = preg_replace("/[^qwrtpsdfghjklzxcvbnmeyuioa1234567890\-]/i", "", $str);
    	$str = preg_replace("/([qwrtpsdfghjklzxcvbnmQWRTPSDFGHJKLZXCVBNM]+)[jJ]e/", "\${1}e", $str);
    	$str = preg_replace("/([qwrtpsdfghjklzxcvbnmQWRTPSDFGHJKLZXCVBNM]+)[jJ]/", "\${1}j", $str);
    	$str = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $str);
    	$str = preg_replace("/^kh/", "h", $str);
    	$str = preg_replace("/^Kh/", "H", $str);
    	return iconv("utf-8", $encOut, $str);
    }

    public function transliterate_filename($str, $encIn='utf-8', $encOut='utf-8'){
    	$filename = $this->transliterate($str,$encIn,$encOut);
    	return preg_replace('/[^0-9a-z\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', '',$filename);
    }
} 
?>