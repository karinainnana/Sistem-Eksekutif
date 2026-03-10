<?php
/**
 * Admin CRUD - Evaluasi Program (SPAB & DESTANA)
 */
require_once dirname(__DIR__) . '/config/config.php';

// Cek session admin
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$msg = '';
$msg_type = '';

// ============================================================
// CRUD ACTIONS
// ============================================================

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create') {
        $jenis  = in_array($_POST['jenis'], ['spab', 'destana']) ? $_POST['jenis'] : 'spab';
        $judul  = mysqli_real_escape_string($conn, trim($_POST['judul']));
        $isi    = mysqli_real_escape_string($conn, trim($_POST['isi']));
        $now    = date('Y-m-d H:i:s');

        $q = "INSERT INTO evaluasi_program (jenis, judul, isi, created_at, updated_at)
              VALUES ('$jenis', '$judul', '$isi', '$now', '$now')";
        if (mysqli_query($conn, $q)) {
            $msg = 'Evaluasi berhasil ditambahkan.';
            $msg_type = 'success';
        } else {
            $msg = 'Gagal menambahkan evaluasi: ' . mysqli_error($conn);
            $msg_type = 'danger';
        }

    } elseif ($action === 'update') {
        $id     = (int)$_POST['id'];
        $jenis  = in_array($_POST['jenis'], ['spab', 'destana']) ? $_POST['jenis'] : 'spab';
        $judul  = mysqli_real_escape_string($conn, trim($_POST['judul']));
        $isi    = mysqli_real_escape_string($conn, trim($_POST['isi']));
        $now    = date('Y-m-d H:i:s');

        $q = "UPDATE evaluasi_program SET jenis='$jenis', judul='$judul', isi='$isi', updated_at='$now'
              WHERE id = $id";
        if (mysqli_query($conn, $q)) {
            $msg = 'Evaluasi berhasil diperbarui.';
            $msg_type = 'success';
        } else {
            $msg = 'Gagal memperbarui evaluasi: ' . mysqli_error($conn);
            $msg_type = 'danger';
        }

    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $q  = "DELETE FROM evaluasi_program WHERE id = $id";
        if (mysqli_query($conn, $q)) {
            $msg = 'Evaluasi berhasil dihapus.';
            $msg_type = 'success';
        } else {
            $msg = 'Gagal menghapus evaluasi: ' . mysqli_error($conn);
            $msg_type = 'danger';
        }
    }
}

// GET untuk edit (ambil data 1 record)
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM evaluasi_program WHERE id = $edit_id");
    if ($r) $edit_data = mysqli_fetch_assoc($r);
}

// Fetch all data
$all_evaluasi = [];
$r = mysqli_query($conn, "SELECT * FROM evaluasi_program ORDER BY jenis ASC, updated_at DESC");
while ($row = mysqli_fetch_assoc($r)) {
    $all_evaluasi[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Evaluasi Program - Admin PKRR BPBD DIY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary: #043e80;
            --secondary: #e64a19;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }
        /* Topbar */
        .topbar {
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .topbar .brand { font-size: 1.1rem; font-weight: 700; }
        .topbar a { color: rgba(255,255,255,0.85); text-decoration: none; font-size: 0.9rem; margin-left: 20px; transition: color .2s; }
        .topbar a:hover { color: #fff; }
        .topbar .badge-admin { background: var(--secondary); font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; margin-left: 12px; }

        /* Content */
        .content-wrapper { max-width: 1100px; margin: 30px auto; padding: 0 20px 40px; }

        /* Page header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, #0a5299 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 14px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-header h1 { font-size: 1.3rem; font-weight: 700; }
        .page-header p { font-size: 0.85rem; opacity: 0.85; margin-top: 4px; }

        /* Card */
        .card-section {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.07);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card-section .card-head {
            background: var(--primary);
            color: white;
            padding: 14px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-section .card-head.orange { background: var(--secondary); }
        .card-section .card-body-inner { padding: 22px; }

        /* Form */
        .form-label { font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 5px; }
        .form-control, .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 0.9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(4,62,128,0.1);
            outline: none;
        }
        textarea.form-control { min-height: 130px; resize: vertical; }
        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }
        .btn-primary-custom:hover { background: #032d5e; transform: translateY(-1px); }
        .btn-secondary-custom {
            background: #6b7280;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background .2s;
        }
        .btn-secondary-custom:hover { background: #4b5563; color: white; }

        /* Table */
        .eval-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .eval-table thead th {
            background: #f8fafc;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.8rem;
        }
        .eval-table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .eval-table tbody tr:hover { background: #f8fafc; }
        .eval-table tbody tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge-spab {
            background: #dbeafe;
            color: #1e40af;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-destana {
            background: #fce7f3;
            color: #9d174d;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-latest {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 600;
            margin-left: 6px;
        }

        /* Action buttons */
        .btn-edit {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.78rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background .2s;
        }
        .btn-edit:hover { background: #d97706; color: white; }
        .btn-delete {
            background: #ef4444;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.78rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background .2s;
        }
        .btn-delete:hover { background: #dc2626; }

        /* Isi text preview */
        .isi-preview {
            color: #374151;
            max-width: 450px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.6;
        }

        .divider-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 0 4px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }
        .empty-state i { font-size: 2.5rem; margin-bottom: 10px; opacity: 0.4; }

        .alert-box {
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-danger  { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }

        /* Timestamp */
        .text-muted-sm { font-size: 0.75rem; color: #9ca3af; margin-top: 3px; }

        /* Info box */
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 0.84rem;
            color: #1e40af;
            margin-bottom: 20px;
        }
        .info-box i { margin-right: 6px; }
    </style>
</head>
<body>

<!-- Topbar -->
<div class="topbar">
    <div class="brand"><i class="fas fa-shield-alt me-2"></i>PKRR BPBD DIY <span class="badge-admin">ADMIN</span></div>
    <div>
        <a href="index.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
        <a href="spab.php"><i class="fas fa-school me-1"></i>SPAB</a>
        <a href="destana.php"><i class="fas fa-house-user me-1"></i>DESTANA</a>
        <a href="../pages/spab.php" target="_blank"><i class="fas fa-eye me-1"></i>Lihat Dashboard</a>
        <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
    </div>
</div>

<!-- Content -->
<div class="content-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-file-alt me-2"></i>Kelola Evaluasi Program</h1>
            <p>Tambah, edit, dan hapus teks evaluasi yang ditampilkan di dashboard SPAB & DESTANA</p>
        </div>
        <a href="index.php" class="btn-secondary-custom"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    </div>

    <?php if ($msg): ?>
    <div class="alert-box alert-<?= $msg_type ?>">
        <i class="fas fa-<?= $msg_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <!-- Info -->
    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        Teks evaluasi yang paling terbaru (berdasarkan tanggal update) akan ditampilkan secara otomatis di halaman dashboard masing-masing. Pastikan hanya ada <strong>1 evaluasi aktif per jenis</strong> untuk tampilan yang konsisten.
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 25px; align-items: start;">

        <!-- FORM TAMBAH / EDIT -->
        <div class="card-section">
            <div class="card-head <?= $edit_data ? 'orange' : '' ?>">
                <i class="fas fa-<?= $edit_data ? 'edit' : 'plus-circle' ?>"></i>
                <?= $edit_data ? 'Edit Evaluasi' : 'Tambah Evaluasi Baru' ?>
            </div>
            <div class="card-body-inner">
                <form method="POST" action="evaluasi.php" id="formEvaluasi">
                    <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'create' ?>">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= (int)$edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Jenis Program <span style="color:#ef4444">*</span></label>
                        <select name="jenis" class="form-select" required id="selectJenis">
                            <option value="spab"    <?= (!$edit_data || $edit_data['jenis'] === 'spab')    ? 'selected' : '' ?>>SPAB (Satuan Pendidikan Aman Bencana)</option>
                            <option value="destana" <?= ($edit_data && $edit_data['jenis'] === 'destana') ? 'selected' : '' ?>>DESTANA (Desa Tangguh Bencana)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul / Label Internal <span style="color:#ef4444">*</span></label>
                        <input type="text" name="judul" class="form-control" required
                               placeholder="Contoh: Evaluasi SPAB 2026"
                               value="<?= $edit_data ? htmlspecialchars($edit_data['judul']) : '' ?>">
                        <small style="color:#6b7280;font-size:0.78rem;">Judul hanya untuk referensi admin, tidak ditampilkan di dashboard.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Teks Evaluasi <span style="color:#ef4444">*</span></label>
                        <textarea name="isi" class="form-control" required
                                  placeholder="Tulis narasi evaluasi program di sini..."><?= $edit_data ? htmlspecialchars($edit_data['isi']) : '' ?></textarea>
                        <small style="color:#6b7280;font-size:0.78rem;">Teks ini akan ditampilkan langsung di bagian Evaluasi pada dashboard.</small>
                    </div>

                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-<?= $edit_data ? 'save' : 'plus' ?> me-1"></i>
                            <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Evaluasi' ?>
                        </button>
                        <?php if ($edit_data): ?>
                        <a href="evaluasi.php" class="btn-secondary-custom">
                            <i class="fas fa-times me-1"></i>Batal Edit
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE LIST -->
        <div class="card-section">
            <div class="card-head">
                <i class="fas fa-list-alt"></i>
                Daftar Evaluasi Program
                <span style="margin-left:auto;font-size:0.8rem;font-weight:normal;opacity:.8;"><?= count($all_evaluasi) ?> data</span>
            </div>
            <div class="card-body-inner" style="padding: 0;">
                <?php if (empty($all_evaluasi)): ?>
                <div class="empty-state">
                    <div><i class="fas fa-file-alt"></i></div>
                    <p>Belum ada data evaluasi.</p>
                    <small>Tambahkan evaluasi menggunakan form di samping.</small>
                </div>
                <?php else: ?>

                <?php
                // Pisah per jenis & tandai yang terbaru
                $spab_rows    = array_filter($all_evaluasi, fn($r) => $r['jenis'] === 'spab');
                $destana_rows = array_filter($all_evaluasi, fn($r) => $r['jenis'] === 'destana');
                $first_spab    = !empty($spab_rows)    ? array_key_first($spab_rows)    : null;
                $first_destana = !empty($destana_rows) ? array_key_first($destana_rows) : null;
                ?>
                <table class="eval-table">
                    <thead>
                        <tr>
                            <th style="width:90px">Jenis</th>
                            <th>Judul</th>
                            <th>Isi (Ringkasan)</th>
                            <th style="width:100px">Diperbarui</th>
                            <th style="width:100px;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_evaluasi as $idx => $row): ?>
                        <tr>
                            <td>
                                <span class="badge-<?= $row['jenis'] ?>"><?= strtoupper($row['jenis']) ?></span>
                                <?php
                                $is_latest = ($row['jenis'] === 'spab' && $idx === $first_spab) ||
                                             ($row['jenis'] === 'destana' && $idx === $first_destana);
                                if ($is_latest): ?>
                                <span class="badge-latest">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="font-size:0.85rem;"><?= htmlspecialchars($row['judul']) ?></strong>
                            </td>
                            <td>
                                <div class="isi-preview"><?= htmlspecialchars($row['isi']) ?></div>
                            </td>
                            <td>
                                <div style="font-size:0.78rem;color:#6b7280;">
                                    <?= date('d/m/Y', strtotime($row['updated_at'])) ?><br>
                                    <?= date('H:i', strtotime($row['updated_at'])) ?>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <a href="evaluasi.php?edit=<?= $row['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="evaluasi.php" style="display:inline;"
                                          onsubmit="return confirm('Yakin hapus evaluasi ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <?php
    $preview_spab    = null;
    $preview_destana = null;
    foreach ($all_evaluasi as $row) {
        if (!$preview_spab    && $row['jenis'] === 'spab')    $preview_spab    = $row;
        if (!$preview_destana && $row['jenis'] === 'destana') $preview_destana = $row;
    }
    if ($preview_spab || $preview_destana):
    ?>
    <div class="card-section">
        <div class="card-head"><i class="fas fa-eye"></i> Preview Tampilan di Dashboard</div>
        <div class="card-body-inner">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <?php if ($preview_spab): ?>
                <div>
                    <div class="divider-label"><i class="fas fa-school me-1"></i>SPAB — Aktif</div>
                    <div style="background:#f8fafc;border-radius:10px;padding:15px;font-size:0.88rem;line-height:1.7;color:#374151;border-left:3px solid #043e80;">
                        <?= nl2br(htmlspecialchars($preview_spab['isi'])) ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($preview_destana): ?>
                <div>
                    <div class="divider-label"><i class="fas fa-house-user me-1"></i>DESTANA — Aktif</div>
                    <div style="background:#f8fafc;border-radius:10px;padding:15px;font-size:0.88rem;line-height:1.7;color:#374151;border-left:3px solid #e64a19;">
                        <?= nl2br(htmlspecialchars($preview_destana['isi'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
        window.location.href = '../auth/logout.php';
    }
}
</script>
</body>
</html>
