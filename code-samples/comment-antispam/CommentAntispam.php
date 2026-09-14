<?php

declare(strict_types=1);

namespace YounesAbbasi\PortfolioCaseStudy;

/**
 * Public case-study sample.
 *
 * A layered, no-CAPTCHA comment check. Production messaging and
 * compatibility behavior are omitted for clarity.
 */
final class CommentAntispam
{
    private const ACTION = 'ya_comment_form';
    private const NONCE_FIELD = 'ya_comment_nonce';
    private const HONEYPOT_FIELD = 'ya_company_website';
    private const TIME_FIELD = 'ya_form_started';
    private const SIGNATURE_FIELD = 'ya_form_signature';
    private const MIN_SECONDS = 5;
    private const MAX_SECONDS = DAY_IN_SECONDS;
    private const RATE_SECONDS = 90;
    private const MAX_LINKS = 2;

    public static function register(): void
    {
        add_action('comment_form_after_fields', [self::class, 'renderFields']);
        add_action('comment_form_logged_in_after', [self::class, 'renderFields']);
        add_filter('preprocess_comment', [self::class, 'validate']);
    }

    public static function renderFields(): void
    {
        $started = time();
        $signature = wp_hash($started . '|' . self::ACTION);

        wp_nonce_field(self::ACTION, self::NONCE_FIELD);
        ?>
        <p class="comment-form-company" hidden aria-hidden="true">
            <label>
                <?php esc_html_e('Company website', 'ya-portfolio'); ?>
                <input
                    type="text"
                    name="<?php echo esc_attr(self::HONEYPOT_FIELD); ?>"
                    value=""
                    tabindex="-1"
                    autocomplete="off"
                >
            </label>
        </p>

        <input
            type="hidden"
            name="<?php echo esc_attr(self::TIME_FIELD); ?>"
            value="<?php echo esc_attr($started); ?>"
        >
        <input
            type="hidden"
            name="<?php echo esc_attr(self::SIGNATURE_FIELD); ?>"
            value="<?php echo esc_attr($signature); ?>"
        >
        <?php
    }

    /**
     * @param array<string, mixed> $commentData
     * @return array<string, mixed>
     */
    public static function validate(array $commentData): array
    {
        if (is_admin()) {
            return $commentData;
        }

        $nonce = isset($_POST[self::NONCE_FIELD])
            ? sanitize_text_field(wp_unslash($_POST[self::NONCE_FIELD]))
            : '';

        if (! wp_verify_nonce($nonce, self::ACTION)) {
            self::reject(__('The comment form expired. Please reload and try again.', 'ya-portfolio'));
        }

        $honeypot = isset($_POST[self::HONEYPOT_FIELD])
            ? trim((string) wp_unslash($_POST[self::HONEYPOT_FIELD]))
            : '';

        if ($honeypot !== '') {
            self::reject(__('The comment could not be submitted.', 'ya-portfolio'));
        }

        $started = isset($_POST[self::TIME_FIELD])
            ? absint($_POST[self::TIME_FIELD])
            : 0;

        $signature = isset($_POST[self::SIGNATURE_FIELD])
            ? sanitize_text_field(
                wp_unslash($_POST[self::SIGNATURE_FIELD])
            )
            : '';

        $expected = wp_hash($started . '|' . self::ACTION);

        if (
            ! $started
            || ! hash_equals($expected, $signature)
        ) {
            self::reject(__('The comment form is invalid.', 'ya-portfolio'));
        }

        $elapsed = time() - $started;

        if (
            $elapsed < self::MIN_SECONDS
            || $elapsed > self::MAX_SECONDS
        ) {
            self::reject(__('Please reload the page before commenting.', 'ya-portfolio'));
        }

        $content = (string) ($commentData['comment_content'] ?? '');
        preg_match_all('#https?://#i', $content, $matches);

        if (count($matches[0] ?? []) > self::MAX_LINKS) {
            self::reject(__('Please reduce the number of links.', 'ya-portfolio'));
        }

        $rateKey = self::rateKey();

        if (get_transient($rateKey)) {
            self::reject(__('Please wait before submitting another comment.', 'ya-portfolio'));
        }

        set_transient($rateKey, 1, self::RATE_SECONDS);

        return $commentData;
    }

    private static function rateKey(): string
    {
        if (is_user_logged_in()) {
            $identity = 'user:' . get_current_user_id();
        } else {
            $address = isset($_SERVER['REMOTE_ADDR'])
                ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']))
                : 'unknown';

            $identity = 'ip:' . hash('sha256', $address);
        }

        return 'ya_comment_rate_' . md5($identity);
    }

    private static function reject(string $message): void
    {
        wp_die(
            esc_html($message),
            esc_html__('Comment not submitted', 'ya-portfolio'),
            ['response' => 403]
        );
    }
}

// Production bootstrap:
// CommentAntispam::register();
