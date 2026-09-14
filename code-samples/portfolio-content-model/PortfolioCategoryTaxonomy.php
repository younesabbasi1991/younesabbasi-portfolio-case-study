<?php

declare(strict_types=1);

namespace YounesAbbasi\PortfolioCaseStudy;

/**
 * Public case-study sample.
 *
 * Registers a hierarchical classification for portfolio projects.
 */
final class PortfolioCategoryTaxonomy
{
    public const TAXONOMY = 'portfolio_category';

    public static function register(): void
    {
        $labels = [
            'name'              => __('Portfolio Categories', 'ya-portfolio'),
            'singular_name'     => __('Portfolio Category', 'ya-portfolio'),
            'search_items'      => __('Search Categories', 'ya-portfolio'),
            'all_items'         => __('All Categories', 'ya-portfolio'),
            'parent_item'       => __('Parent Category', 'ya-portfolio'),
            'parent_item_colon' => __('Parent Category:', 'ya-portfolio'),
            'edit_item'         => __('Edit Category', 'ya-portfolio'),
            'update_item'       => __('Update Category', 'ya-portfolio'),
            'add_new_item'      => __('Add New Category', 'ya-portfolio'),
            'new_item_name'     => __('New Category Name', 'ya-portfolio'),
            'menu_name'         => __('Categories', 'ya-portfolio'),
        ];

        register_taxonomy(
            self::TAXONOMY,
            [PortfolioPostType::POST_TYPE],
            [
                'labels'            => $labels,
                'public'            => true,
                'hierarchical'      => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'rewrite'           => [
                    'slug'         => 'portfolio-category',
                    'with_front'   => false,
                    'hierarchical' => true,
                ],
            ]
        );
    }
}

// Production bootstrap:
// add_action('init', [PortfolioCategoryTaxonomy::class, 'register']);
