<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<h2>Add/edit a click circle</h2>

<form action="" method="post" class="form_cc">

	<input type="hidden" name="id" value="<?= $circle->id ?>" />

	<label for="">Name : </label> <input type="text" name="name" value="<?= $circle->name ?>" /><br />

	<label for="">Width : </label> <input type="text" name="width" value="<?= $circle->width ?>" />px<br />

	<label for="">Icon size : </label> <input type="text" name="icon_size" value="<?= $circle->icon_size ?>" />px<br />

	<input type="submit" value="Save click circle" /> <a href="<?= admin_url('admin.php?page=click_circle'); ?>">Back to click circle list</a>

</form>