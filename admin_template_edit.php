<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php';
require_admin();

/* ===== GET ID ===== */
$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID template tidak ditemukan');
}

/* ===== LOAD TEMPLATE FROM DB ===== */
$stmt = $conn->prepare("SELECT * FROM templates WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$template = $stmt->get_result()->fetch_assoc();

if (!$template) {
    die('Template tidak ditemukan');
}

$error = null;

/* ===== HANDLE SUBMIT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key   = trim($_POST['template_key']);
    $title = trim($_POST['title']);
    $isPremium = isset($_POST['is_premium']) ? 1 : 0;
    $imagePath = $template['image'];

    if (!$key || !$title) {
        $error = "Template key dan judul wajib diisi";
    }

    /* === UPLOAD IMAGE BARU (OPTIONAL) === */
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'assets/templates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            $error = "Format gambar tidak valid";
        } else {
            $newName = $key . '-' . time() . '.' . $ext;
            $newPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $newPath)) {
                $imagePath = $newPath;
            } else {
                $error = "Gagal upload gambar";
            }
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("
            UPDATE templates
            SET template_key=?, title=?, image=?, is_premium=?
            WHERE id=?
        ");
        $stmt->bind_param("sssii", $key, $title, $imagePath, $isPremium, $id);
        $stmt->execute();

        header("Location: admin_list.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Template</title>

    <link rel="stylesheet" href="/main.css">
    <link rel="stylesheet" href="/admin.css">
    <style>
        /* ================= TEMPLATE EDIT LAYOUT ================= */

.template-edit-grid{
    display:grid;
    grid-template-columns: 1.2fr 1fr;
    gap:32px;
    align-items:start;
    margin-top:20px;
}

.template-form label{
    display:block;
    font-weight:600;
    margin-bottom:6px;
    color:var(--text);
}

.template-form input[type="text"],
.template-form input[type="file"]{
    width:100%;
    padding:12px;
    border-radius:12px;
    border:1px solid #e5e7eb;
    margin-bottom:16px;
    font-size:14px;
}

.template-form input[type="file"]{
    border:2px dashed var(--pink);
    background:#fdf2f8;
}

.template-form input:focus{
    outline:none;
    border-color:var(--pink);
    box-shadow:0 0 0 3px rgba(236,72,153,.15);
}

.premium-check{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:20px;
    font-weight:600;
}

/* ===== PREVIEW ===== */

.template-preview{
    background:#fff;
    border-radius:20px;
    padding:18px;
    box-shadow:0 12px 28px rgba(0,0,0,.15);
}

.template-preview h4{
    margin-bottom:12px;
    color:#be185d;
}

.template-preview img{
    width:100%;
    border-radius:16px;
    object-fit:cover;
}

    </style>
</head>

<body class="page-bg">

<header class="lc-header">
    <div class="lc-container header-flex">
        <div class="logo-text">LoveCrafted <span>Admin</span></div>
        <nav>
            <a href="admin_list.php">⬅ Kembali</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</header>

<main class="lc-container">

<section class="card">

    <h2>✏️ Edit Template Greeting Card</h2>

    <?php if ($error): ?>
        <div class="badge badge-unpaid" style="margin-bottom:16px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="template-edit-grid">

        <!-- ===== FORM KIRI ===== -->
        <form method="post" enctype="multipart/form-data" class="template-form">

            <label>Template Key</label>
            <input type="text" name="template_key"
                   value="<?= htmlspecialchars($template['template_key']) ?>">

            <label>Judul Template</label>
            <input type="text" name="title"
                   value="<?= htmlspecialchars($template['title']) ?>">

            <label>Gambar Template (opsional)</label>
            <input type="file" name="image" accept="image/*">

            <label class="premium-check">
                <input type="checkbox" name="is_premium"
                       <?= $template['is_premium'] ? 'checked' : '' ?>>
                <span>Template Premium</span>
            </label>

            <button type="submit" class="btn-edit">
                💾 Simpan Perubahan
            </button>

        </form>

        <!-- ===== PREVIEW KANAN ===== -->
        <div class="template-preview">
            <h4>Preview Template</h4>
            <img src="<?= htmlspecialchars($template['image']) ?>" alt="Preview">
        </div>

    </div>

</section>

</main>

<footer class="lc-footer">
    <small>LoveCrafted &copy; 2025</small>
</footer>

</body>
</html>
