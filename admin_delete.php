<?php require 'config.php';require_admin();
$id=$_GET['id']??null;if(!$id)die('ID tidak diberikan');$card=load_card($id);if(!$card)die('Kartu tidak ditemukan');
if($_SERVER['REQUEST_METHOD']==='POST'){if(($_POST['confirm']??'')==='yes')delete_card($id);header('Location: admin_list.php');exit;}
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><title>Hapus Kartu - LoveCrafted</title>
<link rel="stylesheet" href="style.css"></head>
<body class="page-bg"><main class="lc-container"><div class="card card-narrow">
<h2>Hapus Kartu?</h2>
<p>ID: <b><?=htmlspecialchars($card['id'])?></b><br>Untuk: <b><?=htmlspecialchars($card['to'])?></b></p>
<form method="post" class="form-inline">
<button class="btn-danger" name="confirm" value="yes">Ya, hapus</button>
<a class="btn-secondary" href="admin_list.php">Batal</a>
</form>
</div></main></body></html>
