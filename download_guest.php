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

            /// Array untuk nama hari dan bulan dalam Bahasa Indonesia
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
            // Gantilah 'logo.png' dengan path file logo Anda
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
            $pdf->Cell(0, 10, 'Lampiran: -', 0, 1);
            $pdf->Cell(0, 10, 'Perihal: Undangan Wisuda', 0, 1);
            $pdf->Ln(5);  // Jarak antar bagian Nomor, Lampiran, dan Perihal

            // Menambahkan "Kepada"
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Kepada Yth:', 0, 1, 'L');  // Teks "Kepada Yth"
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, "$kepada", 0, 1, 'L');  // Nama penerima
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, "Di tempat", 0, 1, 'L');  // Nama penerima
            $pdf->Ln(5);  // Jarak sebelum isi surat

            // Isi Surat
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 10,
                "Dengan hormat,\n"
                . "Sehubungan dengan akan diselenggarakan acara Wisuda Lulusan Program Sarjana STMIK Bandung melalui surat ini kami bermaksud mengundang Bapak/Ibu Orang Tua/Wali Wisudawan untuk dapat menghadiri kegiatan Wisuda tersebut, yang dilaksanakan pada :\n"
            );
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk Tanggal
            $pdf->MultiCell(0, 10, "Tanggal: $tgl_wisuda\n");
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk Waktu
            $pdf->MultiCell(0, 10, "Waktu: $waktu s.d Selesai\n");
            $pdf->SetX(20);  // Geser sedikit ke kanan untuk Tempat
            $pdf->MultiCell(0, 10, "Tempat: Aula Hotel Savoy Homann\n");
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 10,
                "Demikianlah surat undangan ini kami sampaikan, atas perhatian dan kehadiran Bapak/Ibu kami ucapkan terima kasih\n\n"
            );
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetX(130);
            $pdf->MultiCell(0, 10,
                "Hormat kami,\n"
                    . "Panitia Wisuda STMIK Bandung"
            );

            // Output PDF ke browser (langsung download)
            $pdf->Output('D', 'Surat_Undangan_Wisuda_' . $kepada . '.pdf');  // Menggunakan NIM untuk nama file PDF
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
