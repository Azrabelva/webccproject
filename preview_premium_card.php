<?php
session_start();
require_once 'config.php';

/* =========================================================
   PREVIEW PREMIUM CARD (FULL FILE FINAL)
   - Support POST (from create form) + GET?id (from saved card)
   - Smooth step-by-step transitions (no tabrakan)
   - Spotify autoplay: starts after FIRST user interaction (tap/swipe)
   - Grain/paper texture
   - Back button clickable
   - Save button: if not paid -> show pay modal (must payment first)
   ========================================================= */

/* ---------- Helpers ---------- */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function getSpotifyEmbed($url){
    $url = trim((string)$url);
    if ($url === '') return '';
    if (preg_match('~open\.spotify\.com/(track|album|playlist)/([A-Za-z0-9]+)~', $url, $m)) {
        return "https://open.spotify.com/embed/{$m[1]}/{$m[2]}?utm_source=generator";
    }
    return $url;
}

/* ---------- Theme labels ---------- */
$labels = [
    'birthday'   => ['Happy Birthday – Premium', '#f472b6', '#b91c1c'],
    'anniversary'=> ['Happy Anniversary – Premium', '#eab308', '#92400e'],
    'mother'     => ['Happy Mother’s Day – Premium', '#ec4899', '#9d174d'],
    'father'     => ['Happy Father’s Day – Premium', '#3b82f6', '#1d4ed8'],
    'eid'        => ['Eid Mubarak – Premium', '#22c55e', '#166534'],
    'wedding'    => ['Wedding Celebration – Premium', '#c084fc', '#6b21a8'],
    'confess'    => ['Love Confession – Premium', '#fb7185', '#b91c1c']
];

/* ---------- Load card flow ----------
   A) If GET?id exists => load_card($id)
   B) Else POST => build data + upload photos => create temp card record (unpaid) so can go to payment
------------------------------------*/
$id = $_GET['id'] ?? null;
$card = null;

if ($id) {
    $card = load_card($id);
    if (!$card) die("Kartu tidak ditemukan");
} else {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: user_home.php');
        exit;
    }

    $type    = $_POST['type']     ?? 'birthday';
    $to      = $_POST['to']       ?? '';
    $from    = $_POST['from']     ?? '';
    $msg1    = $_POST['message1'] ?? '';
    $msg2    = $_POST['message2'] ?? '';
    $msg3    = $_POST['message3'] ?? '';
    $spotify = $_POST['spotify']  ?? '';

    /* Upload photos (maks 3) */
    $photos = [];
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_FILES["photo$i"]['name']) && ($_FILES["photo$i"]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            if (!is_dir('uploads_premium')) mkdir('uploads_premium', 0777, true);

            $ext = pathinfo($_FILES["photo$i"]['name'], PATHINFO_EXTENSION);
            $safeExt = preg_replace('~[^a-zA-Z0-9]~', '', $ext);
            if ($safeExt === '') $safeExt = 'jpg';

            $filename = 'prem_' . time() . "_{$i}." . $safeExt;
            $dest = "uploads_premium/$filename";

            if (move_uploaded_file($_FILES["photo$i"]['tmp_name'], $dest)) {
                $photos[] = $dest;
            }
        }
    }

    /* Create a temp card record so payment.php can reference id */
    // generate_public_id() expected in config.php. If not exists, fallback:
    if (!function_exists('generate_public_id')) {
        function generate_public_id(){
            return substr(bin2hex(random_bytes(6)), 0, 12);
        }
    }

    do {
        $id = generate_public_id();
        $exist = load_card($id);
    } while ($exist);

    $card = [
        'id'             => $id,
        'type'           => $type,
        'to'             => $to,
        'from'           => $from,
        'message1'       => $msg1,
        'message2'       => $msg2,
        'message3'       => $msg3,
        'spotify_url'    => $spotify,
        'photos'         => $photos,
        'payment_status' => 'unpaid',
        'created_at'     => date('c'),
    ];

    save_card($card);
}

/* ---------- Normalize fields ---------- */
$type    = $card['type'] ?? 'birthday';
$to      = $card['to'] ?? '';
$from    = $card['from'] ?? '';
$msg1    = $card['message1'] ?? '';
$msg2    = $card['message2'] ?? '';
$msg3    = $card['message3'] ?? '';
$spotify = $card['spotify_url'] ?? '';
$photos  = $card['photos'] ?? [];

[$title, $themeColor, $panelColor] = $labels[$type] ?? $labels['birthday'];

$spotifyEmbed = getSpotifyEmbed($spotify);
$isPaid = (($card['payment_status'] ?? '') === 'paid');

/* User name for sweet talk (optional) */
$viewerName = 'Acha';
if (isset($_SESSION['user']['fullname']) && $_SESSION['user']['fullname']) {
    $viewerName = $_SESSION['user']['fullname'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview Premium Card - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root{
    --theme-main: <?= h($themeColor) ?>;
    --panel-color: <?= h($panelColor) ?>;
    --ink: #111827;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Poppins',sans-serif;
    background:
        radial-gradient(circle at 20% 20%, rgba(236,72,153,.18), transparent 48%),
        radial-gradient(circle at 80% 80%, rgba(168,85,247,.18), transparent 48%),
        linear-gradient(135deg,#ffe4f5,#fdf2f8 45%,#f5d0fe 100%);
    padding:24px;
    overflow:hidden;
    position:relative;
}

/* Subtle grain/paper texture */
body::before{
    content:"";
    position:absolute;
    inset:-60px;
    background-image:
      repeating-linear-gradient(0deg, rgba(0,0,0,.025) 0, rgba(0,0,0,.025) 1px, transparent 1px, transparent 3px),
      repeating-linear-gradient(90deg, rgba(0,0,0,.018) 0, rgba(0,0,0,.018) 1px, transparent 1px, transparent 4px);
    opacity:.25;
    pointer-events:none;
    mix-blend-mode:multiply;
    filter:blur(.2px);
}

/* Wrapper */
.stage-wrapper{
    max-width:980px;
    width:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:14px;
    position:relative;
    z-index:2;
}

/* Header text */
.preview-title{
    font-family:'Playfair Display',serif;
    font-size:26px;
    text-align:center;
    color:#7f1d1d;
}
.preview-sub{
    font-size:14px;
    text-align:center;
    color:#4b5563;
    margin-top:4px;
}

/* Panel (lebih panjang dikit daripada sebelumnya) */
.red-panel{
    width:100%;
    max-width:520px;
    aspect-ratio: 9/18; /* lebih tinggi (panjang) */
    background:var(--panel-color);
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
    box-shadow:0 34px 72px rgba(0,0,0,.35);
    overflow:hidden;
    transform:translateZ(0);
}

/* Panel depth / sparkle */
.red-panel::before{
    content:"";
    position:absolute;
    inset:0;
    background:
      radial-gradient(circle at 22% 18%, rgba(255,255,255,.18), transparent 46%),
      radial-gradient(circle at 78% 82%, rgba(255,255,255,.12), transparent 46%),
      linear-gradient(to top, rgba(0,0,0,.18), transparent 58%);
    pointer-events:none;
}

/* Small floating decor inside panel */
.panel-decor{
    position:absolute;
    inset:0;
    pointer-events:none;
    opacity:.35;
}
.panel-decor::before,
.panel-decor::after{
    content:"✨";
    position:absolute;
    font-size:26px;
    animation:panelFloat 6.5s ease-in-out infinite;
}
.panel-decor::before{ left:18px; top:18px; content:"💖"; }
.panel-decor::after { right:18px; bottom:18px; content:"🎀"; animation-delay:.4s; }

@keyframes panelFloat{
    0%{ transform:translateY(0); opacity:.7; }
    50%{ transform:translateY(-10px); opacity:1; }
    100%{ transform:translateY(0); opacity:.7; }
}

/* Top controls */
.controls{
    position:absolute;
    top:12px; left:12px; right:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    z-index:40;
    pointer-events:none;
    gap:10px;
}
.ctrl-left, .ctrl-right{
    display:flex;
    align-items:center;
    gap:8px;
    pointer-events:auto;
}
.ctrl-btn{
    border:none;
    padding:10px 12px;
    border-radius:999px;
    background:rgba(255,255,255,.18);
    color:#fff;
    font-weight:700;
    font-size:12px;
    cursor:pointer;
    backdrop-filter:blur(10px);
    transition:.25s;
    display:flex;
    align-items:center;
    gap:8px;
}
.ctrl-btn:hover{ transform:translateY(-2px); background:rgba(255,255,255,.28); }
.ctrl-btn:active{ transform:scale(.98); }

/* Progress */
.dots{ display:flex; gap:7px; pointer-events:none; align-items:center; }
.dot{
    width:7px;height:7px;border-radius:50%;
    background:rgba(255,255,255,.35);
    transition:.25s;
}
.dot.active{ background:#fff; transform:scale(1.25); }

.progressbar{
    position:absolute;
    top:52px;
    left:12px;
    right:12px;
    height:6px;
    border-radius:999px;
    background: rgba(255,255,255,.18);
    overflow:hidden;
    z-index:35;
    pointer-events:none;
}
.progressbar > span{
    display:block;
    height:100%;
    width:0%;
    border-radius:999px;
    background: rgba(255,255,255,.9);
    transition: width .45s cubic-bezier(.22,1,.36,1);
}

/* Scene */
.scene{
    position:relative;
    width:280px;
    height:420px;
    display:flex;
    align-items:flex-end;
    justify-content:center;
    perspective:1200px;
    touch-action:pan-y;
}

/* Envelope core */
.envelope{
    position:relative;
    width:260px;
    height:190px;
    transform-style:preserve-3d;
}

/* Envelope layers */
.env-back{
    position:absolute; inset:0;
    background:#f5f5f5;
    border-radius:18px 18px 10px 10px;
    box-shadow:0 18px 24px rgba(0,0,0,.35);
    z-index:1;
}
.env-flap{
    position:absolute;
    top:-68px; left:0; right:0; margin:auto;
    width:0;height:0;
    border-left:130px solid transparent;
    border-right:130px solid transparent;
    border-bottom:68px solid #f3e5f5;
    transform-origin:bottom center;
    transform:rotateX(0deg);
    transition:transform .95s cubic-bezier(.22,1,.36,1);
    z-index:6;
}
.env-front{
    position:absolute;
    left:0;right:0;bottom:0;margin:auto;
    width:100%;
    height:126px;
    background:#f3f4f6;
    border-radius:0 0 16px 16px;
    box-shadow:0 12px 20px rgba(0,0,0,.35);
    overflow:hidden;
    z-index:4;
}
.env-front::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(to top, rgba(0,0,0,.12), transparent 55%);
    pointer-events:none;
}
.env-heart{
    position:absolute;
    left:0;right:0;bottom:18px;margin:auto;
    width:22px;height:22px;border-radius:50%;
    background:var(--theme-main);
    box-shadow:0 12px 20px rgba(0,0,0,.18);
}

/* Pages */
.card-page{
    position:absolute;
    left:12px; right:12px; margin:auto;
    width:calc(100% - 24px);
    height:150px;
    background:#fdfcf8;
    border-radius:16px;
    box-shadow:0 12px 22px rgba(0,0,0,.22);
    transform-origin:bottom center;
    transform:translateY(44px) rotateX(90deg);
    opacity:0;
    padding:16px 16px;
    transition:transform .95s cubic-bezier(.22,1,.36,1), opacity .95s cubic-bezier(.22,1,.36,1);
    z-index:8;
    overflow:hidden;
    backdrop-filter: blur(8px);
}
.card-page::before{
    content:"";
    position:absolute;
    inset:0;
    background: linear-gradient(to bottom, rgba(255,255,255,.94), rgba(255,255,255,.80));
    pointer-events:none;
}
.card-page > *{ position:relative; z-index:2; }

.card-page h1{
    font-family:'Playfair Display',serif;
    font-size:18px;
    color:#7f1d1d;
    margin-bottom:6px;
    text-shadow:0 8px 18px rgba(0,0,0,.12);
}
.card-page p{
    font-size:12px;
    line-height:1.55;
    color:#374151;
}

/* Page 1 (cover) visible by default */
.page-cover{
    transform:translateY(34px) rotateX(0deg);
    opacity:1;
}

/* Page 2 (message) taller & scrollable */
.page-message{
    height:205px;
    padding:18px 16px 14px;
}
.page-message p{
    max-height:140px;
    overflow:auto;
    padding-right:8px;
    scrollbar-width:thin;
}
.page-message p::-webkit-scrollbar{ width:6px; }
.page-message p::-webkit-scrollbar-thumb{
    background: rgba(0,0,0,.18);
    border-radius: 999px;
}

/* Slide 2 decoration (like your reference) */
.page-message::after{
    content:"💌 ✨ 🎀 💖";
    position:absolute;
    top:12px;
    right:12px;
    font-size:16px;
    opacity:.35;
    transform:rotate(6deg);
    pointer-events:none;
}

/* Photos */
.photo-stack{
    position:absolute;
    width:220px;
    height:190px;
    bottom:10px;
    left:50%;
    transform:translateX(-50%) translateY(180px);
    opacity:0;
    transition:transform .95s cubic-bezier(.22,1,.36,1), opacity .95s cubic-bezier(.22,1,.36,1);
    z-index:9;
}
.photo-card{
    position:absolute;
    width:150px;height:150px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 12px 20px rgba(0,0,0,.30);
    overflow:hidden;
    display:flex;align-items:center;justify-content:center;
    border:2px solid rgba(255,255,255,.75);
}
.photo-card img{ width:100%; height:100%; object-fit:cover; display:block; }
.photo-card::after{
    content:"";
    position:absolute;
    inset:0;
    background: linear-gradient(to top, rgba(0,0,0,.18), transparent 55%);
    pointer-events:none;
}
.photo-1{ top:14px; left:12px; transform:rotate(-4deg); }
.photo-2{ top:30px; right:10px; transform:rotate(6deg); }
.photo-3{ top:54px; left:36px; transform:rotate(-9deg); }

/* Hint */
.tap-hint{
    position:absolute;
    bottom:14px; left:0; right:0;
    margin:auto;
    width:max-content;
    padding:7px 12px;
    font-size:11px;
    background:rgba(0,0,0,.38);
    color:#fff;
    border-radius:999px;
    display:flex; align-items:center; gap:7px;
    animation:hintPulse 1.4s infinite;
    z-index:30;
    backdrop-filter: blur(10px);
    border:1px solid rgba(255,255,255,.22);
}
.tap-dot{ width:8px;height:8px;border-radius:50%;background:#fbbf24; }
@keyframes hintPulse{
    0%{ transform:translateY(0); opacity:1; }
    50%{ transform:translateY(-2px); opacity:.75; }
    100%{ transform:translateY(0); opacity:1; }
}

/* Step states (NO TABRAKAN) */
.stage-open-1 .env-flap{ transform:rotateX(-140deg); }
.stage-open-1 .page-cover{ transform:translateY(-46px) rotateX(0deg); }
.stage-open-1 .tap-hint{ opacity:0; pointer-events:none; }

.stage-open-2 .page-cover{ transform:translateY(-160px) rotateX(-12deg); opacity:1; }
.stage-open-2 .page-message{ transform:translateY(-12px) rotateX(0deg); opacity:1; }

.stage-open-3 .page-message{ transform:translateY(-210px) rotateX(-16deg); opacity:1; }
.stage-open-3 .photo-stack{ transform:translateX(-50%) translateY(0); opacity:1; }

.stage-open-1 .envelope,
.stage-open-2 .envelope,
.stage-open-3 .envelope{
    animation: softBounce .55s cubic-bezier(.2,1.2,.3,1);
}
@keyframes softBounce{
    0%{ transform: translateY(0); }
    60%{ transform: translateY(-6px); }
    100%{ transform: translateY(0); }
}

/* Photo pop */
.stage-open-3 .photo-card{ animation: photoPop .65s cubic-bezier(.2,1.2,.3,1) both; }
.stage-open-3 .photo-2{ animation-delay:.08s; }
.stage-open-3 .photo-3{ animation-delay:.16s; }
@keyframes photoPop{
    from{ transform: translateY(12px) scale(.92) rotate(0deg); opacity:0; }
    to{ transform: translateY(0) scale(1) rotate(0deg); opacity:1; }
}

/* Music section: hidden until first interaction (so autoplay works via gesture) */
.music-section{
    width:100%;
    max-width:520px;
    background:#fff;
    border-radius:18px;
    box-shadow:0 18px 28px rgba(0,0,0,.16);
    padding:14px 14px 12px;
    animation:fadeMusic .8s ease;
    border:1px solid rgba(236,72,153,.18);
    display:none;
}
.music-label{ font-size:13px; margin-bottom:6px; color:#4b5563; }
@keyframes fadeMusic{ from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:none} }
.music-section iframe{
    width:100%;
    height:152px;
    border:0;
    border-radius:14px;
}

/* Modal */
.modal{
    position:fixed;
    inset:0;
    display:none;
    justify-content:center;
    align-items:center;
    background:rgba(15,15,25,.55);
    backdrop-filter:blur(10px);
    z-index:9999;
    padding:18px;
}
.modal-card{
    width:420px;
    max-width:100%;
    background:rgba(255,255,255,.95);
    border-radius:26px;
    padding:22px;
    position:relative;
    box-shadow:0 30px 70px rgba(236,72,153,.35);
}
.modal-card h3{
    font-size:18px;
    color:#be185d;
    margin-bottom:6px;
}
.modal-card p{
    font-size:13px;
    color:#4b5563;
    line-height:1.55;
}
.modal-actions{
    display:flex;
    gap:10px;
    margin-top:14px;
}
.modal-actions a, .modal-actions button{
    flex:1;
    padding:12px 14px;
    border-radius:999px;
    border:none;
    cursor:pointer;
    font-weight:800;
    font-size:13px;
}
.btn-primary{
    background:linear-gradient(135deg,#ec4899,#a855f7);
    color:#fff;
    text-decoration:none;
    text-align:center;
}
.btn-ghost{
    background:rgba(0,0,0,.06);
    color:#111827;
}
.modal-close{
    position:absolute;
    top:10px;
    right:10px;
    width:40px;
    height:40px;
    border-radius:50%;
    border:none;
    cursor:pointer;
    background:rgba(236,72,153,.12);
    color:#ec4899;
    font-size:22px;
    font-weight:900;
}

/* Mobile */
@media(max-width:640px){
    body{ padding:14px; }
    .red-panel{ max-width:380px; aspect-ratio: 9/18; }
    .scene{ width:250px; height:390px; }
    .envelope{ width:240px; height:180px; }
    .music-section iframe{ height:160px; }
}
</style>
</head>

<body>

<div class="stage-wrapper">

    <div>
        <div class="preview-title">Premium Greeting Card</div>
        <div class="preview-sub">Tap / Swipe kiri-kanan untuk lanjut (smooth, gak tabrakan) 💌</div>
    </div>

    <div class="red-panel" id="panel">
        <div class="panel-decor"></div>

        <div class="controls">
            <div class="ctrl-left">
                <button class="ctrl-btn" id="backBtn" type="button">← Back</button>
            </div>

            <div class="dots" aria-hidden="true">
                <span class="dot active" id="dot1"></span>
                <span class="dot" id="dot2"></span>
                <span class="dot" id="dot3"></span>
            </div>

            <div class="ctrl-right">
                <button class="ctrl-btn" id="skipBtn" type="button">Skip ✨</button>
                <button class="ctrl-btn" id="saveBtn" type="button">Save 💾</button>
            </div>
        </div>

        <div class="progressbar"><span id="pbFill"></span></div>

        <div class="scene" id="scene">

            <!-- optional SFX (place file: assets/sfx/open.mp3) -->
            <audio id="openSfx" preload="auto">
                <source src="assets/sfx/open.mp3" type="audio/mpeg">
            </audio>

            <div class="envelope">
                <div class="env-back"></div>
                <div class="env-flap"></div>

                <!-- PAGE 1 -->
                <div class="card-page page-cover">
                    <h1><?= h($title) ?></h1>
                    <p style="margin-top:6px;">Tap to open 💖</p>
                    <p style="margin-top:10px;font-size:11px;color:#6b7280;">
                        Untuk: <b><?= h($to) ?></b> • Dari: <b><?= h($from) ?></b>
                    </p>
                </div>

                <!-- PAGE 2 -->
                <div class="card-page page-message">
                    <h1>Make a Wish ✨</h1>
                    <p style="font-size:11.5px;margin-top:6px;text-align:left;">
                        <?= nl2br(h($msg1 . "\n\n" . $msg2 . "\n\n" . $msg3)) ?>
                    </p>
                </div>

                <!-- PHOTOS -->
                <?php if (!empty($photos)): ?>
                <div class="photo-stack">
                    <?php if (!empty($photos[0])): ?>
                        <div class="photo-card photo-1"><img src="<?= h($photos[0]) ?>" alt="photo 1"></div>
                    <?php endif; ?>
                    <?php if (!empty($photos[1])): ?>
                        <div class="photo-card photo-2"><img src="<?= h($photos[1]) ?>" alt="photo 2"></div>
                    <?php endif; ?>
                    <?php if (!empty($photos[2])): ?>
                        <div class="photo-card photo-3"><img src="<?= h($photos[2]) ?>" alt="photo 3"></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="env-front">
                    <div class="env-heart"></div>
                </div>
            </div>

            <div class="tap-hint" id="tapHint">
                <span class="tap-dot"></span> Tap / Swipe to continue
            </div>

        </div>
    </div>

    <!-- MUSIC (lazy-load after first interaction) -->
    <?php if ($spotifyEmbed): ?>
    <div class="music-section" id="musicSection">
        <div class="music-label">🎧 Lagu kamu (auto-play setelah kartu dibuka):</div>
        <iframe
            id="spotifyFrame"
            src=""
            data-src="<?= h($spotifyEmbed) ?>"
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
            loading="lazy"></iframe>
    </div>
    <?php endif; ?>

</div>

<!-- PAY MODAL (Save locked if not paid) -->
<div class="modal" id="payModal">
    <div class="modal-card">
        <button class="modal-close" id="closeModal" type="button">×</button>
        <h3>Save terkunci 🔒</h3>
        <p>
            Hey <b><?= h($viewerName) ?></b> 💖<br>
            Biar kartu ini bisa <b>disimpan & dibagikan</b>, kamu perlu selesaikan pembayaran Premium dulu ya.
        </p>
        <p style="margin-top:8px; font-size:12px; opacity:.85;">
            Setelah bayar, kamu bisa akses lagi kartu ini pakai link: <b>preview_premium_card.php?id=<?= h($id) ?></b>
        </p>
        <div class="modal-actions">
            <a class="btn-primary" href="payment.php?id=<?= h($id) ?>">Bayar Sekarang (QRIS)</a>
            <button class="btn-ghost" id="laterBtn" type="button">Nanti dulu</button>
        </div>
    </div>
</div>

<script>
(() => {
    const scene   = document.getElementById('scene');
    const panel   = document.getElementById('panel');
    const tapHint = document.getElementById('tapHint');
    const skipBtn = document.getElementById('skipBtn');
    const backBtn = document.getElementById('backBtn');
    const saveBtn = document.getElementById('saveBtn');

    const pbFill  = document.getElementById('pbFill');
    const dot1 = document.getElementById('dot1');
    const dot2 = document.getElementById('dot2');
    const dot3 = document.getElementById('dot3');

    const openSfx = document.getElementById('openSfx');

    const payModal  = document.getElementById('payModal');
    const closeModal= document.getElementById('closeModal');
    const laterBtn  = document.getElementById('laterBtn');

    const musicSection = document.getElementById('musicSection');
    const spotifyFrame = document.getElementById('spotifyFrame');

    let step = 0;           // 0 cover, 1 flap open, 2 message, 3 photos
    let isAnimating = false;
    let sfxPlayed = false;
    let musicLoaded = false;

    const IS_PAID = <?= $isPaid ? 'true' : 'false' ?>;

    function setDots(){
        dot1.classList.toggle('active', step === 0);
        dot2.classList.toggle('active', step === 1);
        dot3.classList.toggle('active', step >= 2);
        const pct = (step / 3) * 100;
        if (pbFill) pbFill.style.width = pct + "%";
    }

    function bouncePanel(){
        if (!panel) return;
        panel.animate(
            [{transform:'translateY(0)'},{transform:'translateY(-6px)'},{transform:'translateY(0)'}],
            {duration:420, easing:'cubic-bezier(.2,1.2,.3,1)'}
        );
    }

    function playSfxOnce(){
        if (sfxPlayed || !openSfx) return;
        sfxPlayed = true;
        openSfx.volume = 0.35;
        openSfx.play().catch(()=>{});
    }

    function loadMusicOnce(){
        if (musicLoaded || !spotifyFrame) return;
        musicLoaded = true;

        // Show section then set src (so autoplay is tied to this user gesture flow)
        if (musicSection) musicSection.style.display = "block";
        const src = spotifyFrame.getAttribute('data-src');
        if (src) spotifyFrame.src = src;
    }

    function applyStep(targetStep){
        // Apply classes sequentially; no overlap/tabrakan
        if (targetStep >= 1) scene.classList.add('stage-open-1');
        if (targetStep >= 2) scene.classList.add('stage-open-2');
        if (targetStep >= 3) scene.classList.add('stage-open-3');

        // Hide hint after first open
        if (targetStep >= 1 && tapHint) tapHint.style.opacity = 0;

        step = targetStep;
        setDots();
        bouncePanel();
    }

    function goNext(){
        if (isAnimating) return;
        if (step >= 3) return;

        isAnimating = true;

        // This is a user gesture path → allow SFX + Spotify
        playSfxOnce();

        // Step by step timing (biar halus)
        const nextStep = step + 1;

        // music starts when envelope opened (step 1)
        if (nextStep >= 1) loadMusicOnce();

        applyStep(nextStep);

        // unlock after animation settles
        setTimeout(() => { isAnimating = false; }, 820);
    }

    function goSkip(){
        if (isAnimating) return;
        isAnimating = true;

        playSfxOnce();
        loadMusicOnce(); // skip => still open -> play music

        // chain steps with small gaps (aesthetic)
        const chain = [1,2,3].filter(s => s > step);
        let i = 0;
        const run = () => {
            if (i >= chain.length) {
                setTimeout(()=>{ isAnimating = false; }, 900);
                return;
            }
            applyStep(chain[i]);
            i++;
            setTimeout(run, 240);
        };
        run();
    }

    // Tap/click
    scene.addEventListener('click', () => {
        if (isAnimating) return;
        goNext();
    });

    // Swipe (mobile)
    let sx=0, sy=0, st=0, tracking=false;
    scene.addEventListener('touchstart', (e)=>{
        if (!e.touches || !e.touches[0]) return;
        tracking = true;
        sx = e.touches[0].clientX;
        sy = e.touches[0].clientY;
        st = Date.now();
    }, {passive:true});

    scene.addEventListener('touchend', (e)=>{
        if (!tracking) return;
        tracking = false;
        const t = e.changedTouches && e.changedTouches[0];
        if (!t) return;

        const dx = t.clientX - sx;
        const dy = t.clientY - sy;
        const dt = Date.now() - st;

        const strong = Math.abs(dx) > 55 && Math.abs(dx) > Math.abs(dy);
        const quick  = dt < 600;

        if (strong && quick && !isAnimating) goNext();
    }, {passive:true});

    // Buttons
    skipBtn.addEventListener('click', (e)=>{
        e.stopPropagation();
        goSkip();
    });

    backBtn.addEventListener('click', (e)=>{
        e.stopPropagation();
        // fallback: user_home
        if (window.history.length > 1) window.history.back();
        else window.location.href = "user_home.php";
    });

    saveBtn.addEventListener('click', (e)=>{
        e.stopPropagation();
        if (IS_PAID) {
            // Paid → allow "save/share" by showing link copy prompt
            const url = window.location.origin + window.location.pathname + "?id=<?= h($id) ?>";
            // Try copy
            navigator.clipboard?.writeText(url).catch(()=>{});
            alert("✅ Link kartu berhasil disalin!\n\nShare / simpan:\n" + url);
        } else {
            // Not paid → show modal pay
            if (payModal) payModal.style.display = "flex";
        }
    });

    function closePayModal(){
        if (payModal) payModal.style.display = "none";
    }
    closeModal?.addEventListener('click', closePayModal);
    laterBtn?.addEventListener('click', closePayModal);
    payModal?.addEventListener('click', (e)=>{
        if (e.target === payModal) closePayModal();
    });

    // init
    setDots();
})();
</script>

</body>
</html>
