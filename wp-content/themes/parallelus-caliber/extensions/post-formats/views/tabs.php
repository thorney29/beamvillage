<div id="cf-post-format-tabs" class="cf-nav" style="display: none;">
	<h2 class="nav-tab-wrapper">
		<?php

		foreach ( $this->post_formats as $format ) {
			$class = ( $format == $current_post_format || ( empty( $current_post_format ) && $format == 'standard' ) ? 'current nav-tab-active' : '' );

			if ( $format == 'standard' ) {
				$format_string = __( 'Standard', 'runway' );
				$format_hash   = 'post-format-0';
			} else {
				$format_string = get_post_format_string( $format );
				$format_hash   = 'post-format-' . $format;
			}

			echo '<a class="' . esc_attr( $class ) . ' nav-tab" href="#' . esc_attr( $format_hash ) . '">' . esc_html( $format_string ) . '</a>';
		}

		?>
	</h2>
</div>