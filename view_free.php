<?php
require_once 'config.php';


$backUrl = 'login.php';

if (!empty($_SESSION['user'])) {
  $backUrl = 'user_home.php';
} elseif (!empty($_SESSION['is_admin'])) {
  $backUrl = 'admin_list.php';
}

/* ================= GET ID ================= */
$id = $_GET['id'] ?? null;
if (!$id) {
  http_response_code(404);
  exit("Card not found.");
}

/* ================= GET CARD FROM DB ================= */
$stmt = $conn->prepare("
    SELECT 
        c.*,
        u.premium AS user_premium
    FROM cards c
    JOIN users u ON u.id = c.user_id
    WHERE c.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
  http_response_code(404);
  exit("Card not found.");
}

/* ================= MAP DATA ================= */
$type = strtoupper($data['template_type'] ?? 'BIRTHDAY');
$to = htmlspecialchars($data['receiver_name'] ?? 'You');
$from = htmlspecialchars($data['sender_name'] ?? 'Someone');
$msg = nl2br(htmlspecialchars($data['main_message'] ?? ''));

$isPaid = ($data['payment_status'] === 'paid');
$isPremium = !empty($data['user_premium']) || $isPaid;


$extraTitles = [];
$extraTexts = [];

if (!empty($data['extra_title']) && !empty($data['extra_text'])) {
  $extraTitles = json_decode($data['extra_title'], true) ?? [];
  $extraTexts = json_decode($data['extra_text'], true) ?? [];
}


$payUrl = $BASE_URL . '/payment.php?id=' . urlencode($id);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($type) ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

  <!-- ================= CSS TIDAK DIUBAH ================= -->
  <style>
    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #fde2f3, #fbcfe8, #e0e7ff);
      font-family: system-ui;
    }

    .card {
      width: 525px;
      height: 640px;
      background: #fff;
      border-radius: 32px;
      box-shadow: 0 30px 70px rgba(0, 0, 0, .28);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .topbar {
      padding: 18px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .brand {
      font-weight: 900
    }

    .pill {
      margin-left: 8px;
      padding: 4px 12px;
      border-radius: 999px;
      background: #fde2f3;
      color: #ec4899;
      font-size: 10px;
      font-weight: 900;
    }

    .icon-btn {
      width: 38px;
      height: 38px;
      border: none;
      border-radius: 12px;
      background: #f3f4f6;
      cursor: pointer;
    }

    .stage {
      flex: 1;
      position: relative;
      overflow: hidden;
    }

    .scene {
      position: absolute;
      inset: 0;
      padding: 24px 20px;
      display: none;
    }

    .scene.active {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      gap: 16px;
    }

    .typewriter {
      font-family: 'Press Start 2P', monospace;
      font-size: 26px;
      color: #ec4899;
      border-right: 3px solid #ec4899;
      white-space: nowrap;
      overflow: hidden;
      padding-right: 6px;
    }

    .btn {
      background: #ec4899;
      color: #fff;
      padding: 14px 26px;
      border: none;
      border-radius: 16px;
      font-weight: 900;
      cursor: pointer;
    }

    .btn.ghost {
      background: #f3f4f6;
      color: #111
    }

    .envelope {
      width: 150px;
      margin: 30px auto
    }

    .paper {
      width: min(340px, 88%);
      height: 500px;
      background: url("assets/bgtxt.png") center/cover no-repeat;
      border-radius: 14px;
      padding: 20px 30px;
      filter: sepia(.15);
      opacity: 0;
      transform: scale(1.15);
      animation: paperIn 1.3s ease-out forwards;
    }

    @keyframes paperIn {
      0% {
        opacity: 0;
        transform: scale(1.15)
      }

      60% {
        opacity: 1;
        transform: scale(1.03)
      }

      100% {
        opacity: 1;
        transform: scale(1)
      }
    }

    .paper-text {
      font-family: "Courier New", cursive;
      font-size: 13.5px;
      line-height: 1.9;
      color: #5a4632;
      white-space: pre-line;
      height: 100%;
      overflow-y: auto;
    }

    .paper-sign {
      text-align: right;
      margin-top: 18px
    }

    .options {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      width: 100%;
    }

    .opt {
      border: 1px solid #f9a8d4;
      border-radius: 16px;
      padding: 14px;
      font-weight: 800;
      cursor: pointer;
      text-align: center;
    }

    .lock {
      background: #fee2e2;
      border-radius: 18px;
      padding: 16px;
    }

    @keyframes popIn {
      from {
        transform: scale(.92);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .recording-hide {
      display: none !important;
    }
  </style>
</head>

<body>

  <div class="card">

    <div class="topbar">
      <div>
        <span class="brand">LoveCrafted</span>
        <?php if ($isPremium): ?><span class="pill">PREMIUM</span><?php endif; ?>
      </div>
      <div style="display:flex;gap:8px;">
        <a href="<?= $backUrl ?>" class="icon-btn" title="Back to Home">⬅️</a>
        <button class="icon-btn" id="btnRestart" title="Restart">🔄</button>
        <button class="icon-btn" id="btnRecord" title="Export Video">🎥</button>

      </div>
    </div>


    <div class="stage">

      <!-- SCENE 1 -->
      <div class="scene active" id="s1">
        <h1 class="typewriter" data-text="<?= $type ?>"></h1>
        <div>For <b><?= $to ?></b></div>
        <button class="btn" data-next="s2">Next →</button>
      </div>

      <!-- SCENE 2 -->
      <div class="scene" id="s2">
        <h2 class="typewriter" data-text="OPEN IT"></h2>
        <img src="assets/amplop.png" class="envelope">
        <button class="btn" data-next="s3">Open 💌</button>
        <button class="btn ghost" data-prev="s1">Back</button>
      </div>

      <!-- SCENE 3 -->
      <div class="scene" id="s3">
        <div class="paper">
          <div class="paper-text">
            <?= $msg ?>

            <div class="paper-sign">
              with love,<br>
              <b><?= $from ?></b>
            </div>
          </div>
        </div>
        <button class="btn" data-next="s4">Next</button>
      </div>

      <!-- SCENE 4 -->
      <!-- SCENE 4 -->
      <div class="scene" id="s4">
        <h2 class="typewriter" data-text="NEEDS MORE?"></h2>

        <?php if ($isPremium && count($extraTitles) > 0): ?>
          <div class="options" id="opts">
            <?php foreach ($extraTitles as $i => $title): ?>
              <div class="opt" data-index="<?= $i ?>">
                <?= htmlspecialchars($title) ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php elseif (!$isPremium): ?>
          <div class="lock">
            Premium only<br><br>
            <button class="btn" id="btnUpgrade">Upgrade Sekarang</button>
          </div>
        <?php else: ?>
          <div style="opacity:.6">No extra message</div>
        <?php endif; ?>
      </div>




    </div>
  </div>

  <div class="modal" id="upgradeModal" style="
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.45);
  z-index:999;
  justify-content:center;
  align-items:center;
">
    <div style="
    width:360px;
    background:#fff;
    border-radius:28px;
    padding:28px;
    text-align:center;
  ">
      <h2 style="color:#ec4899">Upgrade Premium 💎</h2>

      <div style="
      background:#fde2f3;
      padding:12px;
      border-radius:999px;
      font-weight:900;
      margin:14px 0;
    ">
        Rp 15.000
      </div>

      <ul style="text-align:left;line-height:1.8">
        <li>✨ Semua template premium</li>
        <li>✨ Tanpa watermark</li>
        <li>✨ Musik & animasi</li>
        <li>✨ Unlimited edit</li>
      </ul>

      <button class="btn" id="confirmUpgrade" style="width:100%;margin-top:16px">
        Upgrade Sekarang
      </button>

      <small id="closeModal" style="display:block;margin-top:12px;cursor:pointer">
        Nanti dulu
      </small>
    </div>
  </div>
  <div class="modal" id="confirmPaidModal" style="
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.45);
  z-index:999;
  justify-content:center;
  align-items:center;
">
    <div style="
    width:340px;
    background:#fff;
    border-radius:24px;
    padding:24px;
    text-align:center;
  ">
      <h3>Sudah melakukan pembayaran?</h3>
      <p style="font-size:14px;opacity:.7">
        Pastikan pembayaran berhasil
      </p>

      <button class="btn" id="btnYesPaid" style="width:100%;margin-top:14px">
        Ya, sudah bayar
      </button>

      <button class="btn ghost" id="btnNoPaid" style="width:100%;margin-top:10px">
        Belum
      </button>
    </div>
  </div>


  <!-- EXTRA LETTER POPUP (FINAL) -->
  <div class="modal" id="extraModal" style="
  display:none;
  position:fixed;
  inset:0;
  z-index:999;

  display:none;
  justify-content:center;
  align-items:center;

  background:rgba(255, 220, 235, 0.35); /* pink soft */
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
">


    <div style="
    width:380px;
    height:650px;
 
    border-radius:28px;
    padding:0;
    overflow:hidden;
    animation:popIn .4s ease;
    display:flex;
    flex-direction:column;
  ">

      <!-- PAPER WRAPPER -->
      <div style="
      flex:1;
      display:flex;
      justify-content:center;
      align-items:center;
      padding:20px;
    ">

        <!-- LETTER BACKGROUND -->
        <div style="
        width:100%;
        height:100%;
        background:url('assets/bgtext2.png') center/contain no-repeat;
        position:relative;
      ">

          <!-- TEXT AREA -->
          <div id="extraLetter" style="
          position:absolute;
          top:140px;
          left:32px;
          right:32px;
          bottom:40px;

          padding:18px;
          font-family:'Courier New', cursive;
          font-size:14px;
          line-height:1.9;
          color:#5a4632;

          overflow-y:auto;
          white-space:pre-line;
          background:rgba(255,255,255,0);
        "></div>

        </div>
      </div>

      <!-- FOOTER -->
      <div style="
      padding:14px 18px;
      border-top:1px solid #f3f4f6;
    ">
        <button class="btn ghost" id="btnCloseExtra" style="width:100%">
          Close
        </button>
      </div>

    </div>
  </div>




  <script>
    const extraTexts = <?= json_encode($extraTexts, JSON_UNESCAPED_UNICODE) ?>;

    const extraModal = document.getElementById('extraModal');
    const extraBox = document.getElementById('extraLetter');

    document.getElementById('opts')?.addEventListener('click', e => {
      const opt = e.target.closest('.opt');
      if (!opt) return;

      const idx = opt.dataset.index;

      // isi text LANGSUNG
      extraBox.innerText = extraTexts[idx] || '';

      // tampilkan popup
      extraModal.style.display = 'flex';
    });

    document.getElementById('btnCloseExtra').onclick = () => {
      extraModal.style.display = 'none';
    };



    function typeText(el) {
      const t = el.dataset.text;
      el.textContent = '';
      let i = 0;
      const it = setInterval(() => {
        el.textContent += t[i++];
        if (i >= t.length) clearInterval(it);
      }, 90);
    }

    let current = 's1';
    function go(id) {
      document.getElementById(current).classList.remove('active');
      current = id;
      const s = document.getElementById(id);
      s.classList.add('active');
      const h = s.querySelector('.typewriter');
      if (h) typeText(h);
    }

    document.addEventListener('click', e => {
      if (e.target.dataset.next) go(e.target.dataset.next);
      if (e.target.dataset.prev) go(e.target.dataset.prev);
    });

    const modal = document.getElementById('upgradeModal');

    document.getElementById('btnUpgrade')?.addEventListener('click', () => {
      modal.style.display = 'flex';
    });

    document.getElementById('closeModal').onclick = () => {
      modal.style.display = 'none';
    };

    document.getElementById('confirmUpgrade').onclick = () => {
      window.open("<?= htmlspecialchars($payUrl) ?>", "_blank", "noopener");
      modal.style.display = 'none';
    };

    typeText(document.querySelector('#s1 .typewriter'));
    document.getElementById('btnRestart').onclick = () => go('s1');

    const paidModal = document.getElementById('confirmPaidModal');

    document.getElementById('confirmUpgrade').onclick = () => {
      window.open("<?= htmlspecialchars($payUrl) ?>", "_blank", "noopener");

      // tutup modal upgrade
      document.getElementById('upgradeModal').style.display = 'none';

      // munculin popup "sudah bayar?"
      setTimeout(() => {
        paidModal.style.display = 'flex';
      }, 1000);
    };

    document.getElementById('btnNoPaid').onclick = () => {
      paidModal.style.display = 'none';
    };

    document.getElementById('btnYesPaid').onclick = () => {
      fetch("confirm_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "card_id=<?= $id ?>"
      })
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            alert("🎉 Premium aktif!");
            window.location.href = "user_home.php";
          } else {
            alert("❌ Pembayaran belum terverifikasi");
          }
        });
    };
    var mediaRecorder = null;
    var recordedChunks = [];
    var isRecording = false;

    var card = document.querySelector('.card');
    var btnRecord = document.getElementById('btnRecord');

    /* === AUTO PLAY SCENE S1 → S4 === */
    function playAllScenesForVideo() {
      go('s1');
      setTimeout(function () { go('s2'); }, 2500);
      setTimeout(function () { go('s3'); }, 5000);
      setTimeout(function () { go('s4'); }, 9000);
    }

    /* === UI === */
    function hideUI() {
      var topbar = document.querySelector('.topbar');
      if (topbar) topbar.classList.add('recording-hide');
    }

    function showUI() {
      var topbar = document.querySelector('.topbar');
      if (topbar) topbar.classList.remove('recording-hide');
    }

    /* === START RECORD === */
    async function startRecord() {
      if (isRecording) return;
      isRecording = true;

      hideUI();
      recordedChunks = [];

      let stream;

      try {
        stream = await navigator.mediaDevices.getDisplayMedia({
          video: { frameRate: 60 },
          audio: false
        });
      } catch (e) {
        alert("Record dibatalkan");
        showUI();
        isRecording = false;
        return;
      }

      let options = {};
      if (MediaRecorder.isTypeSupported('video/webm;codecs=vp8')) {
        options.mimeType = 'video/webm;codecs=vp8';
      }

      mediaRecorder = new MediaRecorder(stream, options);

      mediaRecorder.ondataavailable = e => {
        if (e.data.size > 0) recordedChunks.push(e.data);
      };

      mediaRecorder.onstop = () => {
        showUI();
        downloadVideo();
        isRecording = false;
      };

      mediaRecorder.start();

      playAllScenesForVideo();

      setTimeout(() => {
        mediaRecorder.stop();
        stream.getTracks().forEach(t => t.stop());
      }, 12000);
    }


    /* === DOWNLOAD === */
    function downloadVideo() {
      if (!recordedChunks.length) {
        alert("❌ Video gagal direkam");
        return;
      }

      var blob = new Blob(recordedChunks, { type: 'video/webm' });
      var url = URL.createObjectURL(blob);

      var a = document.createElement('a');
      a.href = url;
      a.download = 'LoveCrafted-Card.webm';
      document.body.appendChild(a);
      a.click();

      setTimeout(function () {
        URL.revokeObjectURL(url);
        document.body.removeChild(a);
      }, 100);
    }

    /* === BUTTON === */
    if (btnRecord) {
      btnRecord.addEventListener('click', startRecord);
    }
  </script>

</body>

</html>