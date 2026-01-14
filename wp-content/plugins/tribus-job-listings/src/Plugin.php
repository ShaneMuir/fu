<?php
declare(strict_types=1);

namespace TRIBUS_JL;

final class Plugin {
    public static function init(): void
    {
        // Load translations.
        add_action('init', [__CLASS__, 'load_textdomain']);

        // Confirm plugin boot.
        add_action('admin_notices', [__CLASS__, 'admin_notice_booted']);
    }

    public static function load_textdomain(): void
    {
        load_plugin_textdomain(
            'tribus-job-listings',
            false,
            dirname(plugin_basename(TRIBUS_JL_PLUGIN_FILE)) . '/languages'
        );
    }

    public static function admin_notice_booted(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        printf(
            '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html__('Tribus Job Listings loaded.', 'tribus-job-listings')
        );
    }
}