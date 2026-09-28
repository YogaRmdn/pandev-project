# Navbar Layout - Home Page

## Overview

Membuat komponen navbar sederhana untuk halaman utama website Arkana, sebuah software house di Indonesia.

## Wireframe

```
| [Arkana]             Layanan  Portofolio  Tentang  Kontak |
```

## Struktur Navigasi

| Elemen       | Tipe        | Keterangan                              |
| ------------ | ----------- | --------------------------------------- |
| Arkana       | Logo/Brand  | Teks nama brand, berada di sisi kiri    |
| Layanan     | Navigasi    | Link ke halaman layanan                 |
| Portofolio  | Navigasi    | Link ke halaman portofolio              |
| Tentang     | Navigasi    | Link ke halaman tentang kami            |
| Kontak      | Navigasi    | Link ke halaman kontak                  |

## Layout Behavior

- **Desktop**: Logo di kiri, menu navigasi di kanan dalam satu baris
- **Mobile**: Logo di kiri, menu berubah menjadi hamburger menu (ikon tiga garis)

## Komponen yang Dibutuhkan

1. **Navbar Container** - Wrapper utama yang menempel di bagian atas halaman
2. **Brand/Logo** - Teks "Arkana" sebagai identitas brand
3. **Navigation Links** - Empat link navigasi: Layanan, Portofolio, Tentang, Kontak
4. **Mobile Menu Button** - Tombol hamburger untuk layar kecil
5. **Mobile Menu Dropdown** - Menu dropdown yang muncul saat hamburger diklik

## Interaksi

- Hover pada link navigasi menampilkan efek visual (perubahan warna atau underline)
- Klik link menuju halaman yang sesuai
- Pada mobile, klik hamburger membuka/tutup menu dropdown

## Posisi

- Navbar menempel di bagian atas halaman (sticky/fixed position)
- Selalu terlihat saat user scroll ke bawah

## Halaman Terkait

- `/` - Home page (halaman utama navbar ini ditempatkan)
- `/layanan` - Halaman Layanan
- `/portofolio` - Halaman Portofolio
- `/tentang` - Halaman Tentang Kami
- `/kontak` - Halaman Kontak
