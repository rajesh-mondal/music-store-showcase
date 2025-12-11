# MUSIC SHOWCASE GENERATOR

A PHP-based application designed to demonstrate robust, seed-based data generation, content reproducibility, and dynamic file output (images, audio, and ZIP archives). This project mimics a functional music store API by creating unique, consistent, and localized song records on demand.

## 🚀 Getting Started
To run this project locally, follow the steps below.
## Prerequisites

* PHP 8.0+.
* Composer for dependency management and autoloading.


## Installation

### 1. Clone the repository:

```bash
git clone https://github.com/rajesh-mondal/music-showcase-generator
cd music-showcase-generator
```

### 2. Install PHP dependencies:

```bash
composer install
```

### 3. Setup Web Server:

Configure your web server to serve the public/ directory, or use PHP's built-in server:

```bash
php -S localhost:8000 -t public
```

### 3. Access the Application:
Open `http://localhost:8000/index.html` in your browser.


## ✨ Key Features

This application showcases advanced features primarily focused on data consistency, localization, and on-the-fly content generation.

### 🧬 Reproducible Data Generation (Seeding)
Every piece of generated data is fully deterministic:

* **Global Seed Consistency:** By setting the initial "Seed" value, every song record, from its title and artist to its calculated "Likes" count and generated audio, remains **identical** across subsequent page loads and user sessions.
* `SeededRNG` **Utility:** Utilizes the `App\Utils\SeededRNG` class to ensure all randomization (e.g., choosing an artist, generating a frequency for music) is predictable and tied to the `$seed` and `$index` parameters.
* **Customizable Averages:** The average "Likes" value can be adjusted via a slider, which deterministically influences the individual song likes around that average for the specified seed.

### 🌍 Localization and Data Assets
* **Multi-language Support:** All core song metadata (Titles, Artists, Albums, Genres) are sourced from dedicated **JSON files** (`de_DE.json`, `en_US.json`).
* **Dynamic Language Switching:** The entire dataset switches instantly when the "Language/Region" selector is changed, maintaining full reproducibility for the given seed in the new language.


### 🖥️ Dynamic Display and User Experience
* **View Mode Switching:** Supports two display modes: **Table View** (for dense data and pagination) and **Gallery View** (for image-focused browsing and infinite scroll).
* **Pagination & Infinite Scroll:** Implements traditional pagination for the Table View and modern infinite scrolling for the Gallery View, both correctly handled by the API.
* **Toggleable Details (Table View):** Clicking a row in the Table View expands to show the album cover, full review text, and the in-page audio player.

### 🖼️ Dynamic Cover Art Generation
* `ImageGenerator.php`**:** The cover images are generated dynamically on the server via the `api.php?action=get_cover` endpoint.
* **Content-Aware Design:** The generated image includes the Song Title and Artist name (passed via the URL parameters), ensuring the cover is accurate for that specific song.
* **Caching Ready:** The API structure supports content caching (though not explicitly implemented) since the URL parameters (seed, index, title, artist) are fully deterministic.

### 🔊 On-the-Fly Audio Synthesis
* `MusicSynthesizer.php`**:** This core feature fulfills the requirement to generate actual, playable music audio directly in the browser.
* **WAV Output:** The endpoint streams a short, uncompressed WAV file (simulating music data) to the front-end.
* **Guaranteed Reproducibility:** The audio waveform (frequency, duration, rhythm) is derived using the song's unique `$seed` and `$index`, meaning the same song title will always play the exact same preview clip.
* **In-Page Playback:** Audio is played using the HTML5 `<audio>` element for immediate user experience without navigating away or relying on console inspection.

## 💡 Future Work

The following feature is planned for development to expand the project's utility:

* **Export and Archiving:** Implementing a server-side utility (`ZipGenerator.php`) to generate a downloadable ZIP archive containing all currently viewed songs. Each file will be named descriptively: `[Song Title] - [Artist Name] - [Album Name]`.mp3.
