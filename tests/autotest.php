<?php

// set up Composer's auto loader. 
if( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
    require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );
    $loader = include( dirname( __DIR__ ) . '/vendor/autoload.php' );
} elseif( file_exists( dirname( __DIR__ ) . '/../../../vendor/autoload.php' ) ) {
    require_once( dirname( __DIR__ ) . '/../../../vendor/autoload.php' );
    $loader = include( dirname( __DIR__ ) . '/../../../vendor/autoload.php' );
}

/** @var \Composer\Autoload\ClassLoader $loader */

$loader->addPsr4( 'Cena\\Cena\\', dirname( __DIR__ ) .'/src' );
$loader->addPsr4( 'Tests\\', __DIR__ );
$loader->register();

