<?php

/**
 * @class FLRichTextModule
 */
class FEEPostsModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __('Blog', 'fl-builder'),
			'description'   	=> __('List posts your blog or a custom post type.', 'fl-builder'),
			'category'      	=> __('Posts', 'fl-builder'),
			// 'partial_refresh'	=> true
		));
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FEEPostsModule', array(
	'general'       => array( // Tab
		'title'         => __('General', 'fl-builder'), // Tab title
		'sections'      => array( // Tab Sections
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'post_type'  => array(
						'type'          => 'post-type',
						'label'         => __('Content Source', 'fl-builder'),
						'default'       => 'post',
						'help'          => __('The post type to use as the content source.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'posts_per_page'  => array(
						'type'          => 'text',
						'label'         => __('Number of Posts', 'fl-builder'),
						'default'       => get_option('posts_per_page'), // WP default in "Settings > Reading"
						'maxlength'     => '4',
						'size'          => '5',
						'help'          => __('How many posts to shown per page.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'pagination'  => array(
						'type'          => 'select',
						'label'         => __('Pagination', 'fl-builder'),
						'default'       => 'hide',
						'options'       => array(
							'hide'        => __('Hide', 'fl-builder'),
							'show'        => __('Show', 'fl-builder')
						),
						'help'          => __('Display the navigation controls for paging. ( 1, 2, 3...)', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'categories'  => array(
						'type'          => 'select',
						'label'         => __('Categories', 'fl-builder'),
						'default'       => 'all',
						'options'       => array(
							'all'         => __('All', 'fl-builder'),
							'include'     => __('Include only', 'fl-builder'),
							'exclude'     => __('Exclude only', 'fl-builder'),
							'both'        => __('Include and exclude', 'fl-builder')
						),
						'toggle'        => array(
							'include'     => array(
								'fields'    => array('category_include')
							),
							'exclude'     => array(
								'fields'    => array('category_exclude')
							),
							'both'        => array(
								'fields'    => array('category_include','category_exclude')
							)
						),
						'help'          => __('Specify categories to limit results.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'category_include'  => array(
						'type'          => 'text',
						'label'         => __('Include Categories', 'fl-builder'),
						'placeholder'   => __( '1,2,3', 'fl-builder' ),
						'default'       => '',
						'help'          => __('Comma separated list of category IDs to include.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'category_exclude'  => array(
						'type'          => 'text',
						'label'         => __('Exclude Categories', 'fl-builder'),
						'placeholder'   => __( '4,5,6', 'fl-builder' ),
						'default'       => '',
						'help'          => __('Comma separated list of category IDs to exclude.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
				)
			),
			/*
			'background'    => array(
				'title'         => __('Background', 'fl-builder'),
				'fields'        => array(
					'bg_type'      => array(
						'type'          => 'select',
						'label'         => __('Type', 'fl-builder'),
						'default'       => 'none',
						'options'       => array(
							'none'          => _x( 'None', 'Background type.', 'fl-builder' ),
							'color'         => _x( 'Color', 'Background type.', 'fl-builder' ),
						),
						'toggle'        => array(
							'color'         => array(
								'sections'      => array('bg_color')
							),
						),
						'preview'         => array(
							'type'            => 'none'
						)
					)
				)
			),
			'bg_color'     => array(
				'title'         => __('Background Color', 'fl-builder'),
				'fields'        => array(
					'bg_color'      => array(
						'type'          => 'color',
						'label'         => __('Color', 'fl-builder'),
						'show_reset'    => true,
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'bg_opacity'    => array(
						'type'          => 'text',
						'label'         => __('Opacity', 'fl-builder'),
						'default'       => '100',
						'description'   => '%',
						'maxlength'     => '3',
						'size'          => '5',
						'preview'         => array(
							'type'            => 'none'
						)
					)
				)
			),*/
		)
	)
));