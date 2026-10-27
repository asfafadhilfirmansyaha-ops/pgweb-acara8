<?php
session_start();

// Membuat token untuk permintaan penghapusan.
if (!isset($_SESSION["token_hapus"])) {
    $_SESSION["token_hapus"] = bin2hex(random_bytes(32));
}

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

    // Mengambil data dari tabel penduduk.
    $sql = "SELECT * FROM penduduk ORDER BY id";
    $result = $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    exit(
        "Gagal mengakses database: " .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8")
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penduduk Kecamatan</title>
</head>
<body>
    <h2>Data Penduduk Kecamatan</h2>

    <p>
        <a href="input/index.html">Input Data</a>
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Id</th>
            <th>Kecamatan</th>
            <th>Longitude</th>
            <th>Latitude</th>
            <th>Luas</th>
            <th>Jumlah Penduduk</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["id"] ?></td>
                <td>
                    <?= htmlspecialchars(
                        $row["kecamatan"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
                <td><?= $row["longitude"] ?></td>
                <td><?= $row["latitude"] ?></td>
                <td><?= $row["luas"] ?></td>
                <td><?= $row["jumlah_penduduk"] ?></td>

                <td>
                    <form
                        action="hapus.php"
                        method="post"
                        onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                    >
                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $row["id"] ?>"
                        >

                        <input
                            type="hidden"
                            name="token_hapus"
                            value="<?= htmlspecialchars(
                                $_SESSION["token_hapus"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                        >

                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>

        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="7">Belum ada data.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>