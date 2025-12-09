<?php

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../src/Database/config.php';

use App\Data\Generator;

// Set default parameters
$seed = $_GET['seed'] ?? '123456789';
$locale = $_GET['locale'] ?? 'en_US';
$page = (int) ( $_GET['page'] ?? 1 );
$average_likes = (float) ( $_GET['likes'] ?? 5.0 );

// Input validation
$page = max( 1, $page );
$average_likes = max( 0.0, min( 10.0, $average_likes ) );
$seed = preg_replace( '/[^0-9a-zA-Z]/', '', $seed );

$action = $_GET['action'] ?? 'get_data';

header( 'Content-Type: application/json' );

switch ( $action ) {
case 'get_data':
    $generator = new Generator( $locale );
    $data = $generator->generateBatch( $seed, $average_likes, $page );

    echo json_encode( ['data' => $data, 'page' => $page, 'total_pages' => 50] );
    break;

case 'get_cover':
    header( 'Content-Type: application/json' );
    echo json_encode( ['error' => 'Cover image generation not implemented in this skeleton.'] );
    break;

case 'get_preview':
    header( 'Content-Type: application/json' );
    echo json_encode( ['error' => 'Audio generation not implemented in this skeleton.'] );
    break;

default:
    http_response_code( 404 );
    echo json_encode( ['error' => 'Invalid API action.'] );
    break;
}