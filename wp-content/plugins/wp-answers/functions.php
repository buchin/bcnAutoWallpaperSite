<?php
add_action('admin_menu', 'SOF_add_menu');

function SOF_add_menu() {
	if (!get_option('SOF_bonoOK')){
		  update_option( 'SOF_register', false);
		  update_option( 'SOF_bonoOK', '1');
		  update_option( 'SOF_bonoKO', '1');
		  update_option( 'SOF_bonoVOTE', '0.1');
	}	  
		  
  add_options_page('WP-Answers', 'WP-Answers', 9, basename(__FILE__), 'SOF_plugin_options');
}

function SOF_plugin_options() {
	global $wpdb;
	
	// Actualizamos las opciones
	if (isset($_POST['action']) && $_POST['action'] == 'modificar') {
		  update_option( 'SOF_register', $_POST['register'] == 'S'?'true':'false');
		  update_option( 'SOF_bonoOK', $_POST['bonoOK']);
		  update_option( 'SOF_bonoKO', $_POST['bonoKO']);
		  update_option( 'SOF_bonoVOTE', $_POST['bonoVOTE']);
		  update_option( 'SOF_category', $_POST['category']);
	}
	
	// Cargamos opciones
	$register = get_option('SOF_register') == 'true'?'checked="checked"':'';
	$bonoOK = get_option('SOF_bonoOK');
	$bonoKO = get_option('SOF_bonoKO');
	$bonoVOTE = get_option('SOF_bonoVOTE');
    $category = get_option('SOF_category');


	/* Limpieza de usuarios*/
	if (isset($_POST['karma_user_ID'])) {
			update_usermeta( intval($_POST['karma_user_ID']), "karma", 0);
	}
	
	/* Limpieza de artículos*/
	if (isset($_POST['karma_post_ID'])){
		$wpdb->query($wpdb->prepare("	UPDATE `$wpdb->comments` 
										SET `comment_karma` = 0 
										WHERE `comment_post_ID` =%d", intval($_POST['karma_post_ID'])));
	}
	
	
	?>
	<div class="wrap">
		<h2><?php _e('WP-Answers Options'); ?></h2>
		<form method="post" action="">
			<input name="action" value="modificar" type="hidden" />
			<h3>Register</h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">Register required</th>
						<td><input type="checkbox" name="register" value="S" <?=$register?> /></td>
					</tr>

					<tr>
						<th scope="row">Asociate to a category</th>
						<td>
                        <?php wp_dropdown_categories('hide_empty=0&name=category&selected='.$category); ?>
						All blog comments?<input name="category" type="checkbox" class="category" value="-1"/>
                        </td>
					</tr>
				</tbody>
			</table>
			<script type="text/javascript">
			jQuery("input.category").bind("click", function(){
			    var $sel = jQuery("select#category");
			    if (!$sel.attr('disabled')){
			        $sel.attr('disabled', 'disabled');
			        jQuery(this).attr('name', 'category');
			    } else {
			        $sel.attr('disabled', '');
			        jQuery(this).attr('name', 'nocategory');
			    }
			});
			<?php if ($category == -1) echo 'jQuery("input.category").click();';?>
			</script>

			<h3>Karma Options</h3>
			
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">Karma increment on positive vote</th>
						<td><input type="text" name="bonoOK" value="<?=$bonoOK?>" /></td>
					</tr>

					<tr>
						<th scope="row">Karma decrement on negative vote</th>
						<td><input type="text" name="bonoKO" value="<?=$bonoKO?>" /></td>
					</tr>


					<tr>
						<th scope="row">Karma increment on vote action</th>
						<td><input type="text" name="bonoVOTE" value="<?=$bonoVOTE?>" /></td>
					</tr>

				</tbody>
			</table>
			<input type="submit" value="Guardar" />
		</form>
		<h2>Top 25 users</h2>
		<table class="widefat post fixed">
			<thead>
				<tr>
					<th style="" class="manage-column column-cb check-column" id="position" scope="col">Nr.</th>
					<th style="" class="manage-column column-title" id="username" scope="col">User</th>
					<th style="" class="manage-column column-date" id="karma" scope="col">Karma</th>
					<th style="" class="manage-column column-date" id="actions" scope="col">Acciones</th>
				</tr>
			</thead>
			<tbody>
		<?php
		// Los 25 usuarios con m�s Karma
		$sql = "SELECT user.ID, user.user_email, user.user_nicename, meta.meta_value
						FROM $wpdb->usermeta meta, $wpdb->users user
						WHERE meta.meta_key = 'karma'
						AND meta.user_id = user.ID
						ORDER BY CONVERT( meta.meta_value, SIGNED ) DESC
						LIMIT 25";
		$results = $wpdb->get_results($sql);
		
		if (is_array($results)) {
			$x=0;
			foreach($results as $row): 
			?>
			<tr class="<?=(($x++ % 2) == 0)?'alternate':''?>">
				<td><?=$x?></td>
				<td class="username column-username">
					 <?php echo get_avatar( $row->user_email, 32 ); ?>
					<strong><a href="user-edit.php?user_id=<?=$row->ID?>&amp;wp_http_referer=users.php"><?=$row->user_nicename?></a></strong>
				</td>
				<td><?=$row->meta_value?></td>
				<td>
					<form action="" method="post">
						<input type="hidden" name="karma_user_ID" value="<?=$row->ID?>" />
						<input type="submit" value="Vaciar" />
					</form>
				</td>
			</tr>
			<?php 
			endforeach;
		}
		?>
		</tbody>
			<tfoot>
				<tr>
					<th style="" class="manage-column column-cb check-column" id="" scope="col">Nr.</th>
					<th style="" class="manage-column column-title" id="username" scope="col">User</th>
					<th style="" class="manage-column column-date" id="karma" scope="col">Karma</th>
					<th style="" class="manage-column column-date" id="actions" scope="col">Acciones</th>
				</tr>
			</tfoot>
	</table>
		<h2>Top 25 articles</h2>
		<table class="widefat post fixed">
			<thead>
				<tr>
					<th style="" class="manage-column column-cb check-column" id="position" scope="col">Nr.</th>
					<th style="" class="manage-column column-title" id="username" scope="col">Article</th>
					<th style="" class="manage-column column-date" id="karma" scope="col">Karma</th>
				</tr>
			</thead>
			<tbody>
		<?php
		// Los 25 usuarios con m�s Karma
		$sql = "SELECT sum( c.comment_karma ) as suma, p.post_title, p.guid, p.ID
				FROM $wpdb->comments c, $wpdb->posts p
				WHERE c.comment_post_ID = p.ID
				AND c.comment_karma > 0
				GROUP BY 2
				ORDER BY 1 DESC
				LIMIT 25";
		$results = $wpdb->get_results($sql);
		
		if (is_array($results)) {
			$x=0;
			foreach($results as $row): 
			?>
			<tr class="<?=(($x++ % 2) == 0)?'alternate':''?>">
				<td><?=$x?></td>
				<td class="username column-username">
					 (<?=$row->suma?>)
					<strong><a href="<?=$row->guid?>"><?=$row->post_title?></a></strong>
				</td>
				<td><form action="" method="post">
					<input type="hidden" name="karma_post_ID" value="<?=$row->ID?>" />
					<input type="submit" value="Vaciar" />
					</form>
				</td>
			</tr>
			<?php 
			endforeach;
		}
		?>
		</tbody>
			<tfoot>
				<tr>
					<th style="" class="manage-column column-cb check-column" id="" scope="col">Nr.</th>
					<th style="" class="manage-column column-title" id="username" scope="col">Article</th>
					<th style="" class="manage-column column-date" id="karma" scope="col">Karma</th>
				</tr>
			</tfoot>
	</table>
	</div>
  <?php
}
?>