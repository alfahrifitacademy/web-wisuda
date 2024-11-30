<?php
// Memulai session dan menghubungkan ke database
require_once('admin/db_connnection.php');  // Pastikan file koneksi database benar
require_once('fpdf/fpdf.php');  // Sertakan library FPDF

// Memeriksa apakah ID guest (id_guest) diterima dalam URL
if (isset($_GET['id_guest']) && is_numeric($_GET['id_guest'])) {
    $id_guest = $_GET['id_guest'];  // Mengambil ID guest dari URL

    // Query untuk mengambil data undangan (guest)
    $sql = "SELECT guest.kepada, guest.created_at, guest.status, users.nama, users.nim, dokumen.tgl_wisuda, dokumen.waktu
            FROM guest
            JOIN users ON guest.create_by = users.id_users
            JOIN dokumen ON dokumen.create_by = users.id_users
            WHERE guest.id_guest = ? AND dokumen.status = 'approved'";

    // Persiapkan dan eksekusi query
    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("i", $id_guest); // Binding parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Cek apakah ada data yang ditemukan
        if ($result->num_rows > 0) {
            // Ambil data yang ditemukan
            $row = $result->fetch_assoc();
            $kepada = htmlspecialchars($row['kepada']);
            $nama = htmlspecialchars($row['nama']);
            $nim = htmlspecialchars($row['nim']);
            $tgl_wisuda = htmlspecialchars($row['tgl_wisuda']);
            $waktu = htmlspecialchars($row['waktu']);

            // Format tanggal wisuda menjadi format yang lebih mudah dibaca
            $tgl_wisuda = date("d F Y", strtotime($tgl_wisuda));

            // Membuat objek FPDF untuk generate surat undangan dalam bentuk PDF
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();

            // Menambahkan judul surat
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Surat Undangan Wisuda', 0, 1, 'C');
            $pdf->Ln(10);  // Jarak antar bagian

            // Menambahkan isi surat undangan
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Nomor: 001/Undangan/2024', 0, 1);
            $pdf->Ln(5);

            // Menambahkan "Dengan hormat"
            $pdf->MultiCell(0, 10, "Dengan hormat,\n\n"
                . "Kepada: $kepada\n\n"
                . "Sehubungan dengan acara wisuda yang akan dilaksanakan, kami mengundang Saudara/i:\n\n"
                . "Nama: $nama\n"
                . "NIM: $nim\n\n"
                . "Untuk menghadiri acara Wisuda yang akan diselenggarakan pada:\n\n"
                . "Tanggal: $tgl_wisuda\n"
                . "Waktu: $waktu\n\n"
                . "Acara ini akan dilaksanakan di:\n"
                . "Tempat: Aula Universitas XYZ\n\n"
                . "Demikian undangan ini kami sampaikan. Kehadiran Saudara/i sangat kami harapkan.\n\n"
                . "Hormat kami,\n"
                . "Panitia Wisuda Universitas XYZ"
            );

            // Output PDF ke browser (langsung download)
            $pdf->Output('D', 'Surat_Undangan_Wisuda_' . $nim . '.pdf');  // Menggunakan NIM untuk nama file PDF
            exit;
        } else {
            echo "Undangan tidak ditemukan atau dokumen belum disetujui.";
        }

        // Menutup statement setelah query selesai
        $stmt->close();
    } else {
        echo "Terjadi kesalahan dalam pengambilan data.";
    }
} else {
    echo "ID undangan tidak valid.";
}

// Menutup koneksi database
$koneksi->close();
?>
