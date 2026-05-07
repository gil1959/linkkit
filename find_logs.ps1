Get-ChildItem -Recurse -Filter "*.log" -ErrorAction SilentlyContinue | Sort-Object LastWriteTime -Descending | Select-Object -First 5 FullName, Length, LastWriteTime
Write-Host "---"
if(Test-Path "php_errors.log") { Get-Content "php_errors.log" -Tail 30 }
if(Test-Path "error.log") { Get-Content "error.log" -Tail 30 }
