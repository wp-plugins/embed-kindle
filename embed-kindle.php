<?php

/*
  Plugin Name: Embed Kindle
  Plugin URI: http://wordpress.org/extend/plugins/embed-kindle
  Description: embed Kindle e-book
  Author: @Akky Akimoto
  Version: 1.0.1
  Author URI: http://akimoto.jp/
  License: GPL2
 */

if (version_compare(PHP_VERSION, '5.3', '<')) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
    deactivate_plugins( __FILE__ );
    wp_die('Embed Kindle requires PHP version 5.3 or more');
}

spl_autoload_register(
    function($className) {
        if (strncasecmp($className,
                        'Akky_EmbedKindle',
                        strlen('Akky_EmbedKindle')) !== 0) {
            return false;
        }
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        require 'includes/' . $fileName;
    }
);
new Akky_EmbedKindle();
