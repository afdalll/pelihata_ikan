<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'check_related_data_error.log'); // Ubah path sesuai kebutuhan

header('Content-Type: application/json');

include_once 'db.php';

// Konstanta untuk nama tabel
const TABLE_KOLAM = 'data_kolam';
const TABLE_IKAN = 'data_ikan';
const TABLE_PAKAN = 'data_pakan';
const TABLE_TEBAR_BIBIT = 'tebar_bibit';
const TABLE_PEMELIHARAAN = 'pemeliharaan_ikan';
const TABLE_BIAYA_PAKAN = 'biaya_pakan';
const TABLE_BIAYA_PENDUKUNG = 'biaya_pendukung';
const TABLE_PANEN = 'panen';
const TABLE_USER = 'user'; // Tambahkan konstanta untuk tabel user

function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'check_related_data_error.log');
}

function sendJsonResponse($data) {
    echo json_encode($data);
    exit();
}

if (isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    if (!is_string($id) || empty($id)) {
        sendJsonResponse(['error' => 'Invalid ID']);
    }
    
    $hasRelatedData = false;
    $relatedDataInfo = [];
    
    try {
        switch ($table) {
            case TABLE_KOLAM:
                // Cek biaya pendukung terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pendukung WHERE id_kolam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pendukung";
                }

                // Cek data ikan
                $query = "SELECT COUNT(*) as count FROM data_ikan WHERE id_kolam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Ikan";
                }

                // Cek tebar bibit terkait
                $query = "SELECT COUNT(*) as count FROM tebar_bibit tb
                          JOIN data_ikan di ON tb.id_ikan = di.id_ikan
                          WHERE di.id_kolam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Tebar Bibit";
                }

                // Cek pemeliharaan ikan terkait
                $query = "SELECT COUNT(*) as count FROM pemeliharaan_ikan WHERE id_kolam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Pemeliharaan Ikan";
                }

                // Cek biaya pakan terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pakan WHERE id_kolam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pakan";
                }

                // Tambahkan pengecekan untuk panen
                $query = "SELECT COUNT(*) as count FROM panen WHERE id_kolam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Panen";
                }
                break;
            
            case TABLE_IKAN:
                // Cek biaya pendukung terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pendukung WHERE id_ikan = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pendukung";
                }

                // Cek tebar bibit terkait
                $query = "SELECT COUNT(*) as count FROM tebar_bibit WHERE id_ikan = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Tebar Bibit";
                }

                // Cek pemeliharaan ikan terkait
                $query = "SELECT COUNT(*) as count FROM pemeliharaan_ikan WHERE id_ikan = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Pemeliharaan Ikan";
                }

                // Cek biaya pakan terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pakan WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pakan";
                }

                // Tambahkan pengecekan untuk panen
                $query = "SELECT COUNT(*) as count FROM panen WHERE id_ikan = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Panen";
                }
                break;
            
            case TABLE_PAKAN:
                // Cek pemeliharaan ikan terkait
                $query = "SELECT COUNT(*) as count FROM pemeliharaan_ikan WHERE nama_pakan = (SELECT nama_pakan FROM data_pakan WHERE id_pakan = ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Pemeliharaan Ikan";
                }

                // Cek biaya pakan terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pakan WHERE nama_pakan = (SELECT nama_pakan FROM data_pakan WHERE id_pakan = ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pakan";
                }
                break;

            case TABLE_TEBAR_BIBIT:
                // Cek biaya pendukung terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pendukung WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?))";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pendukung";
                }

                // Cek pemeliharaan ikan terkait
                $query = "SELECT COUNT(*) as count FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Pemeliharaan Ikan";
                }

                // Cek biaya pakan terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pakan WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?))";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pakan";
                }

                // Tambahkan pengecekan untuk panen
                $query = "SELECT COUNT(*) as count FROM panen WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Panen";
                }
                break;

            case TABLE_PEMELIHARAAN:
                // Cek biaya pendukung terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pendukung WHERE id_pemeliharaan = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pendukung";
                }

                // Cek biaya pakan terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pakan WHERE id_pemeliharaan = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pakan";
                }

                // Tambahkan pengecekan untuk panen
                $query = "SELECT COUNT(*) as count FROM panen WHERE id_ikan = (SELECT id_ikan FROM pemeliharaan_ikan WHERE id_pemeliharaan = ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Panen";
                }
                break;

            case TABLE_BIAYA_PAKAN:
                // Tidak perlu memeriksa data terkait untuk biaya pakan
                break;

            case TABLE_BIAYA_PENDUKUNG:
                // Biaya pendukung tidak memiliki data terkait yang perlu diperiksa
                break;

            case TABLE_PANEN:
                // Panen tidak memiliki data terkait yang perlu diperiksa
                break;

            case TABLE_USER:
                // Cek panen terkait
                $query = "SELECT COUNT(*) as count FROM panen WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Panen";
                }

                // Cek biaya pendukung terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pendukung WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pendukung";
                }

                // Cek biaya pakan terkait
                $query = "SELECT COUNT(*) as count FROM biaya_pakan WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Biaya Pakan";
                }

                // Cek pemeliharaan ikan terkait
                $query = "SELECT COUNT(*) as count FROM pemeliharaan_ikan WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Pemeliharaan Ikan";
                }

                // Cek tebar bibit terkait
                $query = "SELECT COUNT(*) as count FROM tebar_bibit WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Tebar Bibit";
                }

                // Cek data ikan terkait
                $query = "SELECT COUNT(*) as count FROM data_ikan WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Ikan";
                }

                // Cek data kolam terkait
                $query = "SELECT COUNT(*) as count FROM data_kolam WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Kolam";
                }

                // Cek data pakan terkait
                $query = "SELECT COUNT(*) as count FROM data_pakan WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row['count'] > 0) {
                    $hasRelatedData = true;
                    $relatedDataInfo[] = "Data Pakan";
                }
                break;

            default:
                sendJsonResponse(['error' => 'Invalid table']);
        }
        
        sendJsonResponse([
            'success' => true,
            'hasRelatedData' => $hasRelatedData,
            'relatedDataInfo' => implode(", ", $relatedDataInfo)
        ]);
    } catch (Exception $e) {
        logError("Error in check_related_data: " . $e->getMessage());
        sendJsonResponse(['error' => 'An error occurred while checking related data']);
    }
} else {
    sendJsonResponse(['error' => 'Invalid request parameters']);
}