<?php
class KarmaWidget extends WP_Widget {
    function KarmaWidget() {
        parent::WP_Widget(false, $name = 'Top Karma Users');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $wpdb;
        extract( $args );

		// Params
   	    $count = esc_attr($instance['count']);
   	    $title = esc_attr($instance['title']);
   	    $avatar = isset( $instance['avatar'] ) ? $instance['avatar'] : false;
   	    $listado =isset( $instance['listado'] ) ? $instance['listado'] : false;

		$sql = "SELECT user.ID, user.user_email, user.user_nicename, meta.meta_value
				FROM $wpdb->usermeta meta, $wpdb->users user
				WHERE meta.meta_key = 'karma'
				AND meta.user_id = user.ID
				ORDER BY CONVERT( meta.meta_value, SIGNED ) DESC
				LIMIT ".intval($count);
		$results = $wpdb->get_results($sql);
        ?>
              <?php echo $before_widget;  ?>
			<h3><?=str_replace('%c', $count, $title)?></h3>
			<?php if ($listado) echo '<ul class="wp_answers_karma_users">';
						foreach($results as $row):?>
						<?php if ($listado) echo '<li>'; ?>
					 		<?php if ($avatar) echo get_avatar( $row->user_email, 32 ); ?>
							<strong><?=$row->user_nicename?> (<?=$row->meta_value?>)</strong>
						<?php if ($listado) echo '</li>'; ?>
                  <?php endforeach; ?>
              <?php 
				if ($listado) echo '</ul>';
				echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $count = esc_attr($instance['count']);
        $avatar = esc_attr($instance['avatar']);
        $listado = esc_attr($instance['listado']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título (%c == Número de usuarios):'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Número de usuarios:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" /></label></p>
        
		<p><input class="checkbox" id="<?php echo $this->get_field_id('avatar'); ?>" name="<?php echo $this->get_field_name('avatar'); ?>" type="checkbox" <?php checked( $instance['avatar'], true ); ?> /><label for="<?php echo $this->get_field_id('avatar'); ?>"><?php _e(' Mostrar el avatar'); ?> </label></p>
		
        <p><input class="checkbox" id="<?php echo $this->get_field_id('listado'); ?>" name="<?php echo $this->get_field_name('listado'); ?>" type="checkbox" <?php checked( $instance['listado'], true ); ?> /><label for="<?php echo $this->get_field_id('listado'); ?>"><?php _e(' Mostrar en listado'); ?> </label></p>
        <?php 
    }

} // class FooWidget

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("KarmaWidget");'));

?>