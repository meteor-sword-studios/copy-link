<?php 
require_once '../../../wp-load.php';
//require_once 'wp-image.php';
//$WPImage=new WPImage(1);
$nonce=@$_POST['nonce'];
if(!wp_verify_nonce($nonce,$WPCopy->nonce))die('SEcurity check');
$task=@$_GET['my_task'];
switch($task){
	case 'add':
		require_once 'includes/Browser.php';
		global $current_user;
		get_currentuserinfo();
		$user_id=$current_user->ID;
		$browser=new Browser();
		$user_agent=$browser->getBrowser();
		$content=@$_POST['content'];
		$link=@$_POST['link'];
		$ip=@$_SERVER['REMOTE_ADDR'];
		$content=strip_tags($content);
			global $wpdb;
			if($WPCopy->options['save']){
			if(!$WPCopy->options['save_content']){
				$content='';
				$date=gmmktime();
				$wpdb->insert(
				$WPCopy->table_copied,array('id'=>'','ip'=>$ip,'user_id'=>$user_id,
				'user_agent'=>$user_agent,'added'=>$date,'link'=>$link,'content'=>'','trackingcode'=>'')		
				);
				
			}else if(!empty($content)){
				$date=gmmktime();
				$wpdb->insert(
				$WPCopy->table_copied,array('id'=>'','ip'=>$ip,'user_id'=>$user_id,
				'user_agent'=>$user_agent,'added'=>$date,'link'=>$link,'content'=>$content,'trackingcode'=>'')		
				);
				}	
		}
	break;
	default:
	die('');
	break;		
}