# 🧑‍💼 Simple HRIS
_A Human Resource Information System built with Laravel + Filament + Spatie_

---

## 🇮🇩 Versi Bahasa Indonesia  

### 📌 Deskripsi
**Simple HRIS** adalah aplikasi Human Resource Information System sederhana yang dibangun menggunakan **Laravel**, **Filament Admin**, dan **Spatie Laravel Permission**.  
Sistem ini memiliki tiga peran utama:  

- 👑 **Super Admin**  
- 🧑‍💼 **HRD**  
- 👷 **Employee (Karyawan)**  

---

### ✨ Fitur Utama (MVP)
- 🆕 Membuat akun baru  
- 📝 HRD dapat melakukan **approve/reject resign**  
- ⏱️ Karyawan dapat mengajukan **lembur (maksimal 3 kali per minggu)**  

---

### ➕ Fitur Tambahan  
#### HRD  
- 👨‍👩‍👦 **CRUD Employee**  
- 📋 **Melihat absen semua karyawan**  

#### Employee  
- 🔎 **Melihat data pribadi dari HRD**  
- 📤 **Mengajukan resign** (dapat CRUD jika status *pending*, tidak dapat edit jika *rejected* atau *success*)  
- ⏱️ **Mengajukan lembur**  

---

### 📊 Hak Akses Berdasarkan Role  

| Fitur                                   | Super Admin | HRD | Employee |
|-----------------------------------------|-------------|-----|----------|
| Membuat Akun                            | ✅          | ❌  | ❌       |
| Approve/Reject Resign                   | ❌          | ✅  | ❌       |
| Mengajukan Lembur (maks 3/minggu)       | ❌          | ❌  | ✅       |
| CRUD Employee                           | ❌          | ✅  | ❌       |
| Melihat Absen Semua Karyawan            | ❌          | ✅  | ❌       |
| Melihat Data Pribadi                    | ❌          | ❌  | ✅       |
| Mengajukan & CRUD Resign (kondisional)  | ❌          | ❌  | ✅       |

#### 🔢 Total Fitur
- **Super Admin**: 1  
- **HRD**: 3  
- **Employee**: 3  

---

### ⚙️ Teknologi
- [Laravel](https://laravel.com/)  
- [Filament Admin](https://filamentphp.com/)  
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)  

---

### 🚀 Instalasi
```bash
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
```

---

## 🇬🇧 English Version  

### 📌 Description
**Simple HRIS** is a lightweight Human Resource Information System built using **Laravel**, **Filament Admin**, and **Spatie Laravel Permission**.  
The system has three main roles:  

- 👑 **Super Admin**  
- 🧑‍💼 **HRD (Human Resource Department)**  
- 👷 **Employee**  

---

### ✨ Core Features (MVP)
- 🆕 Create new accounts  
- 📝 HRD can **approve/reject resignation requests**  
- ⏱️ Employees can **request overtime (maximum 3 times per week)**  

---

### ➕ Additional Features  
#### HRD  
- 👨‍👩‍👦 **CRUD Employee**  
- 📋 **View all employees’ attendance**  

#### Employee  
- 🔎 **View personal data from HRD**  
- 📤 **Submit resignation** (can CRUD if status is *pending*, cannot edit if *rejected* or *success*)  
- ⏱️ **Submit overtime requests**  

---

### 📊 Role-based Access  

| Feature                                  | Super Admin | HRD | Employee |
|------------------------------------------|-------------|-----|----------|
| Create Account                           | ✅          | ❌  | ❌       |
| Approve/Reject Resignation               | ❌          | ✅  | ❌       |
| Request Overtime (max 3/week)            | ❌          | ❌  | ✅       |
| CRUD Employee                            | ❌          | ✅  | ❌       |
| View All Employee Attendance             | ❌          | ✅  | ❌       |
| View Personal Data                       | ❌          | ❌  | ✅       |
| Submit & CRUD Resignation (conditional)  | ❌          | ❌  | ✅       |

#### 🔢 Total Features
- **Super Admin**: 1  
- **HRD**: 3  
- **Employee**: 3  

---

### ⚙️ Technologies
- [Laravel](https://laravel.com/)  
- [Filament Admin](https://filamentphp.com/)  
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)  

---

### 🚀 Installation
```bash
# 1. Clone repository
git clone https://github.com/yourusername/simple-hris.git
cd simple-hris

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Run migration & seeding
php artisan migrate --seed

# 5. Start local server
php artisan serve
```

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.
