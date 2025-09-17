🧑‍💼 Simple HRIS

A Human Resource Information System built with Laravel + Filament + Spatie

🇮🇩 Versi Bahasa Indonesia
📌 Deskripsi

Simple HRIS adalah aplikasi Human Resource Information System sederhana yang dibangun menggunakan Laravel, Filament Admin, dan Spatie Laravel Permission.
Sistem ini memiliki tiga peran utama:

👑 Super Admin

🧑‍💼 HRD

👷 Employee (Karyawan)

✨ Fitur Utama (MVP)

🆕 Membuat akun baru.

📝 HRD dapat melakukan approve/reject resign.

⏱️ Karyawan dapat mengajukan lembur (maksimal 3 kali per minggu).

➕ Fitur Tambahan
HRD

👨‍👩‍👦 CRUD Employee

📋 Melihat absen semua karyawan

Employee

🔎 Melihat data pribadi dari HRD

📤 Mengajukan resign (dapat CRUD jika status pending, tidak dapat edit jika rejected atau success)

⏱️ Mengajukan lembur

📊 Hak Akses Berdasarkan Role
Fitur	Super Admin	HRD	Employee
Membuat Akun	✅	❌	❌
Approve/Reject Resign	❌	✅	❌
Mengajukan Lembur (maks 3/minggu)	❌	❌	✅
CRUD Employee	❌	✅	❌
Melihat Absen Semua Karyawan	❌	✅	❌
Melihat Data Pribadi	❌	❌	✅
Mengajukan & CRUD Resign (kondisional)	❌	❌	✅
⚙️ Teknologi

Laravel

Filament Admin

Spatie Laravel Permission

🚀 Instalasi
# 1. Clone repository
git clone https://github.com/yourusername/simple-hris.git
cd simple-hris

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Migrasi & seeding database
php artisan migrate --seed

# 5. Jalankan server lokal
php artisan serve

👤 Default Roles & Credentials
Role	Email	Password
Super Admin	admin@example.com	password
HRD	hrd@example.com	password
Employee	employee@example.com	password
📄 Lisensi

Proyek ini dirilis dengan lisensi MIT.

🇬🇧 English Version
📌 Description

Simple HRIS is a lightweight Human Resource Information System built with Laravel, Filament Admin, and Spatie Laravel Permission.
The system supports three main roles:

👑 Super Admin

🧑‍💼 HRD (Human Resource Department)

👷 Employee

✨ Core Features (MVP)

🆕 Create new accounts.

📝 HRD can approve/reject resignation requests.

⏱️ Employees can request overtime (max 3 times per week).

➕ Additional Features
HRD

👨‍👩‍👦 CRUD Employee

📋 View all employees’ attendance

Employee

🔎 View personal data provided by HRD

📤 Submit resignation (can CRUD if status is pending, cannot edit if rejected or approved)

⏱️ Submit overtime requests

📊 Role & Feature Access
Feature	Super Admin	HRD	Employee
Create Account	✅	❌	❌
Approve/Reject Resignation	❌	✅	❌
Overtime Request (max 3/week)	❌	❌	✅
CRUD Employee	❌	✅	❌
View All Attendance	❌	✅	❌
View Personal Data	❌	❌	✅
Submit & Manage Resignation (conditional)	❌	❌	✅
⚙️ Technologies

Laravel

Filament Admin

Spatie Laravel Permission

🚀 Installation
# 1. Clone repository
git clone https://github.com/yourusername/simple-hris.git
cd simple-hris

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Migrate & seed database
php artisan migrate --seed

# 5. Run local server
php artisan serve

👤 Default Roles & Credentials
Role	Email	Password
Super Admin	admin@example.com	password
HRD	hrd@example.com	password
Employee	employee@example.com	password
📄 License

This project is released under the MIT License.