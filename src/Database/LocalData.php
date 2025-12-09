<?php
namespace App\Database;

class LocalData {
    private \PDO $pdo;
    private array $cache = [];

    public function __construct() {
        $this->pdo = get_db_connection();
    }

    // Fetches all localized data arrays for a given locale from the database.
    public function getLocaleData( string $locale_code ): array {
        if ( isset( $this->cache[$locale_code] ) ) {
            return $this->cache[$locale_code];
        }

        // Prepare the query to select all values for the given locale
        $sql = "SELECT data_type, value FROM locale_data WHERE locale_code = :locale_code";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [':locale_code' => $locale_code] );
        $results = $stmt->fetchAll();

        // Structure the data into the format needed by the Generator
        $structured_data = [];
        foreach ( $results as $row ) {
            $type = $row['data_type'];
            if ( !isset( $structured_data[$type] ) ) {
                $structured_data[$type] = [];
            }
            $structured_data[$type][] = $row['value'];
        }

        $this->cache[$locale_code] = $structured_data;
        return $structured_data;
    }

    public function getAvailableLocales(): array {
        $sql = "SELECT code, name FROM locales ORDER BY name ASC";
        $stmt = $this->pdo->query( $sql );
        return $stmt->fetchAll();
    }
}