<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';          // sudah load BASE_URL, DB (PDO), dll
require_once __DIR__ . '/vendor/autoload.php';

/* =====================================================
   INIT GOOGLE CLIENT (pakai config)
   ===================================================== */
$client = new Google_Client();
$client->setClientId($GOOGLE_CLIENT_ID);
$client->setClientSecret($GOOGLE_CLIENT_SECRET);
$client->setRedirectUri($GOOGLE_REDIRECT_URI);

/* =====================================================
   VALIDASI CODE
   ===================================================== */
if (!isset($_GET['code'])) {
    die('Login Google gagal: kode tidak ditemukan');
}

/* =====================================================
   EXCHANGE CODE → TOKEN
   ===================================================== */
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die('Login Google gagal: ' . $token['error_description']);
}

$client->setAccessToken($token);

/* =====================================================
   GET USER INFO
   ===================================================== */
$googleService = new Google_Service_Oauth2($client);
$userInfo = $googleService->userinfo->get();

$email = $userInfo->email ?? null;
$name  = $userInfo->name  ?? null;

if (!$email) {
    die('Login Google gagal: email tidak ditemukan');
}

/* =====================================================
   CEK USER DI DB (PDO - SQLite)
   ===================================================== */
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

/* =====================================================
   INSERT JIKA USER BARU
   ===================================================== */
if (!$user) {
    $stmt = $conn->prepare("
        INSERT INTO users (fullname, email, oauth_provider)
        VALUES (:fullname, :email, 'google')
    ");
    $stmt->execute([
        ':fullname' => $name,
        ':email'    => $email
    ]);

    $user_id = $conn->lastInsertId();
} else {
    $user_id = $user['id'];
    $name    = $user['fullname']; // pakai nama dari DB kalau sudah ada
}

/* =====================================================
   SET SESSION (sesuai user_home.php)
   ===================================================== */
$_SESSION['user'] = [
    'id'         => $user_id,
    'fullname'   => $name,
    'email'      => $email,
    'login_type' => 'google'
];

/* =====================================================
   REDIRECT
   ===================================================== */
header('Location: user_home.php');
exit;
