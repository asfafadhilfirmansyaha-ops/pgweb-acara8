<?php
// Memastikan data dikirim melalui formulir.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

// Mengambil data dari formulir.
$kecamatan = trim($_POST["kecamatan"] ?? "");

$longitude = filter_var(
    $_POST["longitude"] ?? "",
    FILTER_VALIDATE_FLOAT
);

$latitude = filter_var(
    $_POST["latitude"] ?? "",
    FILTER_VALIDATE_FLOAT
);

$luas = filter_var(
    $_POST["luas"] ?? "",
    FILTER_VALIDATE_FLOAT
);

$jumlah_penduduk = filter_var(
    $_POST["jumlah_penduduk"] ?? "",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 0]]
);

// Memeriksa data sebelum disimpan.
if ($kecamatan === "") {
    exit("Nama kecamatan wajib diisi.");
}

if (
    $longitude === false ||
    $longitude < -180 ||
    $longitude > 180
) {
    exit("Longitude harus berupa angka antara -180 dan 180.");
}

if (
    $latitude === false ||
    $latitude < -90 ||
    $latitude > 90
) {
    exit("Latitude harus berupa angka antara -90 dan 90.");
}

if ($luas === false || $luas < 1) {
    exit("Luas harus berupa angka minimal 1.");
}

if ($jumlah_penduduk === false) {
    exit(
        "Jumlah penduduk harus berupa bilangan bulat nol atau lebih."
    );
}

// Pengaturan koneksi database.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Menghubungkan PHP dengan database.
    $conn = new mysqli(
        $servername,
        $username,
        $password,
        $dbname
    );

    $conn->set_charset("utf8mb4");

    // Menyiapkan perintah penyimpanan data.
    $sql = "INSERT INTO penduduk
            (kecamatan, longitude, latitude, luas, jumlah_penduduk)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sdddi",
        $kecamatan,
        $longitude,
        $latitude,
        $luas,
        $jumlah_penduduk
    );

    // Menjalankan penyimpanan data.
    $stmt->execute();

    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    exit(
        "Penyimpanan gagal: " .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8")
    );
}

// Kembali ke halaman tabel setelah berhasil disimpan.
header("Location: ../index.php");
exit;