import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.180.167.59', 2025, 'horizon', 'FICT2024')
stdin,stdout,stderr=client.exec_command('sed -i "s/APP_DEBUG=false/APP_DEBUG=true/g" /var/www/kms-fict.horizon.ac.id/wordpress/internship/.env && cd /var/www/kms-fict.horizon.ac.id/wordpress/internship && php artisan optimize:clear')
print(stdout.read().decode())
