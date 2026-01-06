<?php
session_start();
require 'vendor/autoload.php';
require 'config.php'; // koneksi DB

$client = new Google_Client();
$client->setClientId($GOOGLE_CLIENT_ID);
$client->setClientSecret($GOOGLE_CLIENT_SECRET);
$client->setRedirectUri($GOOGLE_REDIRECT_URI);

if (!isset($_GET['code'])) {
    exit('Login gagal');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);

$googleService = new Google_Service_Oauth2($client);
$userInfo = $googleService->userinfo->get();

$email = $userInfo->email;
$name = $userInfo->name;

// cek user di DB
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // user baru → insert
    $stmt = $conn->prepare(
        "INSERT INTO users (fullname, email, oauth_provider) VALUES (?, ?, 'google')"
    );
    $stmt->execute([$name, $email]);
    $user_id = $conn->lastInsertId();
} else {
    $user_id = $user['id'];
}

// === SET SESSION (FORMAT SESUAI user_home.php) ===
$_SESSION['user'] = [
    'id' => $user_id,
    'fullname' => $name,
    'email' => $email,
    'login_type' => 'google'
];


header("Location: user_home.php");
exit;
