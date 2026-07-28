# 🚀 Dự Án Công Nghệ Web (Project_cnw)

Hệ thống ứng dụng Web được xây dựng bằng **PHP thuần (Vanilla PHP)** theo mô hình tách biệt giữa **Logic (Controllers)** và **Giao diện (Views)**, sử dụng PDO để thao tác Cơ sở dữ liệu an toàn.

---

## 🛠️ Công Nghệ Sử Dụng

* **Language:** PHP 8.x
* **Database:** MySQL / MariaDB (Kết nối qua PDO - Prepared Statements)
* **Web Server:** Apache (Laragon / XAMPP)
* **Frontend:** HTML5, CSS3, JavaScript

---

## 📁 Cấu Trúc Thư Mục Dự Án

```text
Project_cnw/
├── .env                        # File cấu hình biến môi trường (Database, App Config)
├── .htaccess                   # Cấu hình Apache (Tắt Directory Browsing)
├── index.php                   # Điều hướng chính (Main Router)
│
├── config/                     # Cấu hình hệ thống
│   └── app_config.php          # Nạp biến môi trường từ .env
│
├── assets/                     # Tài nguyên tĩnh công khai (Public Assets)
│   ├── css/                    # Stylesheet
│   ├── js/                     # JavaScript
│
├── src/                        # Xử lý Logic Backend
│   ├── DB.php                  # Lớp kết nối CSDL PDO (Singleton & Helper Functions)
│   └── Controllers/            # Các bộ điều khiển xử lý Request
│       ├── tai-khoan/
│       │   ├── dang-nhap.php   # Logic Đăng nhập
│       ├── hoc-phan/
│       ├── lop-hoc-phan/
│       ├── dot-dang-ky/
│       ├── nhom-sinh-vien/
│       ├── de-tai/
│       ├── quan-tri/
│       └── trang-chu.php       # Logic Trang chủ
│
└── views/                      # Giao diện người dùng (HTML Templates)
    ├── layouts/                # Thành phần giao diện dùng chung
    │   ├── header.php
    │   └── footer.php
    ├── tai-khoan/              # Giao diện tương ứng Controller
    │   ├── dang-nhap.php
    ├── hoc-phan/
    ├── lop-hoc-phan/
    ├── dot-dang-ky/
    ├── nhom-sinh-vien/
    ├── de-tai/
    ├── quan-tri/
    ├── 404.php                 # Trang báo lỗi 404
    └── trang-chu.php           # Giao diện Trang chủ


## Tải thư viện 
- Trong terminal của laragon, cd đến thư mục project: 
    composer require phpmailer/phpmailer
