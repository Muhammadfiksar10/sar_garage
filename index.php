<?php
include 'config/koneksi.php';

// Pastikan user sudah login, jika belum arahkan ke halaman login
if (!isset($_SESSION['login'])) { 
    header("Location: login.php"); 
    exit; 
}

// Logika Fitur Pencarian Data
$keyword = "";
$query = "SELECT * FROM produk";
if (isset($_POST['cari'])) {
    $keyword = $_POST['keyword'];
    $query = "SELECT * FROM produk WHERE nama_sparepart LIKE '%$keyword%' OR kategori LIKE '%$keyword%'";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SAR GARAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        /* Style khusus untuk area Canvas TTD Digital agar terlihat jelas */
        #canvas-ttd { 
            border: 2px dashed #6c757d; 
            background-color: #ffffff; 
            cursor: crosshair; 
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark px-3 shadow">
        <span class="navbar-brand mb-0 h1 fw-bold text-warning">SAR GARAGE - Sparepart Motor</span>
        <div class="d-flex align-items-center">
            <audio controls class="me-3" style="height: 30px;">
                <source src="assets/media/welcome.mp3" type="audio/mpeg">
                Browser kamu tidak mendukung elemen audio.
            </audio>
            <a href="logout.php" class="btn btn-sm btn-danger fw-bold">Logout</a>
        </div>
    </nav>

    <div class="container my-4">
        
        <h2 class="mb-4 fw-bold text-secondary">Selamat Datang, <?= $_SESSION['nama']; ?>!</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold text-center">
                Video Profil SAR Garage
            </div>
            <div class="card-body text-center">
                <div class="ratio ratio-16x9 mx-auto shadow-sm animate" style="max-width: 560px;">
                    <iframe src="https://www.youtube.com/embed/k93MKOqGcGU" title="USAHA BENGKEL MOTOR" allowfullscreen></iframe>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                + Tambah Sparepart
            </button>
        </div>

        <div class="card mb-4 shadow-sm p-3">
            <form method="POST" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari nama sparepart atau kategori..." value="<?= htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" name="cari" class="btn btn-dark fw-bold">Cari</button>
                </div>
                <?php if ($keyword !== ""): ?>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-outline-secondary">Reset Filter</a>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 25%;">Nama Sparepart</th>
                            <th scope="col" style="width: 20%;">Kategori</th>
                            <th scope="col" style="width: 15%;">Harga</th>
                            <th scope="col" style="width: 10%;">Stok</th>
                            <th scope="col" style="width: 15%;">Foto</th>
                            <th scope="col" style="width: 15%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td class="fw-semibold text-capitalize"><?= $row['nama_sparepart']; ?></td>
                                <td class="text-capitalize"><span class="badge bg-secondary"><?= $row['kategori']; ?></span></td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?= $row['stok']; ?> <small class="text-muted">Pcs</small></td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                    <?php
                                    $p_id = $row['id'];
                                    $g_res = mysqli_query($conn, "SELECT nama_file FROM gambar_produk WHERE produk_id = $p_id");
                                    if (mysqli_num_rows($g_res) > 0) {
                                        while($g = mysqli_fetch_assoc($g_res)) {
                                            echo "<img src='assets/uploads/".$g['nama_file']."' width='55' height='55' class='img-thumbnail me-1 mb-1 shadow-sm' style='object-fit: cover;'>";
                                        }
                                    } else {
                                        echo "<span class='text-muted small'>Tidak ada foto</span>";
                                    }
                                    ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="proses_produk.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-danger px-3 fw-bold" onclick="return confirm('Apakah anda yakin ingin menghapus data sparepart ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Data sparepart tidak ditemukan atau masih kosong.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        Canvas TTD Digital (Bukti Terima Barang)
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-2">
                            <canvas id="canvas-ttd" width="450" height="180" class="shadow-sm"></canvas>
                        </div>
                        <div class="d-flex justify-content-start gap-2">
                            <button class="btn btn-sm btn-secondary fw-bold" onclick="clearCanvas()">Reset TTD</button>
                            <button class="btn btn-sm btn-success fw-bold" onclick="alert('Tanda tangan berhasil disimpan secara lokal!')">Simpan TTD</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="proses_produk.php" method="POST" enctype="multipart/form-data" class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel">Tambah Sparepart Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Sparepart</label>
                        <input type="text" name="nama_sparepart" class="form-control" placeholder="Contoh: Oli Mesin Shell Helix" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Pelumas / Rem / Pengapian" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Contoh: 65000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stok" class="form-control" placeholder="Contoh: 15" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi produk di sini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Produk <span class="text-danger small">*Bisa pilih banyak sekaligus</span></label>
                        <input type="file" name="gambar[]" class="form-control" multiple>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_produk" class="btn btn-primary fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika Menggambar pada Canvas HTML5
        const canvas = document.getElementById('canvas-ttd');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        // Event Listener Mouse
        canvas.addEventListener('mousedown', () => drawing = true);
        canvas.addEventListener('mouseup', () => { drawing = false; ctx.beginPath(); });
        canvas.addEventListener('mousemove', draw);

        // Event Listener Layar Sentuh (Touchscreen HP) biar responsive pas dites dosen
        canvas.addEventListener('touchstart', (e) => { drawing = true; e.preventDefault(); });
        canvas.addEventListener('touchend', () => { drawing = false; ctx.beginPath(); });
        canvas.addEventListener('touchmove', (e) => {
            if (!drawing) return;
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000000';
            ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
        });

        // Fungsi menggambar mouse desktop
        function draw(e) {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000000';
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        // Fungsi hapus isi canvas tanda tangan
        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    </script>
</body>
</html>