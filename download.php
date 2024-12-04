<?php
// Memulai session dan menghubungkan ke database
require_once('admin/db_connnection.php');  // Pastikan file koneksi database benar
require_once('fpdf/fpdf.php');  // Sertakan library FPDF

// Memeriksa apakah ID pengguna (id_users) diterima dalam URL
if (isset($_GET['id_users']) && is_numeric($_GET['id_users'])) {
    $id_users = $_GET['id_users'];  // Mengambil ID pengguna dari URL

    // Query untuk mengambil data pengguna dan dokumen yang sudah disetujui
    $sql = "SELECT users.nama, users.nim, dokumen.tgl_wisuda, dokumen.waktu, dokumen.status
            FROM users
            JOIN dokumen ON users.id_users = dokumen.create_by
            WHERE users.id_users = ? AND dokumen.status = 'approved'";

    // Persiapkan dan eksekusi query
    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("i", $id_users); // Binding parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Cek apakah ada data yang ditemukan
        if ($result->num_rows > 0) {
            // Ambil data yang ditemukan
            $row = $result->fetch_assoc();
            $nama = htmlspecialchars($row['nama']);
            $nim = htmlspecialchars($row['nim']);
            $tgl_wisuda = htmlspecialchars($row['tgl_wisuda']);
            $waktu = htmlspecialchars($row['waktu']);

            // Array untuk nama hari dan bulan dalam Bahasa Indonesia
            $hari = array("Sunday" => "Minggu", "Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jumat", "Saturday" => "Sabtu");
            $bulan = array("January" => "Januari", "February" => "Februari", "March" => "Maret", "April" => "April", "May" => "Mei", "June" => "Juni", "July" => "Juli", "August" => "Agustus", "September" => "September", "October" => "Oktober", "November" => "November", "December" => "Desember");

            // Mendapatkan nama hari dan bulan dalam bahasa Indonesia
            $dayOfWeek = date("l", strtotime($tgl_wisuda)); // Hari dalam bahasa Inggris (misalnya: Monday)
            $month = date("F", strtotime($tgl_wisuda)); // Bulan dalam bahasa Inggris (misalnya: January)

            // Mengganti dengan nama hari dan bulan dalam Bahasa Indonesia
            $hari_indonesia = $hari[$dayOfWeek];
            $bulan_indonesia = $bulan[$month];

            // Format tanggal wisuda dengan nama hari dan bulan dalam Bahasa Indonesia
            $tgl_wisuda = "$hari_indonesia, " . date("d", strtotime($tgl_wisuda)) . " $bulan_indonesia " . date("Y", strtotime($tgl_wisuda));
            $waktu = date("H:i", strtotime($waktu)) . " WIB";  // Menambahkan WIB pada waktu

            // Membuat objek FPDF untuk generate surat undangan dalam bentuk PDF
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();

            // Menentukan Nomor Surat
            $year = date("Y");  // Tahun saat ini
            $month = date("m");  // Bulan saat ini
            $month_roman = '';   // Bulan dalam format romawi
            switch ($month) {
                case '01':
                    $month_roman = 'I';
                    break;
                case '02':
                    $month_roman = 'II';
                    break;
                case '03':
                    $month_roman = 'III';
                    break;
                case '04':
                    $month_roman = 'IV';
                    break;
                case '05':
                    $month_roman = 'V';
                    break;
                case '06':
                    $month_roman = 'VI';
                    break;
                case '07':
                    $month_roman = 'VII';
                    break;
                case '08':
                    $month_roman = 'VIII';
                    break;
                case '09':
                    $month_roman = 'IX';
                    break;
                case '10':
                    $month_roman = 'X';
                    break;
                case '11':
                    $month_roman = 'XI';
                    break;
                case '12':
                    $month_roman = 'XII';
                    break;
            }

            // Ambil nomor urut surat (misal, dapatkan jumlah surat yang diterbitkan di bulan ini)
            $sql_urut = "SELECT COUNT(*) AS total_surat FROM dokumen WHERE MONTH(tgl_wisuda) = MONTH(CURRENT_DATE()) AND YEAR(tgl_wisuda) = YEAR(CURRENT_DATE())";
            $result_urut = $koneksi->query($sql_urut);
            $row_urut = $result_urut->fetch_assoc();
            $nomor_urut = $row_urut['total_surat'] + 1;  // Nomor urut surat ditambahkan 1

            // Format Nomor Surat
            $nomor_surat = "$nomor_urut/STMIK-BDG/$month_roman/$year";

            // Kop Surat dengan Logo
            $pdf->SetFont('Arial', 'B', 14);

            // Menyisipkan logo di sebelah kiri atas
            $pdf->Image('assets/img/logo.png', 10, 10, 30);  // 10, 10 adalah posisi x, y dari gambar, 30 adalah ukuran lebar logo

            // Menyusun teks kop surat
            $pdf->SetX(50);  // Setelah logo, geser ke kanan untuk teks
            $pdf->Cell(0, 7, 'STMIK Bandung', 0, 1, 'L');  // Nama universitas
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetX(50);  // Menambahkan Sekolah Tinggi
            $pdf->Cell(0, 7, '(Sekolah Tinggi Manajemen Informatika & Komputer Bandung)', 0, 1, 'L');
            $pdf->SetX(50);  // Menjaga alamat di posisi yang sama dengan nama universitas
            $pdf->Cell(0, 7, 'Alamat: Jl.Cikutra No.113-A, Telp (022) 7207777', 0, 1, 'L');
            $pdf->SetX(50);  // Menjaga website dan email di posisi yang sama dengan alamat
            $pdf->Cell(0, 7, 'Website: www.stmik-bandung.ac.id | Email: info@stmik-bandung.ac.id', 0, 1, 'L');
            $pdf->Ln(10);  // Jarak antar kop dan judul surat

            // Menambahkan garis pemisah
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->Line(10, 40, 200, 40);  // Garis horizontal

            // Nomor Surat, Lampiran, dan Perihal
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Nomor: ' . $nomor_surat, 0, 1);
            $pdf->Cell(0, 10, 'Lampiran: 1 Lembar', 0, 1);
            $pdf->Cell(0, 10, 'Perihal: Undangan Wisuda', 0, 1);
            $pdf->Ln(5);  // Jarak antar bagian Nomor, Lampiran, dan Perihal

            // Menambahkan "Kepada"
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Kepada Yth:', 0, 1, 'L');  // Teks "Kepada Yth"
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, "$nama", 0, 1, 'L');  // Nama penerima
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, "Di tempat", 0, 1, 'L');  // Nama penerima
            $pdf->Ln(5);  // Jarak sebelum isi surat

            // Isi Surat
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(
                0,
                10,
                "Dengan hormat,\n"
                    . "Sehubungan dengan acara wisuda yang akan dilaksanakan, kami mengundang Saudara/i:\n"
            );
            // Geser ke kanan sedikit untuk Nama, NIM, Tanggal, Waktu, dan Tempat
            $pdf->SetX(20);  // Geser sedikit ke kanan dari posisi sebelumnya
            $pdf->MultiCell(0, 10, "Nama: $nama\n");
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk NIM
            $pdf->MultiCell(0, 10, "NIM: $nim\n");
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(
                0,
                10,
                "Untuk menghadiri acara Wisuda yang akan diselenggarakan pada:\n"
            );
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk Tanggal
            $pdf->MultiCell(0, 10, "Tanggal: $tgl_wisuda\n");
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk Waktu
            $pdf->MultiCell(0, 10, "Waktu: $waktu\n");
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk Tempat
            $pdf->MultiCell(0, 10, "Tempat: Aula Hotel Savoy Homann\n");
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(
                0,
                10,
                "Demikian undangan ini kami sampaikan. Kehadiran Saudara/i sangat kami harapkan.\n\n"
            );
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetX(130);
            $pdf->MultiCell(
                0,
                10,
                "Hormat kami,\n"
                    . "Panitia Wisuda STMIK Bandung"
            );

            // Output PDF ke browser (langsung download)
            $pdf->Output('D', 'Surat_Undangan_Wisuda_' . $nim . '_' . $nama . '.pdf');  // Menggunakan NIM untuk nama file PDF
            exit;
        } else {
            echo "Dokumen tidak ditemukan atau belum disetujui.";
        }

        // Menutup statement setelah query selesai
        $stmt->close();
    } else {
        echo "Terjadi kesalahan dalam pengambilan data.";
    }
} else {
    echo "ID pengguna tidak valid.";
}

// Menutup koneksi database
$koneksi->close();
