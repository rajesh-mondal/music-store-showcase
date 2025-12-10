<?php

namespace App\Data;

use App\Database\LocalData;
use App\Utils\SeededRNG;

class Generator {
    private LocalData $localData;
    private array $data;
    private string $locale;

    private const RECORDS_PER_PAGE = 20;

    // --- Hardcoded Locale Data
    private $locale_data = [
        'en_US' => [
            'titles'         => [
                "Fast Carrot Plus", "Midnight Drive", "Echoes of Silence", "Neon Heartbeat",
                "Quantum Leap", "The Forgotten Algorithm", "Bootstrap Blues", "Raw PHP Revolution",
            ],
            'artists_person' => [
                "Sarah K.", "John Doe", "Maya Li", "Alex Stone", "Elias Vance", "Olivia Chen",
            ],
            'artists_band'   => [
                "The Code Breakers", "Bootstrap Boiz", "Raw PHP", "Syntax Error",
                "The Lambda Functions", "Midnight Commit",
            ],
            'genres'         => ["Rock", "Pop", "Electronic", "Jazz", "Lo-Fi", "Hip-Hop"],
            'reviews'        => [
                "A modern classic that redefines the genre.", "Visually stunning and aurally immersive.",
                "Perfect for a late-night drive, truly mesmerizing.", "A masterpiece of rhythm and code.",
                "Simple, yet profoundly moving.", "An unexpected gem of digital sound.",
            ],
        ],
        'de_DE' => [
            'titles'         => [
                "Schnelle Karotte Plus", "Mitternachtsfahrt", "Echos der Stille", "Neon Herzschlag",
                "Quantensprung", "Der Vergessene Algorithmus", "Bootstrap Blues", "Rohe PHP Revolution",
            ],
            'artists_person' => [
                "Lena Müller", "Klaus Schmidt", "Anja Bauer", "Timo Herbst", "Sina Vogt", "Max Richter",
            ],
            'artists_band'   => [
                "Die Code Brecher", "Bootstrap Jungs", "Rohe PHP", "Syntax Fehler",
                "Die Lambda Funktionen", "Mitternachts-Commit",
            ],
            'genres'         => ["Rock", "Pop", "Elektronisch", "Jazz", "Lo-Fi", "Hip-Hop"],
            'reviews'        => [
                "Ein moderner Klassiker, der das Genre neu definiert.", "Visuell beeindruckend und akustisch immersiv.",
                "Perfekt für eine späte Nachtfahrt, wirklich faszinierend.", "Ein Meisterwerk aus Rhythmus und Code.",
                "Einfach, aber zutiefst bewegend.", "Ein unerwartetes Juwel des digitalen Klangs.",
            ],
        ],
    ];

    private function loadDataFromFile( string $locale ): ?array {

        $path = dirname( __DIR__ ) . "/Locales/{$locale}.json";

        if ( file_exists( $path ) ) {
            $json_content = file_get_contents( $path );

            if ( $json_content === false ) {
                return null;
            }

            $data = json_decode( $json_content, true );

            if ( json_last_error() === JSON_ERROR_NONE && is_array( $data ) && !empty( $data['titles'] ) ) {
                return $data;
            }
        }
        return null;
    }

    public function __construct( string $locale ) {
        $this->locale = $locale;

        // Load data from the local JSON file
        $file_data = $this->loadDataFromFile( $locale );

        if ( $file_data !== null ) {
            $this->data = $file_data;
            return;
        }

        // Try the database (if JSON fails)
        $this->localData = new LocalData();
        $db_data = $this->localData->getLocaleData( $this->locale );

        if ( !empty( $db_data ) && !empty( $db_data['titles'] ) ) {
            $this->data = $db_data;
            return;
        }

        // Last Resort: Hardcoded default data
        if ( isset( $this->locale_data[$locale] ) ) {
            $this->data = $this->locale_data[$locale];
        } else {
            $this->data = $this->locale_data['en_US'];
        }
    }

    public function generateBatch( string $seed, float $average_likes, int $page, int $count = self::RECORDS_PER_PAGE ): array {
        $songs = [];
        $start_index = ( $page - 1 ) * $count + 1;

        for ( $i = 0; $i < $count; $i++ ) {
            $sequence_index = $start_index + $i;

            // Generate reproducible core content
            $title = $this->generateTitle( $seed, $sequence_index );
            $artist = $this->generateArtist( $seed, $sequence_index );
            $album = $this->generateAlbum( $seed, $sequence_index );
            $genre = $this->generateGenre( $seed, $sequence_index );
            $review = $this->generateReview( $seed, $sequence_index );

            // Generate reproducible like count
            $likes = $this->generateLikes( $seed, $sequence_index, $average_likes );

            $songs[] = [
                'index'       => $sequence_index,
                'title'       => $title,
                'artist'      => $artist,
                'album'       => $album,
                'genre'       => $genre,
                'likes'       => $likes,
                'review'      => $review,
                'cover_url'   => 'api.php?action=get_cover&seed=' . urlencode( $seed ) . '&index=' . $sequence_index . '&title=' . urlencode( $title ) . '&artist=' . urlencode( $artist ),
                'preview_url' => 'api.php?action=get_preview&seed=' . urlencode( $seed ) . '&index=' . $sequence_index,
            ];
        }

        return $songs;
    }

    private function generateTitle( string $seed, int $index ): string {
        $titles = $this->data['titles'];
        $rand_index = SeededRNG::getSeededInt( $seed, $index, 'title', 0, count( $titles ) - 1 );
        return $titles[$rand_index];
    }

    private function generateArtist( string $seed, int $index ): string {
        $is_band = SeededRNG::getSeededInt( $seed, $index, 'artist_type', 0, 1 );
        $key = $is_band ? 'artists_band' : 'artists_person';
        $artists = $this->data[$key];
        $rand_index = SeededRNG::getSeededInt( $seed, $index, 'artist_name', 0, count( $artists ) - 1 );
        return $artists[$rand_index];
    }

    private function generateAlbum( string $seed, int $index ): string {
        $is_single = SeededRNG::getSeededInt( $seed, $index, 'album_type', 1, 4 ) === 1;
        if ( $is_single ) {
            return "Single";
        }
        $titles = $this->data['titles'];
        $rand_index = SeededRNG::getSeededInt( $seed, $index, 'album_title', 0, count( $titles ) - 1 );
        return "The " . $titles[$rand_index] . " Collection";
    }

    private function generateGenre( string $seed, int $index ): string {
        $genres = $this->data['genres'];
        $rand_index = SeededRNG::getSeededInt( $seed, $index, 'genre', 0, count( $genres ) - 1 );
        return $genres[$rand_index];
    }

    private function generateReview( string $seed, int $index ): string {
        $reviews = $this->data['reviews'];
        $rand_index = SeededRNG::getSeededInt( $seed, $index, 'review', 0, count( $reviews ) - 1 );
        return $reviews[$rand_index] . " (Index: " . $index . ", Seed: " . $seed . ")";
    }

    private function generateLikes( string $seed, int $index, float $average_likes ): int {
        $L = max( 0.0, min( 10.0, $average_likes ) );

        if ( $L == 10.0 ) {
            return 10;
        }
        if ( $L == 0.0 ) {
            return 0;
        }

        $integer_part = (int) floor( $L );
        $fractional_part = $L - $integer_part;

        $random_float = SeededRNG::getSeededFloat( $seed, $index, 'likes_prob' );

        $extra_like = 0;
        if ( $random_float < $fractional_part ) {
            $extra_like = 1;
        }

        return $integer_part + $extra_like;
    }
}