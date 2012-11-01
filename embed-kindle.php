<?php

/*
  Plugin Name: Embed Kindle
  Plugin URI: http://wordpress.org/extend/plugins/embed-kindle
  Description: embed Kindle e-book
  Author: @Akky Akimoto
  Version: 1.0.0
  Author URI: http://akimoto.jp/
  License: GPL2
 */

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
