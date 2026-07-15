/**
 * 1. Force the product permalink to ONLY include the top-level parent category slug
 */
add_filter('post_type_link', function($post_link, $post) {
    // Check if we are handling an object and ensure the post type matches your ACF slug (usually 'product')
    if (is_object($post) && 'product' === $post->post_type) {
        $terms = wp_get_object_terms($post->ID, 'product-category');
        
        if (!empty($terms) && !is_wp_error($terms)) {
            $category_slug = '';
            
            // Look for the true top parent among assigned categories
            foreach ($terms as $term) {
                $ancestors = get_ancestors($term->term_id, 'product-category');
                if (!empty($ancestors)) {
                    $top_parent_id = end($ancestors);
                    $top_parent_term = get_term($top_parent_id, 'product-category');
                    if ($top_parent_term && !is_wp_error($top_parent_term)) {
                        $category_slug = $top_parent_term->slug;
                        break;
                    }
                } else {
                    $category_slug = $term->slug;
                    break;
                }
            }
            
            if (!empty($category_slug)) {
                return str_replace('%product-category%', $category_slug, $post_link);
            }
        }
        
        // Fallback placeholder if no categories are linked yet
        return str_replace('%product-category%', 'all', $post_link);
    }
    return $post_link;
}, 10, 2);


/**
 * 2. Dedicated Rewrite Routing Rule for deep links
 */
add_action('init', function() {
    add_rewrite_rule(
        '^products/([^/]+)/([^/]+)/?$',
        'index.php?product=$matches[2]',
        'top'
    );
});
