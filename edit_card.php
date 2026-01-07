<?php
require_once 'config.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$userId = $_SESSION['user']['id'];

/* ================= GET CARD ================= */
$id = $_GET['id'] ?? null;
if (!$id) die("Kartu tidak ditemukan");

$stmt = $conn->prepare("SELECT * FROM cards WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$card = $stmt->get_result()->fetch_assoc();

if (!$card) die("Kartu tidak ditemukan");

/* ================= SECURITY ================= */
if ($card['user_id'] != $userId) die("Akses ditolak");

/* ================= CEK PREMIUM USER ================= */
$stmt = $conn->prepare("SELECT premium FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$isPremium = !empty($res['premium']);

/* ================= MODE AWAL DARI DB ================= */
$cardMode = $card['card_mode'] ?? 'letter';

/* ================= HANDLE UPDATE ================= */
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $to      = trim($_POST['to'] ?? '');
  $from    = trim($_POST['from'] ?? '');
  $message = trim($_POST['message'] ?? '');
  $mode    = $_POST['card_mode'] ?? null;
  $music   = trim($_POST['spotify_link'] ?? '');

  if (!$to || !$from || !$message) {
    $error = "Semua field wajib diisi";
  }

  /* ===== MODE DEFAULT ===== */
  if (!$mode) {
    $mode = $cardMode; // kalau user tidak pilih, pakai mode lama
  }

  /* ================= VALIDASI MODE ================= */
  if ($mode === 'greeting' && !$isPremium) {
    die("Akses ditolak: Greeting Card hanya untuk Premium.");
  }

  /* ================= EXTRA TEXT ================= */
  $extraText  = $card['extra_text'];
  $extraTitle = $card['extra_title'];

  if ($isPremium && $mode === 'greeting') {
    $extraText  = $_POST['extra_text']  ?? $extraText;
    $extraTitle = $_POST['extra_title'] ?? $extraTitle;
  }

  /* ================= MUSIC ================= */
  if (!($isPremium && $mode === 'greeting')) {
    $music = null;
  }

  /* ================= PAYMENT STATUS ================= */
  // RULE: premium user → selalu free
  // free user → hanya boleh letter
  $newPaymentStatus = 'free';

  /* ================= UPDATE DB ================= */
  if (!$error) {

    $stmt = $conn->prepare("
      UPDATE cards
      SET 
        receiver_name=?,
        sender_name=?,
        main_message=?,
        extra_text=?,
        extra_title=?,
        spotify_link=?,
        card_mode=?,
        payment_status=?
      WHERE id=?
    ");

    $stmt->bind_param(
      "ssssssssi",
      $to,
      $from,
      $message,
      $extraText,
      $extraTitle,
      $music,
      $mode,
      $newPaymentStatus,
      $id
    );

    if(!$stmt->execute()){
      die("Update gagal: ".$stmt->error);
    }

    header("Location: view.php?id=" . $id);
    exit;
  }
}

/* ================= TEMPLATE NAME ================= */
$themeName = [
  'birthday'    => 'Happy Birthday',
  'anniversary' => 'Happy Anniversary',
  'mother'      => 'Mother’s Day',
  'father'      => 'Father’s Day',
  'wedding'     => 'Wedding',
  'eid'         => 'Eid Mubarak',
  'confess'     => 'Confession Love'
][$card['template_type']] ?? 'Custom Card';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Greeting Card - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box}
body{
  margin:0;font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#fde2f3,#ffd6e5,#f8c9d8);
}
.page-wrap{min-height:100vh;display:flex;justify-content:center;align-items:center;padding:40px 16px}
.form-card{
  width:100%;max-width:460px;background:#fff;padding:36px 34px;
  border-radius:32px;box-shadow:0 22px 55px rgba(236,72,153,.25)
}
.title{text-align:center;font-size:26px;font-weight:800;color:#be185d}
.subtitle{text-align:center;font-size:13px;color:#6b7280;margin:6px 0 18px}
label{font-size:14px;font-weight:600;color:#be185d;margin-bottom:6px;display:block}
input,textarea{
  width:100%;padding:14px;border-radius:16px;border:1px solid #f3bfd8;
  font-size:14px;margin-bottom:18px
}
textarea{min-height:110px}
.btn-submit{
  width:100%;padding:15px;border:none;border-radius:20px;
  background:linear-gradient(135deg,#ec4899,#d92672);
  color:#fff;font-size:16px;font-weight:700;cursor:pointer
}
.back-link{text-align:center;display:block;margin-top:16px;font-size:13px;color:#6b7280;text-decoration:none}
.error{background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:14px;margin-bottom:18px}

/* ===== RADIO MODE (FONT KECIL) ===== */
.mode-box{margin-top:24px;}
.mode-box .title2{font-size:13px;font-weight:700;margin-bottom:8px;color:#be185d}
.mode-options{display:flex;gap:12px;}
.mode-item{
  flex:1;
  padding:10px 12px;
  border-radius:14px;
  background:#fce7f3;
  display:flex;
  align-items:center;
  gap:8px;
  cursor:pointer;
  font-size:12px;
  font-weight:600;
  color:#be185d;
}
.mode-item.premium{
  background:#fff7cc;
  color:#7c2d12;
}
.mode-info{
  margin-top:8px;
  font-size:12px;
  color:#6b7280;
}
</style>
</head>

<body>
<div class="page-wrap">
<div class="form-card">

<div class="title">Edit Greeting Card</div>
<div class="subtitle">Template: <b><?= htmlspecialchars($themeName) ?></b></div>

<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">

<label>Untuk Siapa?</label>
<input type="text" name="to" value="<?= htmlspecialchars($card['receiver_name']) ?>" required>

<label>Dari Siapa?</label>
<input type="text" name="from" value="<?= htmlspecialchars($card['sender_name']) ?>" required>

<label>Pesan Utama</label>
<textarea name="message" required><?= htmlspecialchars($card['main_message']) ?></textarea>

<label>Link Musik (Premium)</label>
<input type="url" name="spotify_link"
value="<?= htmlspecialchars($card['spotify_link'] ?? '') ?>"
<?= !($isPremium && $cardMode==='greeting') ? 'disabled' : '' ?>>

<?php if (!($isPremium && $cardMode==='greeting')): ?>
<small style="color:#be185d;display:block;margin-top:-10px;margin-bottom:18px;font-size:12px">
  Fitur musik hanya untuk Premium
</small>
<?php endif; ?>

<!-- ================= MODE (PALING BAWAH) ================= -->
<div class="mode-box">

  <div class="title2">Pilih Jenis Kartu</div>

  <div class="mode-options">

    <!-- LETTER -->
    <label class="mode-item">
      <input type="radio" name="card_mode" value="letter"
        <?= $cardMode==='letter' ? 'checked' : '' ?>>
      Letter Only
    </label>

    <!-- GREETING -->
    <?php if ($isPremium): ?>
    <label class="mode-item premium">
      <input type="radio" name="card_mode" value="greeting"
        <?= $cardMode==='greeting' ? 'checked' : '' ?>>
      Greeting Card
    </label>
    <?php endif; ?>

  </div>

  <?php if (!$isPremium): ?>
    <small style="font-size:12px;color:#be185d;display:block;margin-top:6px">
      Mode Greeting hanya untuk Premium
    </small>
  <?php endif; ?>

  <div id="modeInfo" class="mode-info"></div>
</div>

<button class="btn-submit">Simpan Perubahan</button>
</form>

<a href="view.php?id=<?= $card['id'] ?>" class="back-link">
  Kembali ke Preview
</a>

</div>
</div>

<script>
const radios = document.querySelectorAll('input[name="card_mode"]');
const modeInfo = document.getElementById('modeInfo');
const musicInput = document.querySelector('input[name="spotify_link"]');

function updateModeInfo(){
  const checked = document.querySelector('input[name="card_mode"]:checked');

  if(!checked){
    modeInfo.innerHTML = "Pilih jenis kartu terlebih dahulu.";
    return;
  }

  if(checked.value === 'letter'){
    modeInfo.innerHTML = "Letter Only → tanpa animasi (FREE)";
    if(musicInput) musicInput.disabled = true;
  }else{
    modeInfo.innerHTML = "Greeting Card → animasi & musik (PREMIUM)";
    if(musicInput) musicInput.disabled = false;
  }
}

updateModeInfo();
radios.forEach(r => r.addEventListener('change', updateModeInfo));
</script>

</body>
</html>
