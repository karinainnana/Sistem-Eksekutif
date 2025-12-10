<?php
/**
 * SPAB Functions
 * Fungsi-fungsi untuk mengelola data SPAB
 * Schema: id_spab, nama_sekolah, kabupaten, tahun, sumber_pendanaan, tingkatan
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Build WHERE clause from filters
 */
function buildSPABWhereClause($filters = []) {
    global $conn;
    $where = " WHERE 1=1";
    
    if (!empty($filters['kabupaten'])) {
        $kabupaten = mysqli_real_escape_string($conn, $filters['kabupaten']);
        $where .= " AND kabupaten = '$kabupaten'";
    }
    
    if (!empty($filters['tingkatan'])) {
        $tingkatan = mysqli_real_escape_string($conn, $filters['tingkatan']);
        $where .= " AND tingkatan = '$tingkatan'";
    }
    
    if (!empty($filters['tahun'])) {
        $tahun = mysqli_real_escape_string($conn, $filters['tahun']);
        $where .= " AND tahun = '$tahun'";
    }
    
    if (!empty($filters['sumber_pendanaan'])) {
        $pendanaan = mysqli_real_escape_string($conn, $filters['sumber_pendanaan']);
        $where .= " AND sumber_pendanaan LIKE '%$pendanaan%'";
    }
    
    return $where;
}

/**
 * Get all SPAB data with optional filters
 */
function getAllSPAB($filters = [], $order = 'ASC') {
    global $conn;
    $where = buildSPABWhereClause($filters);
    $query = "SELECT * FROM spab $where ORDER BY nama_sekolah $order";
    return mysqli_query($conn, $query);
}

/**
 * Get SPAB by ID
 */
function getSPABById($id) {
    global $conn;
    $id = (int)$id;
    $query = "SELECT * FROM spab WHERE id_spab = $id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

/**
 * Add new SPAB
 */
function addSPAB($data) {
    global $conn;
    
    $nama_sekolah = mysqli_real_escape_string($conn, $data['nama_sekolah']);
    $kabupaten = mysqli_real_escape_string($conn, $data['kabupaten']);
    $tahun = mysqli_real_escape_string($conn, $data['tahun']);
    $sumber_pendanaan = mysqli_real_escape_string($conn, $data['sumber_pendanaan']);
    $tingkatan = mysqli_real_escape_string($conn, $data['tingkatan']);
    
    $query = "INSERT INTO spab (nama_sekolah, kabupaten, tahun, sumber_pendanaan, tingkatan) 
              VALUES ('$nama_sekolah', '$kabupaten', '$tahun', '$sumber_pendanaan', '$tingkatan')";
    
    return mysqli_query($conn, $query);
}

/**
 * Update SPAB
 */
function updateSPAB($id, $data) {
    global $conn;
    
    $id = (int)$id;
    $nama_sekolah = mysqli_real_escape_string($conn, $data['nama_sekolah']);
    $kabupaten = mysqli_real_escape_string($conn, $data['kabupaten']);
    $tahun = mysqli_real_escape_string($conn, $data['tahun']);
    $sumber_pendanaan = mysqli_real_escape_string($conn, $data['sumber_pendanaan']);
    $tingkatan = mysqli_real_escape_string($conn, $data['tingkatan']);
    
    $query = "UPDATE spab SET 
              nama_sekolah = '$nama_sekolah',
              kabupaten = '$kabupaten',
              tahun = '$tahun',
              sumber_pendanaan = '$sumber_pendanaan',
              tingkatan = '$tingkatan'
              WHERE id_spab = $id";
    
    return mysqli_query($conn, $query);
}

/**
 * Delete SPAB
 */
function deleteSPAB($id) {
    global $conn;
    $id = (int)$id;
    $query = "DELETE FROM spab WHERE id_spab = $id";
    return mysqli_query($conn, $query);
}

/**
 * Count total SPAB with optional filters
 */
function countTotalSPAB($filters = []) {
    global $conn;
    $where = buildSPABWhereClause($filters);
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM spab $where");
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}

/**
 * Count SPAB by tingkatan
 */
function countSPABByTingkatan($tingkatan) {
    global $conn;
    $tingkatan = mysqli_real_escape_string($conn, $tingkatan);
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM spab WHERE tingkatan = '$tingkatan'");
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}

/**
 * Get SPAB data grouped by year (for charts) - with filters
 */
function getSPABByYear($filters = []) {
    global $conn;
    $where = buildSPABWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT tahun as label, COUNT(*) as value FROM spab $where GROUP BY tahun ORDER BY tahun ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => (string)$row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get SPAB data grouped by kabupaten (for charts) - with filters
 */
function getSPABByKabupaten($filters = []) {
    global $conn;
    $where = buildSPABWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT kabupaten as label, COUNT(*) as value FROM spab $where GROUP BY kabupaten ORDER BY value DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get SPAB data grouped by tingkatan (for charts) - with filters
 */
function getSPABByTingkatan($filters = []) {
    global $conn;
    $where = buildSPABWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT tingkatan as label, COUNT(*) as value FROM spab $where GROUP BY tingkatan ORDER BY tingkatan ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get SPAB data grouped by sumber pendanaan (for charts) - with filters
 */
function getSPABByPendanaan($filters = []) {
    global $conn;
    $where = buildSPABWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT sumber_pendanaan as label, COUNT(*) as value FROM spab $where GROUP BY sumber_pendanaan ORDER BY value DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get list of kabupaten with count
 */
function getKabupatenList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT kabupaten, COUNT(*) as total FROM spab GROUP BY kabupaten ORDER BY kabupaten ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['kabupaten'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get list of sumber pendanaan with count
 */
function getPendanaanList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT sumber_pendanaan, COUNT(*) as total FROM spab GROUP BY sumber_pendanaan ORDER BY sumber_pendanaan ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['sumber_pendanaan'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get list of tingkatan with count
 */
function getTingkatanList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT tingkatan, COUNT(*) as total FROM spab GROUP BY tingkatan ORDER BY FIELD(tingkatan, 'TK', 'SD', 'SMP', 'SMA', 'SLB')");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['tingkatan'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get list of tahun with count
 */
function getTahunList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT tahun, COUNT(*) as total FROM spab GROUP BY tahun ORDER BY tahun DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['tahun'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get SPAB by pendanaan APBD per kabupaten
 */
function getSPABAPBDByKabupaten() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT kabupaten as label, COUNT(*) as value FROM spab WHERE sumber_pendanaan LIKE '%APBD%' GROUP BY kabupaten ORDER BY value DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}
?>
