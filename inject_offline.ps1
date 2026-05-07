$file = "themes\altum\views\store_checkout\index.php"
$content = [System.IO.File]::ReadAllText($file)

$offlinePanel = @"

                    <?php if(!empty(`$data->offline_instructions)): ?>
                    <div class="offline-panel" id="offlinePanel">
                        <div class="offline-panel-title"><i class="fas fa-university fa-sm"></i> Instruksi Transfer Bank</div>
                        <div class="offline-instructions"><?= nl2br(htmlspecialchars(`$data->offline_instructions)) ?></div>
                        <p style="font-size:.75rem;color:#92400e;margin:10px 0 0"><i class="fas fa-info-circle fa-xs"></i> Transfer sejumlah total di bawah, lalu upload bukti di halaman konfirmasi.</p>
                    </div>
                    <?php endif ?>

                    <button type="submit" class="btn-pay" id="btnPay">
                        <i class="fas fa-lock"></i>
                        <span id="btnPayText">Bayar Sekarang</span>
                    </button>
"@

$oldBtn = "                    <button type=""submit"" class=""btn-pay"">
                        <i class=""fas fa-lock""></i>
                        Bayar Sekarang
                    </button>"

$content = $content.Replace($oldBtn, $offlinePanel)
[System.IO.File]::WriteAllText($file, $content, [System.Text.Encoding]::UTF8)
Write-Host "Done. Size: $($content.Length)"
