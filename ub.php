<?php

/**
 * @link              https://unibase.ru
 * @since             1.0.0
 * @package           Ub
 *
 * @wordpress-plugin
 * Plugin Name:       UniBase
 * Plugin URI:        https://unibase.ru/help/unibase-wordpress-plugin
 * Description:       Готовое решение для вашего бизнеса по конвертации трафика в продажи
 * Version:           1.0.0
 * Author:            Nikita Kazeichev
 * Author URI:        https://unibase.ru
 * License:           MIT
 * Text Domain:       ub
 * Domain Path:       /languages
 */

/*
MIT License

Copyright (c) 2018 https://unibase.ru

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */

define('UB_PLUGIN_DIR', str_replace('\\','/', dirname(__FILE__)));

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

add_action( 'plugins_loaded', 'loadTextDomain' );
/**
 * Загружаем языковую версию
 */
function loadTextDomain() {
    load_plugin_textdomain( 'ub', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action('admin_menu', 'ubAddMenu');

/**
 * Создаем меню в админ. панеле
 */
function ubAddMenu()
{
    add_menu_page(
        'UniBase',
        'UniBase',
        'manage_options',
        'unibase',
        'ubMainMenu',
        plugins_url() . '/unibase/images/ub-favicon.svg',
        4
    );
}

/**
 * Рендерим меню в админ. панеле
 */
function ubMainMenu()
{
    require_once(UB_PLUGIN_DIR . '/includes/options.php');
}


add_action('admin_init', 'ubSaveForm');
/**
 * Обрабатываем сохранение формы
 */
function ubSaveForm()
{
    if (
        isset($_POST['ubSubmit'])
        && check_admin_referer('unibase', '_ubNonce')
        && current_user_can('edit_user')
    ) {
        // ID пользователя | Например: UB-234321345
        $rawUbUserId = strip_tags($_POST['ubUserId']);

        // ID источника | Например: 32
        $rawUbSourceId = (int) $_POST['ubSourceId'];

        // Статус ошибки
        $_POST['isUbError'] = false;

        if (
            !empty($rawUbSourceId)
            && !empty($rawUbUserId)
            && strpos($rawUbUserId, 'UB') === 0
            && $rawUbSourceId > 0
        ) {
            $clearUbUserId = sanitize_text_field($rawUbUserId);
            $clearUbSourceId = sanitize_text_field($rawUbSourceId);

            update_option('ub_tracking_user_id', $clearUbUserId);
            update_option('ub_tracking_source_id', $clearUbSourceId);
        } else {
            update_option('ub_tracking_user_id', '');
            update_option('ub_tracking_source_id', '');
            $_POST['isUbError'] = true;
        }
    }

}

add_action('wp_head', 'ubInsertCode', 0);
/**
 * Вставляем код на страницы
 */
function ubInsertCode()
{
    $userId = get_option('ub_tracking_user_id');
    $sourceId = get_option('ub_tracking_source_id');

    if (!empty($userId) && !empty($sourceId)) {
        $userId = esc_textarea($userId);
        $sourceId = esc_textarea($sourceId);

        echo "\n", '<!-- UniBase Tracking Code start -->', "\n";
        echo "
            <script>
                (function(u,n,i,b,a,s,e){u['UniBaseObject']=a;u[a]=u[a]||function(){
                    (u[a].q=u[a].q||[]).push(arguments)},u[a].l=1*new Date();s=n.createElement(i),
                    e=n.getElementsByTagName(i)[0];s.async=1;s.src=b;e.parentNode.insertBefore(s,e)
                })(window,document,'script', 'https://tracker.unibase.ru/unibase.js','ub');

                ub('create', '{$userId}', '{$sourceId}');
                ub('send', 'pageview');
            </script>
        ", "\n";
        echo '<!-- UniBase Tracking Code end -->', "\n";
    }
}
