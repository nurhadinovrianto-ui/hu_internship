import paramiko

host       = '103.180.167.59'
port       = 2025
username   = 'horizon'
password   = 'FICT2024'
remote_dir = '/var/www/kms-fict.horizon.ac.id/wordpress/internship'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=port, username=username, password=password, timeout=30)

stdin, stdout, stderr = ssh.exec_command(f"cd {remote_dir} && php artisan tinker --execute=\"print_r(config('filesystems.links')); print_r([public_path('storage'), storage_path('app/public')]);\"", timeout=30)
print(stdout.read().decode('utf-8'))
print(stderr.read().decode('utf-8'))
ssh.close()
