<?php

/**
 * Public case-study sample: manually ordered featured portfolio projects.
 *
 * Expected argument:
 * - project_ids: array or comma-separated project IDs.
 */

if (! defined('ABSPATH')) {
    exit;
}

$raw_ids = $args['project_ids'] ?? [];

if (! is_array($raw_ids)) {
    $raw_ids = explode(',', (string) $raw_ids);
}

$project_ids = array_map('absint', $raw_ids);
$project_ids = array_values(array_unique(array_filter($project_ids)));
$project_ids = array_slice($project_ids, 0, 6);

if ($project_ids === []) {
    return;
}

$projects = new WP_Query(
    [
        'post_type'              => 'ya_portfolio',
        'post_status'            => 'publish',
        'posts_per_page'         => count($project_ids),
        'post__in'               => $project_ids,
        'orderby'                => 'post__in',
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    ]
);

if (! $projects->have_posts()) {
    return;
}
?>

<section
    class="featured-projects"
    aria-labelledby="featured-projects-title"
>
    <header class="featured-projects__header">
        <h2 id="featured-projects-title">
            <?php esc_html_e('Featured Projects', 'ya-portfolio'); ?>
        </h2>
    </header>

    <div class="featured-projects__grid">
        <?php while ($projects->have_posts()) : ?>
            <?php
            $projects->the_post();

            $project_id    = get_the_ID();
            $title         = get_the_title($project_id);
            $permalink     = get_permalink($project_id);
            $excerpt       = wp_strip_all_tags(get_the_excerpt($project_id));
            $terms         = get_the_terms($project_id, 'portfolio_category');
            $first_term    = ! is_wp_error($terms) && $terms ? reset($terms) : null;
            ?>

            <article class="featured-projects__card">
                <a
                    class="featured-projects__media"
                    href="<?php echo esc_url($permalink); ?>"
                    aria-label="<?php
                    echo esc_attr(
                        sprintf(
                            /* translators: %s: project title */
                            __('View case study: %s', 'ya-portfolio'),
                            $title
                        )
                    );
                    ?>"
                >
                    <?php if (has_post_thumbnail($project_id)) : ?>
                        <?php
                        echo get_the_post_thumbnail(
                            $project_id,
                            'large',
                            [
                                'class'    => 'featured-projects__image',
                                'alt'      => $title,
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]
                        );
                        ?>
                    <?php else : ?>
                        <span
                            class="featured-projects__image-placeholder"
                            aria-hidden="true"
                        ></span>
                    <?php endif; ?>
                </a>

                <div class="featured-projects__content">
                    <?php if ($first_term instanceof WP_Term) : ?>
                        <span class="featured-projects__category">
                            <?php echo esc_html($first_term->name); ?>
                        </span>
                    <?php endif; ?>

                    <h3 class="featured-projects__title">
                        <a href="<?php echo esc_url($permalink); ?>">
                            <?php echo esc_html($title); ?>
                        </a>
                    </h3>

                    <?php if ($excerpt !== '') : ?>
                        <p><?php echo esc_html($excerpt); ?></p>
                    <?php endif; ?>

                    <a
                        class="featured-projects__link"
                        href="<?php echo esc_url($permalink); ?>"
                    >
                        <?php esc_html_e('View Case Study', 'ya-portfolio'); ?>
                    </a>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
