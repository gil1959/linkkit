$content = Get-Content 'app\core\Router.php' -Raw

$newRoute = @"

            'store-checkout-success' => [
                'controller' => 'StoreCheckoutSuccess',
                'settings' => [
                    'wrapper' => 'store_wrapper',
                    'no_authentication_check' => true,
                    'has_view' => true,
                ]
            ],
"@

# Insert after the store-checkout block
$pattern = "('store-checkout' => \[[\s\S]*?'has_view' => true,\s*\]\s*\],)"
$content = [regex]::Replace($content, $pattern, { param($m) $m.Groups[1].Value + $newRoute })

Set-Content 'app\core\Router.php' $content -Encoding UTF8
Write-Host "Done"
