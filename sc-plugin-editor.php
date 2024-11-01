<?php
/*
Plugin Name: Solid Code Plugin Editor
Description: Adds a special editor to the plugin editor with more functionality
Version: 1.0.0
Author: Dagan Lev
Author URI: http://solid-code.co.uk

Copyright 2011  Dagan Lev  (email : daganlev@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//add actions
add_action( 'admin_menu', 'scpe_add_plugin_page' );
add_action('admin_print_scripts', 'scpe_scripts');
add_action('admin_print_styles', 'scpe_styles');
add_action('template_redirect', 'scpe_redirect_special');

function scpe_redirect_special(){
	//download file
	if(preg_match('/solid-code-plugin-editor\/downloadfile\//',$_SERVER["REQUEST_URI"],$main_id)){
		include(WP_PLUGIN_DIR . '/solid-code-plugin-editor/sc-plugin-downloader.php');
		exit;
	}
	//download theme backup
	if(preg_match('/solid-code-plugin-editor\/downloadbackup\//',$_SERVER["REQUEST_URI"],$main_id)){
		include(WP_PLUGIN_DIR . '/solid-code-plugin-editor/sc-plugin-backup.php');
		exit;
	}
}

function scpe_add_plugin_page() {
	add_plugins_page('Solid Code Plugin Editor', 'SC Plugin Editor','edit_plugins', 'scpe-plugin-editor', 'scpe_plugin_editor_page');
}

function scpe_styles(){
	if($_GET['page']=='scpe-plugin-editor'){
		wp_register_style('scpe-style', WP_PLUGIN_URL.'/solid-code-plugin-editor/scpe-style.css');
		wp_enqueue_style('scpe-style');
	}
}
function scpe_scripts(){
	if($_GET['page']=='scpe-plugin-editor'){
		wp_register_script('scpe_script', WP_PLUGIN_URL.'/solid-code-plugin-editor/scpe-script.js', array('jquery'));
		wp_enqueue_script('scpe_script');
	}
}

function scpe_plugin_editor_page(){
	if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this site.').'</p>');
	
	$plugins = get_plugins();
	$plugins_files = array_keys($plugins);
	$file = '';
	$pluginDir = '';
	$pluginKey = '';
	
	//set plugin
	if(!isset($_GET['plugin'])){
		$plugin = $plugins[$plugins_files[0]];
		$file = $plugins_files[0];
		$pluginKey = $file;
		$fileDir = explode("/",$plugins_files[0]);
		$pluginDir = WP_PLUGIN_DIR . '/' . $fileDir[0] . '/';
		$file = $fileDir[1];
	}else{
		$plugin = $plugins[$_GET['plugin']];
		$file = $_GET['plugin'];
		$pluginKey = $file;
		$fileDir = explode("/",$_GET['plugin']);
		$pluginDir = WP_PLUGIN_DIR . '/' . $fileDir[0] . '/';
		$file = $fileDir[1];
	}
	
	//set file
	if(isset($_GET['file'])){
		$file = $_GET['file'];
	}	

	if(isset($_POST['newcontent'])){
		$newcontent = stripslashes($_POST['newcontent']);
		if (is_writeable($pluginDir . '/' . $file)) {
			//is_writable() not always reliable, check return value. see comments @ http://uk.php.net/is_writable
			$f = fopen($pluginDir . '/' . $file, 'w+');
			if ($f !== FALSE) {
				fwrite($f, $newcontent);
				fclose($f);
				echo '<script type="text/javascript">
					<!--
					window.location = \'plugins.php?page=scpe-plugin-editor&plugin=' . urlencode($pluginKey) . '&file=' . urlencode($file) . '&saved=1\';
					//-->
					</script>';
				exit();
			}
		}
		
	}
	?>
	<div class="wrap">
	<div id="icon-plugins" class="icon32"><br /></div><h2>Solid Code Plugin Editor</h2>
	<p><b style="color:red;">WARNING!!!</b> - changing Plugin files may harm your WordPress Site installation, please do not proceed unless you know exactly what you are doing.</p>
	<?php
	if(isset($_GET['saved'])){
		echo '<div id="message" class="updated"><p>File edited successfully.</p></div>';
	}
	?>
		<div class="fileedit-sub">
			<div class="alignleft">
			<h3 style="margin-top:0px;"><?php echo $plugin['Name']; ?> - <span style="font-weight:normal;"><?php echo $file; ?></span></h3>
			</div>
			<div class="alignright">
				<form action="plugins.php?page=scpe-plugin-editor" method="get">
					<input type="hidden" name="page" id="page1" value="<?php echo $_GET['page']; ?>" />
					<strong><label for="theme">Select plugin to edit: </label></strong>
					<select name="plugin" id="plugin1">
						<?php
						foreach($plugins as $plugin_key => $plugin_val){
							if($plugin_val['Name']==$plugin['Name']){
								echo '<option selected="selected" value="'. $plugin_key .'">'. $plugin_val['Name'] .'</option>';
							}else{
								echo '<option value="'. $plugin_key .'">'. $plugin_val['Name'] .'</option>';	
							}
						}
						?>
					</select>
					<input type="submit" name="Submit" id="Submit" class="button" value="Select" />
				</form>
			</div>
			<br class="clear" />
		</div>
		
		<?php
		$content = '';
		$allowedFileExt = array('php','css','js','xml','html','htm','txt');
		$urlFile = $file;
		$file = $pluginDir . '/' . $file;
		if(file_exists($file)){
			//check valid ext
			$fxt = explode('.',$file);
			if(in_array($fxt[count($fxt)-1],$allowedFileExt)){
				$content = esc_textarea( file_get_contents($file) );
			}
		}else{
			$content = 'File does not exist...';
		}
		?>
		<div class="scpe_content_left">
			<ul>
				<li style="float:left;"><a href="<?php echo WP_PLUGIN_URL; ?>/solid-code-plugin-editor/downloadfile/?plugin=<?php echo urlencode($pluginKey); ?>&amp;file=<?php echo urlencode($urlFile); ?>">Download File</a></li>
				<li style="float:left;margin-left:20px;"><a href="<?php echo WP_PLUGIN_URL; ?>/solid-code-plugin-editor/downloadbackup/?plugin=<?php echo urlencode($pluginKey); ?>">Download Whole Plugin</a> (ZIP)</li>
			</ul>
			<div style="clear:both;"><!-- EMPTY --></div>
			<form name="textarea_form" id="textarea_form" method="post" action="plugins.php?page=scpe-plugin-editor&amp;plugin=<?php echo urlencode($pluginKey); ?>&amp;file=<?php echo urlencode($urlFile); ?>">
				<?php if(in_array($fxt[count($fxt)-1],$allowedFileExt)){ ?>
					<textarea wrap="off" cols="70" rows="25" name="newcontent" id="newcontent" tabindex="1"><?php echo $content; ?></textarea>
					<p>
					<input type="submit" name="submit" id="submit" class="button-primary" value="Update File" tabindex="2" />
					</p>
				<?php }else{
					echo '<p>File does not match allowed file extension ' . join(',',$allowedFileExt) . '</p>';
				} ?>
			</form>
		</div>
		<div class="scpe_content_right">
			<div class="scpe_inside_right">
				<h2>Files</h2>
				<?php
				//loop through all plugin files
				echo scpe_loopThroughFiles($pluginDir,$pluginDir,$pluginKey,$urlFile);
				?>
			</div>
		</div>
		<div style="clear:both;"><!-- empty --></div>
	</div>
	<?php
}

function scpe_loopThroughFiles($maindir,$dir,$plugin,$sfile){
	$strtmp = '';
	$strtmpdir = '';
	if ($handle = opendir($dir)) {
		if(str_ireplace(str_ireplace($maindir,'',$dir),'',$sfile) != $sfile){
			$strtmpdir = '<ul class="scpe_show">';
		}else{
			$strtmpdir = '<ul>';	
		}
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != ".svn") {
				if(is_dir($dir . '/' . $file)){
					$strtmpdir .= '<li>' . $file . scpe_loopThroughFiles($maindir,$dir . '/'. $file,$plugin,$sfile) . '</li>';	
				}else{
					if($sfile==(str_ireplace($maindir,'',$dir . '/') . $file)){
						$strtmp .= '<li><a class="scpe_selected_file" href="plugins.php?page=scpe-plugin-editor&amp;plugin='.urlencode($plugin).'&amp;file='.urlencode(str_ireplace($maindir,'',$dir . '/') . $file).'">' .$file. '</a></li>';	
					}else{
						$strtmp .= '<li><a href="plugins.php?page=scpe-plugin-editor&amp;plugin='.urlencode($plugin).'&amp;file='.urlencode(str_ireplace($maindir,'',$dir . '/') . $file).'">' .$file. '</a></li>';		
					}
				}
			}
		}
		$strtmp = $strtmpdir . $strtmp . '</ul>';
		closedir($handle);
	}
	return $strtmp;
}
?>