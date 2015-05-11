<?php
if (basename ( __FILE__ ) == basename ( $_SERVER ['SCRIPT_FILENAME'] ))
	die ( 'This page cannot be called directly.' );
	?>
<div class="wrap">
<h2><?php
echo __ ( "Wp Copy >> Copied content ", $WPCopy->plugin_domain );
?></h2>
<?php 
$my_action=@$_POST['my_action'];
if(!empty($my_action)){
	$id=@$_POST['my_id'];
	if($my_action=='delete'){
		global $wpdb;
		$sql="delete from ".$WPCopy->table_copied." where id=".$id;
		$wpdb->query($sql);
	}
}
$url=home_url('/').'wp-admin/admin.php?page='.$WPCopy->dir.'/admin/copied.php';
$per_page=10;
$order='';
$page=@$_POST['page'];
if(!isset($page))$page=1;
$o=@$_POST['order'];
$column=@$_POST['order_column'];
$colums=array('id'=>'desc','ip'=>'desc','added'=>'desc','user_agent'=>'desc','link'=>'desc');
if(isset($o)&&!empty($o)&&isset($column)&&!empty($column)){
	$order=$column." ".$o;
	$colums[$column]=$o=='desc'?'asc':'desc';
}
$ret=$WPCopy->get_copied($page,$per_page,$order);
if(count($ret['results'])==0){
	echo '<span style="color:red">'.__("There are no saved results !",$WPCopy->plugin_domain).'</span>';
}else {
	?>
	<form id="form_id" action="<?php echo $url;?>" method="post">
<input type="hidden" name="page" id="page_id" value="<?php echo $page?>"/>
<input type="hidden" name="order" id="order" value="<?php if(isset($o)&&!empty($o))echo $o;?>"/>
<input type="hidden" name="order_column" id="order_column" value="<?php if(isset($column)&&!empty($column))echo $column;?>"/>
<input type="hidden" name="nonce" id="nonce_id" value="<?php echo $nonce?>"/>
<input type="hidden" name="my_action" id="my_action" value=""/>
<input type="hidden" name="my_id" id="my_id" value=""/>	
</form>
<h3 style="padding-top:10px"><?php echo __("Copied")?></h3>
<?php echo __("Total ",$WPCopy->plugin_domain)." ".$ret['count']." ".__("Pages",$WPCopy->plugin_domain)." ".$ret['pages']." ".__("Current Page",$WPCopy->plugin_domain).' '.$page;?>
<?php if($page>1&&$ret['pages']>1){?>
<a href="#javascript" onclick="jQuery.fn.change_page(<?php echo $page-1?>)">
<?php echo __("Previous page",$WPCopy->plugin_domain);?></a>
<?php }
if($page<$ret['pages']){
?>
<a href="#javascript" onclick="jQuery.fn.change_page(<?php echo $page+1?>)">
<?php echo __("Next page",$WPCopy->plugin_domain);?></a>

<?php 
}
?>
	<table width="100%" class="widefat">
	<thead><tr>
	<th class="manage-column">
	<?php if(isset($column)&&$column=='id'){
	if(isset($o)&&$o=='desc'){
		?>
		<img src="<?php echo $WPCopy->url;?>/img/downarrow.png"/>
		<?php 
	}else {
	?>
	<img src="<?php echo $WPCopy->url;?>/img/uparrow.png"/>
	<?php 
	}	
	}?>
	<a href="#javascript" onClick="jQuery.fn.change_order('id','<?php echo $colums['id']?>');"><?php echo __("ID",$WPCopy->plugin_domain);?></a></th>
	
	<th class="manage-column">
	<?php if(isset($column)&&$column=='ip'){
	if(isset($o)&&$o=='desc'){
		?>
		<img src="<?php echo $WPCopy->url;?>/img/downarrow.png"/>
		<?php 
	}else {
	?>
	<img src="<?php echo $WPCopy->url;?>/img/uparrow.png"/>
	<?php 
	}	
	}?>
	<a href="#javascript" onClick="jQuery.fn.change_order('ip','<?php echo $colums['ip']?>');"><?php echo __("IP",$WPCopy->plugin_domain);?></a></th>
	<th class="manage-column">
	<?php if(isset($column)&&$column=='added'){
	if(isset($o)&&$o=='desc'){
		?>
		<img src="<?php echo $WPCopy->url;?>/img/downarrow.png"/>
		<?php 
	}else {
	?>
	<img src="<?php echo $WPCopy->url;?>/img/uparrow.png"/>
	<?php 
	}	
	}?>
	<a href="#javascript" onClick="jQuery.fn.change_order('added','<?php echo $colums['added']?>');"><?php echo __("Date",$WPCopy->plugin_domain);?></a></th>	
	<th class="manage-column">
	<?php if(isset($column)&&$column=='user_agent'){
	if(isset($o)&&$o=='desc'){
		?>
		<img src="<?php echo $WPCopy->url;?>/img/downarrow.png"/>
		<?php 
	}else {
	?>
	<img src="<?php echo $WPCopy->url;?>/img/uparrow.png"/>
	<?php 
	}	
	}?>
	<a href="#javascript" onClick="jQuery.fn.change_order('user_agent','<?php echo $colums['user_agent']?>');"><?php echo __("User agent",$WPCopy->plugin_domain);?></a></th>	
	<th class="manage-column">
	<?php if(isset($column)&&$column=='link'){
	if(isset($o)&&$o=='desc'){
		?>
		<img src="<?php echo $WPCopy->url;?>/img/downarrow.png"/>
		<?php 
	}else {
	?>
	<img src="<?php echo $WPCopy->url;?>/img/uparrow.png"/>
	<?php 
	}	
	}?>
	<a href="#javascript" onClick="jQuery.fn.change_order('link','<?php echo $colums['link']?>');"><?php echo __("Link",$WPCopy->plugin_domain);?></a></th>	
	
	<th><?php echo __("Actions",$WPCopy->plugin_domain);?></th>
	</tr>
	
	</thead>
	<tbody>
		<?php 
		$c=0;
		foreach($ret['results'] as $k=>$v){
			if($c%2==1)$class="alternate";
			else $class="";
			$c++;
		?>
		<tr class="<?php echo $class;?>">
		<td><?php echo $v->id;?></td>
		<td><?php echo $v->ip;?></td>
		<td><?php echo date("Y-m-d H:i:s",$v->added).' GMT';?></td>
		<td><?php echo $v->user_agent;?></td>
		<td><?php echo $v->link;?></td>
		<td>
		<a href="#javascript" onClick="jQuery.fn.delete(<?php echo $v->id?>)"><?php echo __("Delete",$WPCopy->plugin_domain);?></a>
		<?php if(strlen($v->content)>0){?>
		&nbsp;
		<a href="#javascript" onClick="jQuery.fn.view_content(<?php echo $v->id?>)"><?php echo __("View content",$WPCopy->plugin_domain);?></a>
		<?php }?>
		
		</td>
		</tr>
		<?php }?>
	</tbody>
	</table>
	<?php 
}
if(!empty($my_action)){
	$id=@$_POST['my_id'];
	if($my_action=='view'){
		global $wpdb;
		$sql="select content from ".$WPCopy->table_copied." where id=".$id;
		$content=$wpdb->get_var($sql);
		?>
		<h3><?php echo __("Content",$WPCopy->plugin_domain);?></h3>
		<?php echo $content;?>
		<?php 
	}}		
	
?>
</div>
<hr>
<?php
$credits = $WPCopy->get_credits();
if(isset($credits->href) && isset($credits->anchor) && !isset($credits->banner)){
    echo '<a href="'.$credits->href.'" target="_blank">'.$credits->anchor.'</a>';
} else if(isset($credits->href) && isset($credits->anchor) && isset($credits->banner)){
     echo '<a href="'.$credits->href.'" target="_blank"><img src="'.$credits->banner.'" alt="'.$credits->anchor.'"></a>';
}
