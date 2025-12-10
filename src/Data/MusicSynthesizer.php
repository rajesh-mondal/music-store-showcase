<?php

namespace App\Data;

use App\Utils\SeededRNG;

class MusicSynthesizer {

    private const SAMPLE_RATE = 44100;
    // Length of the preview clip
    private const DURATION_SECONDS = 3;

    /**
     * Generates a reproducible, simple WAV audio file directly to the output.
     */
    public function generatePreview( string $seed, int $index ): void {

        // Generate predictable audio parameters
        $base_frequency = SeededRNG::getSeededInt( $seed, $index, 'freq_base', 200, 800 );

        // Use the seed and index to determine the rhythmic speed
        $note_duration_factor = SeededRNG::getSeededFloat( $seed, $index, 'rhythm_speed' );

        // Generate a simple, repeatable sequence of relative frequency changes
        $sequence_length = 8;
        $sequence = [];
        for ( $i = 0; $i < $sequence_length; $i++ ) {
            // Generates a predictable step (0 to 12 semitones) for each note in the sequence
            $step = SeededRNG::getSeededInt( $seed, $index, "note_step_{$i}", -5, 7 );
            // Calculate frequency using semitones
            $sequence[] = $base_frequency * pow( 2, $step / 12 );
        }

        // Generate wav data

        $data = '';
        $num_samples = self::SAMPLE_RATE * self::DURATION_SECONDS;
        $num_notes = count( $sequence );
        $samples_per_note = floor( $num_samples / $num_notes );

        for ( $i = 0; $i < $num_samples; $i++ ) {
            $current_note_index = floor( $i / $samples_per_note ) % $num_notes;
            $frequency = $sequence[$current_note_index];

            $amplitude = 0.5;
            $sample = $amplitude * sin( 2 * M_PI * $frequency * ( $i / self::SAMPLE_RATE ) );

            $int_sample = floor( $sample * 32767 );

            $data .= pack( 'v', $int_sample );
        }

        // Construct wav header

        $data_size = strlen( $data );
        $file_size = $data_size + 36;
        $bit_depth = 16;
        $num_channels = 1;
        $byte_rate = self::SAMPLE_RATE * $num_channels * ( $bit_depth / 8 );

        $wav_header =
        'RIFF' .
        pack( 'V', $file_size ) .
        'WAVE' .
        'fmt ' .
        pack( 'V', 16 ) .
        pack( 'v', 1 ) .
        pack( 'v', $num_channels ) .
        pack( 'V', self::SAMPLE_RATE ) .
        pack( 'V', $byte_rate ) .
        pack( 'v', $num_channels * ( $bit_depth / 8 ) ) .
        pack( 'v', $bit_depth ) .
        'data' .
        pack( 'V', $data_size ) .
            $data;

        // Output headers and data
        ob_clean();

        header( 'Content-Type: audio/wav' );
        header( 'Content-Length: ' . strlen( $wav_header ) );
        header( 'Content-Disposition: inline; filename="preview_' . $seed . '_' . $index . '.wav"' );

        echo $wav_header;
        exit;
    }
}