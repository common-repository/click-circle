<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script>

	jQuery(document).ready(function(){

		jQuery('.form_cc input[name="color"], .form_cc input[name="bg_color"]').wpColorPicker();

		jQuery('.form_cc input[name="icon"]').keyup(function(){

			//on fait un autocomplète si la valeur n'est pas une URL
			if(jQuery(this).val().indexOf('http') == -1 && jQuery(this).val().length > 0)
			{
				jQuery('.form_cc .loading').show();

				//autocomplète ajax pour la choix de l'icone
				jQuery.post(ajaxurl, {action: 'cc_fa_icons_list', q: jQuery(this).val(), _ajax_nonce: '<?= wp_create_nonce( "fa_icons_list" ); ?>' }, function(icons){

					jQuery('.form_cc .icons_list_search').html(icons);

					jQuery('.form_cc .icons_list_search li').click(function(){

						var icon = jQuery(this).attr('rel');

						jQuery('.form_cc #new_icon').attr('class', 'fa fa-'+icon);

						jQuery('.form_cc input[name=icon]').val(icon);

						jQuery('.form_cc .icons_list_search').html('');

					});

					jQuery('.form_cc .loading').hide();
				});
			}
			else
				jQuery('.form_cc .icons_list_search').html('');

		});

		jQuery('.form_cc').submit(function(){

			var icon = jQuery(this).find('input[type=icon]').val();
			var name = jQuery(this).find('input[type=name]').val();

			if(name == "" || icon == "")
				alert('Please fill in all fields !');
			else
				jQuery.post(ajaxurl, jQuery(this).serialize(), function(){

					window.location.href = "<?= admin_url('admin.php?page=click_circle&task=manage&saved=1&id='.$circle->id) ?>";

				});

			return false;

		});

		jQuery('.cc_icons_list .remove').click(function(){

			var id = jQuery(this).attr('rel');

			jQuery.post(ajaxurl, { action: 'cc_remove_icon', id: id, _ajax_nonce: '<?= wp_create_nonce( "cc_remove_icon" ); ?>' }, function(){

				jQuery('.cc_icons_list li[rel='+id+']').remove();

			});

		});

		//changement d'ordre des icons
		jQuery('.cc_icons_list').sortable({
			update: function( event, ui ) {
				//effectuer le changement de position en BDD par Ajax
				jQuery.post(ajaxurl, {action: 'cc_order_icon', id: jQuery(ui.item).attr('rel'), order: (ui.item.index()), _ajax_nonce: '<?= wp_create_nonce( "cc_order_icon" ); ?>' });
			}
		});

	});

</script>

<h2>Manage click circle "<?= $circle->name ?>"</h2>

<form action="" method="post" class="form_cc">

	<input type="hidden" name="id" value="<?= $icon->id ?>" />
	<input type="hidden" name="id_content" value="<?= $circle->id ?>" />
	<input type="hidden" name="action" , value="cc_save_icon" />
	<?php wp_nonce_field( "cc_save_icon" ); ?>

	<div class="name_line">
		<label for="">Icon:</label> 
		<input type="text" name="icon" value="<?= $icon->icon ?>" autocomplete="off" placeholder="Type to search..." />
		<i id="new_icon" class="fa fa-<?= $icon->icon ?>" style="font-size: <?= $circle_content->text_size ?>px"></i>
		<img src="<?= plugins_url( 'images/loading.gif', dirname(__FILE__)) ?>" class="loading" />
		<a href="https://fortawesome.github.io/Font-Awesome/icons/" target="_blank">List of all icons avalaible</a>
		<br />
		<div class="icons_list_search">
		</div>
	</div>

	<label for="">Icon color:</label> <input type="text" name="color" value="<?= $icon->color ?>" /><br />

	<label for="">Background color:</label> <input type="text" name="bg_color" value="<?= $icon->bg_color ?>" /><br />

	<label for="">Link:</label> <input type="text" name="link"  value="<?= $icon->link ?>" /><br />

	<label for="">Blank:</label> <input type="checkbox" name="blank" value="1" <?= ($icon->blank ? 'checked="checked"' : '') ?> /><br />

	<input type="submit" value="Save icon" />

</form>

<?php if(isset($_GET['saved'])) : ?>
	<h3>Icon saved!</h3>
<?php endif; ?>

<?php

	if(sizeof($icons) > 0)
	{
		echo '<ul class="cc_icons_list">';

		foreach( $icons as $icon )
		{
			echo '<li rel="'.$icon->id.'">
			<div class="cc_icon" style="background: '.$icon->bg_color.'" title="'.$icon->name.'">
			<i class="fa fa-'.$icon->icon.'" style="color: '.$icon->color.';"></i>';
			echo '</div>
			<a href="'.admin_url('admin.php?page=click_circle&task=manage&id='.$circle->id).'&id_icon='.$icon->id.'"><img src="'.plugins_url( 'images/edit.png', dirname(__FILE__) ).'" /></a>
			<a href="#" rel="'.$icon->id.'" class="remove"><img src="'.plugins_url( 'images/remove.png', dirname(__FILE__) ).'" /></a>
			</li>';

		}

		echo '</ul>';

	}
	else {
	
		echo '<p>No icons yet.</p>';

		}	

?>

<a href="<?= admin_url('admin.php?page=click_circle'); ?>">Back to click circles list </a>
