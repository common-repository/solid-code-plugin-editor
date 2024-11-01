<?php
header('HTTP/1.1 200 OK');
if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this site.').'</p>');

$fileDir = explode("/",$_GET['plugin']);
$file = WP_PLUGIN_DIR . '/' . $fileDir[0] . '/' . $_GET['file'];
$content = '';

if(file_exists($file)){
    $content = file_get_contents($file);
}else{
    $content = 'File does not exist...';
}
$filename = explode("/","/" . $_GET['file']);

$fsize = strlen($content);

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-Disposition: attachment; filename=" . $filename[count($filename)-1]);
header("Content-Length: ".$fsize);
header("Expires: 0");
header("Pragma: public");

echo $content;

exit;
?>