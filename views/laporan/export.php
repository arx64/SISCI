<?php

/**
 * ============================================================
 * FILE: views/laporan/export.php
 * ------------------------------------------------------------
 * Halaman export/cetak laporan
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

// Ambil data user yang sedang login
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Evaluasi ISO/IEC 27001 - SMKN 1 Galang</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 80px;
            height: auto;
        }

        .judul-laporan {
            text-align: center;
            margin: 30px 0;
        }

        .judul-laporan h3 {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000 !important;
        }

        .table th {
            background-color: #f0f0f0 !important;
            font-weight: bold;
            text-align: center;
        }

        .nilai-besar {
            font-size: 36pt;
            font-weight: bold;
        }

        .kategori-badge {
            font-size: 14pt;
            padding: 8px 16px;
        }

        .ttd {
            margin-top: 50px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body class="p-4">
    <!-- Tombol Cetak -->
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="bi bi-printer me-2"></i>Cetak Laporan
        </button>
        <a href="<?php echo BASE_URL; ?>index.php?page=laporan" class="btn btn-secondary btn-lg">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="row align-items-center">
            <div class="col-2 text-center">
                <!-- Logo Placeholder -->
                <!-- <div style="width: 80px; height: 80px; background: #1a365d; border-radius: 50%; 
                            display: flex; align-items: center; justify-content: center; color: white; 
                            font-size: 36px; margin: 0 auto;">
                    <i class="bi bi-shield-check"></i>
                </div> -->
                <img src="https://smkn1galang.sch.id/wp-content/uploads/2024/09/cropped-LOGO-SMK.png" alt="Logo SMKN 1 Galang" class="kop-logo">
            </div>
            <div class="col-8 text-center">
                <h5 class="mb-0">PEMERINTAH PROVINSI SUMATERA UTARA</h5>
                <h5 class="mb-0">DINAS PENDIDIKAN</h5>
                <h4 class="fw-bold mb-0">SMK NEGERI 1 GALANG</h4>
                <small>Jalan Perkebunan Desa, Timbang Deli, Kec. Galang, Kabupaten Deli Serdang, Sumatera Utara 20585</small><br>
                <small>Telp: (061) 123456 | Email: smkn1galang@edu.go.id</small>
            </div>
            <div class="col-2 text-center">
                <div style="width: 80px; height: 80px; background: #2c5282; border-radius: 50%; 
                            display: flex; align-items: center; justify-content: center; color: white; 
                            font-size: 24px; margin: 0 auto;">
                    ISO
                </div>
            </div>
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="judul-laporan">
        <h3>Laporan Evaluasi Kepatuhan</h3>
        <h3>Keamanan Informasi</h3>
        <p class="mb-0">Berdasarkan Standar ISO/IEC 27001:2013</p>
        <p>Tanggal: <?php echo formatDateIndo(date('Y-m-d')); ?></p>
    </div>

    <!-- Ringkasan -->
    <h5 class="fw-bold mb-3">I. RINGKASAN EVALUASI</h5>
    <table class="table table-bordered">
        <tr>
            <th style="width: 40%">Persentase Kepatuhan</th>
            <td class="text-center">
                <span class="nilai-besar text-primary"><?php echo $persentaseKepatuhan; ?>%</span>
            </td>
        </tr>
        <tr>
            <th>Tingkat Kepatuhan</th>
            <td class="text-center">
                <span class="badge bg-<?php echo $kategoriKepatuhan['class']; ?> kategori-badge">
                    <?php echo $kategoriKepatuhan['label']; ?>
                </span>
            </td>
        </tr>
        <tr>
            <th>Rata-rata Skor</th>
            <td class="text-center"><?php echo round($rekap['rata_rata_skor'], 2); ?> dari 5</td>
        </tr>
        <tr>
            <th>Total Kontrol Dievaluasi</th>
            <td class="text-center"><?php echo $rekap['total_dievaluasi']; ?> dari <?php echo $rekap['total_kontrol']; ?> kontrol</td>
        </tr>
        <tr>
            <th>Total Skor Diperoleh</th>
            <td class="text-center"><?php echo $rekap['total_skor_diperoleh']; ?> dari <?php echo $rekap['total_skor_maksimal']; ?></td>
        </tr>
        <tr>
            <th>Kontrol Perlu Perbaikan</th>
            <td class="text-center"><?php echo count($gapAnalysis); ?> kontrol (skor < 3)</td>
        </tr>
    </table>

    <!-- Detail Domain -->
    <h5 class="fw-bold mb-3 mt-4">II. DETAIL KEPATUHAN PER DOMAIN</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Domain</th>
                <th>Persentase</th>
                <th>Skor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domainStats as $index => $stat):
                $status = getKategoriKepatuhan($stat['persentase']);
            ?>
                <tr>
                    <td class="text-center"><?php echo $index + 1; ?></td>
                    <td class="text-center fw-bold"><?php echo $stat['kode']; ?></td>
                    <td><?php echo $stat['nama']; ?></td>
                    <td class="text-center"><?php echo $stat['persentase']; ?>%</td>
                    <td class="text-center"><?php echo $stat['skor']; ?>/<?php echo $stat['maksimal']; ?></td>
                    <td class="text-center">
                        <span class="badge bg-<?php echo $status['class']; ?>">
                            <?php echo $status['label']; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Gap Analysis -->
    <?php if (!empty($gapAnalysis)): ?>
        <div class="page-break"></div>
        <h5 class="fw-bold mb-3 mt-4">III. GAP ANALYSIS - KONTROL PERLU PERBAIKAN</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Kontrol</th>
                    <th>Nama Kontrol</th>
                    <th>Domain</th>
                    <th>Skor</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gapAnalysis as $index => $gap): ?>
                    <tr>
                        <td class="text-center"><?php echo $index + 1; ?></td>
                        <td class="text-center"><?php echo $gap['kode_kontrol']; ?></td>
                        <td><?php echo $gap['nama_kontrol']; ?></td>
                        <td><?php echo $gap['kode_domain']; ?></td>
                        <td class="text-center">
                            <span class="badge bg-danger"><?php echo $gap['skor']; ?></span>
                        </td>
                        <td><?php echo $gap['catatan'] ?: '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Kesimpulan -->
    <h5 class="fw-bold mb-3 mt-4">IV. KESIMPULAN</h5>
    <div class="border p-3">
        <p>
            Berdasarkan hasil evaluasi kepatuhan keamanan informasi menggunakan kerangka kerja ISO/IEC 27001:2013
            pada SMKN 1 Galang, diperoleh tingkat kepatuhan sebesar <strong><?php echo $persentaseKepatuhan; ?>%</strong>
            dengan kategori <strong><?php echo $kategoriKepatuhan['label']; ?></strong>.
        </p>
        <p class="mb-0">
            <?php if ($persentaseKepatuhan >= 81): ?>
                Organisasi telah mencapai tingkat kepatuhan yang sangat baik terhadap standar ISO/IEC 27001.
                Tetap pertahankan dan lakukan peningkatan berkelanjutan.
            <?php elseif ($persentaseKepatuhan >= 61): ?>
                Organisasi memiliki tingkat kepatuhan yang baik. Perbaikan masih diperlukan pada beberapa area.
            <?php elseif ($persentaseKepatuhan >= 41): ?>
                Organisasi memiliki tingkat kepatuhan yang cukup. Diperlukan perbaikan signifikan pada beberapa kontrol.
            <?php elseif ($persentaseKepatuhan >= 21): ?>
                Organisasi memiliki tingkat kepatuhan yang rendah. Diperlukan perbaikan besar pada kebanyakan kontrol.
            <?php else: ?>
                Organisasi memiliki tingkat kepatuhan yang sangat rendah. Segera lakukan perbaikan menyeluruh.
            <?php endif; ?>
        </p>
    </div>

    <!-- Tanda Tangan -->
    <div class="row ttd">
        <div class="col-md-6">
            <p class="mb-4">Mengetahui,</p>
            <p>Kepala SMKN 1 Galang</p>
            <br><br><br>
            <p class="ttd-nama">...................................</p>
            <p>NIP. ...............................</p>
        </div>
        <div class="col-md-6 text-end">
            <p class="mb-4">Galang, <?php echo formatDateIndo(date('Y-m-d')); ?></p>
            <p>Auditor/Evaluator</p>
            <br><br><br>
            <p class="ttd-nama"><?php echo $currentUser ? htmlspecialchars($currentUser['nama']) : '...................................'; ?></p>
            <p>NIP/NIK. ...............................</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>