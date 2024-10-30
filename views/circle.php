<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<h2>All click circles</h2>



<a href="<?= admin_url('admin.php?page=click_circle&task=new') ?>">Add a new click circle</a>
<?php


	if(sizeof($circles) > 0)
	{

		foreach($circles as $icon)

		{

			echo '<div class="beautiful_chart"><h3>'.$icon->name.'</h3>

			<a href="'.admin_url('admin.php?page=click_circle&task=manage&id='.$icon->id).'" title="Manage icons"><img src="'.plugins_url( 'images/manage.png', dirname(__FILE__) ).'" /></a>

			<a href="'.admin_url('admin.php?page=click_circle&task=edit&id='.$icon->id).'" title="Edit circle content"><img src="'.plugins_url( 'images/edit.png', dirname(__FILE__) ).'" /></a>

			<a href="'.admin_url('admin.php?page=click_circle&task=remove&id='.$icon->id).'" title="Remove circle content"><img src="'.plugins_url( 'images/remove.png', dirname(__FILE__) ).'" /></a>

			<br />

			<b>Shortcode : </b>

			<input type="text" value="[click-circle id='.$icon->id.']" readonly onClick="this.select()" />

			</div>';

		}

	}

	else

		echo 'No click circle created yet !';



?>

<h3>Like InfoD74 to check new plugins: <a href="https://www.facebook.com/infod74/" target="_blank"><img src="<?php echo plugins_url( 'images/fb.png', dirname(__FILE__)) ?>" alt="" /></a></h3>