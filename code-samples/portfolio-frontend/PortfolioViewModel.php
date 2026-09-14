<?php

declare(strict_types=1);

namespace YounesAbbasi\PortfolioCaseStudy;

/**
 * Public case-study sample.
 *
 * Converts raw WordPress metadata into predictable template data.
 */
final class PortfolioViewModel
{
    private const FIELDS = [
        'client'       => '_ya_project_client',
        'status'       => '_ya_project_status',
        'order_date'   => '_ya_project_order_date',
        'final_date'   => '_ya_project_final_date',
        'location'     => '_ya_project_location',
        'location_url' => '_ya_project_location_url',
        'project_url'  => '_ya_project_url',
    ];

    private const STATUS_LABELS = [
        'in-progress' => 'In progress',
        'completed'   => 'Completed',
        'paused'      => 'Paused',
        'cancelled'   => 'Cancelled',
    ];

    /**
     * @return array<string, array{label: string, value: string, url?: string}>
     */
    public static function details(int $postId): array
    {
        $raw = [];

        foreach (self::FIELDS as $name => $metaKey) {
            $raw[$name] = trim(
                (string) get_post_meta($postId, $metaKey, true)
            );
        }

        $details = [];

        self::addText(
            $details,
            'client',
            __('Client', 'ya-portfolio'),
            $raw['client']
        );

        $status = self::STATUS_LABELS[$raw['status']] ?? '';

        self::addText(
            $details,
            'status',
            __('Status', 'ya-portfolio'),
            $status
        );

        self::addText(
            $details,
            'order_date',
            __('Started', 'ya-portfolio'),
            self::formatDate($raw['order_date'])
        );

        self::addText(
            $details,
            'final_date',
            __('Delivered', 'ya-portfolio'),
            self::formatDate($raw['final_date'])
        );

        if ($raw['location'] !== '') {
            $details['location'] = [
                'label' => __('Location', 'ya-portfolio'),
                'value' => $raw['location'],
            ];

            if ($raw['location_url'] !== '') {
                $details['location']['url'] = esc_url_raw(
                    $raw['location_url']
                );
            }
        }

        if ($raw['project_url'] !== '') {
            $details['project_url'] = [
                'label' => __('Live project', 'ya-portfolio'),
                'value' => __('Visit website', 'ya-portfolio'),
                'url'   => esc_url_raw($raw['project_url']),
            ];
        }

        return $details;
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     url: string,
     *     width: int,
     *     height: int,
     *     alt: string,
     *     layout: string
     * }>
     */
    public static function gallery(int $postId): array
    {
        $stored = get_post_meta(
            $postId,
            '_ya_portfolio_gallery',
            true
        );

        if (! is_array($stored)) {
            return [];
        }

        $gallery = [];

        foreach ($stored as $item) {
            $attachmentId = absint($item['id'] ?? 0);

            if (! $attachmentId) {
                continue;
            }

            $source = wp_get_attachment_image_src(
                $attachmentId,
                'large'
            );

            if (! $source) {
                continue;
            }

            [$url, $width, $height] = $source;

            $layout = sanitize_key($item['layout'] ?? '');

            if (! in_array($layout, ['horizontal', 'vertical'], true)) {
                $layout = $height > $width
                    ? 'vertical'
                    : 'horizontal';
            }

            $alt = trim(
                (string) get_post_meta(
                    $attachmentId,
                    '_wp_attachment_image_alt',
                    true
                )
            );

            if ($alt === '') {
                $alt = get_the_title($postId);
            }

            $gallery[] = [
                'id'     => $attachmentId,
                'url'    => esc_url_raw((string) $url),
                'width'  => absint($width),
                'height' => absint($height),
                'alt'    => sanitize_text_field($alt),
                'layout' => $layout,
            ];
        }

        return $gallery;
    }

    /**
     * @param array<string, array{label: string, value: string}> $details
     */
    private static function addText(
        array &$details,
        string $key,
        string $label,
        string $value
    ): void {
        if ($value === '') {
            return;
        }

        $details[$key] = [
            'label' => $label,
            'value' => $value,
        ];
    }

    private static function formatDate(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();

        if (
            ! $date
            || (
                is_array($errors)
                && (
                    $errors['warning_count'] > 0
                    || $errors['error_count'] > 0
                )
            )
        ) {
            return '';
        }

        return wp_date(
            (string) get_option('date_format'),
            $date->getTimestamp()
        );
    }
}
