<?php
/*
Plugin Name: PWA Installer
Description: Turns your WordPress site into a PWA and shows an install notification on the homepage.
Version: 1.2
Author: Strong Anchor Tech
Author URI: https://stronganchortech.com
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Enqueue the necessary scripts and manifest
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('pwa-install-script', plugin_dir_url(__FILE__) . 'js/install.js', [], filemtime(plugin_dir_path(__FILE__) . 'js/install.js'), true);
    wp_enqueue_script('service-worker-register', plugin_dir_url(__FILE__) . 'js/service-worker-register.js', [], filemtime(plugin_dir_path(__FILE__) . 'js/service-worker-register.js'), true);

    // Pass translatable strings to JavaScript
    wp_localize_script('pwa-install-script', 'pwaInstallVars', [
        'installText' => __('Install App', 'pwa-installer'),
        'dismissText' => __('Dismiss', 'pwa-installer'),
    ]);
});

// Generate dynamic manifest file
add_action('init', function () {
    if (isset($_GET['pwa-manifest'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'name' => get_bloginfo('name'),
            'short_name' => get_bloginfo('name'),
            'start_url' => home_url('/'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#0073aa',
            'icons' => [
                [
                    'src' => get_site_icon_url(192),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => get_site_icon_url(512),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
        ]);
        exit;
    }
});

add_action('wp_head', function () {
    echo '<link rel="manifest" href="' . esc_url(home_url('?pwa-manifest=1')) . '">';
});

add_action('wp_footer', function () {
    echo '<div id="pwa-install-notification" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #333; color: #fff; padding: 10px 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); width: 90%; max-width: 400px; text-align: center;">
        <div style="display: flex; justify-content: center; gap: 10px;">
            <button id="pwa-install-btn" style="background: #0073aa; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; flex: 1;">' . __('Uygulamayı İndir', 'pwa-installer') . '</button>
            <button id="pwa-dismiss-btn" style="background: #555; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; flex: 1;">' . __('Kapat', 'pwa-installer') . '</button>
        </div>
    </div>';
});

add_action('init', function () {
    // Register service worker route
    if (isset($_GET['service-worker'])) {
        header('Content-Type: application/javascript');
        readfile(plugin_dir_path(__FILE__) . 'js/service-worker.js');
        exit;
    }
});
