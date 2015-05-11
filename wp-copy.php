<?php
if (basename ( __FILE__ ) == basename ( $_SERVER ['SCRIPT_FILENAME'] ))
	die ( 'This page cannot be called directly.' );
/*
Plugin Name: CopyLink
Version: 1.1
Plugin URI: http://dejanseo.com.au/copylink
Author: Dejan SEO
Author URI: http://dejanseo.com.au/
Description: CopyLink adds a custom link attribution under content copied from your website or blog. 
*/
if ( !class_exists('WPCopy') ) :
class WPCopy{
	var $dir;
	var $dirname;
	var $url;
	var $options;
	var $options_name;
	var $nonce="469328659823fhsdkjfhksdjfhsa";
	var $user_access="admin_wp_copy";
	var $table_copied;
	var $plugin_domain='wp-copy';
	function WPCopy(){
		$this->options_name="wp-copy";
		//Set dirname of the plugin
		$this->dir=dirname(plugin_basename(__FILE__));
	   	//Set Url for the plugin
		$this->url=defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) : trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)).'/'; 
	   	//Set dirname for the plugin
		$this->dirname=dirname(__FILE__);
		//Get options of the plugin
        $this->options=$this->get_option();
        global $wpdb;
        $this->table_copied=$wpdb->prefix."wp_copy";		
	}	
/**
	 * Add option to plugin
	 *
	 * @param string option name $name
	 * @param optiona value $value
	 */
    function add_option($name,$value){
		$this->options[$name]=$value;
	}
    /**
     * Save option to database 
     *
     * @param string option name $name
     * @param value of the option $value
     */
	function save_option($name,$value){
		//$options=$this->get_option();
		$this->options[$name]=$value;
		$this->set_options();
	}
	/**
	 * Return options from database
	 *
	 * @return array of options
	 */
    function get_option(){
		return get_option($this->options_name);
	}
	/**
	 * Save options to database
	 *
	 */
    function set_options(){
		update_option($this->options_name,$this->options);
	}
	/**
	 * Delete options after deinstall
	 *
	 */
	function delete_options(){
		delete_option($this->options_name);
	}
	function print_scripts(){
		if(!is_admin()){
			wp_enqueue_script("jquery");
		}else if(is_admin()){
				wp_enqueue_script("jquery");
		}
	}
	function wp_head(){
		if(!is_admin()){
			$nonce=wp_create_nonce($this->nonce);
			$ip=$_SERVER['REMOTE_ADDR'];
			$exclude=$this->options['exclude'];
			$req_uri=$_SERVER['REQUEST_URI'];
			$req_arr=array();
			if(strpos($req_uri,'?')!==false){
				//$req_arr[]=substr($req_uri,0,strpos($req_uri,'?'));
				$query=substr($req_uri,strpos($req_uri,'?'));
				$arr=explode("&",$query);
				$str=substr($req_uri,0,strpos($req_uri,'?'));//$req_arr[0];
				$p=0;
				foreach($arr as $k=>$v){
					if(!empty($v)){
						if($p)$str.='&';
						$str.=$v;
						$p++;
					}
					$req_arr[]=$str;
				}
			}else $req_arr[]=$req_uri;
			/*echo '<script type="text/javascript">';
			foreach($req_arr as $k=>$v)
			echo 'var my_req'.$k.'="'.$v.'";';
			foreach($exclude as $k=>$v)
			echo 'var my_ex'.$k.'="'.$v.'";';
			*/
			//echo '</script>';
			//if(in_array($req_uri,$exclude))$ex=1;
			//else $ex=0;
			//echo '<pre>'.print_r($exclude,true).'</pre>';
			//echo '<pre>'.print_r($req_arr,true).'</pre>';
			$ex=0;
			if(!empty($exclude)){
			foreach($req_arr as $k=>$v){
			if($v!=''){	
				if(in_array($v,$exclude)){
			 	$ex=1;
			 	break;
			 }}
			}}
			//echo $ex;
			echo '<script type="text/javascript" src="'.$this->url.'/script/jscript3.js"></script>';
			echo '<script type="text/javascript">var wp_copy={tag:"'.$this->options['tag'].'",ex:'.$ex.',url:"'.$this->url.'",nonce:"'.$nonce.'",ip:"'.$ip.'",save:'.$this->options['save'].',save_content:'.$this->options['save_content'].'};</script>';		
		}
	}
	function install(){
		global $wp_roles;
		$wp_roles->add_cap ( 'administrator', $this->user_access );
		global $wpdb;
		$sql="create table if not exists ".$this->table_copied."( 
		id				bigint unsigned auto_increment primary key,
		ip				char(30),
		user_agent		char(255),
		added			int unsigned,			
		user_id			bigint unsigned,
		link			text 		CHARACTER SET UTF8,
		content			text		CHARACTER SET UTF8,
		trackingcode	char(255)
		)";
		$wpdb->query($sql);
		$options['save']=0;
		$options['save_content']=0;
		$options['tag']='Read more';
		$this->options=$options;
		$this->set_options();
	}
	function deinstall(){
		global $wp_roles;
		$wp_roles->remove_cap ( 'administrator', $this->user_access );
		global $wpdb;
		$sql="drop table ".$this->table_copied;
		$wpdb->query($sql);
		$this->delete_options();
		
	}
	function admin_menus(){
		add_menu_page(__('Link copy',$this->plugin_domain),__('Link copy',$this->plugin_domain),$this->user_access,$this->dir.'/admin/copied.php');	    
	    //add_submenu_page($this->dir.'/admin/post.php','Status','Status',8,$this->dir.'/admin/status.php');
	    add_submenu_page($this->dir.'/admin/copied.php',__('Settings',$this->plugin_domain),__('Settings',$this->plugin_domain),$this->user_access,$this->dir.'/admin/settings.php');
	   
		
	}
	function get_copied(&$page,$per_page=1,$order=''){
		global $wpdb;
		$count=$wpdb->get_var('select count(*) as num from '.$this->table_copied);
		$pages=ceil($count/$per_page);
		if($page<1||$page>$pages)$page=1;
		$sql="select * from ".$this->table_copied;
		if($order!='')$sql.=' order by '.$order;
		$poc=($page-1)*$per_page;
		$sql.=" limit ".$poc.",".$per_page;
		$ret=$wpdb->get_results($sql);
		$return['pages']=$pages;
		$return['count']=$count;
		$return['results']=$ret;
		return $return;
	}
        function get_credits(){
            $credits_url = "http://linkserver.dejanseo.org/api.php?consumer=WpCopy";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $credits_url);
            $returned = curl_exec($ch);

            return json_decode($returned);
        }
	function admin_head(){
		$page=@$_GET['page'];
		if(isset($page)){
			if($page==$this->dir.'/admin/copied.php'){
				echo '<script type="text/javascript" src="'.$this->url.'/script/admin.js"></script>';
				echo '<script type="text/javascript">var copy_url="'.$this->url.'";</script>';
			}
		}
	}
}	
endif;
if (class_exists ( "WPCopy" )) :
	//echo 'Ok';
	$WPCopy = new WPCopy ();
	if (isset ( $WPCopy )) {
		register_activation_hook ( __FILE__, array (&$WPCopy, "install" ) );
		register_deactivation_hook ( __FILE__, array (&$WPCopy, "deinstall" ) );
		add_action ( 'wp_print_scripts', array (&$WPCopy, 'print_scripts' ) );
		add_action ('wp_head',array(&$WPCopy,'wp_head'));
		add_action ( 'admin_menu', array (&$WPCopy, 'admin_menus' ), 20 );
		add_action ( 'admin_head', array (&$WPCopy, 'admin_head' ) );
	}
endif;