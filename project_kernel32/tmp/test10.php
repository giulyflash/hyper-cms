<?php
//$file_name = 'c:/dump/dump.sql';
$file_name = 'c:/dump/apatit.sql';
$input = fopen($file_name,'c+');
$line = 0;
$limit = 100;
while(($line = fgets($input)) !== false){
	//if(preg_match('%-- Current Database: `(.*)`%',$line,$db_name))
		//echo "DROP DATABASE `{$db_name[1]}`;<br/>";
	if(preg_match('%-- Current Database: `(.*)`%',$line,$db_name) && ($db_name[1]=='_onsite' || $db_name[1]=='apatit')){
		echo $db_name[1].'<br/>';
		$db_name = $db_name[1].'.sql';
		if(!empty($output))
			fclose($output);
		if(file_exists($db_name))
			unlink($db_name);
		$output = fopen($db_name,'a');
	}
	if(isset($output))
		fwrite($output,$line."\n");
}
fclose($output);
fclose($input);
?>