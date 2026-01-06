<?php
require 'config.php';

$client = new Google_Client();
$client->setClientId($GOOGLE_CLIENT_ID);
$client->setClientSecret($GOOGLE_CLIENT_SECRET);
$client->setRedirectUri($GOOGLE_REDIRECT_URI);
$client->setPrompt('select_account');
$client->addScope("email");
$client->addScope("profile");

header('Location: ' . $client->createAuthUrl());
exit;
