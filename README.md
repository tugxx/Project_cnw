Project_cnw/
├── .env                       # File cấu hình mật khẩu/DB
├── index.php                  # Cổng điều hướng chính (Router)
├── config/
│   └── app_config.php         # Nạp file .env
├── assets/                    # File tĩnh cho trình duyệt
│   ├── css/
│   ├── js/
│   └── uploads/
├── src/                       # TOÀN BỘ CODE BACKEND (Logic)
│   ├── DB.php                 # File kết nối Database
│   └── Controllers/           # Xử lý dữ liệu & nhận request
│       ├── QuanLyTaiKhoan/
│       │   ├── dangnhap.php
│       │   └── dangky.php
│       └── trangchu.php
└── views/                     # TOÀN BỘ GIAO DIỆN (HTML)
    ├── layouts/               # Phần dùng chung toàn trang
    │   ├── header.php
    │   └── footer.php
    ├── QuanLyTaiKhoan/        # Giao diện tương ứng với Controller
    │   ├── dangnhap.php
    │   └── dangky.php
    ├── 404.php
    └── trangchu.php           # Giao diện trang chủ