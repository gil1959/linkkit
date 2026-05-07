# Remove UTF-8 BOM dari Router.php yang dirusak PowerShell
$file = "app\core\Router.php"
$bytes = [System.IO.File]::ReadAllBytes($file)

# Cek apakah ada BOM (EF BB BF)
if($bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
    Write-Host "BOM ditemukan! Menghapus..."
    $newBytes = $bytes[3..($bytes.Length - 1)]
    [System.IO.File]::WriteAllBytes($file, $newBytes)
    Write-Host "BOM berhasil dihapus. File size: $($newBytes.Length) bytes"
} else {
    Write-Host "Tidak ada BOM. Bytes 0-2: $($bytes[0]) $($bytes[1]) $($bytes[2])"
    Write-Host "File size: $($bytes.Length) bytes"
}
