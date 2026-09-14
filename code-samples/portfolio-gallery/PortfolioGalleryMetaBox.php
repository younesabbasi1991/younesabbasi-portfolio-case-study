<?php

declare(strict_types=1);

namespace YounesAbbasi\PortfolioCaseStudy;

/**
 * Public case-study sample.
 *
 * Demonstrates ordered attachment IDs, per-image layout choices,
 * contextual admin assets, and safe persistence.
 */
final class PortfolioGalleryMetaBox
{
    private const POST_TYPE   = 'ya_portfolio';
    private const META_KEY    = '_ya_portfolio_gallery';
    private const NONCE_NAME  = 'ya_portfolio_gallery_nonce';
    private const NONCE_ACTION = 'save_ya_portfolio_gallery';

    public static function register(): void
    {
        add_action('add_meta_boxes', [self::class, 'add']);
        add_action(
            'save_post_' . self::POST_TYPE,
            [self::class, 'save'],
            10,
            2
        );
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAssets']);
    }

    public static function add(): void
    {
        add_meta_box(
            'ya-portfolio-gallery',
            __('Project Gallery', 'ya-portfolio'),
            [self::class, 'render'],
            self::POST_TYPE,
            'normal',
            'default'
        );
    }

    public static function enqueueAssets(string $hook): void
    {
        if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $screen = get_current_screen();

        if (! $screen || $screen->post_type !== self::POST_TYPE) {
            return;
        }

        wp_enqueue_media();

        // Production also enqueues a small sortable Media Library controller
        // only on these portfolio editing screens.
    }

    public static function render(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $items = get_post_meta($post->ID, self::META_KEY, true);
        $items = is_array($items) ? $items : [];
        ?>
        <div class="portfolio-gallery-editor">
            <button
                type="button"
                class="button portfolio-gallery-editor__select"
            >
                <?php esc_html_e('Select gallery images', 'ya-portfolio'); ?>
            </button>

            <ul class="portfolio-gallery-editor__items">
                <?php foreach ($items as $item) : ?>
                    <?php
                    $attachment_id = absint($item['id'] ?? 0);
                    $layout = sanitize_key($item['layout'] ?? 'horizontal');

                    if (! $attachment_id) {
                        continue;
                    }

                    if (! in_array($layout, ['horizontal', 'vertical'], true)) {
                        $layout = 'horizontal';
                    }
                    ?>
                    <li data-attachment-id="<?php echo esc_attr($attachment_id); ?>">
                        <?php
                        echo wp_get_attachment_image(
                            $attachment_id,
                            'thumbnail',
                            false,
                            ['loading' => 'lazy']
                        );
                        ?>

                        <input
                            type="hidden"
                            name="ya_gallery_ids[]"
                            value="<?php echo esc_attr($attachment_id); ?>"
                        >

                        <label>
                            <span class="screen-reader-text">
                                <?php esc_html_e('Image layout', 'ya-portfolio'); ?>
                            </span>
                            <select
                                name="ya_gallery_layouts[<?php
                                echo esc_attr($attachment_id);
                                ?>]"
                            >
                                <option
                                    value="horizontal"
                                    <?php selected($layout, 'horizontal'); ?>
                                >
                                    <?php esc_html_e('Horizontal', 'ya-portfolio'); ?>
                                </option>
                                <option
                                    value="vertical"
                                    <?php selected($layout, 'vertical'); ?>
                                >
                                    <?php esc_html_e('Vertical', 'ya-portfolio'); ?>
                                </option>
                            </select>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }

    public static function save(int $postId, \WP_Post $post): void
    {
        if (
            ! isset($_POST[self::NONCE_NAME])
            || ! wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash($_POST[self::NONCE_NAME])
                ),
                self::NONCE_ACTION
            )
        ) {
            return;
        }

        if (
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            || wp_is_post_revision($postId)
            || $post->post_type !== self::POST_TYPE
            || ! current_user_can('edit_post', $postId)
        ) {
            return;
        }

        $rawIds = isset($_POST['ya_gallery_ids'])
            ? (array) wp_unslash($_POST['ya_gallery_ids'])
            : [];

        $rawLayouts = isset($_POST['ya_gallery_layouts'])
            ? (array) wp_unslash($_POST['ya_gallery_layouts'])
            : [];

        $ids = array_values(
            array_unique(
                array_filter(array_map('absint', $rawIds))
            )
        );

        $gallery = [];

        foreach ($ids as $attachmentId) {
            if (get_post_type($attachmentId) !== 'attachment') {
                continue;
            }

            $layout = sanitize_key(
                $rawLayouts[$attachmentId] ?? 'horizontal'
            );

            if (! in_array($layout, ['horizontal', 'vertical'], true)) {
                $layout = 'horizontal';
            }

            $gallery[] = [
                'id'     => $attachmentId,
                'layout' => $layout,
            ];
        }

        if ($gallery === []) {
            delete_post_meta($postId, self::META_KEY);
            return;
        }

        update_post_meta($postId, self::META_KEY, $gallery);
    }
}

// Production bootstrap:
// PortfolioGalleryMetaBox::register();
