LoveCrafted - Midtrans QRIS Final (JSON + Web Cute)

1. Ekstrak ke:
   C:\xampp\htdocs\lovecrafted_midtrans_final

2. Edit config.php:
   - Ganti $BASE_URL jika perlu.
   - Ganti $MIDTRANS_SERVER_KEY dan $MIDTRANS_CLIENT_KEY dengan key Sandbox kamu sendiri.

3. Jalankan Apache (pastikan extension cURL aktif di php.ini).

4. Buka:
   http://localhost/lovecrafted_midtrans_final/login.php

   Login:
   - username: admin
   - password: lovecrafted2025

5. Buat kartu di admin, ambil link view.php?id=XXXX, kirim ke customer.

6. Atur Payment Notification URL di Midtrans Sandbox:
   http://localhost/lovecrafted_midtrans_final/callback_midtrans.php
