#!/usr/bin/php
<?php

if ( count( $argv ) < 2 ) {
	print( 'You should provide the output path like "wikisource-fr-good.atom"' );
	exit( 1 );
}

$basePath = realpath( __DIR__ . '/..' );
$tempPath = sys_get_temp_dir();

$wsexportConfig = [
	'basePath' => $basePath, 'tempPath' => $tempPath, 'stat' => true
];
include_once $basePath . '/book/init.php';

$lang = 'fr';
$category = 'Catégorie:Bon_pour_export';
$outputFile = $argv[1];
$exportPath = 'https://tools.wmflabs.org/wsexport/tool/book.php';

try {
	date_default_timezone_set( 'UTC' );
	$api = new Api( $lang );
	$provider = new BookProvider( $api, [ 'categories' => false, 'images' => false ] );

	$atomGenerator = new OpdsBuilder( $provider, $lang, $exportPath );
	file_put_contents( $outputFile, $atomGenerator->buildFromCategory( $category ) );

	echo "The OPDS file has been created: $outputFile\n";
} catch ( Exception $exception ) {
	echo "Error: $exception\n";
}
