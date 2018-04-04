<?php

$container_wrap_class = apply_filters( 'fl_rw_row_content_wrap_class', 'fl-row-content-wrap', $row );

$container_class      = 'fl-row-content ';
if ( isset( $row->settings->content_width ) ) {
	$container_class .= ' fl-row-' . $row->settings->content_width . '-width';
}
$container_class .= ' fl-node-content';
$container_class = apply_filters( 'fl_rw_row_content_class', $container_class, $row );

?>

<div<?php FLBuilder::render_row_attributes( $row ); ?>>
	<div class="<?php echo esc_attr( $container_wrap_class ); ?>">
		<?php FLBuilder::render_row_bg( $row ); ?>
		<div class="<?php echo esc_attr( $container_class ); ?>">
			<?php
			// $groups received as a magic variable from template loading.
			foreach ( $groups as $group ) {
				FLBuilder::render_column_group( $group );
			}

			?>
		</div>
	</div>
</div>
