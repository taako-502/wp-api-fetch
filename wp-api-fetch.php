<?php
namespace WpApiFetch;
/*
 * Plugin Name:       Wp Api Fetch Plugin
 * Plugin URI:        https://github.com/and-ai/wp-api-fetch
 * Description:       @wordpress/api-fetchを利用してみる
 * Version:           0.0.0
 * Requires at least: 6.2.2
 * Requires PHP:      7.3
 * Author:            Takao
 * Author URI:        https://ap-ep.com/
 * Text Domain:       wp-api-fetch
 */
define('WAF_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('WAF_PLUGIN_URI', plugin_dir_url( __FILE__ ));

/**
 * 管理画面
 */
const OPTION_GROUP   = 'wp-api-fetch_setting';
const ME_PLUGIN_SLUG = 'wp-api-fetch-plugin';
const DB_NAMES       = 'waf_settings';

// オプションページの追加
add_action('admin_menu', function() {
  add_menu_page(
    __('WP API Fetch 管理','wp-api-fetch'),
    __('WP API Fetch 管理','wp-api-fetch'),
    'manage_options',
    OPTION_GROUP,
    function() { echo '<div id="wp-api-fetch"></div>'; },
    'dashicons-wordpress',
    58
  );
});

/**
 * 必要ファイル読み込み
 * @param  string $hook_suffix 管理ページのフックサフィックス
 */
add_action('admin_enqueue_scripts', function ( $hook_suffix ) {
    // 作成したオプションページ以外では読み込まない
    if ( 'toplevel_page_'.OPTION_GROUP !== $hook_suffix ) {
        return;
    }
    
    // CSSファイルの読み込み
    wp_enqueue_style(
        ME_PLUGIN_SLUG,
        WAF_PLUGIN_URI . 'build/index.css',
        array('wp-components')
    );
    
    // JavaScriptファイルの読み込み
    $asset_file = include_once ( WAF_PLUGIN_PATH . 'build/index.asset.php') ;
    wp_enqueue_script (
        ME_PLUGIN_SLUG,
        WAF_PLUGIN_URI . 'build/index.js',
        $asset_file['dependencies'],
        $asset_file['version'],
        true
    );
});

/**
 * 設定項目の登録
 */
add_action('init', function () {
  register_setting(
    ME_PLUGIN_SLUG,
    DB_NAMES,
    array(
      'type'         => 'array',
      'show_in_rest' => array(
        'schema' => array(
          'type'       => 'object',
          'items'      => '',
          'properties' => array(
            'waf_text'        => array(
              'type'              => 'string',
              'sanitize_callback' => 'sanitize_text_field',
              'default'           => '',
            ),
          )
        ),
      ),
    ),
  );
});

/**
 * カスタマイザーの設定値取得
 */
function get_customizer_setting($key) {
  $settings = get_option(DB_NAMES);
  return isset($settings[$key]) ? $settings[$key] : get_default_customizer_setting($key);
}

/**
 * デフォルト値取得
 */
function get_default_customizer_setting($key) {
  return get_default_customizer_settings()[$key];
}

/**
 * デフォルト値リスト取得
 */
function get_default_customizer_settings() {
  return array(
    'waf_text' => 'これはデフォルトのテキストです。',
  );
}
