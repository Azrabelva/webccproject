<?php
/* =====================================================
   SESSION
   ===================================================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================================================
   ERROR REPORTING
   (MATIKAN DISPLAY ERROR BIAR JSON BERSIH)
   ===================================================== */
error_reporting(E_ALL);
ini_set('display_errors', 0);

/* =====================================================
   DATABASE CONNECTION (MYSQLI)
   ===================================================== */
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'lovecrafted';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    die("Database error");
}
$conn->set_charset("utf8mb4");

/* =====================================================
   ADMIN LOGIN
   ===================================================== */
$ADMIN_USER = "admin";
$ADMIN_PASS = "admin"; // sandbox / lokal

/* =====================================================
   BASE URL (AUTO)
   ===================================================== */
$BASE_URL =
    ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://')
    . $_SERVER['HTTP_HOST']
    . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

/* =====================================================
   CARD STORAGE (JSON)
   ===================================================== */
$CARD_DIR = __DIR__ . '/cards';
if (!is_dir($CARD_DIR)) {
    mkdir($CARD_DIR, 0777, true);
}

/* =====================================================
   MIDTRANS CONFIGURATION (SANDBOX)
   ===================================================== */
require_once __DIR__ . '/vendor/autoload.php';

/* === ENVIRONMENT FLAG (INI YANG TADI ERROR) === */
$MIDTRANS_ENV = 'sandbox'; // WAJIB ADA

/* === SANDBOX KEYS === */
$MIDTRANS_SERVER_KEY = "Mid-server-G_MncuZhAiv9L4WpvmZ5jjGL";
$MIDTRANS_CLIENT_KEY = "Mid-client-bML5eC8KgU0m0b4L";

/* === APPLY MIDTRANS CONFIG === */
\Midtrans\Config::$isProduction = false; // SANDBOX
\Midtrans\Config::$serverKey    = $MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey    = $MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isSanitized  = true;
\Midtrans\Config::$is3ds        = true;

/* =====================================================
   JSON CARD FUNCTIONS
   ===================================================== */
function load_card_json($id){
    global $CARD_DIR;
    $file = $CARD_DIR . "/$id.json";
    return is_file($file) ? json_decode(file_get_contents($file), true) : null;
}

function save_card_json($data){
    global $CARD_DIR;
    if (empty($data['id'])) return false;

    $file = $CARD_DIR . "/{$data['id']}.json";
    return (bool) file_put_contents(
        $file,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function delete_card_json($id){
    global $CARD_DIR;
    $file = $CARD_DIR . "/$id.json";
    return is_file($file) ? unlink($file) : false;
}

/* =====================================================
   DATABASE CARD FUNCTIONS
   ===================================================== */
function load_card_db($publicId){
    global $conn;

    $stmt = $conn->prepare(
        "SELECT * FROM cards WHERE public_id = ? LIMIT 1"
    );
    $stmt->bind_param("s", $publicId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        return [
            'id'             => $row['public_id'],
            'type'           => $row['template_type'],
            'to'             => $row['receiver_name'],
            'from'           => $row['sender_name'],
            'message1'       => $row['main_message'],
            'spotify_url'    => $row['spotify_link'],
            'photos'         => array_filter([
                $row['photo1'], $row['photo2'], $row['photo3']
            ]),
            'payment_status' => $row['payment_status'],
            'created_at'     => $row['created_at'],
        ];
    }
    return null;
}

function save_card_db($card){
    global $conn;

    $stmt = $conn->prepare("
        INSERT INTO cards
        (public_id, user_id, template_type, receiver_name, sender_name,
         main_message, spotify_link,
         photo1, photo2, photo3,
         status, payment_status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "sissssssssss",
        $card['id'],
        $_SESSION['user']['id'],
        $card['type'],
        $card['to'],
        $card['from'],
        $card['message1'],
        $card['spotify_url'],
        $card['photos'][0] ?? null,
        $card['photos'][1] ?? null,
        $card['photos'][2] ?? null,
        'draft',
        'unpaid'
    );

    $stmt->execute();
}

/* =====================================================
   UTILITIES
   ===================================================== */
function generate_public_id($len = 8){
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $out = '';
    for ($i = 0; $i < $len; $i++) {
        $out .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $out;
}

/* =====================================================
   AUTH HELPERS
   ===================================================== */
function require_admin(){
    if (empty($_SESSION['is_admin'])) {
        header("Location: login.php");
        exit;
    }
}

function require_user(){
    if (empty($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}
