<?php

/**
 * Tabela: add script in admin
 */
add_action('admin_enqueue_scripts', function ($hook) {
    global $post;  
    if (($hook == 'post-new.php' || $hook == 'post.php') && $post->post_type == 'tabelas')
        wp_enqueue_script('table_js', get_stylesheet_directory_uri() . '/admin/js/table_script.js');
}, 10, 1);

/**
 * Creating new submenus for Pages
 */
add_action ('admin_menu', function () {
    remove_submenu_page('edit.php?post_type=page', 'edit.php?post_type=page'); // remove 'all pages'
    add_pages_page('Páginas', 'Páginas', 'manage_options', 'edit.php?post_type=page&template=default', null, 0); // pages
    add_pages_page('Calculadoras', 'Calculadoras', 'manage_options', 'edit.php?post_type=page&template=calculator', null, 1); // calculators pages
    add_pages_page('Mapas do Site', 'Mapas do Site', 'manage_options', 'edit.php?post_type=page&template=sitemap', null, 2); // sitemaps pages
    add_pages_page('Tabelas', 'Tabelas', 'manage_options', 'edit.php?post_type=page&template=tables', null, 3); // tables pages
});

/**
 * Changing title and selecting menu
 */
add_action('admin_footer', function () {
    global $pagenow;

    if ($pagenow != 'edit.php' || $_GET['post_type'] != 'page' || empty($_GET['template'])) return;

    // page title and checking current
    switch($_GET['template']) {
        case 'calculator':
            $title = 'Calculadoras';
            break;
        case 'sitemap':
            $title = 'Mapas do Site';
            break;
        case 'tables':
            $title = 'Tabelas';
            break;
        default:
            $title = 'Páginas';
    }

    // js to tag selected menu
    $js = "<script type=\"text/javascript\">(function ($) {";
    $js .= "$('h1.wp-heading-inline').html('{$title}');";
    $js .= "$('ul.wp-submenu a[href$=\"post_type=page&template={$_GET['template']}\"]:first').addClass('current').parent('li').addClass('current');";
    $js .= "})(jQuery);</script>";
    echo $js;
});

/**
 * Redirect 'all pages' to pages with parameter template 'default'
 */
add_action ('admin_init', function () {
    global $pagenow;
    if ($pagenow == 'edit.php' && 
        isset($_GET['post_type']) && $_GET['post_type'] == 'page' && 
        !isset($_GET['all_posts']) && 
        (!isset($_GET['template']) || empty($_GET['template']))) {
        wp_redirect(admin_url('/edit.php?post_type=page&template=default'));
        exit;
    }
});

/**
 * Page filter by template
 */
add_action ('pre_get_posts', function ($query) {
    global $current_screen;

    if (!is_admin() || !$query->is_main_query() || $current_screen->id !== 'edit-page' || empty($_GET['template'])) return;
    
    // template filter
    switch ($_GET['template']) {
        case 'default':
            $templates = ['default', 'template-home.php', 'authors.php', ''];
            break;
        case 'calculator':
            $templates = ['page-templates/calculadoras.php', 'page-templates/calculadora.php'];
            break;
        case 'tables':
            $templates = ['page-templates/tables.php', 'page-templates/table.php'];
            break;
        default:
            $templates = [sanitize_text_field($_GET['template']) . ".php"];
    }

    // set query parameters
    $meta_query = (array) $query->get('meta_query');
    $meta_query[] = [
        'key' => '_wp_page_template',
        'value' => $templates,
        'compare' => 'IN'
    ];
    $query->set('meta_query', $meta_query);
});

/**
 * Create a new filter in Banners
 */
add_action('restrict_manage_posts', function () {
    global $current_screen;

    if ($current_screen->id !== 'edit-mt-banner') return;

    // options like field acf
    $options = [
        'ASA',
        'Auxílio Brasil',
        'Comparativo',
        'Descomplicado',
        'Ebook Consignado',
        'Margen 5%',
        'Novo Empréstimo',
        'Portabilidade'
    ];

    // create select
    $current = $_GET['admin_filter_category'] ?? null;
    $select = "<select name=\"admin_filter_category\"><option value=\"\">Todas as Categorias</option>";
    foreach ($options as $option) {
        $selected = $option == $current ? 'selected="selected"': null;
        $select .= "<option value=\"{$option}\" {$selected}>{$option}</option>";
    }
    $select .= "</select>";
    echo $select;
});

/**
 * Add query parameters to filter categories in Banners
 */
add_filter('parse_query', function ($query) {
    global $pagenow;

    if (!is_admin() || !$query->is_main_query() || $pagenow !== 'edit.php' || empty($_GET['admin_filter_category'])) return;
    
    // add query
    $query->query_vars['meta_key'] = 'categoria';
    $query->query_vars['meta_value'] = $_GET['admin_filter_category'];
});

/**
 * Add the custom columns in Banners
 */
add_filter('manage_mt-banner_posts_columns', function ($columns) {
    $columns['categoria'] = 'Categoria';
    $columns['status'] = 'Status';
    return $columns;
});

/**
 * Add content to custom columns in Banners
 */ 
add_action('manage_mt-banner_posts_custom_column' , function ($column, $post_id) {
    switch ($column) {
        case 'categoria':
            echo get_post_meta(get_post($post_id)->ID, 'categoria', true);
            break;
        case 'status':
            $status = get_post_status() == 'publish' ? 'good' : 'bad';
            echo "<div aria-hidden=\"true\" class=\"wpseo-score-icon {$status}\"><span class=\"wpseo-score-text screen-reader-text\"></span></div>";
            break;
    }
}, 10, 2);