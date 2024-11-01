<?php
//TODO - from here
header('HTTP/1.1 200 OK');
if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this site.').'</p>');

$fileDir = explode("/",$_GET['plugin']);
//Get the directory to zip
$directory = WP_PLUGIN_DIR . '/' . $fileDir[0] . '/';

// create object
$zip = new ZipArchive();

// open output file for writing
$zipname = date('Ymdhis') . '.zip';
if ($zip->open(WP_PLUGIN_DIR . '/solid-code-plugin-editor/tempZips/' . $zipname, ZIPARCHIVE::CREATE) !== TRUE) {
    die ("Could not open archive");
}

// initialize an iterator
// pass it the directory to be processed
$iterator  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$directory"));

// iterate over the directory
// add each file found to the archive
foreach ($iterator as $key=>$value) {
    $zip->addFile(realpath($key), $theme . '/' . str_ireplace($directory,'',$key)) or die ("ERROR: Could not add file: $key");        
}

// close and save archive
$zip->close();

$file = WP_PLUGIN_DIR . '/solid-code-plugin-editor/tempZips/' . $zipname;
if(file_exists($file)){
    $content = file_get_contents($file);
}else{
    $content = 'File does not exist...';
}

$fsize = strlen($content);

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-Disposition: attachment; filename=" . $fileDir[0] . '.zip');
header("Content-Length: ".$fsize);
header("Expires: 0");
header("Pragma: public");

echo $content;
unlink($file);
exit;
?>