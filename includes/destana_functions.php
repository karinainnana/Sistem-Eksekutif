<?php
/**
 * DESTANA Functions
 * Fungsi-fungsi untuk mengelola data DESTANA
 * Schema: id_destana, desa, kecamatan, kabupaten, tahun_pembentukan, sumber_pendanaan, indeks, tingkat
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Build WHERE clause from filters
 */
function buildDESTANAWhereClause($filters = []) {
    global $conn;
    $where = " WHERE 1=1";
    
    if (!empty($filters['kabupaten'])) {
        $kabupaten = mysqli_real_escape_string($conn, $filters['kabupaten']);
        $where .= " AND kabupaten = '$kabupaten'";
    }
    
    if (!empty($filters['kecamatan'])) {
        $kecamatan = mysqli_real_escape_string($conn, $filters['kecamatan']);
        $where .= " AND kecamatan = '$kecamatan'";
    }
    
    if (!empty($filters['tingkat'])) {
        $tingkat = mysqli_real_escape_string($conn, $filters['tingkat']);
        $where .= " AND tingkat = '$tingkat'";
    }
    
    if (!empty($filters['tahun_pembentukan'])) {
        $tahun = mysqli_real_escape_string($conn, $filters['tahun_pembentukan']);
        $where .= " AND tahun_pembentukan = '$tahun'";
    }
    
    return $where;
}

/**
 * Get all DESTANA data with optional filters
 */
function getAllDESTANA($filters = [], $order = 'ASC') {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $query = "SELECT * FROM destana $where ORDER BY desa $order";
    return mysqli_query($conn, $query);
}

/**
 * Get DESTANA by ID
 */
function getDESTANAById($id) {
    global $conn;
    $id = (int)$id;
    $query = "SELECT * FROM destana WHERE id_destana = $id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

/**
 * Add new DESTANA
 */
function addDESTANA($data) {
    global $conn;
    
    $desa = mysqli_real_escape_string($conn, $data['desa']);
    $kecamatan = mysqli_real_escape_string($conn, $data['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $data['kabupaten']);
    $tahun_pembentukan = mysqli_real_escape_string($conn, $data['tahun_pembentukan']);
    $sumber_pendanaan = mysqli_real_escape_string($conn, $data['sumber_pendanaan']);
    $indeks = floatval($data['indeks']);
    $tingkat = mysqli_real_escape_string($conn, $data['tingkat']);
    
    $query = "INSERT INTO destana (desa, kecamatan, kabupaten, tahun_pembentukan, sumber_pendanaan, indeks, tingkat) 
              VALUES ('$desa', '$kecamatan', '$kabupaten', '$tahun_pembentukan', '$sumber_pendanaan', $indeks, '$tingkat')";
    
    return mysqli_query($conn, $query);
}

/**
 * Update DESTANA
 */
function updateDESTANA($id, $data) {
    global $conn;
    
    $id = (int)$id;
    $desa = mysqli_real_escape_string($conn, $data['desa']);
    $kecamatan = mysqli_real_escape_string($conn, $data['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $data['kabupaten']);
    $tahun_pembentukan = mysqli_real_escape_string($conn, $data['tahun_pembentukan']);
    $sumber_pendanaan = mysqli_real_escape_string($conn, $data['sumber_pendanaan']);
    $indeks = floatval($data['indeks']);
    $tingkat = mysqli_real_escape_string($conn, $data['tingkat']);
    
    $query = "UPDATE destana SET 
              desa = '$desa',
              kecamatan = '$kecamatan',
              kabupaten = '$kabupaten',
              tahun_pembentukan = '$tahun_pembentukan',
              sumber_pendanaan = '$sumber_pendanaan',
              indeks = $indeks,
              tingkat = '$tingkat'
              WHERE id_destana = $id";
    
    return mysqli_query($conn, $query);
}

/**
 * Delete DESTANA
 */
function deleteDESTANA($id) {
    global $conn;
    $id = (int)$id;
    $query = "DELETE FROM destana WHERE id_destana = $id";
    return mysqli_query($conn, $query);
}

/**
 * Count total DESTANA with optional filters
 */
function countTotalDESTANA($filters = []) {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM destana $where");
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}

/**
 * Count DESTANA by tingkat
 */
function countDESTANAByTingkat($tingkat) {
    global $conn;
    $tingkat = mysqli_real_escape_string($conn, $tingkat);
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM destana WHERE tingkat = '$tingkat'");
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}

/**
 * Get DESTANA data grouped by year (for charts) - with filters
 */
function getDESTANAByYear($filters = []) {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT tahun_pembentukan as label, COUNT(*) as value FROM destana $where GROUP BY tahun_pembentukan ORDER BY tahun_pembentukan ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => (string)$row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get DESTANA data grouped by kabupaten (for charts) - with filters
 */
function getDESTANAByKabupaten($filters = []) {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT kabupaten as label, COUNT(*) as value FROM destana $where GROUP BY kabupaten ORDER BY value DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get DESTANA data grouped by tingkat (for charts) - with filters
 */
function getDESTANAByTingkat($filters = []) {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT tingkat as label, COUNT(*) as value FROM destana $where GROUP BY tingkat ORDER BY FIELD(tingkat, 'Tangguh Pratama', 'Tangguh Madya', 'Tangguh Utama')");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get DESTANA data grouped by sumber pendanaan (for charts) - with filters
 */
function getDESTANAByPendanaan($filters = []) {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT sumber_pendanaan as label, COUNT(*) as value FROM destana $where GROUP BY sumber_pendanaan ORDER BY value DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => (int)$row['value']];
    }
    return $data;
}

/**
 * Get average indeks by kabupaten - with filters
 */
function getAvgIndeksByKabupaten($filters = []) {
    global $conn;
    $where = buildDESTANAWhereClause($filters);
    $data = [];
    $result = mysqli_query($conn, "SELECT kabupaten as label, ROUND(AVG(indeks), 2) as value FROM destana $where GROUP BY kabupaten ORDER BY value DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['label' => $row['label'], 'value' => floatval($row['value'])];
    }
    return $data;
}

/**
 * Get list of kabupaten with count
 */
function getDestanaKabupatenList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT kabupaten, COUNT(*) as total FROM destana GROUP BY kabupaten ORDER BY kabupaten ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['kabupaten'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get list of kecamatan with count
 */
function getKecamatanList($kabupaten = null) {
    global $conn;
    $data = [];
    $query = "SELECT kecamatan, COUNT(*) as total FROM destana";
    if ($kabupaten) {
        $kabupaten = mysqli_real_escape_string($conn, $kabupaten);
        $query .= " WHERE kabupaten = '$kabupaten'";
    }
    $query .= " GROUP BY kecamatan ORDER BY kecamatan ASC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['kecamatan'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get list of tingkat with count
 */
function getTingkatList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT tingkat, COUNT(*) as total FROM destana GROUP BY tingkat ORDER BY FIELD(tingkat, 'Tangguh Pratama', 'Tangguh Madya', 'Tangguh Utama')");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['tingkat'], 'total' => (int)$row['total']];
    }
    return $data;
}

/**
 * Get list of tahun pembentukan with count
 */
function getDestanaTahunList() {
    global $conn;
    $data = [];
    $result = mysqli_query($conn, "SELECT tahun_pembentukan, COUNT(*) as total FROM destana GROUP BY tahun_pembentukan ORDER BY tahun_pembentukan DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = ['nama' => $row['tahun_pembentukan'], 'total' => (int)$row['total']];
    }
    return $data;
}
?>
