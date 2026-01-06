<?php
require_once 'config.php';
// WAJIB, supaya koneksi DB + session konsisten

/* ================= AUTH ================= */
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$userSession = $_SESSION['user'];
$userId = $userSession['id'];

/* ================= GET PREMIUM STATUS FROM DB ================= */
$stmt = $conn->prepare("SELECT premium FROM users WHERE id = ?");
$stmt->execute([$userId]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

$isPremium = !empty($res['premium']);

/* ================= GET TYPE ================= */
$type = $_GET['type'] ?? null;
if (!$type) {
  die("Template tidak ditemukan.");
}

/* ================= PREMIUM LOCK ================= */
$premiumTemplates = ['eid', 'wedding', 'confess'];

if (in_array($type, $premiumTemplates) && !$isPremium) {
  header("Location: dashboard.php?error=premium_required");
  exit;
}

/* ================= TEMPLATE NAME ================= */
$themeName = [
  'birthday' => 'Happy Birthday',
  'anniversary' => 'Happy Anniversary',
  'mother' => 'Mother’s Day',
  'father' => 'Father’s Day',
  'wedding' => 'Wedding',
  'eid' => 'Eid Mubarak',
  'confess' => 'Confession Love'
][$type] ?? 'Custom Card';


?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Buat Greeting Card - LoveCrafted</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fde2f3, #ffd6e5, #f8c9d8);
    }

    .page-wrap {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 16px;
    }

    .form-card {
      width: 100%;
      max-width: 460px;
      background: #fff;
      padding: 36px 34px;
      border-radius: 32px;
      box-shadow: 0 22px 55px rgba(236, 72, 153, .25);
      animation: fadeUp .6s ease;
    }

    .title {
      text-align: center;
      font-size: 26px;
      font-weight: 800;
      color: #be185d;
    }

    .subtitle {
      text-align: center;
      font-size: 13px;
      color: #6b7280;
      margin: 6px 0 28px;
    }

    label {
      font-size: 14px;
      font-weight: 600;
      color: #be185d;
      margin-bottom: 6px;
      display: block;
    }

    input,
    textarea {
      width: 100%;
      padding: 14px;
      border-radius: 16px;
      border: 1px solid #f3bfd8;
      font-size: 14px;
      margin-bottom: 18px;
    }

    input:focus,
    textarea:focus {
      border-color: #ec4899;
      box-shadow: 0 0 8px rgba(236, 72, 153, .25);
      outline: none;
    }

    textarea {
      min-height: 110px
    }

    .preview-box {
      display: flex;
      gap: 10px;
      margin: -6px 0 18px;
    }

    .preview-box img {
      width: 72px;
      height: 72px;
      border-radius: 14px;
      object-fit: cover;
      border: 2px solid #fbcfe8;
    }

    /* ===== TAMBAH TEKS ===== */
    .extra-text-box {
      display: none;
      animation: fadeUp .4s ease;
    }

    /* ===== BUTTON ===== */
    .btn-submit {
      width: 100%;
      padding: 15px;
      border: none;
      border-radius: 20px;
      background: linear-gradient(135deg, #ec4899, #d92672);
      color: #fff;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
    }

    .btn-submit:hover {
      background: #be185d
    }

    .btn-add-text {
      width: 100%;
      padding: 13px;
      border-radius: 18px;
      background: #fce7f3;
      color: #be185d;
      border: 2px dashed #ec4899;
      font-weight: 700;
      cursor: pointer;
      margin-bottom: 18px;
    }

    .btn-add-text:hover {
      background: #fbcfe8
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 16px;
      font-size: 13px;
      color: #6b7280;
      text-decoration: none;
    }

    /* ===== MODAL ===== */
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .45);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    .modal.active {
      display: flex
    }

    .modal-box {
      background: #fff;
      padding: 26px;
      border-radius: 26px;
      width: 340px;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
    }

    .modal-box h3 {
      color: #be185d;
      margin-bottom: 8px;
    }

    .modal-box p {
      font-size: 14px;
      color: #6b7280;
    }

    .btn-upgrade {
      display: inline-block;
      margin-top: 16px;
      padding: 12px 22px;
      border-radius: 999px;
      background: #8b5cf6;
      color: #fff;
      font-weight: 700;
      text-decoration: none;
    }

    .btn-close {
      display: block;
      margin-top: 14px;
      font-size: 13px;
      color: #6b7280;
      cursor: pointer;
    }

    .label-premium {
      font-weight: 700;
      color: #ec4899;
      margin-top: 18px;
      display: block;
    }

    .premium-select {
      width: 100%;
      padding: 14px 16px;
      border-radius: 18px;
      border: 2px solid #f9a8d4;
      background: #fff;
      font-size: 14px;
      margin-top: 8px;
    }

    /* ===== OVERLAY ===== */
    .premium-overlay {
      position: fixed;
      inset: 0;
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;

      background:
        radial-gradient(circle at center,
          rgba(255, 200, 220, .35),
          rgba(0, 0, 0, .45));

      backdrop-filter: blur(4px);
    }

    .premium-overlay.active {
      display: flex;
    }

    /* ===== MODAL CARD ===== */
    .premium-modal-card {
      width: 360px;
      background: #fff;
      border-radius: 32px;
      padding: 34px 28px;
      text-align: center;

      box-shadow: 0 25px 70px rgba(0, 0, 0, .28);
      animation: popIn .35s ease;
    }

    @keyframes popIn {
      from {
        transform: scale(.92);
        opacity: 0
      }

      to {
        transform: scale(1);
        opacity: 1
      }
    }

    .premium-title {
      font-size: 24px;
      font-weight: 900;
      color: #be185d;
      margin-bottom: 16px;
    }

    .premium-price {
      background: #fde2f3;
      color: #ec4899;
      font-weight: 900;
      font-size: 20px;
      padding: 14px;
      border-radius: 999px;
      margin-bottom: 20px;
    }

    .premium-benefit {
      list-style: none;
      padding: 0;
      margin: 0 0 26px;
      text-align: left;
      font-size: 14px;
      line-height: 1.8;
    }

    .premium-benefit li {
      margin-bottom: 6px;
    }

    .premium-btn {
      width: 100%;
      padding: 15px;
      border: none;
      border-radius: 999px;
      background: linear-gradient(135deg, #ec4899, #d92672);
      color: #fff;
      font-size: 16px;
      font-weight: 800;
      cursor: pointer;
    }

    .premium-btn:hover {
      opacity: .9;
    }

    .premium-cancel {
      display: block;
      margin-top: 14px;
      font-size: 13px;
      color: #6b7280;
      cursor: pointer;
    }



    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(20px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }
  </style>

  <script>
    function previewImages(e) {
      const box = document.getElementById('preview');
      box.innerHTML = '';
      [...e.target.files].slice(0, 3).forEach(f => {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(f);
        box.appendChild(img);
      });
    }

    function addExtraText() {
      <?php if (!$isPremium): ?>
        document.getElementById('upgradeModal').classList.add('active');
      <?php else: ?>
        document.getElementById('extraTextBox').style.display = 'block';
      <?php endif; ?>
    }

    function closeUpgrade() {
      document.getElementById('upgradeModal').classList.remove('active');
    }
  </script>
</head>

<body>

  <div class="page-wrap">
    <div class="form-card">

      <div class="title">Buat Greeting Card 🎀</div>
      <div class="subtitle">Template: <b><?= htmlspecialchars($themeName) ?></b></div>

      <form action="create_free_card_process.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

        <label>Untuk Siapa? 💌</label>
        <input type="text" name="to" required>

        <label>Dari Siapa? 🎀</label>
        <input type="text" name="from" required>

        <label>Pesan Utama ✨</label>
        <textarea name="message" required></textarea>



        <button type="button" class="btn-add-text" id="btnAddExtraText">
          ➕ Tambah Teks (Premium)
        </button>


        <div class="extra-text-box" id="extraTextBox">
          <div id="extraContainer"></div>
        </div>

        <label>Foto (max 3)</label>
        <input type="file" name="images[]" multiple accept="image/*" onchange="previewImages(event)">
        <div class="preview-box" id="preview"></div>
        <!-- TAMBAH TEKS PREMIUM -->

        <!-- MUSIC LINK -->
        <label>Link Musik 🎵 (Premium)</label>
        <input type="url" name="spotify_link" id="musicLink" placeholder="https://open.spotify.com/..." <?= !$isPremium ? 'disabled' : '' ?>>

        <?php if (!$isPremium): ?>
          <small style="color:#be185d;display:block;margin-top:-10px;margin-bottom:18px;">
            🔒 Fitur musik hanya untuk Premium
          </small>
        <?php endif; ?>


        <button class="btn-submit">Lanjutkan ➜</button>
      </form>

      <a href="user_home.php" class="back-link">← Kembali ke Dashboard</a>

    </div>
  </div>

  <!-- MODAL UPGRADE -->
  <!-- OVERLAY -->
  <div class="premium-overlay" id="upgradeModal">

    <!-- MODAL -->
    <div class="premium-modal-card">

      <h2 class="premium-title">
        Upgrade Premium <span>💎</span>
      </h2>

      <div class="premium-price">
        Rp 15.000
      </div>

      <ul class="premium-benefit">
        <li>✨ Semua template premium</li>
        <li>✨ Tanpa watermark</li>
        <li>✨ Musik & animasi</li>
        <li>✨ Unlimited edit</li>
      </ul>

      <button class="premium-btn" id="confirmUpgrade">
        Upgrade Sekarang
      </button>

      <span class="premium-cancel" onclick="closeUpgrade()">
        Nanti dulu
      </span>

    </div>

  </div>
  <!-- MODAL KONFIRMASI BAYAR -->
  <div class="premium-overlay" id="confirmPaidModal">
    <div class="premium-modal-card">

      <h2 class="premium-title">Sudah melakukan pembayaran?</h2>

      <p style="font-size:14px;color:#6b7280;line-height:1.6">
        Pastikan pembayaran di Midtrans<br>
        sudah berhasil sebelum melanjutkan.
      </p>

      <button class="premium-btn" id="btnYesPaid">
        Ya, sudah bayar
      </button>

      <span class="premium-cancel" id="btnNoPaid">
        Belum
      </span>

    </div>
  </div>


  <template id="extraTemplate">
    <div class="extra-item" style="margin-bottom:18px">
      <label class="label-premium">Judul Teks Tambahan 💎</label>
      <select name="extra_title[]" class="premium-select">
        <option value="">-- Pilih Judul --</option>
        <option value="Thank You">Thank You</option>
        <option value="Happy">Happy</option>
        <option value="Sad">Sad</option>
        <option value="Sorry">Sorry</option>
      </select>

      <label>Teks Tambahan 💎</label>
      <textarea name="extra_text[]" placeholder="Teks tambahan untuk kartu premium..."></textarea>
    </div>
  </template>
  <script>
    const upgradeModal = document.getElementById('upgradeModal');
    const paidModal = document.getElementById('confirmPaidModal');

    /* ===== OPEN / CLOSE ===== */
    function openUpgrade() {
      upgradeModal.classList.add('active');
    }

    function closeUpgrade() {
      upgradeModal.classList.remove('active');
    }

    function openPaid() {
      paidModal.classList.add('active');
    }

    function closePaid() {
      paidModal.classList.remove('active');
    }

    /* ===== TOMBOL UPGRADE ===== */
    document.getElementById("confirmUpgrade").addEventListener("click", () => {
      // buka Midtrans
      window.open("<?= htmlspecialchars($payUrl ?? 'payment.php') ?>", "_blank", "noopener");

      // tutup modal upgrade
      closeUpgrade();

      // munculin popup "sudah bayar?"
      setTimeout(() => {
        openPaid();
      }, 1000);
    });

    /* ===== TOMBOL BELUM ===== */
    document.getElementById("btnNoPaid").addEventListener("click", () => {
      closePaid();
    });

    /* ===== TOMBOL SUDAH BAYAR ===== */
    document.getElementById("btnYesPaid").addEventListener("click", () => {
      fetch("confirm_payment.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "card_id=<?= $id ?? '' ?>"
      })
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            alert("🎉 Premium aktif!");
            window.location.href = "user_home.php";
          } else {
            alert("❌ Pembayaran belum terverifikasi");
          }
        })
        .catch(() => {
          alert("⚠️ Gagal menghubungi server");
        });
    });
    /* ===== OPEN UPGRADE DARI FORM ===== */
    const isPremium = <?= $isPremium ? 'true' : 'false' ?>;

    const extraBox = document.getElementById('extraTextBox');
    const extraContainer = document.getElementById('extraContainer');
    const extraTemplate = document.getElementById('extraTemplate');

    /* ===== BUTTON TAMBAH TEKS ===== */
    document.getElementById('btnAddExtraText').addEventListener('click', () => {

      // USER FREE → POPUP
      if (!isPremium) {
        upgradeModal.classList.add('active');
        return;
      }

      // USER PREMIUM → TAMBAH FIELD
      extraBox.style.display = 'block';
      extraContainer.appendChild(
        extraTemplate.content.cloneNode(true)
      );
    });

    /* ===== CLOSE POPUP ===== */
    function closeUpgrade() {
      upgradeModal.classList.remove('active');
    }
  </script>



</body>

</html>