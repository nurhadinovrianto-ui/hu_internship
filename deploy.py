import os
import zipfile
import paramiko
import time

# ==========================================
# KONFIGURASI VPS & DEPLOYMENT
# ==========================================
HOST = "103.180.167.59"
PORT = 2025
USER = "horizon"
PASSWORD = "FICT2024"
REMOTE_DIR = "/var/www/kms-fict.horizon.ac.id/wordpress/internship"

# Konfigurasi Database (Telah disiapkan jika suatu saat ingin auto-sync DB)
DB_USER = "root"
DB_PASS = "JB@yu19&2025"
# DB_HOST = "localhost"

# File sementara yang digunakan untuk transfer
ZIP_FILENAME = "deploy_internship.zip"
EXCLUDE_DIRS = ['vendor', 'node_modules', '.git', '.idea', 
                'storage/framework/cache', 'storage/framework/views', 'storage/logs']
EXCLUDE_FILES = ['.env', 'deploy.py', ZIP_FILENAME]

def zip_project(output_filename):
    print(f"[1/4] Membuat arsip proyek (mengecualikan vendor & node_modules)...")
    with zipfile.ZipFile(output_filename, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk('.'):
            # Normalisasi path root agar cocok dengan format EXCLUDE_DIRS (menggunakan slash forward)
            current_path = root.replace('\\', '/').lstrip('./')
            
            # Hapus folder dari list 'dirs' jika path gabungannya ada di EXCLUDE_DIRS
            # Ini mencegah os.walk masuk ke dalam folder tersebut sama sekali
            dirs[:] = [d for d in dirs if not any((current_path + '/' + d).strip('/') == ex or (current_path + '/' + d).strip('/').startswith(ex + '/') for ex in EXCLUDE_DIRS)]
                
            for file in files:
                if file in EXCLUDE_FILES or file.endswith('.zip'):
                    continue
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, '.')
                zipf.write(file_path, arcname)
    print("      [OK] Zip berhasil dibuat.")

def upload_to_vps(local_file, remote_path):
    print(f"[2/4] Menghubungkan ke VPS {HOST}:{PORT} via SFTP...")
    transport = paramiko.Transport((HOST, PORT))
    try:
        transport.connect(username=USER, password=PASSWORD)
        sftp = paramiko.SFTPClient.from_transport(transport)
        remote_file_path = f"{remote_path}/{local_file}"
        
        print(f"      Mengunggah {local_file} ke {remote_file_path} ...")
        
        def print_progress(transferred, tobe_transferred):
            percent = (transferred / tobe_transferred) * 100
            # Print over the same line
            print(f"\r      Progress Upload: {percent:.2f}% ({transferred}/{tobe_transferred} bytes)", end="", flush=True)

        sftp.put(local_file, remote_file_path, callback=print_progress)
        print("\n      [OK] Upload selesai.")
    finally:
        if 'sftp' in locals(): sftp.close()
        transport.close()

def run_remote_commands():
    print(f"[3/4] Menjalankan perintah post-deploy di VPS...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try:
        client.connect(hostname=HOST, port=PORT, username=USER, password=PASSWORD)
        
        commands = [
            # 1. Ekstrak zip (-o untuk menimpa file tanpa konfirmasi, -q agar tidak spam log)
            f"cd {REMOTE_DIR} && unzip -q -o {ZIP_FILENAME}",
            # 2. Hapus file zip di server
            f"cd {REMOTE_DIR} && rm {ZIP_FILENAME}",
            # 3. Install composer dependensi (tanpa dev package)
            f"cd {REMOTE_DIR} && composer install --no-dev --optimize-autoloader",
            # 4. Clear & cache config/routes/views Laravel
            f"cd {REMOTE_DIR} && php artisan optimize:clear",
            f"cd {REMOTE_DIR} && php artisan optimize"
        ]
        
        for cmd in commands:
            print(f"      Eksekusi: {cmd}")
            stdin, stdout, stderr = client.exec_command(cmd)
            # Tunggu hingga perintah selesai
            exit_status = stdout.channel.recv_exit_status()
            out = stdout.read().decode().strip()
            err = stderr.read().decode().strip()
            
            if out: 
                # Menampilkan 5 baris pertama output jika terlalu panjang
                lines = out.split('\n')
                print(f"        Output: {lines[0]}")
                if len(lines) > 1: print(f"        ... ({len(lines)-1} baris lainnya)")
            if err:
                print(f"        Error/Warning: {err}")
            
        print("      [OK] Perintah remote selesai.")
    finally:
        client.close()

if __name__ == "__main__":
    start_time = time.time()
    print("=== Memulai proses deployment Laravel ===")
    
    try:
        zip_project(ZIP_FILENAME)
        upload_to_vps(ZIP_FILENAME, REMOTE_DIR)
        run_remote_commands()
        
        print(f"[4/4] Membersihkan file lokal...")
        if os.path.exists(ZIP_FILENAME):
            os.remove(ZIP_FILENAME)
            print("      [OK] File zip lokal dihapus.")
            
        duration = time.time() - start_time
        print(f"\n=== Deployment Berhasil (Selesai dalam {duration:.1f} detik) ===")
    except Exception as e:
        print(f"\n[ERROR] Deployment gagal: {e}")
        # Hapus sisa zip lokal jika gagal
        if os.path.exists(ZIP_FILENAME):
            os.remove(ZIP_FILENAME)
