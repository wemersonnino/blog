<?php

use MasterPopups\Includes\Functions;

$options = get_option('settings-master-popups');

$xbox->add_field(array(
	'id' => 'display-on-all-site',
	'name' => __( 'Display on All Site', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'on',
));
$xbox->add_field(array(
	'id' => 'display-on-homepage',
	'name' => __( 'Display on Homepage', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'on'
));
$xbox->add_field(array(
	'id' => 'display-on-archive',
	'name' => __( 'Display on Archives', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'on'
));


// $wp_post_types = get_post_types( array('public' => true, '_builtin' => true ), 'objects' );
// unset( $wp_post_types['attachment'] );
// $wp_post_types = array_reverse( $wp_post_types );

$wp_post_types = array(
	'page' => 'Pages',
	'post' => 'Posts',
);

foreach( $wp_post_types as $post_type_name => $post_type_label ) {
	$xbox->open_mixed_field(array('name' => sprintf(__( 'Display on %s', 'masterpopups' ) , $post_type_label)));
		$xbox->add_field(array(
			'id' => 'display-on-'.$post_type_name,
            'name' => sprintf(__( 'Show in all %s', 'masterpopups' ) , $post_type_label),
            'type' => 'switcher',
            'default' => 'on',
			'options' => array(
				//'show_name' => false
			),
		));
		$xbox->add_field(array(
			'id' => 'display-on-'.$post_type_name.'-include',
			'name' => __( 'Include IDs', 'masterpopups' ),
			'type' => 'text',
			'desc' => __( 'Enter the IDs where you want to display the popup. e.g: 7, 18', 'masterpopups' ),
			'grid' => '3-of-8',
			'options' => array(
				'desc_tooltip' => true
			),
			'attributes' => array(
				'placeholder' => '7, 18'
			)
		));
		$xbox->add_field(array(
			'id' => 'display-on-'.$post_type_name.'-exclude',
			'name' => __( 'Exclude IDs', 'masterpopups' ),
			'type' => 'text',
			'desc' => __( "Enter the IDs where you do not want to display the popup. e.g: 5, 9, 23", 'masterpopups' ),
			'grid' => '3-of-8',
			'options' => array(
				'desc_tooltip' => true
			),
			'attributes' => array(
				'placeholder' => '5, 9, 23'
			)
		));
	$xbox->close_mixed_field();


// $wp_taxonomies = get_taxonomies(array(
// 	'public' => true,
// 	'_builtin' => true,
// 	'object_type' => array( $post_type->name )
// 	),'objects' );
// unset( $wp_taxonomies['post_format'] );

	$wp_taxonomies = array();
	if( $post_type_name == 'post' ){
		$wp_taxonomies = array(
			'category' => 'Categories',
			'post_tag' => 'Tags',
		);
	}

	//Taxonomies
	foreach( $wp_taxonomies as $taxonomy_name => $taxonomy_label ){
		$xbox->open_mixed_field(array('name' => sprintf(__( 'Display on %s', 'masterpopups' ) , $taxonomy_label)));
		$xbox->add_field(array(
			'id' => 'display-on-taxonomy-'.$taxonomy_name,
            'name' => sprintf(__( 'Show in all %s', 'masterpopups' ) , $taxonomy_label),
			'type' => 'switcher',
			'default' => 'on',
			'options' => array(
				//'show_name' => false
			),
		));
        $xbox->add_field(array(
            'id' => 'display-on-posts-with-taxonomy-'.$taxonomy_name,
            'name' => sprintf(__( 'Show in posts with the selected %s', 'masterpopups' ) , $taxonomy_label),
            'type' => 'switcher',
            'default' => 'off',
            'options' => array(
                //'show_name' => false
            ),
        ));

        $show_tags = isset( $options['target-display-all-tags'] ) ? $options['target-display-all-tags'] : 'off';

		if( $taxonomy_name == 'category' || $show_tags == 'on' ){//Para excluir tags porque pueden ser muchos y relentiza la carga
			$xbox->add_field(array(
				'id' => 'display-on-taxonomy-'.$taxonomy_name.'-terms',
				'name' => $taxonomy_label,
				'type' => 'checkbox',
				'items' => XboxItems::terms( $taxonomy_name ),
			));
		}

		$xbox->close_mixed_field();
	}
}

//Display on Specific URLs
$xbox->open_mixed_field(
    array(
        'name' => __( 'Display on Specific URLs', 'masterpopups' ),
        'desc' => 'Add the Urls separated by commas. Use (*) at the end of the url to take into account the daughters pages.',
    ));
$xbox->add_field(array(
	'id' => 'display-on-specific-urls',
	'name' => __( 'Show in URLs', 'masterpopups' ),
	'type' => 'textarea',
	'default' => 'https://example.com,
https://example.com/shop/*,',
    'grid' => '4-of-8',
    'attributes' => array(
        'rows' => 4,
    )
));

$xbox->add_field(array(
    'id' => 'display-on-specific-urls-exclude',
    'name' => __( 'Not show in URLs', 'masterpopups' ),
    'type' => 'textarea',
    'default' => 'https://example.com/exclude-page,
https://example.com/exclude-all-pages/*,',
    'grid' => '4-of-8',
    'attributes' => array(
        'rows' => 4,
    )
));
$xbox->close_mixed_field();



$post_types = $class->get_not_builtin_post_types();
$enabled = isset( $options['target-enabled-custom-post-types'] ) ? $options['target-enabled-custom-post-types'] : 'on';

//WooCommerce
if( Functions::is_woocommerce_activated() && array_key_exists('product', $post_types) ){
    unset( $post_types['product'] );
    $xbox->add_field(array(
        'name' => __( 'WooCommerce', 'masterpopups' ),
        'type' => 'title',
    ));
    $xbox->open_mixed_field(array('name' => sprintf(__( 'Display on %s', 'masterpopups' ) , 'Products')));
    $xbox->add_field(array(
        'id' => 'display-on-product',
        'name' => __( 'Show in all Products', 'masterpopups' ),
        'type' => 'switcher',
        'default' => $enabled,
        'options' => array(
            //'show_name' => false
        ),
    ));
    $xbox->add_field(array(
        'id' => 'display-on-product-include',
        'name' => __( 'Include IDs', 'masterpopups' ),
        'type' => 'text',
        'desc' => __( 'Enter the IDs where you want to display the popup. e.g: 7, 18', 'masterpopups' ),
        'grid' => '3-of-8',
        'options' => array(
            'desc_tooltip' => true
        ),
        'attributes' => array(
            'placeholder' => '7, 18'
        )
    ));
    $xbox->add_field(array(
        'id' => 'display-on-product-exclude',
        'name' => __( 'Exclude IDs', 'masterpopups' ),
        'type' => 'text',
        'desc' => __( "Enter the IDs where you do not want to display the popup. e.g: 5, 9, 23", 'masterpopups' ),
        'grid' => '3-of-8',
        'options' => array(
            'desc_tooltip' => true
        ),
        'attributes' => array(
            'placeholder' => '5, 9, 23'
        )
    ));
    $xbox->close_mixed_field();

    $woo_taxonomies = array(
        'product_cat' => 'Product Categories',
        'product_tag' => 'Product Tags',
    );
    //WooCommerce Taxonomies
    foreach( $woo_taxonomies as $taxonomy_name => $taxonomy_label ){
        $xbox->open_mixed_field(array('name' => sprintf(__( 'Display on %s', 'masterpopups' ) , $taxonomy_label)));
        $xbox->add_field(array(
            'id' => 'display-on-taxonomy-'.$taxonomy_name,
            'name' => sprintf(__( 'Show in all %s', 'masterpopups' ) , $taxonomy_label),
            'type' => 'switcher',
            'default' => 'on',
            'options' => array(
                //'show_name' => false
            ),
        ));
        $xbox->add_field(array(
            'id' => 'display-on-products-with-taxonomy-'.$taxonomy_name,
            'name' => sprintf(__( 'Show in products with the selected %s', 'masterpopups' ) , $taxonomy_label),
            'type' => 'switcher',
            'default' => 'off',
            'options' => array(
                //'show_name' => false
            ),
        ));

        $show_tags = isset( $options['target-display-all-tags'] ) ? $options['target-display-all-tags'] : 'off';

        if( $taxonomy_name == 'product_cat' || $show_tags == 'on' ){//Para excluir tags porque pueden ser muchos y relentiza la carga
            $xbox->add_field(array(
                'id' => 'display-on-taxonomy-'.$taxonomy_name.'-terms',
                'name' => $taxonomy_label,
                'type' => 'checkbox',
                'items' => XboxItems::terms( $taxonomy_name ),
            ));
        }

        $xbox->close_mixed_field();
    }
}



//Display on Custom Post Types

$xbox->add_field(array(
	'name' => __( 'Display Popup on Custom Post Types', 'masterpopups' ),
	'type' => 'title',
));

if( empty( $post_types ) ){
	$xbox->add_field(array(
		'id' => 'not-found-post-types',
		'type' => 'html',
		'content' => "<div class='xbox-field-description'>".__( 'Not custom post types found', 'masterpopups' )."</div>",
		'options' => array(
			'show_name' => false,
		)
	));
}


foreach( $post_types as $post_type ) {
	$xbox->open_mixed_field(array('name' => sprintf(__( 'Display on %s', 'masterpopups' ) , $post_type->label)));
		$xbox->add_field(array(
			'id' => 'display-on-'.$post_type->name,
            'name' => __( 'Show in all', 'masterpopups' ),
			'type' => 'switcher',
			'default' => $enabled,
			'options' => array(
				//'show_name' => false
			),
		));
		$xbox->add_field(array(
			'id' => 'display-on-'.$post_type->name.'-include',
			'name' => __( 'Include IDs', 'masterpopups' ),
			'type' => 'text',
			'desc' => __( 'Enter the IDs where you want to display the popup. e.g: 7, 18', 'masterpopups' ),
			'grid' => '3-of-8',
			'options' => array(
				'desc_tooltip' => true
			),
			'attributes' => array(
				'placeholder' => '7, 18'
			)
		));
		$xbox->add_field(array(
			'id' => 'display-on-'.$post_type->name.'-exclude',
			'name' => __( 'Exclude IDs', 'masterpopups' ),
			'type' => 'text',
			'desc' => __( "Enter the IDs where you do not want to display the popup. e.g: 5, 9, 23", 'masterpopups' ),
			'grid' => '3-of-8',
			'options' => array(
				'desc_tooltip' => true
			),
			'attributes' => array(
				'placeholder' => '5, 9, 23'
			)
		));
	$xbox->close_mixed_field();
}