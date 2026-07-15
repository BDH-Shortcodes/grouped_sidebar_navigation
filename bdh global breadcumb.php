// SHORTCODE: [bdh_global_breadcrumbs] - DYNAMIC MAIN VS SUB-CATEGORY TRAIL ENGINE
add_shortcode('bdh_global_breadcrumbs', function() {
    $taxonomy = 'product-category';
    
    // Base container with clean font settings and spacing
    $html = '<div class="bdh-global-breadcrumbs" style="font-family:sans-serif; font-size:0.95rem; color:#334155; margin-bottom:20px; display:flex; align-items:center; justify-content:flex-start; text-align:left; flex-wrap:wrap; gap:6px; width:100%;">';
    $html .= '  <a href="/" style="color:#334155; text-decoration:none; font-weight:500;">Home</a>';

    // SCENARIO 1: Single Product Detail Page
    if ( is_singular('product') ) {
        $html .= '  <span style="color:#94a3b8;">/</span>';
        $html .= '  <a href="/products/" style="color:#334155; text-decoration:none; font-weight:500;">Products</a>';
        
        global $post;
        if ( ! empty($post) ) {
            $terms = wp_get_post_terms($post->ID, $taxonomy);
            
            if ( ! empty($terms) && ! is_wp_error($terms) ) {
                $deepest_term = null;
                foreach ( $terms as $term ) {
                    if ( $term->parent != 0 ) {
                        $deepest_term = $term;
                        break;
                    }
                }
                
                if ( ! $deepest_term ) {
                    $deepest_term = $terms[0];
                }
                
                if ( $deepest_term->parent != 0 ) {
                    $parent_term = get_term($deepest_term->parent, $taxonomy);
                    if ( $parent_term && ! is_wp_error($parent_term) ) {
                        $html .= '  <span style="color:#94a3b8;">/</span>';
                        $html .= '  <a href="' . esc_url(get_term_link($parent_term)) . '" style="color:#334155; text-decoration:none; font-weight:500;">' . esc_html($parent_term->name) . '</a>';
                    }
                }
                
                $html .= '  <span style="color:#94a3b8;">/</span>';
                $html .= '  <a href="' . esc_url(get_term_link($deepest_term)) . '" style="color:#334155; text-decoration:none; font-weight:500;">' . esc_html($deepest_term->name) . '</a>';
            }
        }
        
        $html .= '  <span style="color:#94a3b8;">/</span>';
        $html .= '  <span style="color:#006747; font-weight:600;">' . esc_html(get_the_title()) . '</span>';

    // SCENARIO 2: Category & Subcategory Taxonomy Archives (Smart Separation)
    } elseif ( is_tax($taxonomy) ) {
        $html .= '  <span style="color:#94a3b8;">/</span>';
        $html .= '  <a href="/products/" style="color:#334155; text-decoration:none; font-weight:500;">Products</a>';
        
        $current_term = get_queried_object();
        if ( $current_term && ! is_wp_error($current_term) ) {
            // Check if this has a parent category (Meaning it IS a sub-category)
            if ( $current_term->parent != 0 ) {
                $parent_term = get_term($current_term->parent, $taxonomy);
                if ( $parent_term && ! is_wp_error($parent_term) ) {
                    $html .= '  <span style="color:#94a3b8;">/</span>';
                    $html .= '  <a href="' . esc_url(get_term_link($parent_term)) . '" style="color:#334155; text-decoration:none; font-weight:500;">' . esc_html($parent_term->name) . '</a>';
                }
            }
            
            // Current Archive item label output
            $html .= '  <span style="color:#94a3b8;">/</span>';
            $html .= '  <span style="color:#006747; font-weight:600;">' . esc_html($current_term->name) . '</span>';
        }

    // SCENARIO 3: Regular Static Pages
    } elseif ( is_page() ) {
        global $post;
        if ( $post && $post->post_parent ) {
            $html .= '  <span style="color:#94a3b8;">/</span>';
            $html .= '  <a href="' . esc_url(get_permalink($post->post_parent)) . '" style="color:#334155; text-decoration:none; font-weight:500;">' . esc_html(get_the_title($post->post_parent)) . '</a>';
        }
        $html .= '  <span style="color:#94a3b8;">/</span>';
        $html .= '  <span style="color:#006747; font-weight:600;">' . esc_html(get_the_title()) . '</span>';
        
    // SCENARIO 4: Fallback Slug Segment Parsing
    } else {
        $url_segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        $potential_slug = end($url_segments);
        $term_lookup = get_term_by('slug', $potential_slug, $taxonomy);
        
        if ( $term_lookup && ! is_wp_error($term_lookup) ) {
            $html .= '  <span style="color:#94a3b8;">/</span>';
            $html .= '  <a href="/products/" style="color:#334155; text-decoration:none; font-weight:500;">Products</a>';
            if ( $term_lookup->parent != 0 ) {
                $parent_term = get_term($term_lookup->parent, $taxonomy);
                if ( $parent_term && ! is_wp_error($parent_term) ) {
                    $html .= '  <span style="color:#94a3b8;">/</span>';
                    $html .= '  <a href="' . esc_url(get_term_link($parent_term)) . '" style="color:#334155; text-decoration:none; font-weight:500;">' . esc_html($parent_term->name) . '</a>';
                }
            }
            $html .= '  <span style="color:#94a3b8;">/</span>';
            $html .= '  <span style="color:#006747; font-weight:600;">' . esc_html($term_lookup->name) . '</span>';
        } else {
            $html .= '  <span style="color:#94a3b8;">/</span>';
            $html .= '  <span style="color:#006747; font-weight:600;">' . esc_html(get_the_title()) . '</span>';
        }
    }

    $html .= '</div>';
    return $html;
});
