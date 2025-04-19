<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'veritabani.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        
        if ($id <= 0) {
            throw new Exception('Geçersiz ID değeri');
        }
        
        $sql = "DELETE FROM stok_hareketleri WHERE id = ?";
        $stmt = $baglanti->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Sorgu hazırlama hatası: ' . $baglanti->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Sorgu çalıştırma hatası: ' . $stmt->error);
        }
        
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Kayıt başarıyla silindi']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Kayıt bulunamadı']);
        }
        
        $stmt->close();
    } else {
        throw new Exception('Geçersiz istek: POST metodu ve id parametresi gerekli');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'details' => [
            'post_data' => $_POST,
            'request_method' => $_SERVER['REQUEST_METHOD']
        ]
    ]);
}
?> 