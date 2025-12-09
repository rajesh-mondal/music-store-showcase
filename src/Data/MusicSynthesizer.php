<?php

namespace App\Data;

use App\Utils\SeededRNG;

class MusicSynthesizer {
    public function generateCover( string $seed, int $index, string $title, string $artist ): string {
        throw new \Exception( "Cover generation is a complex feature and needs full image library integration." );
    }

    public function generateAudio( string $seed, int $index ): string {
        throw new \Exception( "Audio generation is highly complex and requires music theory and dedicated libraries." );
    }
}