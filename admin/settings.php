<?php
if (basename ( __FILE__ ) == basename ( $_SERVER ['SCRIPT_FILENAME'] ))
	die ( 'This page cannot be called directly.' );
	?>
<div class="wrap">
<h2><?php
echo __ ( "Wp Copy >> Settings", $WPPagerank->plugin_domain );
?></h2>
<?php 
if(!empty($_POST['submit_btn'])){
	$save=@$_POST['save'];
	$save_con=@$_POST['save_content'];
	$tag=@$_POST['tag'];
	$exclude=@$_POST['exclude'];
	$exclude=explode("\n",$exclude);
	$excl=array();
	foreach($exclude as $k=>$v){
		if(!empty($v)){
		$v=str_replace(array("\r\n", "\n", "\r"),"",$v);
		//$v=str_replace("\r","",$v);
		//echo $v;	
		if(strpos($v,'/')!==0)$link='/'.$v;
		else $link=$v;
		$excl[]=$link;}
	}
	$WPCopy->save_option('save',$save);
	$WPCopy->save_option('save_content',$save_con);
	$WPCopy->save_option('tag',$tag);
	$WPCopy->save_option('exclude',$excl);
}
$options=$WPCopy->get_option();
//print_r($options);
?>
<form method="post">
<table width="600px">
	<tr>
		<td><?php echo __("Save copy to base");?></td>
		<td>
			<input type="radio" <?php if($options['save']==0)echo 'checked="checked"';?> name="save" value="0"/><?php echo __("No",$WPCopy->plugin_domain);?>
			<input type="radio" <?php if($options['save']==1)echo 'checked="checked"';?> name="save" value="1"/><?php echo __("Yes",$WPCopy->plugin_domain);?>		
		</td>
		
	</tr>
	<tr>
		<td><?php echo __("Save copied content to base");?></td>
		<td>
			<input type="radio" <?php if($options['save_content']==0)echo 'checked="checked"';?> name="save_content" value="0"/><?php echo __("No",$WPCopy->plugin_domain);?>
			<input type="radio" <?php if($options['save_content']==1)echo 'checked="checked"';?> name="save_content" value="1"/><?php echo __("Yes",$WPCopy->plugin_domain);?>		
		</td>
		
	</tr>
	<tr>
		<td><?php echo __("Read tag");?></td>
		<td><input type="text" name="tag" value="<?php if(isset($options['tag']))echo $options['tag'];?>"/></td>
	</tr>
	<tr valign="top"><td><?php echo __("Exclude (relative) links separated by new line (for home page enter /)")?></td>
	<td><textarea name="exclude" cols="40" rows="10"><?php if(isset($options['exclude'])){
		echo implode("\n",$options['exclude']);
	}?></textarea>
	
	</td>
	</tr>	
	<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="submit_btn" value="<?php echo __("Save settings",$WPCopy->plugin_domain);?>"/></td>
	</tr>
</table>
</form>
</div>
<hr>
<?php
$credits = $WPCopy->get_credits();
if(isset($credits->href) && isset($credits->anchor) && !isset($credits->banner)){
    echo '<a href="'.$credits->href.'" target="_blank">'.$credits->anchor.'</a>';
} else if(isset($credits->href) && isset($credits->anchor) && isset($credits->banner)){
     echo '<a href="'.$credits->href.'" target="_blank"><img src="'.$credits->banner.'" alt="'.$credits->anchor.'"></a>';
}
