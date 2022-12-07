<?php

/** Manage post columns */
function set_custom_edit_simuladores_columns($columns) {
    unset( $columns['date'] );
    $columns['shortcode'] = __( 'Shortcode', 'simuladores' );
    $columns['date'] = __( 'Date', 'simuladores' );

    return $columns;
}

function custom_simuladores_column( $column, $post_id ) {
    switch ( $column ) {
        case 'shortcode' :
            _e( '[simuladores id="' . $post_id . '"]');
            break;
    }
}