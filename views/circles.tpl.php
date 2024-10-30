		<div id="click_circle_<?= $circle->id ?>" class="click_circle" style="font-size: <?= $circle->icon_size ?>px">
			<?php

				foreach($circle->icons as $icon)
				{
					echo '<a href="'.$icon->link.'" '.($icon->blank == 1 ? 'target="_blank"' : '').' class="circle" style="width: '.$circle->width.'px; height:'.$circle->width .'px; color: '.$icon->color.'; background: '.$icon->bg_color.'">
					<i class="fa fa-'.$icon->icon.'" style="line-height: '.$circle->width.'px"></i>
					</a>';
				}
			?>
		</div>