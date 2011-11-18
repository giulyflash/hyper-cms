<?php

$allowed_ext = array('php','xsl','js','sql','css');
$dedied_folder = array('extensions','.svn','archive','tmp','phpBB3');

function count_lines($dir=NULL){
	global $allowed_ext;
	global $dedied_folder;
	if(!$dir)
		$dir = getcwd().'\\..\\';
	$lines = array();
	if ($handle = opendir($dir)){
		$src_dir=scandir($dir);
		foreach($src_dir as $file)
			if ($file != "." && $file != ".."){
				if(strpos($file,'.')===false){
					if(!in_array($file,$dedied_folder)){
						$lines_temp=count_lines("$dir$file\\");
						foreach($allowed_ext as $ext)
							@$lines[$ext]+=$lines_temp[$ext];
					}
				}
				elseif(preg_match("%\.([^\.]+)$%", $file, $file_re) && in_array(strtolower($file_re[1]),$allowed_ext)){
					//echo $dir.$file.'<br/>';
					if($file = fopen($dir.$file,'r')){
						while(!feof($file)) {
							$line=fgets($file);
							if($line && trim($line)!='}')
								@$lines[$file_re[1]]++;
						}
						fclose($file);
					};
				}
	        }
		closedir($handle);
	}
	return $lines;
}

$lines = count_lines();
foreach($lines as $line)
	@$lines['all']+=$line;

foreach($lines as $ext=>$line)
	echo "$ext: $line<br/>";

?>