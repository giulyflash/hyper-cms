<?php

$allowed_ext = array('php','xsl','js','sql','css');
$more = array('function','public function','class');
$denied_folder = array('extensions','.svn','archive','tmp','phpBB3');

function count_lines($dir=NULL){
	global $allowed_ext;
	global $denied_folder;
	global $more;
	if(!$dir)
		$dir = getcwd().'\\..\\';
	$lines = array();
	if ($handle = opendir($dir)){
		$src_dir=scandir($dir);
		foreach($src_dir as $file)
			if ($file != "." && $file != ".."){
				if(strpos($file,'.')===false){
					if(!in_array($file,$denied_folder)){
						$lines_temp=count_lines("$dir$file\\");
						foreach($allowed_ext as $ext)
							@$lines[$ext]+=$lines_temp[$ext];
						foreach($more as $more_item)
							@$lines[$more_item]+=$lines_temp[$more_item];
					}
				}
				elseif(preg_match("%\.([^\.]+)$%", $file, $file_re) && in_array(strtolower($file_re[1]),$allowed_ext)){
					//echo $dir.$file.'<br/>';
					if($file = fopen($dir.$file,'r')){
						while(!feof($file)) {
							$line=trim(fgets($file));
							if($line && $line!='}'){
								@$lines[$file_re[1]]++;
								foreach($more as $more_item){
									if(preg_match('%'.$more_item.' .*{%',$line))
										$lines[$more_item]++;
								}
							}
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
foreach($lines as $name=>$line)
	if($name!='function' && $name!='class' && $name!='public function')
		@$lines['all']+=$line;

foreach($lines as $ext=>$line)
	echo "$ext: $line<br/>";

?>