<?php // $card tersedia 
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Premium Card</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    html,
    body {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #8b0000;
      font-family: Poppins, sans-serif;
      cursor: pointer;
    }

    /* STAGE */
    .stage {
      position: relative;
      width: 100%;
      height: 100vh;
      overflow: hidden;
    }

    /* CENTER */
    .envelope-stage {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);

      height: 320px;
      isolation: isolate;
    }

    /* ENVELOPE */
    .envelope {
      position: absolute;
      left: 56%;
      bottom: 0;
      transform: translateX(-50%);
      width: 320px;
      transition: .6s ease;
      z-index: 1;
    }

    .envelope.open {
      opacity: 0
    }

    /* LETTER AREA */
    .letter-area {
      left: 50%;
      width: 500px;
      height: 440px;
      pointer-events: none;
    }

    /* LETTER BASE */
    /* LETTER BASE */
    .letter {
      position: absolute;
      width: 100%;
      background: #f9f6f1;
      padding: 30px;
      top: 100px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, .35);
      text-align: center;
      opacity: 0;
      transform: translateY(80px) scale(.98);
      transition: all .7s cubic-bezier(.2, .9, .3, 1);
      z-index: 5;
    }

    /* LETTER DEPAN */
    .letter.show {
      opacity: 1;
      transform: translateY(0) scale(1);
      z-index: 6;
    }

    /* LETTER BELAKANG (STACKED) */
    .letter.back {
      opacity: 1;
      transform: translateY(14px) scale(.95);
      top: 40px;
      z-index: 4;
      filter: brightness(.97);
    }

    .letter-area.hide {
      opacity: 0;
      pointer-events: none;
    }

    /* LETTER EXIT */
    .letter.exit {
      opacity: 0;
      transform: translateY(40px) scale(.95);
    }

    /* TEXT */
    .letter-title {
      font-size: 28px;
      font-weight: 600
    }

    .letter-message {
      margin-top: 12px;
      font-size: 17px;
      line-height: 1.6;
      max-width: 100%;
      word-break: break-word;
      overflow-wrap: break-word;
      white-space: normal;
    }

    .letter-footer {
      margin-top: 18px;
      font-style: italic
    }

    /* TAP */
    .tap {
      position: absolute;
      top: calc(50% + 210px);
      width: 100%;
      text-align: center;
      color: #fff;
      opacity: .75;
      font-size: 13px;
    }

    /* PHOTO SCENE CONTAINER */
    /* PHOTO SCENE CONTAINER */
    .photo-scene {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 620px;
      /* muat 3 foto */
      height: 300px;
      pointer-events: none;
      opacity: 0;
      z-index: 20;
    }

    /* POLAROID BASE */
    .photo {
      position: absolute;
      width: 200px;

      background: #fff;
      padding: 10px 10px 25px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, .35);
      border-radius: 4px;
      opacity: 0;
      transition: all .9s cubic-bezier(.2, .9, .3, 1);
    }

    /* IMAGE */
    .photo img {
      width: 100%;
      height: 160px;
      /* BATAS TINGGI */
      object-fit: cover;
      /* POTONG RAPI */
      border-radius: 2px;
    }


    /* FOTO KIRI */
    .photo.p1 {
      left: 60px;
      top: 90px;
      transform: rotate(-6deg) scale(.9) translateY(60px);
      z-index: 21;
    }

    /* FOTO TENGAH */
    .photo.p2 {
      left: 225px;
      top: 80px;
      transform: rotate(0deg) scale(.9) translateY(60px);
      z-index: 22;
    }

    /* FOTO KANAN */
    .photo.p3 {
      left: 390px;
      top: 90px;
      transform: rotate(6deg) scale(.9) translateY(60px);
      z-index: 21;
    }

    /* SAAT MUNCUL */
    .photo.show {
      opacity: 1;
      transform: rotate(var(--r, 0deg)) scale(1) translateY(0);
    }

    /* AKTIFKAN SCENE */
    .photo-scene.show {
      opacity: 1;
    }

    /* === BACK TO HOME BUTTON === */
    .btn-home {
      position: fixed;
      top: 20px;
      left: 20px;
      padding: 10px 16px;
      background: rgba(255, 255, 255, .9);
      color: #000;
      font-size: 13px;
      border-radius: 20px;
      text-decoration: none;
      box-shadow: 0 8px 20px rgba(0, 0, 0, .25);
      opacity: 0;
      pointer-events: none;
      transition: .4s ease;
      z-index: 999;
    }

    .btn-home.show {
      opacity: 1;
      pointer-events: auto;
    }

    /* JIKA FOTO CUMA 1 → CENTER */
    .photo-scene.single .photo {
      left: 55% !important;
      top: 80px;
      transform: translateX(-50%) rotate(0deg) scale(1) !important;
    }
  </style>
</head>

<body>

  <div class="stage" id="stage">

    <div class="envelope-stage">

      <!-- ENVELOPE -->
      <img style="width:400px;" src="assets/premium/evenlope_closed.png" class="envelope" id="envClosed">
      <img style="width:400px;" src="assets/premium/evenlope_open.png" class="envelope open" id="envOpen">

      <!-- LETTERS -->
      <div class="letter-area">

        <!-- LETTER 1 -->
        <div class="letter" id="letter1">
          <div class="letter-title">make a wish!</div>
          <img src="assets/premium/cake.png" width="90">
          <div style="margin-top:10px">22.02.2002</div>
        </div>

        <!-- LETTER 2 -->
        <div class="letter" id="letter2">
          <div class="letter-title"><?= htmlspecialchars($card['template_type']) ?></div>
          <div class="letter-message">
            <?= nl2br(htmlspecialchars($card['main_message'])) ?>
          </div>
          <div class="letter-footer">— <?= htmlspecialchars($card['sender_name']) ?></div>
        </div>

      </div>

      <!-- PHOTO SCENE -->
      <?php
      $photoCount = 0;
      foreach (['photo1', 'photo2', 'photo3'] as $p) {
        if (!empty($card[$p])) $photoCount++;
      }
      ?>

      <div class="photo-scene <?= $photoCount === 1 ? 'single' : '' ?>" id="photoScene">


        <?php if (!empty($card['photo1'])): ?>
          <div class="photo p1">
            <img src="<?= htmlspecialchars($card['photo1']) ?>">
          </div>
        <?php endif; ?>

        <?php if (!empty($card['photo2'])): ?>
          <div class="photo p2">
            <img src="<?= htmlspecialchars($card['photo2']) ?>">
          </div>
        <?php endif; ?>

        <?php if (!empty($card['photo3'])): ?>
          <div class="photo p3">
            <img src="<?= htmlspecialchars($card['photo3']) ?>">
          </div>
        <?php endif; ?>

      </div>
    </div>
    <?php if (!empty($card['spotify_link'])): ?>
      <iframe
        style="position:fixed;bottom:20px;right:20px;
           width:300px;height:80px;border-radius:12px;
           z-index:999;"
        src="<?= str_replace(
                'open.spotify.com/',
                'open.spotify.com/embed/',
                htmlspecialchars($card['spotify_link'])
              ) ?>"
        frameborder="0"
        allow="encrypted-media">
      </iframe>
    <?php endif; ?>

    <div style="left:25px;" class="tap">Tap untuk membuka ✨</div>
    <!-- BACK TO HOME BUTTON -->
    <a href="user_home.php" class="btn-home" id="btnHome">
      ⬅ Kembali ke Home
    </a>

  </div>


  <script>
    let step = 0;

    const envClosed = document.getElementById('envClosed');
    const envOpen = document.getElementById('envOpen');
    const letter1 = document.getElementById('letter1');
    const letter2 = document.getElementById('letter2');
    const photoScene = document.getElementById('photoScene');
    const photos = document.querySelectorAll('.photo');


    document.getElementById('stage').addEventListener('click', () => {

      // STEP 1 → OPEN ENVELOPE
      if (step === 0) {
        envClosed.classList.add('open');
        envOpen.classList.remove('open');
        step++;
        return;
      }

      // STEP 2 → SHOW LETTER 1
      if (step === 1) {
        letter1.classList.add('show');
        step++;
        return;
      }

      // STEP 3 → STACK LETTER 2 (LETTER 1 MASIH KELIATAN)
      if (step === 2) {
        letter1.classList.remove('show');
        letter1.classList.add('back');

        letter2.classList.add('show');

        step++;
        return;
      }
      // STEP 4 → PHOTO SCENE 🎉
      if (step === 3) {

        // 🔥 HAPUS LETTER
        document.querySelector('.letter-area').classList.add('hide');
        document.getElementById('btnHome').classList.add('show');

        photoScene.classList.add('show');

        photos.forEach((p, i) => {
          setTimeout(() => p.classList.add('show'), i * 200);
        });

        step++;
        return;
      }

    });
    document.getElementById('stage').addEventListener('click', () => {
      const iframe = document.getElementById('spotifyPlayer');
      if (!iframe) return;

      iframe.src += "?autoplay=1";
    }, {
      once: true
    });
  </script>


</body>

</html>