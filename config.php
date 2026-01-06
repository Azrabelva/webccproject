<?php
/* =====================================================
   SESSION
   ===================================================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================================================
   BASE URL (Otomatis deteksi)
   ===================================================== */
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF']);

// Normalize path separators for URLs (convert backslashes to forward slashes)
$scriptDir = str_replace('\\', '/', $scriptDir);

$BASE_URL = $protocol . $host . rtrim($scriptDir, '/');

/* =====================================================
   CARD STORAGE (JSON) - Keeping this for backward compat
   ===================================================== */
$CARD_DIR = __DIR__ . '/cards';
if (!is_dir($CARD_DIR)) {
    mkdir($CARD_DIR, 0777, true);
}

/* =====================================================
   DATABASE CONNECTION (SQLite via PDO)
   ===================================================== */
$DB_FILE = __DIR__ . '/database.sqlite';
$conn = null;

try {
    $conn = new PDO('sqlite:' . $DB_FILE);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initial Setup (Auto-Migration)
    $conn->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            username TEXT UNIQUE,
            email TEXT UNIQUE,
            password TEXT,
            premium INTEGER DEFAULT 0,
            oauth_provider TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            template_key TEXT NOT NULL,
            title TEXT NOT NULL,
            image TEXT NOT NULL,
            is_premium INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS cards (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            template_type TEXT,
            receiver_name TEXT,
            sender_name TEXT,
            main_message TEXT,
            extra_title TEXT,
            extra_text TEXT,
            photo1 TEXT,
            photo2 TEXT,
            photo3 TEXT,
            spotify_link TEXT,
            payment_status TEXT DEFAULT "unpaid",
            price INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT UNIQUE,
            user_id INTEGER,
            amount INTEGER,
            status TEXT DEFAULT "pending",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id)
        );
    ');

} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

/* =====================================================
   SYSTEM ADMIN CREDENTIALS (HARDCODED)
   ===================================================== */
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'lovecrafted2025';

/* =====================================================
   MIDTRANS CONFIGURATION (SANDBOX)
   ===================================================== */
$MIDTRANS_ENV = 'sandbox'; 
$MIDTRANS_SERVER_KEY = 'Mid-server-G_MncuZhAiv9L4WpvmZ5jjGL';
$MIDTRANS_CLIENT_KEY = 'Mid-client-bML5eC8KgU0m0b4L';
$MIDTRANS_SNAP_URL = 'https://app.sandbox.midtrans.com/snap/snap.js'; // Sandbox

/* =====================================================
   GOOGLE OAUTH CONFIGURATION
   ===================================================== */
$GOOGLE_CLIENT_ID = '615293596362-95gc7m4duel9rbujis8mk5jngjalbucf.apps.googleusercontent.com';
$GOOGLE_CLIENT_SECRET = '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';
$GOOGLE_REDIRECT_URI = $BASE_URL . '/login_google_callback.php';

/* =====================================================
   LOAD COMPOSER DEPENDENCIES
   ===================================================== */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Configure Midtrans only if library is loaded
    if (class_exists('\Midtrans\Config')) {
        \Midtrans\Config::$isProduction = false; // SANDBOX
        \Midtrans\Config::$serverKey    = $MIDTRANS_SERVER_KEY;       
        \Midtrans\Config::$clientKey    = $MIDTRANS_CLIENT_KEY;       
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }
}

/* =====================================================
   AUTH HELPERS
   ===================================================== */
function require_admin(){
    if (empty($_SESSION['is_admin'])) {
        header('Location: login.php');
        exit;
    }
}

function require_user(){
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
