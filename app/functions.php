<?php

function omv_encode($text) { return str_replace(' ', '_', $text); }

function omv_decode($encoded_text) { return str_replace('_', ' ', $encoded_text); }

// Multiple Explode
function explodeX($delimiters,$string) {
return explode(chr(1),str_replace($delimiters,chr(1),$string));
}

// Delete Files and Folder
function recursiveRemoveDirectory($directory){

array_map('unlink', glob("$directory/*.*"));

if(rmdir($directory)) {
return true;
}

return false;
}

// Group By
function group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}

// Nice Time System
function nicetime($date){
if(empty($date)) {
return "No date provided";
}
$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
$lengths = array("60","60","24","7","4.35","12","10");
$now = time();
$unix_date = strtotime($date);
   
// check validity of date
if(empty($unix_date)) { return "Bad date"; }

// is it future date or past date
if($now > $unix_date) { $difference = $now - $unix_date; $tense = "ago";  
} else { $difference = $unix_date - $now; $tense = "from now"; }
   
for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) { $difference /= $lengths[$j]; }
   
$difference = round($difference);
   
if($difference != 1) { $periods[$j].= "s"; }
  
return "$difference $periods[$j] {$tense}";
}

// Get All Series
function omv_get_mangas($f3) {
$mangas = array();
$base_dir = $f3->get('base_dir');
$dirname = "$base_dir/";
$dir = @opendir($dirname);
if ($dir) {
while (($file = @readdir($dir)) !== false) {
if (is_dir($dirname . $file . '/') && ($file != ".") && ($file != "..")) {
$mangas[] = $file;
}
}
@closedir($dir);
}

sort($mangas);

return $mangas;
}

// Get All Chapters
function total_chapters($f3,$manga) {
$omv_chapters_sorting = $f3->get('sort');
$base_dir = $f3->get('base_dir');
$chapters = array();
$chapters_id = array();
$dirname = "$base_dir/$manga/";
$dir = @opendir($dirname);
if ($dir) {
while (($file = @readdir($dir)) !== false) {

$chapter_dir = $dirname.$file;
	
if (is_dir($chapter_dir.'/') && ($file != ".") && ($file != "..")) {
$chapter = array();
$chapter["folder"] = $file;

$chapter['last_update'] = date ("Y-m-d H:i:s", filemtime($chapter_dir));


$pos = strpos($file, ' - ');
if ($pos === false) {
$chapter["number"] = $file;
} else {
$chapter["number"] = trim(substr($file, 0, $pos - 0));
$chapter["title"] = trim(substr($file, $pos + 2));
}

$chapters_id[] = $chapter["number"];

$chapters[] = $chapter;
}
}
@closedir($dir);
}

array_multisort($chapters_id, $omv_chapters_sorting, $chapters);

return count($chapters);
}

// Get All Chapters
function omv_get_chapters($f3,$manga) {
$omv_chapters_sorting = $f3->get('sort');
$base_dir = $f3->get('base_dir');
$chapters = array();
$chapters_id = array();
$dirname = "$base_dir/$manga/";
$dir = @opendir($dirname);
if ($dir) {
while (($file = @readdir($dir)) !== false) {

$chapter_dir = $dirname.$file;
	
if (is_dir($chapter_dir.'/') && ($file != ".") && ($file != "..")) {
$chapter = array();
$chapter["folder"] = $file;

$chapter['last_update'] = date ("Y-m-d H:i:s", filemtime($chapter_dir));


$pos = strpos($file, ' - ');
if ($pos === false) {
$chapter["number"] = $file;
} else {
$chapter["number"] = trim(substr($file, 0, $pos - 0));
$chapter["title"] = trim(substr($file, $pos + 2));
}

$chapters_id[] = $chapter["number"];

$chapters[] = $chapter;
}
}
@closedir($dir);
}

array_multisort($chapters_id, $omv_chapters_sorting, $chapters);

return $chapters;
}


function omv_get_pages($f3,$manga, $chapter) {
$omv_img_types = $f3->get("img_types");
$pages = array();
$base_dir = $f3->get('base_dir');
$dirname = "$base_dir/$manga/$chapter/";
$dir = @opendir($dirname);
if ($dir) {
while (($file = @readdir($dir)) !== false) {
if (!is_dir($dirname . $file . '/')) {
$file_extension = strtolower(substr($file, strrpos($file, ".") + 1));
if (in_array($file_extension, $omv_img_types)) {
$pages[] = $file;
}
}
}
@closedir($dir);
}

sort($pages);

return $pages;
}

// Get Pages Name and Width and Height
function omv_get_all_pages_data($f3,$manga, $chapter) {
$omv_img_types = $f3->get("img_types");
$pages = array();
$base_dir = $f3->get('base_dir');
$dirname = "$base_dir/$manga/$chapter/";
$dir = @opendir($dirname);
if ($dir) {
while (($file = @readdir($dir)) !== false) {
if (!is_dir($dirname . $file . '/')) {
	
list($width,$height) = getimagesize($dirname . $file);

$file_extension = strtolower(substr($file, strrpos($file, ".") + 1));
if (in_array($file_extension, $omv_img_types)) {
$pages[] = array("file_name" => $file,"width" => $width,"height" => $height);
}
}
}
@closedir($dir);
}

sort($pages);

return $pages;
}


// Previous Page and Previous Chapter
function omv_get_previous_page($series, $chapter_number_e, $current_page, $nb_pages, $previous_chapter,$series_id) {
if ($current_page > 1) {
return $series . '/' . $chapter_number_e . '/' . ($current_page - 1);
} else if ($previous_chapter) {
$pages = $nb_pages;
return $series . '/' . $previous_chapter['number'] . '/' .$pages;
} else {
return null;
}
}

// Next Page and Next Chapter

function omv_get_next_page($series, $chapter_number_e, $current_page, $nb_pages, $next_chapter) {
if ($current_page < $nb_pages) {
return $series . '/' . $chapter_number_e . '/' . ($current_page + 1);
} else if ($next_chapter) {
return $series . '/' . $next_chapter['number'];
} else {
return null;
}
}

function omv_get_chapter_index($chapters, $chapter_number) {
$i = 0;
while (($i < count($chapters)) && ($chapters[$i]["number"] != $chapter_number)) $i++;
return ($i < count($chapters)) ? $i : -1;
}

/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,basename($file));
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}