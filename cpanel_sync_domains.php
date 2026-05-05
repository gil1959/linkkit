<?php

$cpanel_host = 'https://127.0.0.1:2083'; // Ganti dengan IP atau hostname cPanel  (pakai https dan port 2083)
$cpanel_user = 'USERNAME_CPANEL_LU';         // Ganti dengan username login cPanel 
$cpanel_api_token = 'API_TOKEN_CPANEL_LU';   // Ganti dengan API Token dari cPanel (Cara buatnya ada di bawah)

// Load konfigurasi database LinkKit
require_once __DIR__ . '/config.php';

// Fungsi untuk request ke cPanel API (Mendukung UAPI dan API 2)
function cpanel_api_request($endpoint, $host, $user, $token, $is_api2 = false) {
    $ch = curl_init();
    
    if ($is_api2) {
        $url = $host . '/json-api/cpanel?cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=' . $endpoint;
    } else {
        $url = $host . '/execute/' . $endpoint;
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: cpanel $user:$token"
    ]);
    // Abaikan SSL certificate verification kalau IP belum pakai SSL resmi
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($result, true);
}

try {
    // 2. Koneksi ke Database LinkKit
    $pdo = new PDO("mysql:host=" . DATABASE_SERVER . ";dbname=" . DATABASE_NAME . ";charset=utf8", DATABASE_USERNAME, DATABASE_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2.5. Bikin kolom is_cpanel_synced kalau belum ada
    try {
        $pdo->query("SELECT is_cpanel_synced FROM domains LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE domains ADD COLUMN is_cpanel_synced TINYINT(1) DEFAULT 0");
    }

    // 3. Ambil domain yang belum sinkron ke cPanel (is_cpanel_synced = 0)
    $stmt = $pdo->query("SELECT host FROM domains WHERE is_cpanel_synced = 0 AND type = 0");
    $db_domains = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Hilangkan spasi atau karakternya biar aman
        $db_domains[] = trim(strtolower($row['host']));
    }

    // 4. Ambil list Parked Domains / Aliases dari cPanel API
    echo "Sedang mengecek domain di cPanel...\n";
    $cpanel_response = cpanel_api_request('DomainInfo/list_domains', $cpanel_host, $cpanel_user, $cpanel_api_token);
    
    if (isset($cpanel_response['errors']) && !empty($cpanel_response['errors'])) {
        die("Error cPanel API: " . implode(", ", $cpanel_response['errors']));
    }

    $cpanel_parked_domains = [];
    if (isset($cpanel_response['data']['parked_domains'])) {
        $cpanel_parked_domains = array_map('strtolower', $cpanel_response['data']['parked_domains']);
    }

    // 5. Bandingkan dan Tambahkan ke cPanel kalau belum ada
    $added_count = 0;
    foreach ($db_domains as $domain) {
        $need_to_add = !in_array($domain, $cpanel_parked_domains);
        
        if ($need_to_add) {
            echo "Domain baru terdeteksi: $domain. Sedang mendaftarkan ke cPanel...\n";
            // Tembak API buat nambahin domain sbg Parked Domain (Alias) menggunakan cPanel API 2
            $park_response = cpanel_api_request("Park&cpanel_jsonapi_func=park&domain=" . urlencode($domain), $cpanel_host, $cpanel_user, $cpanel_api_token, true);
        } else {
            // Kalau udah ada di cPanel tapi di DB masih 0, kita tetep anggap sukses buat diupdate DB-nya
            $park_response = []; 
        }
            
        // Format response API 2 berbeda dengan UAPI
        if (isset($park_response['cpanelresult']['error'])) {
            echo "Gagal menambahkan $domain: " . $park_response['cpanelresult']['error'] . "\n";
        } else {
            if ($need_to_add) {
                echo "SUKSES: Domain $domain berhasil ditambahkan ke cPanel!\n";
            }
            
            // UPDATE database LinkKit jadi Active (1) dan Synced (1)
            $update_stmt = $pdo->prepare("UPDATE domains SET is_enabled = 1, is_cpanel_synced = 1 WHERE host = ?");
            $update_stmt->execute([$domain]);
            echo "SUKSES: Status domain $domain diubah jadi Active & Synced di database LinkKit!\n";
            
            $added_count++;
        }
    }

    echo "\nProses selesai. Total $added_count domain baru didaftarkan.\n";

    // 6. Kalau ada domain baru, panggil AutoSSL biar langsung dibuatin SSL-nya
    if ($added_count > 0) {
        echo "Memicu AutoSSL untuk generate sertifikat HTTPS...\n";
        $autossl_response = cpanel_api_request('SSL/start_autossl_check', $cpanel_host, $cpanel_user, $cpanel_api_token);
        if (isset($autossl_response['errors']) && !empty($autossl_response['errors'])) {
            echo "AutoSSL trigger error: " . implode(", ", $autossl_response['errors']) . "\n";
        } else {
            echo "AutoSSL berhasil dipicu! Sertifikat SSL akan aktif dalam 1-2 menit.\n";
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
