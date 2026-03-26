<?php
include 'includes/koneksi.php'; 

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM peserta WHERE id = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch();

if (!$data) { 
    echo "Data tidak ditemukan!"; 
    exit; 
}

$hobiArray = !empty($data['hobi']) ? explode(", ", $data['hobi']) : [];
$queryProv = $pdo->query("SELECT * FROM provinsi ORDER BY nama_provinsi ASC");
$dataProvinsi = $queryProv->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['update'])) {
    
    $nama    = $_POST['nama'];
    $tempat  = $_POST['tempat'];
    $tanggal = $_POST['tanggal'];
    $agama   = $_POST['agama'];
    $alamat  = $_POST['alamat'];
    $notelp  = $_POST['notelp'];
    $provinsi_id = $_POST['provinsi_id'];
    $kabkot_id   = $_POST['kabkot_id'];

    $jk = isset($_POST['jk']) ? $_POST['jk'] : null;
    $jkValue = ($jk === "Pria") ? 0 : (($jk === "Wanita") ? 1 : null);

    $hobi = !empty($_POST['hobi']) ? implode(", ", $_POST['hobi']) : null;

    $foto = $data['foto']; 
    
    if (!empty($_FILES['foto']['name'])) {
        $target = "uploads/" . basename($_FILES['foto']['name']);
        if (!is_dir("uploads")) mkdir("uploads");
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) { 
            $foto = $target; 
        }
    }

    $sql = "UPDATE peserta SET nama=:nama, \"tempatLahir\"=:tempatLahir, \"tanggalLahir\"=:tanggalLahir, 
            agama=:agama, alamat=:alamat, telepon=:telepon, jk=:jk, hobi=:hobi, foto=:foto, 
            provinsi_id=:provinsi_id, kabkot_id=:kabkot_id WHERE id=:id";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':nama'         => $nama,
        ':tempatLahir'  => $tempat,
        ':tanggalLahir' => $tanggal,
        ':agama'        => $agama,
        ':alamat'       => $alamat,
        ':telepon'      => $notelp,
        ':jk'           => $jkValue,
        ':hobi'         => $hobi,
        ':foto'         => $foto,
        ':provinsi_id'  => $provinsi_id,
        ':kabkot_id'    => $kabkot_id,
        ':id'           => $id
    ]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Siswa</title>
    <link rel="stylesheet" href="assets/css/editstyle.css?=v2">
</head>
<body>

<div class="container">
    <h2>Edit Data Siswa</h2>
    <form id="formEdit" method="POST" enctype="multipart/form-data">
        <label>Nama Calon Siswa</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

        <label>Tempat Lahir</label>
        <input type="text" name="tempat" value="<?= htmlspecialchars($data['tempatLahir']) ?>" required>

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal" value="<?= $data['tanggalLahir'] ?>" required>

        <label>Agama</label>
        <select name="agama">
            <option <?= ($data['agama'] == 'Islam') ? 'selected' : '' ?>>Islam</option>
            <option <?= ($data['agama'] == 'Kristen') ? 'selected' : '' ?>>Kristen</option>
            <option <?= ($data['agama'] == 'Katolik') ? 'selected' : '' ?>>Katolik</option>
            <option <?= ($data['agama'] == 'Hindu') ? 'selected' : '' ?>>Hindu</option>
            <option <?= ($data['agama'] == 'Buddha') ? 'selected' : '' ?>>Buddha</option>
            <option <?= ($data['agama'] == 'Konghucu') ? 'selected' : '' ?>>Konghucu</option>
        </select>

        <label>Provinsi</label>
        <select name="provinsi_id" id="pilihProvinsi" required>
            <option value="">-- Pilih Provinsi --</option>
            <?php foreach ($dataProvinsi as $prov): ?>
                <option value="<?= $prov['id'] ?>" <?= ($data['provinsi_id'] == $prov['id']) ? 'selected' : '' ?>>
                    <?= $prov['nama_provinsi'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Kabupaten/Kota</label>
        <select name="kabkot_id" id="pilihKabkot" data-selected="<?= $data['kabkot_id'] ?>" required>
            <option value="">-- Pilih Kab/Kota --</option>
        </select>
        <label>Alamat</label>
        <textarea name="alamat"><?= htmlspecialchars($data['alamat']) ?></textarea>

        <label>No Telp/HP</label>
        <input type="text" name="notelp" value="<?= htmlspecialchars($data['telepon']) ?>">

        <label>Jenis Kelamin</label>
        <div class="inline">
            <input type="radio" name="jk" value="Pria" <?= ($data['jk'] == 0 && $data['jk'] !== null) ? 'checked' : '' ?>> Pria
            <input type="radio" name="jk" value="Wanita" <?= ($data['jk'] == 1) ? 'checked' : '' ?>> Wanita
        </div>
        
        <label>Hobi</label>
        <div class="inline">
            <input type="checkbox" name="hobi[]" value="Membaca" <?= in_array("Membaca", $hobiArray) ? 'checked' : '' ?>> Membaca
            <input type="checkbox" name="hobi[]" value="Menulis" <?= in_array("Menulis", $hobiArray) ? 'checked' : '' ?>> Menulis
            <input type="checkbox" name="hobi[]" value="Olahraga" <?= in_array("Olahraga", $hobiArray) ? 'checked' : '' ?>> Olahraga
        </div>

        <label>Pas Foto</label>
        <?php if ($data['foto']): ?>
            <img src="<?= $data['foto'] ?>" width="120" id="previewLama" style="display:block; margin-bottom:10px; border-radius:8px;">
        <?php endif; ?>
        
        <input type="file" name="foto" id="inputFotoEdit">
        <img id="previewBaru" src="#" style="display:none; width:120px; margin-top:10px; border-radius:8px; border: 2px solid #333;">

        <div class="btn-group" style="margin-top:20px;">
            <button type="submit" name="update">UPDATE DATA</button>
            <a href="index.php" class="btn-cancel" style="text-decoration:none; padding:10px; background:#eee; color:#333; border-radius:5px; margin-left:10px;">Batal</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/edit.js"></script>

</body>
</html>