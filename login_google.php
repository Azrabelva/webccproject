<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('615293596362-95gc7m4duel9rbujis8mk5jngjalbucf.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost:8000/login_google_callback.php');
$client->setPrompt('select_account');
$client->addScope("email");
$client->addScope("profile");

header('Location: ' . $client->createAuthUrl());
exit;
