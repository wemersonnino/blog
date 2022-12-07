<?php

namespace ToolsMeutudoCalc;

class ToolsMeutudoCalc
{

    public function __construct()
    {
        add_filter('manage_{$post_type}_posts_columns', array($this,'shortcode_custom_colum'));
        add_action( 'manage_{$post_type}_posts_custom_column' , array($this,'fill_shortcode_post_type_columns'), 10, 2 );
    }

    public function shortcode_custom_colum($columns){
        unset(
            $columns['date']
        );
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Shortcode'),
            'custom_column_2' => __('Shortcode','tools-meutudo'),
            'custom_column_1' => __('Custom Column 1'),
            'post_id' =>__( 'Post ID'),
            'date' =>__( 'Date')
        );
        //return $columns;
    }

    public function fill_shortcode_post_type_columns($column, $post_id){
        switch ( $column ) {
            case 'custom_column_1' :
                echo get_post_meta( $post_id , $this->plugin_name.'_custom_column_1' , true );
                break;
            case 'custom_column_2' :
                echo get_post_meta( $post_id , $this->plugin_name.'_custom_column_2' , true );
                break;
            case 'post_id' :
                echo $post_id;
                break;
        }
    }
}

$shortcode_colum = new ToolsMeutudoCalc();