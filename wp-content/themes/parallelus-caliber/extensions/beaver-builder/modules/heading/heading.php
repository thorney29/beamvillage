<?php

/**
 * @class FLRichTextModule
 */
class FEEHeadingModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Heading', 'runway' ),
			'description'     => __( 'Add text headings with titles and sub-titles.', 'runway' ),
			'category'        => __( 'Basic Modules', 'fl-builder' ),
			'partial_refresh' => true
		) );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'FEEHeadingModule', array(
	'general'       => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array(
			'heading_type'    => array(
				'title'  => '',
				'fields' => array(
					'heading_type' => array(
						'type'    => 'select',
						'label'   => __( 'Heading Type', 'fl-builder' ),
						'default' => 'caliber',
						'options' => array(
							'caliber' => __( 'Caliber Heading', 'fl-builder' ),
							'default' => __( 'Deafult Heading', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'refresh'
						)
					),
				)
			),
			'general'         => array(
				'title'  => '',
				'fields' => array(
					'pre_heading' => array(
						'type'    => 'text',
						'label'   => __( 'Label', 'fl-builder' ),
						'default' => '',
						'help'    => __( 'A pre-headline text description for the section or title.', 'fl-builder' ),
						'preview' => array(
							'type' => 'refresh'
						)
					),
					'heading'     => array(
						'type'    => 'text',
						'label'   => __( 'Heading', 'fl-builder' ),
						'default' => '',
						'preview' => array(
							'type' => 'refresh'
						)
					),
					'lead'        => array(
						'type'    => 'textarea',
						'label'   => __( 'Sub Title', 'fl-builder' ),
						'default' => '',
						'help'    => __( 'Text to accompany the title for extra details or a description.',
							'fl-builder' ),
						'preview' => array(
							'type' => 'refresh'
						)
					)
				)
			),
			'caliber_options' => array(
				'title'  => __( 'Options', 'fl-builder' ),
				'fields' => array(
					'heading_element' => array(
						'type'    => 'select',
						'label'   => __( 'Heading Tag', 'fl-builder' ),
						'default' => 'h3',
						'options' => array(
							'h1' => __( 'H1', 'fl-builder' ),
							'h2' => __( 'H2', 'fl-builder' ),
							'h3' => __( 'H3', 'fl-builder' ),
							'h4' => __( 'H4', 'fl-builder' ),
							'h5' => __( 'H5', 'fl-builder' ),
							'h6' => __( 'H6', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'refresh'
						)
					),
					'heading_size'    => array(
						'type'    => 'select',
						'label'   => __( 'Heading Size', 'fl-builder' ),
						'default' => '',
						'options' => array(
							''   => __( 'Normal', 'fl-builder' ),
							'x2' => __( 'Large', 'fl-builder' ), // .x2 class added
						),
						'help'    => __( 'Show at normal size or make it larger.', 'fl-builder' ),
						'preview' => array(
							'type' => 'refresh'
						)
					),
					'heading_align'   => array(
						'type'    => 'select',
						'label'   => __( 'Align Text', 'fl-builder' ),
						'default' => 'center',
						'options' => array(
							'center' => __( 'Center', 'fl-builder' ),
							'left'   => __( 'Left', 'fl-builder' ),
							'right'  => __( 'Right', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'refresh'
						)
					),
				)
			),
			'default_options' => array(
				'title'  => __( 'Link', 'fl-builder' ),
				'fields' => array(
					'link'        => array(
						'type'    => 'link',
						'label'   => __( 'Link', 'fl-builder' ),
						'preview' => array(
							'type' => 'none'
						)
					),
					'link_target' => array(
						'type'    => 'select',
						'label'   => __( 'Link Target', 'fl-builder' ),
						'default' => '_self',
						'options' => array(
							'_self'  => __( 'Same Window', 'fl-builder' ),
							'_blank' => __( 'New Window', 'fl-builder' )
						),
						'preview' => array(
							'type' => 'none'
						)
					)
				)
			)
		)
	),
	'style_default' => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'colors'      => array(
				'title'  => __( 'Colors', 'fl-builder' ),
				'fields' => array(
					'color' => array(
						'type'       => 'color',
						'show_reset' => true,
						'label'      => __( 'Text Color', 'fl-builder' )
					),
				)
			),
			'structure'   => array(
				'title'  => __( 'Structure', 'fl-builder' ),
				'fields' => array(
					'alignment'             => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'left'   => __( 'Left', 'fl-builder' ),
							'center' => __( 'Center', 'fl-builder' ),
							'right'  => __( 'Right', 'fl-builder' )
						),
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-heading',
							'property' => 'text-align'
						)
					),
					'tag'                   => array(
						'type'    => 'select',
						'label'   => __( 'HTML Tag', 'fl-builder' ),
						'default' => 'h3',
						'options' => array(
							'h1' => 'h1',
							'h2' => 'h2',
							'h3' => 'h3',
							'h4' => 'h4',
							'h5' => 'h5',
							'h6' => 'h6'
						)
					),
					'font'                  => array(
						'type'    => 'font',
						'default' => array(
							'family' => 'Default',
							'weight' => 300
						),
						'label'   => __( 'Font', 'fl-builder' ),
						'preview' => array(
							'type'     => 'font',
							'selector' => '.fl-heading-text'
						)
					),
					'font_size'             => array(
						'type'    => 'select',
						'label'   => __( 'Font Size', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'custom_font_size' )
							)
						)
					),
					'custom_font_size'      => array(
						'type'        => 'text',
						'label'       => __( 'Custom Font Size', 'fl-builder' ),
						'default'     => '24',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px'
					),
					'line_height'           => array(
						'type'    => 'select',
						'label'   => __( 'Line Height', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'custom_line_height' )
							)
						)
					),
					'custom_line_height'    => array(
						'type'        => 'text',
						'label'       => __( 'Custom Line Height', 'fl-builder' ),
						'default'     => '1.4',
						'maxlength'   => '4',
						'size'        => '4',
						'description' => 'em'
					),
					'letter_spacing'        => array(
						'type'    => 'select',
						'label'   => __( 'Letter Spacing', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'custom_letter_spacing' )
							)
						)
					),
					'custom_letter_spacing' => array(
						'type'        => 'text',
						'label'       => __( 'Custom Letter Spacing', 'fl-builder' ),
						'default'     => '0',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px'
					)
				)
			),
			'r_structure' => array(
				'title'  => __( 'Mobile Structure', 'fl-builder' ),
				'fields' => array(
					'r_alignment'             => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'r_custom_alignment' )
							)
						),
						'preview' => array(
							'type' => 'none'
						)
					),
					'r_custom_alignment'      => array(
						'type'    => 'select',
						'label'   => __( 'Custom Alignment', 'fl-builder' ),
						'default' => 'center',
						'options' => array(
							'left'   => __( 'Left', 'fl-builder' ),
							'center' => __( 'Center', 'fl-builder' ),
							'right'  => __( 'Right', 'fl-builder' )
						),
						'preview' => array(
							'type' => 'none'
						)
					),
					'r_font_size'             => array(
						'type'    => 'select',
						'label'   => __( 'Font Size', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'r_custom_font_size' )
							)
						)
					),
					'r_custom_font_size'      => array(
						'type'        => 'text',
						'label'       => __( 'Custom Font Size', 'fl-builder' ),
						'default'     => '24',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px'
					),
					'r_line_height'           => array(
						'type'    => 'select',
						'label'   => __( 'Line Height', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'r_custom_line_height' )
							)
						)
					),
					'r_custom_line_height'    => array(
						'type'      => 'text',
						'label'     => __( 'Custom Line Height', 'fl-builder' ),
						'default'   => '1.4',
						'maxlength' => '4',
						'size'      => '4'
					),
					'r_letter_spacing'        => array(
						'type'    => 'select',
						'label'   => __( 'Letter Spacing', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'custom'  => __( 'Custom', 'fl-builder' )
						),
						'toggle'  => array(
							'custom' => array(
								'fields' => array( 'r_custom_letter_spacing' )
							)
						)
					),
					'r_custom_letter_spacing' => array(
						'type'        => 'text',
						'label'       => __( 'Custom Letter Spacing', 'fl-builder' ),
						'default'     => '0',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px'
					)
				)
			)
		)
	)
) );