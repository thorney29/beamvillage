<?php
if ( ! empty( $settings->heading_type ) && $settings->heading_type == 'default' ):
	include_once __DIR__ . '/frontend-bb.php';
else:
	if ( empty( $settings->pre_heading ) && empty( $settings->heading ) && empty( $settings->lead ) && FLBuilderModel::is_builder_active() ) :
		// Empty, no text and we're currently editing
		?>

		<div class="fee-heading" style="color: #ccccce; border: 3px dashed #dddddf; text-align:center; margin: 4px;">
			<div style="padding: 20px;">
				<i class="fa fa-header fa-3x"></i>
			</div>
		</div>

	<?php else:
		// We have content

		// content values
		$align = ( isset( $settings->heading_align ) ) ? 'style="text-align: ' . esc_attr( $settings->heading_align ) . '"' : '';

		?>
	<div class="fee-heading" <?php echo $align; // escaped above
	?>>
		<?php

		// content values
		$pre     = ( isset( $settings->pre_heading ) ) ? $settings->pre_heading : '';
		$heading = ( isset( $settings->heading ) ) ? $settings->heading : '';
		$h       = ( isset( $settings->heading_element ) ) ? $settings->heading_element : 'h3';
		$size    = ( isset( $settings->heading_size ) ) ? $settings->heading_size : '';
		$lead    = ( isset( $settings->lead ) ) ? $settings->lead : '';

		// HTML tags allowed in output
		$allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
				'class' => array()
			),
			'br'     => array(),
			'em'     => array(),
			'i'      => array(
				'class' => array()
			),
			'strong' => array(),
			'b'      => array(),
			'span'   => array(
				'class' => array()
			),
		);
		$allowed_html = apply_filters( 'fee_heading_module_allowed_html', $allowed_html );

		// Start printing the content
		if ( ! empty( $pre ) ) { ?>
			<p class="pre-heading fee-pre-heading"><?php echo wp_kses( $pre, $allowed_html ); ?></p>
		<?php }
		if ( ! empty( $heading ) ) { ?>
			<<?php echo esc_attr( $h ) ?> class="fee-heading <?php echo esc_attr( $size ) ?>"><?php echo wp_kses( $heading, $allowed_html ) ?></<?php echo esc_attr( $h ) ?>>
		<?php }
		if ( ! empty( $lead ) ) { ?>
			<p class="lead fee-lead"><?php echo wp_kses( $lead, $allowed_html ); ?></p>
		<?php } ?>

		</div>
		<?php
	endif;
endif;
