<?php
include 'includes/koneksi.php';

header('Content-Type: application/json');//output kabkot jadi Json

if (isset($_GET['id_prov']) && !empty($_GET['id_prov'])) {//ambil id prov
    $id_prov = $_GET['id_prov'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, nama_kabkot FROM kabkot WHERE provinsi_id = :id_prov ORDER BY nama_kabkot ASC");//menampilkan
        $stmt->execute([':id_prov' => $id_prov]);//menjalankan id provinsi dari database ke html
        $dataKabkot = $stmt->fetchAll(PDO::FETCH_ASSOC);//mgumpulkan data menjadi array
        echo json_encode($dataKabkot);//php jadi json supaya bisa dibaca js
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Gagal mengambil data: ' . $e->getMessage()]);
    }
} else {
}
?>