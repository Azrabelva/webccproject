<?php
require 'config.php';
require_admin();
require_once __DIR__ . '/templates_db.php';

$id = $_GET['id'] ?? '';
if ($id === '') die('ID tidak valid');

templates_delete($id);
header('Location: admin_templates.php?deleted=1');
exit;
