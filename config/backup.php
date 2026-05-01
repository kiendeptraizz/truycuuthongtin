<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Đường dẫn lưu backup chính
    |--------------------------------------------------------------------------
    | Mặc định: storage/app/backups
    */
    'path' => env('BACKUP_PATH', storage_path('app/backups')),

    /*
    |--------------------------------------------------------------------------
    | Số ngày giữ backup daily
    |--------------------------------------------------------------------------
    | Backup daily cũ hơn số ngày này sẽ bị xoá tự động khi cron chạy.
    | Backup manual KHÔNG bị xoá (giữ vĩnh viễn cho đến khi user xoá tay).
    */
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Mirror folder — copy backup sang ổ phụ / network share / USB
    |--------------------------------------------------------------------------
    | Để trống nếu không cần. Ví dụ:
    |   BACKUP_MIRROR_PATH=D:\backups-truycuuthongtin
    |   BACKUP_MIRROR_PATH=\\network-server\share\backups
    |
    | Lưu ý quan trọng: backup chỉ ở 1 ổ là KHÔNG AN TOÀN.
    | Nếu ổ chính hỏng → mất hết. Nên đặt mirror ở ổ vật lý khác.
    */
    'mirror_path' => env('BACKUP_MIRROR_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Path tới mysqldump (override auto-detect)
    |--------------------------------------------------------------------------
    | Để trống → tự dò trong Laragon/XAMPP/PATH.
    */
    'mysqldump_path' => env('MYSQLDUMP_PATH'),
];
