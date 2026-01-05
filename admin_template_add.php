<?php
require 'config.php';
require_admin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key   = trim($_POST['template_key']);
    $title = trim($_POST['title']);
    $isPremium = isset($_POST['is_premium']) ? 1 : 0;

    if (!$key || !$title || empty($_FILES['image']['name'])) {
        $error = "Semua field wajib diisi";
    } else {

        $uploadDir = 'assets/templates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            $error = "Format gambar harus JPG, PNG, atau WEBP";
        } else {
            $fileName = $key . '-' . time() . '.' . $ext;
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $error = "Gagal upload gambar";
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO templates (template_key, title, image, is_premium)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param("sssi", $key, $title, $filePath, $isPremium);
                $stmt->execute();

                header("Location: admin_list.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Template - LoveCrafted</title>

    <!-- WAJIB -->
    <link rel="stylesheet" href="/main.css">
    <link rel="stylesheet" href="/admin.css">
</head>

<body class="page-bg">

<header class="lc-header">
    <div class="lc-container header-flex">
        <div class="logo-text">
            LoveCrafted <span>Admin</span>
        </div>
        <nav>
            <a href="admin_list.php">⬅ Kembali</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</header>

<main class="lc-container">

    <section class="card">

        <h2>➕ Tambah Template Greeting Card</h2>

        <?php if ($error): ?>
            <div class="badge badge-unpaid" style="margin-bottom:16px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" style="max-width:520px;">

            <label style="font-weight:600;">Template Key</label>
            <input type="text" name="template_key" required
                   placeholder="birthday, wedding"
                   style="width:100%;padding:12px;border-radius:12px;border:1px solid #ddd;margin-bottom:16px;">

            <label style="font-weight:600;">Judul Template</label>
            <input type="text" name="title" required
                   placeholder="Happy Birthday"
                   style="width:100%;padding:12px;border-radius:12px;border:1px solid #ddd;margin-bottom:16px;">

            <label style="font-weight:600;">Upload Gambar Template</label>
            <input type="file" name="image" accept="image/*" required
                   style="width:100%;padding:12px;border-radius:12px;border:2px dashed var(--pink);margin-bottom:16px;">

            <label style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <input type="checkbox" name="is_premium">
                <span>Template Premium</span>
            </label>

            <button type="submit" class="btn-edit">
                💾 Simpan Template
            </button>

        </form>

    </section>

</main>

<footer class="lc-footer">
    <small>LoveCrafted &copy; 2025</small>
</footer>

</body>
</html>
