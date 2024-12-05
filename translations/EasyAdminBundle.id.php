<?php

return [
    'page_title' => [
        'dashboard' => 'Dasbor',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Sunting %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Buat %entity_label_singular%',
        'exception' => 'Kesalahan|Kesalahan',
    ],

    'datagrid' => [
        'hidden_results' => 'Beberapa hasil tidak dapat ditampilkan karena Anda tidak memiliki hak akses yang cukup',
        'no_results' => 'Tidak ada hasil yang ditemukan.',
    ],

    'paginator' => [
        'first' => 'Pertama',
        'previous' => 'Sebelumnya',
        'next' => 'Selanjutnya',
        'last' => 'Terakhir',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> dari <strong>%results%</strong>',
        'results' => '{0} Tidak ada hasil|{1} <strong>1</strong> hasil|]1,Inf] <strong>%count%</strong> hasil',
    ],

    'label' => [
        'true' => 'Ya',
        'false' => 'Tidak',
        'empty' => 'Kosong',
        'null' => 'Null',
        'object' => 'Objek PHP',
        'inaccessible' => 'Tidak dapat diakses',
        'inaccessible.explanation' => 'Metode getter tidak ada untuk bidang ini atau bidang tidak dibuat untuk publik',
        'form.empty_value' => 'Tidak ada',
    ],

    'field' => [
        'code_editor.view_code' => 'Lihat kode',
        'text_editor.view_content' => 'Lihat konten',
    ],

    'action' => [
        'entity_actions' => 'Aksi',
        'new' => 'Tambah %entity_label_singular%',
        'search' => 'Cari',
        'detail' => 'Tampilkan',
        'edit' => 'Sunting',
        'delete' => 'Hapus',
        'cancel' => 'Batalkan',
        'index' => 'Kembali ke daftar',
        'deselect' => 'Batalkan pilihan',
        'add_new_item' => 'Tambahkan item baru',
        'remove_item' => 'Hapus item',
        'choose_file' => 'Pilih file',
        'close' => 'Tutup',
        'create' => 'Buat',
        'create_and_add_another' => 'Buat dan tambahkan lainnya',
        'create_and_continue' => 'Buat dan teruskan penyuntingan',
        'save' => 'Simpan perubahan',
        'save_and_continue' => 'Simpan dan teruskan penyuntingan',
    ],

    'batch_action_modal' => [
        'title' => 'Anda akan menerapkan tindakan "%action_name%" ke %num_items% item.',
        'content' => 'Tidak ada pembatalan untuk operasi ini.',
        'action' => 'Proses',
    ],

    'delete_modal' => [
        'title' => 'Apakah Anda benar-benar ingin menghapus item ini?',
        'content' => 'Tidak ada pembatalan untuk operasi ini.',
    ],

    'filter' => [
        'title' => 'Filter',
        'button.clear' => 'Bersihkan',
        'button.apply' => 'Terapkan',
        'label.is_equal_to' => 'sama dengan',
        'label.is_not_equal_to' => 'tidak sama dengan',
        'label.is_greater_than' => 'lebih besar dari',
        'label.is_greater_than_or_equal_to' => 'lebih besar atau sama dengan',
        'label.is_less_than' => 'kurang dari',
        'label.is_less_than_or_equal_to' => 'kurang dari atau sama dengan',
        'label.is_between' => 'antara',
        'label.contains' => 'mengandung',
        'label.not_contains' => 'tidak mengandung',
        'label.starts_with' => 'dimulai dari',
        'label.ends_with' => 'berakhiran dengan',
        'label.exactly' => 'tepat',
        'label.not_exactly' => 'tidak tepat',
        'label.is_same' => 'sama',
        'label.is_not_same' => 'tidak sama',
        'label.is_after' => 'setelah',
        'label.is_after_or_same' => 'setelah atau sama',
        'label.is_before' => 'sebelum',
        'label.is_before_or_same' => 'sebelum atau sama',
    ],

    'form' => [
        'are_you_sure' => 'Anda belum menyimpan perubahan yang dibuat pada formulir ini.',
        'tab.error_badge_title' => 'Satu input tidak valid|%count% input tidak valid',
        'slug.confirm_text' => 'Jika Anda mengubah slug, Anda dapat memutuskan tautan di halaman lain.',
    ],

    'user' => [
        'logged_in_as' => 'Masuk sebagai',
        'unnamed' => 'Pengguna Tanpa Nama',
        'anonymous' => 'Pengguna Anonim',
        'sign_out' => 'Keluar',
        'exit_impersonation' => 'Keluar dari peniruan identitas',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Tampilan',
            'light' => 'Terang',
            'dark' => 'Gelap',
            'auto' => 'Otomatis',
        ],
        'locale' => 'Bahasa',
    ],

    'login_page' => [
        'username' => 'Nama pengguna',
        'password' => 'Kata sandi',
        'sign_in' => 'Masuk',
        'forgot_password' => 'Lupa kata sandi?',
        'remember_me' => 'Ingat saya',
    ],

    'exception' => [
        'entity_not_found' => 'Item ini tidak lagi tersedia.',
        'entity_remove' => 'Item ini tidak dapat dihapus karena item lain bergantung padanya.',
        'forbidden_action' => 'Tindakan yang diminta tidak dapat dilakukan pada item ini.',
        'insufficient_entity_permission' => 'Anda tidak memiliki izin untuk mengakses item ini.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Tidak ada hasil yang ditemukan',
        'no-more-results' => 'Tidak ada hasil lagi',
        'loading-more-results' => 'Memuat hasil lainnya…',
    ],
];
