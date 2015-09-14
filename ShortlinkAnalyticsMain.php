<?php

/**
 * Plugin Name: SVS ShortLink Analytics
 * Plugin URI: http://svs-websoft.com
 * Description: Generate short links & show link analytics.
 * Version: 1.0.1
 * Author: SVS WebSoft
 * Author URI: http://svs-websoft.com
 * License: GPL v3
 */

require_once "ShortlinkAnalyticsUtils.php";
require_once "ShortlinkAnalyticsModel.php";
global $wpdb;

/**
 * Create table structure on plugin install
 */
function shortLinkAnalyticsOptionsInstall() {
    global $wpdb;

    $shortlinkAnalyticsModel = new ShortlinkAnalyticsModel($wpdb);
    $shortlinkAnalyticsModel->createDatabase();
}


/**
 * Add admin menu "SVS Shortlink"
 */
function shortLinkAnalyticsAdminMenu(){
    add_menu_page('SVS Shortlink Analytics', 'SVS ShortLink', 'manage_options', 'svs_shortlink_analytics', 'shortlinkAnalyticsAdminActions', 'dashicons-groups');
}


/**
 * SVS Shortlink administration panel in wp-admin
 */
function shortlinkAnalyticsAdminActions() {
    global $wpdb;
    $shortlinkAnalyticsModel = new ShortlinkAnalyticsModel($wpdb);


    if (array_key_exists('Action',$_GET)){
        if ($_GET['Action'] == 'Add'){
            $shortlinkAnalyticsModel->addLink();
            $_data = $shortlinkAnalyticsModel->getLinks();
            $shortlinkAnalyticsModel->loadView('default', $_data);
        } else if ($_GET['Action'] == 'Delete'){
            $shortlinkAnalyticsModel->deleteLink();
        } else if ($_GET['Action'] == 'Reset'){
            $shortlinkAnalyticsModel->resetLink();
        }
        else if ($_GET['Action'] == 'View'){
            $_data = $shortlinkAnalyticsModel->getStatistics();
            $shortlinkAnalyticsModel->loadView('statistics', $_data);
        }

    }

    $_data = $shortlinkAnalyticsModel->getLinks();
    $shortlinkAnalyticsModel->loadView('default', $_data);

}

register_activation_hook(__FILE__, 'shortLinkAnalyticsOptionsInstall');
add_action('admin_menu','shortLinkAnalyticsAdminMenu');
$shortlinkAnalyticsModel = new ShortlinkAnalyticsModel($wpdb);
$shortlinkAnalyticsModel->checkRedirect();
