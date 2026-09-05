import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.180.167.59', 2025, 'horizon', 'FICT2024')
stdin,stdout,stderr=client.exec_command('tail -n 30 /var/www/kms-fict.horizon.ac.id/wordpress/internship/storage/logs/laravel.log')
print(stdout.read().decode())
