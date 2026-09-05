param(
    [string]$HostIp = "103.180.167.59",
    [string]$User = "horizon",
    [string]$RemotePath = "/var/www/kms-fict.horizon.ac.id/wordpress/internship"
)

Write-Host "Creating update archive..." -ForegroundColor Cyan
if (Test-Path "update.zip") { Remove-Item "update.zip" -Force }
Compress-Archive -Path app, bootstrap, config, database, resources, routes -DestinationPath update.zip -Force
Write-Host "Archive created successfully (update.zip)." -ForegroundColor Green

Write-Host "`nTransferring to VPS via SCP..." -ForegroundColor Cyan
Write-Host "Please enter the password 'FICT2024' when prompted." -ForegroundColor Yellow
scp update.zip ${User}@${HostIp}:${RemotePath}/update.zip

Write-Host "`nRunning migrations on the remote server..." -ForegroundColor Cyan
Write-Host "Please enter the password 'FICT2024' when prompted." -ForegroundColor Yellow
$sshCommand = "cd $RemotePath && unzip -o update.zip && php artisan migrate --force"
ssh ${User}@${HostIp} $sshCommand

Write-Host "`nDeployment process finished." -ForegroundColor Green
