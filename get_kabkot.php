<?php

include 'includes/koneksi.php';

header('Content-Type: application/json');


if (isset($_GET['id_prov']) && !empty($_GET['id_prov'])) {
    $id_prov = $_GET['id_prov'];
    
    try {

        $stmt = $pdo->prepare("SELECT id, nama_kabkot FROM kabkot WHERE provinsi_id = :id_prov ORDER BY nama_kabkot ASC");
        $stmt->execute([':id_prov' => $id_prov]);
        
        $dataKabkot = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($dataKabkot);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Gagal mengambil data: ' . $e->getMessage()]);
    }
} else {

}
?>