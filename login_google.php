<?php
require_once 'config.php';

/* =====================================================
   INIT GOOGLE CLIENT (pakai config)
   ===================================================== */
$googleClient = new Google_Client();
$googleClient->setClientId($GOOGLE_CLIENT_ID);
$googleClient->setClientSecret($GOOGLE_CLIENT_SECRET);
$googleClient->setRedirectUri($GOOGLE_REDIRECT_URI);

$googleClient->setPrompt('select_account');
$googleClient->addScope('email');
$googleClient->addScope('profile');

/* =====================================================
   REDIRECT TO GOOGLE AUTH
   ===================================================== */
header('Location: ' . $googleClient->createAuthUrl());
exit;
