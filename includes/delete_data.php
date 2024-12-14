<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Ambil peran pengguna dari sesi

function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'delete_error.log');
}

function deleteDataKolam($conn, $delete_id, $user_id, $isAdmin) {
    mysqli_begin_transaction($conn);
    try {
        if ($isAdmin) {
            $delete_panen_query = "DELETE FROM panen WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_panen_query = "DELETE FROM panen WHERE id_kolam = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pendukung terkait
        if ($isAdmin) {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_kolam = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pakan terkait
        if ($isAdmin) {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_kolam = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus pemeliharaan ikan terkait
        if ($isAdmin) {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_kolam = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus tebar bibit terkait
        if ($isAdmin) {
            $delete_tebar_bibit_query = "DELETE tb FROM tebar_bibit tb
                                         JOIN data_ikan di ON tb.id_ikan = di.id_ikan
                                         WHERE di.id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_tebar_bibit_query = "DELETE tb FROM tebar_bibit tb
                                         JOIN data_ikan di ON tb.id_ikan = di.id_ikan
                                         WHERE di.id_kolam = ? AND tb.user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data ikan terkait
        if ($isAdmin) {
            $delete_ikan_query = "DELETE FROM data_ikan WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_ikan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_ikan_query = "DELETE FROM data_ikan WHERE id_kolam = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_ikan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data kolam
        if ($isAdmin) {
            $delete_kolam_query = "DELETE FROM data_kolam WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $delete_kolam_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_kolam_query = "DELETE FROM data_kolam WHERE id_kolam = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_kolam_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return $result;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        logError("Error deleting kolam data: " . $e->getMessage());
        return false;
    }
}

function deleteDataIkan($conn, $delete_id, $user_id, $isAdmin) {
    mysqli_begin_transaction($conn);
    try {
        if ($isAdmin) {
            $delete_panen_query = "DELETE FROM panen WHERE id_ikan = ?";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_panen_query = "DELETE FROM panen WHERE id_ikan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pendukung terkait
        if ($isAdmin) {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_ikan = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_ikan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pakan terkait
        if ($isAdmin) {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = ?)";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = ? AND user_id = ?)";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus pemeliharaan ikan terkait
        if ($isAdmin) {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_ikan = ?";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_ikan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus tebar bibit terkait
        if ($isAdmin) {
            $delete_tebar_bibit_query = "DELETE FROM tebar_bibit WHERE id_ikan = ?";
            $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_tebar_bibit_query = "DELETE FROM tebar_bibit WHERE id_ikan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data ikan
        if ($isAdmin) {
            $delete_ikan_query = "DELETE FROM data_ikan WHERE id_ikan = ?";
            $stmt = mysqli_prepare($conn, $delete_ikan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_ikan_query = "DELETE FROM data_ikan WHERE id_ikan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_ikan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return $result;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        logError("Error deleting ikan data: " . $e->getMessage());
        return false;
    }
}

function deleteDataPakan($conn, $delete_id, $user_id, $isAdmin) {
    mysqli_begin_transaction($conn);
    try {
        if ($isAdmin) {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE nama_pakan = (SELECT nama_pakan FROM data_pakan WHERE id_pakan = ?)";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE nama_pakan = (SELECT nama_pakan FROM data_pakan WHERE id_pakan = ? AND user_id = ?)";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data pakan
        if ($isAdmin) {
            $delete_pakan_query = "DELETE FROM data_pakan WHERE id_pakan = ?";
            $stmt = mysqli_prepare($conn, $delete_pakan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_pakan_query = "DELETE FROM data_pakan WHERE id_pakan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_pakan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return $result;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        logError("Error deleting pakan data: " . $e->getMessage());
        return false;
    }
}

function deleteDataTebarBibit($conn, $delete_id, $user_id, $isAdmin) {
    mysqli_begin_transaction($conn);
    try {
        if ($isAdmin) {
            $delete_panen_query = "DELETE FROM panen WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?)";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_panen_query = "DELETE FROM panen WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ? AND user_id = ?)";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pendukung terkait
        if ($isAdmin) {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?))";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ? AND user_id = ?))";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pakan terkait
        if ($isAdmin) {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?))";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_pemeliharaan IN (SELECT id_pemeliharaan FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ? AND user_id = ?))";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus pemeliharaan ikan terkait
        if ($isAdmin) {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ?)";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_ikan = (SELECT id_ikan FROM tebar_bibit WHERE id_tebar = ? AND user_id = ?)";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus tebar bibit
        if ($isAdmin) {
            $delete_tebar_bibit_query = "DELETE FROM tebar_bibit WHERE id_tebar = ?";
            $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_tebar_bibit_query = "DELETE FROM tebar_bibit WHERE id_tebar = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return $result;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        logError("Error deleting tebar bibit data: " . $e->getMessage());
        return false;
    }
}

function deleteDataPemeliharaanIkan($conn, $delete_id, $user_id, $isAdmin) {
    mysqli_begin_transaction($conn);
    try {
        if ($isAdmin) {
            $delete_panen_query = "DELETE FROM panen WHERE id_ikan = (SELECT id_ikan FROM pemeliharaan_ikan WHERE id_pemeliharaan = ?)";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_panen_query = "DELETE FROM panen WHERE id_ikan = (SELECT id_ikan FROM pemeliharaan_ikan WHERE id_pemeliharaan = ? AND user_id = ?)";
            $stmt = mysqli_prepare($conn, $delete_panen_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pendukung terkait
        if ($isAdmin) {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_pemeliharaan = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_pemeliharaan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pakan terkait
        if ($isAdmin) {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_pemeliharaan = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_pemeliharaan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus pemeliharaan ikan
        if ($isAdmin) {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_pemeliharaan = ?";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
        } else {
            $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE id_pemeliharaan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
            mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
        }
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return $result;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        logError("Error deleting pemeliharaan ikan: " . $e->getMessage());
        return false;
    }
}

function deleteDataBiayaPakan($conn, $delete_id, $user_id, $isAdmin) {
    if ($isAdmin) {
        $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_biaya_pakan = ?";
        $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
    } else {
        $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE id_biaya_pakan = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
        mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
    }
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        logError("Error deleting biaya pakan: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function deleteDataBiayaPendukung($conn, $delete_id, $user_id, $isAdmin) {
    if ($isAdmin) {
        $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_biaya_pendukung = ?";
        $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
    } else {
        $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE id_biaya_pendukung = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
        mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
    }
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        logError("Error deleting biaya pendukung: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
    return $result;
}

// Fungsi baru untuk menghapus data panen
function deleteDataPanen($conn, $delete_id, $user_id, $isAdmin) {
    if ($isAdmin) {
        $delete_panen_query = "DELETE FROM panen WHERE id_panen = ?";
        $stmt = mysqli_prepare($conn, $delete_panen_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
    } else {
        $delete_panen_query = "DELETE FROM panen WHERE id_panen = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_panen_query);
        mysqli_stmt_bind_param($stmt, "si", $delete_id, $user_id);
    }
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        logError("Error deleting panen: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function deleteDataUser($conn, $delete_id, $user_id, $isAdmin) {
    mysqli_begin_transaction($conn);
    try {
        // Hapus panen terkait
        $delete_panen_query = "DELETE FROM panen WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_panen_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pendukung terkait
        $delete_biaya_pendukung_query = "DELETE FROM biaya_pendukung WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_biaya_pendukung_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus biaya pakan terkait
        $delete_biaya_pakan_query = "DELETE FROM biaya_pakan WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_biaya_pakan_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus pemeliharaan ikan terkait
        $delete_pemeliharaan_query = "DELETE FROM pemeliharaan_ikan WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_pemeliharaan_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus tebar bibit terkait
        $delete_tebar_bibit_query = "DELETE FROM tebar_bibit WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_tebar_bibit_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data ikan terkait
        $delete_ikan_query = "DELETE FROM data_ikan WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_ikan_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data kolam terkait
        $delete_kolam_query = "DELETE FROM data_kolam WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_kolam_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus data pakan terkait
        $delete_pakan_query = "DELETE FROM data_pakan WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_pakan_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus user itu sendiri
        $delete_user_query = "DELETE FROM user WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_user_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        return $result;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        logError("Error deleting user data: " . $e->getMessage());
        return false;
    }
}

function deleteData($conn, $table, $delete_id, $user_id, $isAdmin) {
    if (empty($delete_id) || (!is_numeric($user_id) && !$isAdmin)) {
        logError("Invalid delete_id or user_id");
        return false;
    }

    switch ($table) {
        case 'user':
            return deleteDataUser($conn, $delete_id, $user_id, $isAdmin);
        case 'data_kolam':
            return deleteDataKolam($conn, $delete_id, $user_id, $isAdmin);
        case 'data_ikan':
            return deleteDataIkan($conn, $delete_id, $user_id, $isAdmin);
        case 'data_pakan':
            return deleteDataPakan($conn, $delete_id, $user_id, $isAdmin);
        case 'tebar_bibit':
            return deleteDataTebarBibit($conn, $delete_id, $user_id, $isAdmin);
        case 'pemeliharaan_ikan':
            return deleteDataPemeliharaanIkan($conn, $delete_id, $user_id, $isAdmin);
        case 'biaya_pakan':
            return deleteDataBiayaPakan($conn, $delete_id, $user_id, $isAdmin);
        case 'biaya_pendukung':
            return deleteDataBiayaPendukung($conn, $delete_id, $user_id, $isAdmin);
        case 'panen':
            return deleteDataPanen($conn, $delete_id, $user_id, $isAdmin);
        default:
            logError("Invalid table name: $table");
            return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && isset($_POST['table'])) {
    $delete_id = $_POST['delete_id'];
    $table = $_POST['table'];
    $return_page = $_POST['return_page'];
    $isAdmin = ($user_role === 'admin'); // Periksa apakah pengguna adalah admin
    
    $result = deleteData($conn, $table, $delete_id, $user_id, $isAdmin);
    logError("Delete attempt: Table: $table, ID: $delete_id, Result: " . ($result ? "Success" : "Fail"));
    if ($result) {
        echo json_encode(['success' => true, 'message' => "Data berhasil dihapus"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Gagal menghapus data: " . mysqli_error($conn)]);
    }
    exit();
}
           