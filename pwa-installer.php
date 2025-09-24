<?php
/*
Plugin Name: PWA Installer
Description: Turns your WordPress site into a PWA and shows an install notification on supported devices (not on /embed/ URLs).
Version: 1.3
Author: Strong Anchor Tech
Author URI: https://stronganchortech.com
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Helper: true if current request path contains /embed/
 */
function pwa_installer_is_embed_context(): bool {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return stripos($uri, '/embed/') !== false;
}

// Enqueue the necessary scripts and manifest
add_action('wp_enqueue_scripts', function () {
    // Always register service worker registration script (ok on any page)
    wp_enqueue_script(
        'service-worker-register',
        plugin_dir_url(__FILE__) . 'js/service-worker-register.js',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'js/service-worker-register.js'),
        true
    );

    // Skip the install UI on /embed/ pages
    if (pwa_installer_is_embed_context()) {
        return;
    }

    wp_enqueue_script(
        'pwa-install-script',
        plugin_dir_url(__FILE__) . 'js/install.js',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'js/install.js'),
        true
    );

    // Pass translatable strings and a tiny bit of context to JS
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

// Install banner container (not on /embed/)
add_action('wp_footer', function () {
    if (pwa_installer_is_embed_context()) {
        return;
    }

    echo '<div id="pwa-install-notification" style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background:#333; color:#fff; padding:10px 20px; border-radius:5px; box-shadow:0 2px 10px rgba(0,0,0,0.2); width:90%; max-width:400px; text-align:center;">
        <div style="display:flex; justify-content:center; gap:10px;">
            <button id="pwa-install-btn" style="background:#0073aa; color:white; padding:10px 15px; border:none; cursor:pointer; border-radius:3px; flex:1;">' . esc_html__('Install App', 'pwa-installer') . '</button>
            <button id="pwa-dismiss-btn" style="background:#555; color:white; padding:10px 15px; border:none; cursor:pointer; border-radius:3px; flex:1;">' . esc_html__('Dismiss', 'pwa-installer') . '</button>
        </div>
    </div>';
});

// Register service worker route
add_action('init', function () {
    if (isset($_GET['service-worker'])) {
        header('Content-Type: application/javascript');
        readfile(plugin_dir_path(__FILE__) . 'js/service-worker.js');
        exit;
    }
});
