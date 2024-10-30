<?php

/*

Plugin Name: Click Circle
Plugin URI: 
Version: 1.02
Description: Create beautiful clickable circle with Font Awesome icons
Author: InfoD74
Author URI: https://www.info-d-74.com/en/shop/
Network: false
Text Domain: click-circle
Domain Path: 

*/



register_activation_hook( __FILE__, 'click_circle_install' );

register_uninstall_hook(__FILE__, 'click_circle_desinstall');



function click_circle_install() {



	global $wpdb;



	$contents_table = $wpdb->prefix . "click_circle";

	$contents_data_table = $wpdb->prefix . "click_circle_data";



	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');



	$sql = "

        CREATE TABLE `".$contents_table."` (

          id int(11) NOT NULL AUTO_INCREMENT,          

          name varchar(50) NOT NULL,

          width int(11) NOT NULL,

          icon_size varchar(10) NOT NULL,

          PRIMARY KEY  (id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

    ";



    dbDelta($sql);



    $sql = "

        CREATE TABLE `".$contents_data_table."` (

          id int(11) NOT NULL AUTO_INCREMENT,        

          icon varchar(500) NOT NULL,

          color varchar(30) NOT NULL,

          bg_color varchar(30) NOT NULL,

          link varchar(255) NOT NULL,

          blank int(1) NOT NULL,

          `order` int(5) NOT NULL,

          id_content int(11),

          PRIMARY KEY (id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

    ";

    

    dbDelta($sql);

}



function click_circle_desinstall() {



	global $wpdb;

	$contents_table = $wpdb->prefix . "click_circle";

	$contents_data_table = $wpdb->prefix . "click_circle_data";

	//suppression des tables

	$sql = "DROP TABLE ".$contents_table.";";

	$wpdb->query($sql);



    $sql = "DROP TABLE ".$contents_data_table.";";   

	$wpdb->query($sql);

}



add_action( 'admin_menu', 'register_click_circle_menu' );

function register_click_circle_menu() {

	add_menu_page('Click Circle', 'Click Circle', 'edit_pages', 'click_circle', 'click_circle', plugins_url( 'images/icon.png', __FILE__ ), 38);

}



add_action('admin_print_styles', 'click_circle_css' );

function click_circle_css() {

    wp_enqueue_style( 'ClickCircleStylesheet', plugins_url('css/admin.css', __FILE__) );

    wp_enqueue_style( 'ClickCircleFontAwesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

    wp_enqueue_style( 'wp-color-picker' );

}


add_action( 'admin_enqueue_scripts', 'load_script_click_circle' );
function load_script_click_circle() {

	wp_enqueue_media();
    wp_enqueue_script( 'wp-color-picker');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

}


function click_circle() {

	global $wpdb;

	$contents_table = $wpdb->prefix . "click_circle";

	$contents_data_table = $wpdb->prefix . "click_circle_data";

	if(current_user_can('edit_pages'))
	{

		if(isset($_GET['task']))
		{

			switch($_GET['task'])
			{

				case 'new':

				case 'edit':

					if(sizeof($_POST))
					{

						$query = "REPLACE INTO ".$contents_table." (`id`, `name`, `width`, `icon_size`)

						VALUES (%d, %s, %d, %d)";

						$query = $wpdb->prepare( $query, (int)$_POST['id'], sanitize_text_field(stripslashes_deep($_POST['name'])), sanitize_text_field($_POST['width']), sanitize_text_field($_POST['icon_size']) );

						$wpdb->query( $query );


						//on affiche tous les circle contents

						$circles = $wpdb->get_results("SELECT * FROM ".$contents_table." ORDER BY name");

						include(plugin_dir_path( __FILE__ ) . 'views/circle.php');



					}

					else

					{

						//édition d'un circle content existant ?

						if(is_numeric($_GET['id']))

						{

							$q = "SELECT * FROM ".$contents_table." WHERE id = %d";

							$query = $wpdb->prepare( $q, $_GET['id']);

							$circle = $wpdb->get_row( $query );

						}



						if(empty($circle))

							$circle = (object)'';


						include(plugin_dir_path( __FILE__ ) . 'views/edit.php');

					}



				break;



				case 'manage':



					if(is_numeric($_GET['id']))

					{


						$q = "SELECT * FROM ".$contents_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, $_GET['id']);

						$circle = $wpdb->get_row( $query );

						if($circle)

						{

							$q = "SELECT * FROM ".$contents_data_table." WHERE id_content = %d ORDER BY `order` ASC";

							$query = $wpdb->prepare( $q, $_GET['id']);

							$icons = $wpdb->get_results( $query );

							if(is_numeric($_GET['id_icon']))
							{
								foreach ($icons as $icon) {
									if($icon->id == $_GET['id_icon'])
										break;
								}
							}

							include(plugin_dir_path( __FILE__ ) . 'views/manage.php');

						}					

					}



				break;



				case 'remove':



					if(is_numeric($_GET['id']))

					{

						//on supprime les données et le graph

						$q = "DELETE FROM ".$contents_data_table." WHERE id_content = %d";

						$query = $wpdb->prepare( $q, $_GET['id']);

						$wpdb->query( $query );



						$q = "DELETE FROM ".$contents_table." WHERE id = %d";

						$query = $wpdb->prepare( $q, $_GET['id']);

						$wpdb->query( $query );

					}



					//on affiche tous les graphs

					$circles = $wpdb->get_results("SELECT * FROM ".$contents_table." ORDER BY name");

					include(plugin_dir_path( __FILE__ ) . 'views/circle.php');

				break;

			}

		}

		else

		{

			if(!is_numeric($_GET['id']))

			{

				//on affiche tous les graphs

				$circles = $wpdb->get_results("SELECT * FROM ".$contents_table." ORDER BY name");

				include(plugin_dir_path( __FILE__ ) . 'views/circle.php');

			}

		}

	}



}



add_shortcode('click-circle', 'display_click_circle');

function display_click_circle($atts) {

	if(is_numeric($atts['id']))

	{

		global $wpdb;


		$contents_table = $wpdb->prefix . "click_circle";

		$contents_data_table = $wpdb->prefix . "click_circle_data";

		$q = "SELECT * FROM ".$contents_table." WHERE id = %d";

		$query = $wpdb->prepare( $q, $atts['id']);

		$circle = $wpdb->get_row( $query );

		if($circle)
		{

			$q = "SELECT * FROM ".$contents_table." WHERE id = %d";

			$query = $wpdb->prepare( $q, $atts['id'] );

			$circle = $wpdb->get_row( $query );

			//print_r($circle_content);

			$q = "SELECT * FROM ".$contents_data_table." WHERE id_content = %d ORDER BY `order` ASC";

			$query = $wpdb->prepare( $q, $atts['id'] );

			$circle->icons = $wpdb->get_results( $query );

			wp_enqueue_style( 'ClickCircleFrontStylesheet', plugins_url('css/front.css', __FILE__) );
			wp_enqueue_style( 'ClickCircleFrontFontAwesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

			$html = '';

			if($atts['show_title'] == true)

				$html .= '<h3>'.$circle->name.'</h3>';

			ob_start();
			include(plugin_dir_path( __FILE__ ) . 'views/circles.tpl.php');
			$html .= ob_get_clean();

			return $html;

		}	

		else

			return 'Click circle ID '.$atts['id'].' not found !';	

	}

}


//Ajax : autocomplète icons
add_action( 'wp_ajax_cc_fa_icons_list', 'cc_fa_icons_list' );

function cc_fa_icons_list() {

	if(current_user_can('edit_pages'))
	{

		check_ajax_referer( 'fa_icons_list' );

		require_once(plugin_dir_path( __FILE__ ) . 'icons_lists.php');

		global $fa_icons;

		if($_POST['q'])
			$icons_list = preg_grep("/^(.*)".$_POST['q']."(.*)$/", $fa_icons);
		else
			$icons_list = $fa_icons;

		if(sizeof($icons_list) > 0)
		{
			include(plugin_dir_path( __FILE__ ) . 'views/icons_list.php');
		}
		else
			echo 'No icon found !';
	}
	wp_die();
}

//Ajax : sauvegarde d'une icone
add_action( 'wp_ajax_cc_save_icon', 'cc_save_icon' );

function cc_save_icon() {

	if(current_user_can('edit_pages'))
	{
		check_ajax_referer( 'cc_save_icon' );

		if(!empty($_POST['icon']))
		{
			global $wpdb;

			$contents_data_table = $wpdb->prefix . "click_circle_data";

			if(empty($_POST['id']))
			{
				//trouve le max order
				$query = "SELECT MAX(`order`)+1 as max FROM ".$contents_data_table." WHERE id_content = %d";

				$query = $wpdb->prepare( $query, $_POST['id_content'] );

				$max = $wpdb->get_row( $query );

				$query = "REPLACE INTO ".$contents_data_table." ( `icon`, `color`, `bg_color`, `link`, `blank`, `order`, `id_content`)
				VALUES (%s, %s, %s, %s, %d, %d, %d)";

				$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['icon'])), sanitize_text_field(stripslashes_deep($_POST['color'])), sanitize_text_field(stripslashes_deep($_POST['bg_color'])), sanitize_text_field(stripslashes_deep($_POST['link'])), sanitize_text_field($_POST['blank']), $max->max, (int)$_POST['id_content'] );

			}
			else
			{

				$query = "UPDATE ".$contents_data_table."
				SET `icon` = %s, `color` = %s, `bg_color` = %s, `link` = %s, `blank` = %d, `id_content` = %d
				WHERE `id` = %d";

				$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['icon'])), sanitize_text_field(stripslashes_deep($_POST['color'])), sanitize_text_field(stripslashes_deep($_POST['bg_color'])), sanitize_text_field(stripslashes_deep($_POST['link'])), sanitize_text_field($_POST['blank']), sanitize_text_field($_POST['id_content']), (int)$_POST['id']);

			}

			$wpdb->query( $query );
		}
	}

	wp_die();

}

//Ajax : autocomplète icons
add_action( 'wp_ajax_cc_remove_icon', 'cc_remove_icon' );

function cc_remove_icon() {

	check_ajax_referer( 'cc_remove_icon' );

	if(is_numeric($_POST['id']))
	{
		global $wpdb;
		$contents_data_table = $wpdb->prefix . "click_circle_data";
		$query = "DELETE FROM ".$contents_data_table." WHERE id = %d";
		$query = $wpdb->prepare( $query, $_POST['id'] );
		$wpdb->query( $query );
	}

	wp_die();

}

//Ajax : changement de position d'une icone
add_action( 'wp_ajax_cc_order_icon', 'cc_order_icon' );

function cc_order_icon() {

	check_ajax_referer( 'cc_order_icon' );

	if (is_admin()) {
		global $wpdb;

		$contents_data_table = $wpdb->prefix . "click_circle_data";

		if(is_numeric($_POST['id']) && is_numeric($_POST['order']))
		{
			$icon = $wpdb->get_row( $wpdb->prepare( "SELECT id_content, `order` FROM ".$contents_data_table." WHERE id = %d", $_POST['id'] ));
			if($_POST['order'] > $icon->order)
				$wpdb->query( $wpdb->prepare( "UPDATE ".$contents_data_table." SET `order` = `order` - 1 WHERE id_content = %d AND `order` <= %d AND `order` > %d", $icon->id_content, $_POST['order'], $icon->order ));
			else
				$wpdb->query( $wpdb->prepare( "UPDATE ".$contents_data_table." SET `order` = `order` + 1 WHERE id_content = %d AND `order` >= %d AND `order` < %d", $icon->id_content, $_POST['order'], $icon->order ));
			$wpdb->query( $wpdb->prepare( "UPDATE ".$contents_data_table." SET `order` = %d WHERE id = %d", $_POST['order'], $_POST['id'] ));
			
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}

?>