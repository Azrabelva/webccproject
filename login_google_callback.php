<?php
session_start();
require 'vendor/autoload.php';
require 'config.php'; // koneksi DB

$client = new Google_Client();
$client->setClientId('615293596362-95gc7m4duel9rbujis8mk5jngjalbucf.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost:8000/login_google_callback.php');

if (!isset($_GET['code'])) {
    exit('Login gagal');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);

$googleService = new Google_Service_Oauth2($client);
$userInfo = $googleService->userinfo->get();

$email = $userInfo->email;
$name  = $userInfo->name;

// cek user di DB
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    // user baru → insert
    $stmt = $conn->prepare(
        "INSERT INTO users (fullname, email, oauth_provider) VALUES (?, ?, 'google')"
    );
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $user_id = $conn->insert_id;
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
