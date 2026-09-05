import paramiko
import traceback

host = '103.180.167.59'
port = 2025
username = 'horizon'
password = 'FICT2024'
remote_path = '/var/www/kms-fict.horizon.ac.id/wordpress/internship/patch2.zip'
local_path = 'patch2.zip'
extract_path = '/var/www/kms-fict.horizon.ac.id/wordpress/internship'

try:
    print('Connecting via SSH...')
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(hostname=host, port=port, username=username, password=password, timeout=10)

    print('Uploading file via SFTP...')
    sftp = ssh.open_sftp()
    sftp.put(local_path, remote_path)
    sftp.close()
    
    print('Extracting file on remote server...')
    stdin, stdout, stderr = ssh.exec_command(f'cd {extract_path} && unzip -o patch2.zip')
    
    exit_status = stdout.channel.recv_exit_status()
    print('Exit status:', exit_status)
    
    print('Stdout:', stdout.read().decode())
    
    err = stderr.read().decode()
    if err:
        print('Stderr:', err)

    ssh.close()
    print('Deployment completed successfully!')
except Exception as e:
    print('Error:', e)
    traceback.print_exc()
