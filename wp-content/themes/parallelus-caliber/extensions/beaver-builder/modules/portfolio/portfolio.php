<?php

/**
 * @class FLRichTextModule
 */
class FEEPortfolioModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __('Portfolio', 'fl-builder'),
			'description'   	=> __('Show a list of portfolio items.', 'fl-builder'),
			'category'      	=> __('Posts', 'fl-builder'),
			// 'partial_refresh'	=> true
		));
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FEEPortfolioModule', array(
	'general'       => array( // Tab
		'title'         => __('General', 'fl-builder'), // Tab title
		'sections'      => array( // Tab Sections
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'post_type'  => array(
						'type'          => 'post-type',
						'label'         => __('Content Source', 'fl-builder'),
						'default'       => 'portfolio',
						'help'          => __('The post type to use as the content source.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'link_to'  => array(
						'type'          => 'select',
						'label'         => __('Click Action', 'fl-builder'),
						'default'       => 'post',
						'options'       => array(
							'post'        => __('Link to Content', 'fl-builder'),
							'lightbox'    => __('Open image in lightbox', 'fl-builder')
						),
						// 'help'          => __('Clicking the item can open the image in a lightbox or link to the portfolio content.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'posts_per_page'  => array(
						'type'          => 'text',
						'label'         => __('Items Per Page', 'fl-builder'),
						'default'       => '12',
						'maxlength'     => '4',
						'size'          => '5',
						// 'help'          => __('How many items to shown per page. ', 'fl-builder'),
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
					'items_to_include'  => array(
						'type'          => 'select',
						'label'         => __('Items to Include', 'fl-builder'),
						'default'       => 'all',
						'options'       => array(
							'all'         => __('All', 'fl-builder'),
							'by_id'       => __('Items by ID', 'fl-builder'),
							'category'    => __('Category', 'fl-builder')
						),
						'toggle'        => array(
							'by_id'     => array(
								'fields'    => array('posts_by_id')
							),
							'category'     => array(
								'fields'    => array('categories')
							)
						),
						// 'help'          => __('Specify the portfolio items to include.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'posts_by_id'  => array(
						'type'          => 'text',
						'label'         => __('Include Posts', 'fl-builder'),
						'placeholder'   => __( '1,2,3', 'fl-builder' ),
						'default'       => '',
						'help'          => __('Comma separated list of item IDs to include.', 'fl-builder'),
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
					'order_by'  => array(
						'type'          => 'select',
						'label'         => __('Order By', 'fl-builder'),
						'default'       => 'hide',
						'options'       => array(
							'menu-order'  => __('Meta Order', 'fl-builder'),
							'newest'      => __('Newest First', 'fl-builder'),
							'oldest'      => __('Oldest First', 'fl-builder'),
							'random'      => __('Random', 'fl-builder')
						),
						// 'help'          => __('Set the order items appear.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
				)
			),
			'content'        => array(
				'title'         => __('Content', 'fl-builder'),
				'fields'        => array(
					'title'  => array(
						'type'          => 'select',
						'label'         => __('Title', 'fl-builder'),
						'default'       => 'show',
						'options'       => array(
							'show'        => __('Show', 'fl-builder'),
							'hide'        => __('Hide', 'fl-builder'),
						),
						'help'          => __('Include the title.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'excerpt'  => array(
						'type'          => 'select',
						'label'         => __('Description', 'fl-builder'),
						'default'       => 'show',
						'options'       => array(
							'show'        => __('Show', 'fl-builder'),
							'hide'        => __('Hide', 'fl-builder'),
						),
						'toggle'        => array(
							'show'     => array(
								'fields'    => array('excerpt_length')
							)
						),
						'help'          => __('Include the description text.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'excerpt_length'  => array(
						'type'          => 'text',
						'label'         => __('Description Length', 'fl-builder'),
						'placeholder'   => __( '25', 'fl-builder' ),
						'default'       => '',
						'maxlength'     => '3',
						'size'          => '5',
						'help'          => __('Trim the description to a set number of words.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					)
					)
			),
			'style'        => array(
				'title'         => __('Style', 'fl-builder'),
				'fields'        => array(
					'size'  => array(
						'type'          => 'select',
						'label'         => __('Size', 'fl-builder'),
						'default'       => 'medium',
						'options'       => array(
							'xl'          => __('Extra Large (2 across)', 'fl-builder'),
							'large'       => __('Large (3 across)', 'fl-builder'),
							'medium'      => __('Medium (4 across)', 'fl-builder'),
							'small'       => __('Small (6 across)', 'fl-builder'),
						),
						'help'          => __('The size of the portfolio grid items.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'ratio'  => array(
						'type'          => 'select',
						'label'         => __('Image Ratio', 'fl-builder'),
						'default'       => '1:1',
						'options'       => array(
							'4:3'         => __('4 &times; 3', 'fl-builder').' &#8596;',
							'3:2'         => __('3 &times; 2', 'fl-builder').' &#8596;',
							'16:9'        => __('16 &times; 9', 'fl-builder').' &#8596;',
							'2:1'         => __('2 &times; 1', 'fl-builder').' &#8596;',
							'1:1'         => __('1 &times; 1', 'fl-builder'),
							'2:3'         => __('2 &times; 3', 'fl-builder').' &#8597;',
							'3:4'         => __('3 &times; 4', 'fl-builder').' &#8597;',
							'custom'      => __('Custom', 'fl-builder')
						),
						'toggle'        => array(
							'custom'     => array(
								'fields'    => array('ratio_width', 'ratio_height')
							)
						),
						'help'          => __('Set the image dimensions.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'ratio_width'  => array(
						'type'          => 'text',
						'label'         => __('Width', 'fl-builder'),
						'placeholder'   => __( '4', 'fl-builder' ),
						'default'       => '',
						'maxlength'     => '4',
						'size'          => '5',
						'help'          => __('Enter a width value. This is not an exact pixel size.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'ratio_height'  => array(
						'type'          => 'text',
						'label'         => __('Height', 'fl-builder'),
						'placeholder'   => __( '3', 'fl-builder' ),
						'default'       => '',
						'maxlength'     => '4',
						'size'          => '5',
						'help'          => __('Enter a height value. This is not an exact pixel size.', 'fl-builder'),
						'preview'         => array(
							'type'          => 'none'
						)
					),
					'hover_color'      => array(
						'type'          => 'color',
						'label'         => __('Hover Color', 'fl-builder'),
						'show_reset'    => true,
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'hover_opacity'    => array(
						'type'          => 'text',
						'label'         => __('Hover Opacity', 'fl-builder'),
						'placeholder'   => __( '25', 'fl-builder' ),
						'default'       => '',
						'description'   => '%',
						'maxlength'     => '3',
						'size'          => '5',
						'preview'         => array(
							'type'            => 'none'
						)
					),
					/*'text_color'    => array(
						'type'          => 'color',
						'label'         => __('Text Color', 'fl-builder'),
						'show_reset'    => true,
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'link_color'    => array(
						'type'          => 'color',
						'label'         => __('Link Color', 'fl-builder'),
						'show_reset'    => true,
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'hover_color'    => array(
						'type'          => 'color',
						'label'         => __('Link Hover Color', 'fl-builder'),
						'show_reset'    => true,
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'heading_color'  => array(
						'type'          => 'color',
						'label'         => __('Heading Color', 'fl-builder'),
						'show_reset'    => true,
						'preview'         => array(
							'type'            => 'none'
						)
					)*/
				)
			),
		)
	)
));