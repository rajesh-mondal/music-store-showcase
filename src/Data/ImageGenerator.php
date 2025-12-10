<?php

namespace App\Data;

use App\Utils\SeededRNG;

class ImageGenerator {

    public function generateCover( string $seed, int $index, string $title, string $artist ): void {

        // Image Configuration
        $width = 300;
        $height = 300;

        // Check if the GD extension is available
        if ( !extension_loaded( 'gd' ) ) {
            header( 'Content-Type: text/plain' );
            echo "Error: PHP GD extension is not enabled.";
            return;
        }

        $image = imagecreatetruecolor( $width, $height );

        // Background and Colors
        $r = SeededRNG::getSeededInt( $seed, $index, 'r_color', 50, 200 );
        $g = SeededRNG::getSeededInt( $seed, $index, 'g_color', 50, 200 );
        $b = SeededRNG::getSeededInt( $seed, $index, 'b_color', 50, 200 );
        $bgColor = imagecolorallocate( $image, $r, $g, $b );
        imagefill( $image, 0, 0, $bgColor );

        $brightness = ( $r * 299 + $g * 587 + $b * 114 ) / 1000;
        $textColor = ( $brightness > 150 )
        ? imagecolorallocate( $image, 20, 20, 20 ) // Dark text
         : imagecolorallocate( $image, 255, 255, 255 ); // Light text

        // Draw Text

        $font_size = 5;
        $char_width = 9;
        $char_height = 16;

        // Title: Center Text Calculation
        $title_length = strlen( $title );
        $xTitle = ( $width / 2 ) - ( ( $title_length * $char_width ) / 2 );
        $yTitle = ( $height / 2 ) - $char_height - 10;

        imagestring( $image, $font_size, (int) $xTitle, (int) $yTitle, $title, $textColor );

        // Artist: Center Text Calculation
        $artist_text = "Artist: " . $artist;
        $artist_length = strlen( $artist_text );
        $xArtist = ( $width / 2 ) - ( ( $artist_length * $char_width ) / 2 );
        $yArtist = ( $height / 2 ) + 10;

        imagestring( $image, $font_size, (int) $xArtist, (int) $yArtist, $artist_text, $textColor );

        // Output Image
        header( 'Content-Type: image/jpeg' );
        imagejpeg( $image, NULL, 90 );
        imagedestroy( $image );
    }
}