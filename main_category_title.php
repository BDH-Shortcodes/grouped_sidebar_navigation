// SHORTCODE: [main_category_title] - TRUE DYNAMIC ARCHIVE EDITION (FIXED TAXONOMY)
add_shortcode('main_category_title', function() {
    $title = '';
    $taxonomy = 'product-category'; // Fixed to match your exact site slug

    // Method 1: Target the native WordPress archive queried object directly
    if ( is_tax($taxonomy) ) {
        $current_term = get_queried_object();
        if ( $current_term && ! is_wp_error($current_term) ) {
            // Displays the exact category or sub-category name of the page you are on
            $title = $current_term->name;
        }
    }

    // Method 2: Global structural breakdown fallback if Method 1 is blocked by custom layouts
    if ( empty($title) && is_tax($taxonomy) ) {
        global $wp_query;
        $cat_obj = $wp_query->get_queried_object();
        if ( $cat_obj && isset($cat_obj->name) ) {
            $title = $cat_obj->name;
        }
    }

    // Method 3: Post-level verification backup loop (For Single Product Pages)
    if ( empty($title) ) {
        global $post;
        $product_id = isset($post->ID) ? $post->ID : get_the_ID();
        if ( $product_id ) {
            $terms = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'all'));
            if ( ! empty($terms) && ! is_wp_error($terms) ) {
                // Prioritize showing the sub-category if available on single layouts
                $deepest_term = null;
                foreach ( $terms as $term ) {
                    if ( $term->parent != 0 ) {
                        $deepest_term = $term;
                        break;
                    }
                }
                $chosen_term = $deepest_term ? $deepest_term : $terms[0];
                $title = $chosen_term->name;
            }
        }
    }

    // Absolute fallback: If everything else fails, get the dynamic page context heading title string safely
    if ( empty($title) ) {
        $title = single_term_title('', false);
    }
    
    // Final fallback to page title if completely empty
    if ( empty($title) ) {
        $title = get_the_title();
    }

    // Return the HTML layout embedded with your target presentation hook style
    return '<h1 class="h1-gradient" style="margin: 0; padding: 0; font-weight: 800;">' . esc_html($title) . '</h1>';
});
