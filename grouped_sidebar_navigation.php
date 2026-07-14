// SHORTCODE: [grouped_sidebar_navigation] - ACF EXTENSION
add_shortcode('grouped_sidebar_navigation', function() {
    $parent_id = 0;
    $taxonomy_slug = 'product-category'; // YOUR EXACT ACF TAXONOMY

    $current_term = get_queried_object();
    if ( isset($current_term->taxonomy) && $current_term->taxonomy === $taxonomy_slug ) {
        $parent_id = ($current_term->parent == 0) ? $current_term->term_id : $current_term->parent;
    }

    if ( !$parent_id ) {
        $url_segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        $potential_slug = end($url_segments);
        $term_lookup = get_term_by('slug', $potential_slug, $taxonomy_slug);
        if ( $term_lookup ) {
            $parent_id = ($term_lookup->parent == 0) ? $term_lookup->term_id : $term_lookup->parent;
        }
    }

    $sub_categories = get_terms(array(
        'taxonomy'   => $taxonomy_slug,
        'parent'     => $parent_id,
        'hide_empty' => false,
    ));

    if ( empty($sub_categories) || is_wp_error($sub_categories) ) {
        return '<p style="color:#64748b; font-size:0.9rem;">No sub-categories available.</p>';
    }

    $output = '<div class="lovable-anchor-menu" style="font-family: sans-serif; position: sticky; top: 140px;">';
    $output .= '<h3 style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Sub-Categories</h3>';
    $output .= '<div style="display: flex; flex-direction: column; gap: 8px;">';

    foreach ( $sub_categories as $sub_cat ) {
        $anchor_link = '#' . $sub_cat->slug;
        $output .= sprintf(
            '<a href="%s" class="anchor-menu-item" style="display: block; padding: 12px 16px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: all 0.2s ease;">%s</a>',
            esc_url($anchor_link),
            esc_html($sub_cat->name)
        );
    }

    $output .= '</div></div>';

    $output .= '<script>
    document.querySelectorAll(".anchor-menu-item").forEach(anchor => {
        anchor.addEventListener("click", function(e) {
            e.preventDefault();
            const targetId = this.getAttribute("href").substring(1);
            const targetEl = document.getElementById(targetId);
            if(targetEl) {
                targetEl.scrollIntoView({ behavior: "smooth", block: "start" });
                document.querySelectorAll(".anchor-menu-item").forEach(i => {
                    i.style.background = "#fff"; i.style.color = "#475569"; i.style.borderColor = "#e2e8f0";
                });
                this.style.background = "#f0fdfa"; this.style.color = "#0d9488"; this.style.borderColor = "#ccfbf1";
            }
        });
    });
    </script>';

    return $output;
});
