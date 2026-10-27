<?php
session_start();

// Memastikan penghapusan dikirim melalui POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Memeriksa token penghapusan.
$token = $_POST["token_hapus"] ?? "";

if (
    !isset($_SESSION["token_hapus"]) ||
    !is_string($token) ||
    !hash_equals($_SESSION["token_hapus"], $token)
) {
    http_response_code(403);
    exit("Permintaan tidak valid. Buka kembali halaman tabel.");
}

// Mengambil ID data yang akan dihapus.
$id = filter_var(
    $_POST["id"] ?? "",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($id === false) {
    http_response_code(400);
    exit("ID data tidak valid.");
}

// Pengaturan koneksi database.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        $servername,
        $username,
        $password,
        $dbname
    );

    $conn->set_charset("utf8mb4");

    // Menghapus satu data berdasarkan ID.
    $stmt = $conn->prepare(
        "DELETE FROM penduduk WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    exit(
        "Gagal menghapus data: " .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8")
    );
}

// Kembali ke halaman tabel.
header("Location: index.php");
exit;