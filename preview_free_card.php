<?php
session_start();
require 'config.php';

$id = $_GET['id'] ?? '';
if (!$id) die("ID kartu tidak ditemukan.");

$folder = "cards/$id";
$dataFile = "$folder/data.json";

if (!file_exists($dataFile)) die("Data kartu tidak ditemukan.");

$card = json_decode(file_get_contents($dataFile), true);

$to      = $card['to'];
$from    = $card['from'];
$message = $card['message'];
$spotify = $card['spotify'];
$imgs    = $card['images'];
$type    = $card['type'];

// Spotify ID extractor
function getSpotifyID($url) {
    if (strpos($url, "track/") !== false) {
        $id = explode("track/", $url)[1];
        return explode("?", $id)[0];
    }
    return "";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Preview Greeting Card - LoveCrafted</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<!-- Download as image -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
body {
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg,#fde2f3,#ffd7ec,#f8c9d8);
    min-height:100vh;
    display:flex;
    justify-content:center;
    padding:40px 0;
}

/* CARD */
.card-preview {
    width: 420px;
    background:#fff;
    padding:24px;
    border-radius:24px;
    box-shadow:0 20px 40px rgba(236,72,153,.28);
    animation: fadeUp .7s ease;
    position:relative;
}

.watermark {
    position:absolute;
    bottom:10px;
    right:10px;
    opacity:.3;
    font-size:12px;
    font-weight:600;
    color:#ec4899;
}

.to-text {
    font-size:20px;
    font-weight:600;
    color:#be185d;
}

.msg-box {
    background:#fff1fa;
    padding:16px;
    border-radius:16px;
    margin-top:10px;
    font-size:14px;
    color:#6b7280;
}

/* IMAGES */
.images {
    margin-top:16px;
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:10px;
}
.images img {
    width:120px;
    height:120px;
    border-radius:16px;
    object-fit:cover;
    border:2px solid #fbcfe8;
}

/* SPOTIFY */
.spotify-box {
    margin-top:16px;
    border-radius:12px;
    overflow:hidden;
}

/* ACTION BUTTONS */
.action {
    text-align:center;
    margin-top:20px;
}

.btn-download {
    background:#ec4899;
    padding:12px 18px;
    border-radius:14px;
    color:#fff;
    text-decoration:none;
    font-weight:600;
    transition:.2s;
    cursor:pointer;
    border:none;
}

.btn-download:hover {
    background:#be185d;
    transform:translateY(-2px);
}

.back {
    display:block;
    text-align:center;
    font-size:13px;
    margin-top:12px;
    color:#6b7280;
    text-decoration:none;
}

/* ANIMATION */
@keyframes fadeUp {
    from {opacity:0; transform:translateY(28px);}
    to   {opacity:1; transform:translateY(0);}
}
</style>
</head>

<body>

<div class="card-preview" id="cardArea">

    <div class="to-text">Untuk: <?= htmlspecialchars($to) ?> 💌</div>

    <div class="msg-box"><?= nl2br(htmlspecialchars($message)) ?></div>

    <?php if (!empty($imgs)): ?>
    <div class="images">
        <?php foreach ($imgs as $img): ?>
            <img src="cards/<?= $id ?>/<?= $img ?>">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($spotify)): ?>
    <div class="spotify-box">
        <iframe style="border-radius:12px"
            src="https://open.spotify.com/embed/track/<?= getSpotifyID($spotify) ?>?utm_source=generator"
            width="100%" height="80" frameBorder="0" allow="autoplay; clipboard-write; encrypted-media"></iframe>
    </div>
    <?php endif; ?>

    <div class="action">
        <button onclick="downloadCard()" class="btn-download">Download Card 📥</button>

        <a href="user_home.php" class="back">← Kembali ke Dashboard</a>
    </div>

    <div class="watermark">LoveCrafted FREE ✨</div>

</div>

<!-- SCRIPT DOWNLOAD -->
<script>
function downloadCard() {
    const card = document.getElementById('cardArea');

    html2canvas(card, {
        scale: 3,
        backgroundColor: null
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = "LoveCrafted_Card_<?= $id ?>.png";
        link.href = canvas.toDataURL("image/png");
        link.click();
    });
}
</script>

</body>
</html>
