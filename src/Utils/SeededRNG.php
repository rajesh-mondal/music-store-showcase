<?php

namespace App\Utils;

class SeededRNG {
    public static function getSeededInt( string $base_seed_value, int $item_index, string $seed_key, int $min, int $max ): int {
        $combined_string = $base_seed_value . '|' . $item_index . '|' . $seed_key;

        $final_seed = crc32( $combined_string ) + crc32( md5( $combined_string ) );

        $final_seed = abs( $final_seed ) % 0x80000000;

        // Seed the generator for this specific record/field
        mt_srand( $final_seed );

        // Generate the random integer
        return mt_rand( $min, $max );
    }

    public static function getSeededFloat( string $base_seed_value, int $item_index, string $seed_key ): float {
        $min = 0;
        $max = mt_getrandmax();

        $combined_string = $base_seed_value . '|' . $item_index . '|' . $seed_key;
        $final_seed = crc32( $combined_string ) + crc32( md5( $combined_string ) );
        $final_seed = abs( $final_seed ) % 0x80000000;

        mt_srand( $final_seed );

        return mt_rand() / $max;
    }
}