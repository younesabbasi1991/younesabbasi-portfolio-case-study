<?php

declare(strict_types=1);

namespace YounesAbbasi\PortfolioCaseStudy;

/**
 * Public case-study sample.
 *
 * Abridged and generalized; not a drop-in production module.
 */
final class PortfolioPostType
{
    public const POST_TYPE = 'ya_portfolio';

    public static function register(): void
    {
        $labels = [
            'name'                  => __('Portfolio', 'ya-portfolio'),
            'singular_name'         => __('Project', 'ya-portfolio'),
            'add_new_item'          => __('Add New Project', 'ya-portfolio'),
            'edit_item'             => __('Edit Project', 'ya-portfolio'),
            'new_item'              => __('New Project', 'ya-portfolio'),
            'view_item'             => __('View Project', 'ya-portfolio'),
            'search_items'          => __('Search Projects', 'ya-portfolio'),
            'not_found'             => __('No projects found.', 'ya-portfolio'),
            'not_found_in_trash'    => __('No projects found in Trash.', 'ya-portfolio'),
            'all_items'             => __('All Projects', 'ya-portfolio'),
            'archives'              => __('Project Archives', 'ya-portfolio'),
            'featured_image'        => __('Project Cover', 'ya-portfolio'),
            'set_featured_image'    => __('Set project cover', 'ya-portfolio'),
            'remove_featured_image' => __('Remove project cover', 'ya-portfolio'),
        ];

        register_post_type(
            self::POST_TYPE,
            [
                'labels'             => $labels,
                'public'             => true,
                'show_in_rest'       => true,
                'has_archive'        => true,
                'menu_icon'          => 'dashicons-portfolio',
                'menu_position'      => 20,
                'rewrite'            => [
                    'slug'       => 'portfolio',
                    'with_front' => false,
                ],
                'supports'           => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                    'revisions',
                    'page-attributes',
                    'comments',
                ],
                'publicly_queryable' => true,
                'show_in_nav_menus'  => true,
                'delete_with_user'   => false,
            ]
        );
    }
}

// Production bootstrap:
// add_action('init', [PortfolioPostType::class, 'register']);
