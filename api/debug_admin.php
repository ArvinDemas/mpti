<?php
// FILE: api/debug_admin.php
// Cek apa masalahnya dengan dashboard admin
header("Content-Type: text/html; charset=utf-8");
require_once 'db.php';

echo "<style>
body { font-family: monospace; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; border-radius: 8px; margin: 10px 0; }
.success { color: green; }
.error { color: red; }
pre { background: #f0f0f0; padding: 15px; border-left: 3px solid #4caf50; overflow-x: auto; }
</style>";

echo "<h1>🔍 Debug Admin Dashboard</h1>";

// 1. Cek Session
echo "<div class='box'>";
echo "<h2>1. Session Check</h2>";
if(isset($_SESSION['user'])) {
    echo "<p class='success'>✅ Session Active</p>";
    echo "<pre>" . print_r($_SESSION['user'], true) . "</pre>";
    
    if($_SESSION['user']['role'] !== 'admin') {
        echo "<p class='error'>⚠️ WARNING: User role is '" . $_SESSION['user']['role'] . "', not 'admin'</p>";
        echo "<p>Logout dan login sebagai admin!</p>";
    }
} else {
    echo "<p class='error'>❌ No Session - User belum login!</p>";
    echo "<p>Silakan login dulu di <a href='../index.html'>index.html</a></p>";
    die("</div>");
}
echo "</div>";

// 2. Cek Tabel ukt_appeals
echo "<div class='box'>";
echo "<h2>2. Tabel ukt_appeals</h2>";
$checkTable = $conn->query("SHOW TABLES LIKE 'ukt_appeals'");
if($checkTable && $checkTable->num_rows > 0) {
    echo "<p class='success'>✅ Tabel exists</p>";
    
    // Hitung jumlah data
    $count = $conn->query("SELECT COUNT(*) as total FROM ukt_appeals")->fetch_assoc();
    echo "<p>Total sanggah: <b>{$count['total']}</b></p>";
    
    if($count['total'] == 0) {
        echo "<p class='error'>⚠️ Tabel kosong! Tidak ada data sanggah.</p>";
        echo "<p><b>Solusi:</b> Login sebagai mahasiswa, ajukan sanggah UKT dulu</p>";
    }
} else {
    echo "<p class='error'>❌ Tabel tidak ditemukan!</p>";
    die("</div>");
}
echo "</div>";

// 3. Test Query Dashboard Admin
echo "<div class='box'>";
echo "<h2>3. Test Query Dashboard</h2>";
$sql = "SELECT a.*, u.name, u.username as nim 
        FROM ukt_appeals a 
        JOIN users u ON a.student_id = u.id 
        ORDER BY 
            CASE a.status 
                WHEN 'Menunggu' THEN 1 
                WHEN 'Disetujui' THEN 2 
                WHEN 'Ditolak' THEN 3 
            END,
            a.request_date DESC";

echo "<p>Query:</p>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

$result = $conn->query($sql);

if($result) {
    echo "<p class='success'>✅ Query berhasil</p>";
    $appeals = [];
    while($row = $result->fetch_assoc()) {
        $appeals[] = $row;
    }
    
    echo "<p>Jumlah data: <b>" . count($appeals) . "</b></p>";
    
    if(count($appeals) > 0) {
        echo "<h3>Data Preview:</h3>";
        echo "<pre>" . print_r($appeals, true) . "</pre>";
    } else {
        echo "<p class='error'>⚠️ Query sukses tapi tidak ada data</p>";
    }
} else {
    echo "<p class='error'>❌ Query error: " . $conn->error . "</p>";
}
echo "</div>";

// 4. Simulate API Response
echo "<div class='box'>";
echo "<h2>4. Simulated API Response</h2>";

$response = [
    "status" => "success", 
    "data" => ["appeals" => $appeals]
];

echo "<p>JSON yang akan dikirim ke frontend:</p>";
echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";

if(empty($appeals)) {
    echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107;margin-top:15px;'>";
    echo "<h3>⚠️ ROOT CAUSE FOUND!</h3>";
    echo "<p><b>Problem:</b> Tabel sanggah UKT kosong (tidak ada data)</p>";
    echo "<p><b>Solution:</b></p>";
    echo "<ol>";
    echo "<li>Logout dari admin</li>";
    echo "<li>Login sebagai mahasiswa (124230001 / password)</li>";
    echo "<li>Buka menu 'Sanggah UKT'</li>";
    echo "<li>Isi alasan dan klik 'Kirim'</li>";
    echo "<li>Logout dan login lagi sebagai admin</li>";
    echo "</ol>";
    echo "</div>";
}
echo "</div>";

// 5. Test Connection ke get_dashboard_admin.php
echo "<div class='box'>";
echo "<h2>5. Test API Endpoint</h2>";
echo "<p>Mencoba hit API endpoint secara langsung...</p>";

// Simulate API call
$_GET = []; // Clear GET params
ob_start();
include 'get_dashboard_admin.php';
$apiResponse = ob_get_clean();

echo "<p>Raw Response:</p>";
echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";

$decoded = json_decode($apiResponse, true);
if($decoded) {
    if($decoded['status'] === 'success') {
        echo "<p class='success'>✅ API Response Valid</p>";
    } else {
        echo "<p class='error'>❌ API Error: " . ($decoded['message'] ?? 'Unknown') . "</p>";
    }
} else {
    echo "<p class='error'>❌ Invalid JSON Response</p>";
}
echo "</div>";

// 6. Recommendations
echo "<div class='box'>";
echo "<h2>6. Recommendations</h2>";
echo "<ul>";
echo "<li>✅ Check browser console (F12) untuk JavaScript errors</li>";
echo "<li>✅ Test API directly: <a href='get_dashboard_admin.php' target='_blank'>get_dashboard_admin.php</a></li>";
echo "<li>✅ Pastikan ada data sanggah di database</li>";
echo "<li>✅ Clear browser cache dan reload</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align:center;margin-top:30px;'>";
echo "<a href='../index.html' style='background:#4caf50;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;'>← Back to Login</a>";
echo "</div>";
?>