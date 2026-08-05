<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Bahasa Indonesia
    |--------------------------------------------------------------------------
    |
    | Berkas ini berisi terjemahan pesan kesalahan validasi. Hanya aturan yang
    | benar-benar dipakai StudentHub AI yang diterjemahkan. Aturan lain yang
    | belum ada di sini otomatis memakai versi Bahasa Inggris bawaan Laravel,
    | sesuai pengaturan APP_FALLBACK_LOCALE pada berkas .env.
    |
    */

    'accepted' => ':attribute harus disetujui.',
    'after' => ':attribute harus berisi tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berisi tanggal setelah atau sama dengan :date.',
    'array' => ':attribute harus berupa daftar.',
    'before' => ':attribute harus berisi tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berisi tanggal sebelum atau sama dengan :date.',

    'between' => [
        'array' => ':attribute harus memiliki antara :min dan :max item.',
        'file' => ':attribute harus berukuran antara :min dan :max kilobyte.',
        'numeric' => ':attribute harus bernilai antara :min dan :max.',
        'string' => ':attribute harus berisi antara :min dan :max karakter.',
    ],

    'boolean' => ':attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi salah.',
    'date' => ':attribute bukan tanggal yang sah.',
    'date_format' => ':attribute tidak sesuai format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak sesuai.',
    'email' => ':attribute harus berupa alamat email yang sah.',
    'ends_with' => ':attribute harus diakhiri salah satu dari: :values.',
    'exists' => ':attribute yang dipilih tidak sah.',
    'file' => ':attribute harus berupa berkas.',
    'filled' => ':attribute wajib diisi.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak sah.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'json' => ':attribute harus berupa teks JSON yang sah.',
    'lowercase' => ':attribute harus berupa huruf kecil.',

    'max' => [
        'array' => ':attribute tidak boleh lebih dari :max item.',
        'file' => ':attribute tidak boleh lebih dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],

    'mimes' => ':attribute harus berupa berkas berjenis: :values.',
    'mimetypes' => ':attribute harus berupa berkas berjenis: :values.',

    'min' => [
        'array' => ':attribute harus memiliki minimal :min item.',
        'file' => ':attribute harus berukuran minimal :min kilobyte.',
        'numeric' => ':attribute harus bernilai minimal :min.',
        'string' => ':attribute harus berisi minimal :min karakter.',
    ],

    'not_in' => ':attribute yang dipilih tidak sah.',
    'numeric' => ':attribute harus berupa angka.',
    'present' => ':attribute wajib ada.',
    'prohibited' => ':attribute tidak diizinkan.',
    'regex' => 'Format :attribute tidak sah.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi bila :other bernilai :value.',
    'required_with' => ':attribute wajib diisi bila terdapat :values.',
    'required_without' => ':attribute wajib diisi bila tidak terdapat :values.',
    'same' => ':attribute dan :other harus sama.',

    'size' => [
        'array' => ':attribute harus berisi :size item.',
        'file' => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus bernilai :size.',
        'string' => ':attribute harus berisi :size karakter.',
    ],

    'starts_with' => ':attribute harus diawali salah satu dari: :values.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'uppercase' => ':attribute harus berupa huruf besar.',
    'url' => ':attribute bukan tautan yang sah.',

    /*
    |--------------------------------------------------------------------------
    | Nama Kolom dalam Bahasa Indonesia
    |--------------------------------------------------------------------------
    |
    | Menggantikan nama kolom apa adanya dengan istilah yang dimengerti
    | pengguna. Tanpa ini, pesan yang muncul berbunyi "universitas wajib diisi"
    | dengan huruf kecil dan terasa seperti nama kolom database.
    |
    */

    'attributes' => [
        'name' => 'Nama',
        'email' => 'Email',
        'password' => 'Kata sandi',
        'password_confirmation' => 'Konfirmasi kata sandi',
        'universitas' => 'Universitas',
        'jurusan' => 'Jurusan',
        'semester' => 'Semester',
        'minat' => 'Minat',
        'skill' => 'Kemampuan',
        'judul' => 'Judul',
        'penyelenggara' => 'Penyelenggara',
        'kategori' => 'Kategori',
        'deskripsi' => 'Deskripsi',
        'deadline' => 'Batas waktu',
        'biaya' => 'Biaya',
        'tingkat' => 'Tingkat',
        'link' => 'Tautan',
        'poster' => 'Poster',
        'syarat' => 'Syarat',
        'catatan_admin' => 'Catatan admin',
    ],

    'custom' => [],

];
