<?php
function meutudoblog_widgets(){
    register_sidebar([
        'meutudoblog_sidebar'  => __('Theme Sidebar','meutudoblog'),
        'id'                    => 'meutudoblog_sidebar',
        'description'           => __('Sidebar for the blog meutudo', 'meutudoblog')
    ]);
}