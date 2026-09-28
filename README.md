# PanDev Website

Website resmi PanDev (software house) yang dibangun dengan Next.js, Prisma ORM, dan shadcn/ui.

## Tech Stack

- **Framework:** Next.js 16.3.1 (App Router, Turbopack)
- **UI Library:** React 19, shadcn/ui (radix-nova), Tailwind CSS 4
- **ORM:** Prisma 6.12.0 (MySQL)
- **State Management:** TanStack React Query
- **Form Handling:** React Hook Form + Zod
- **Bahasa:** TypeScript 5

## Prasyarat

- Node.js 20+
- MySQL (XAMPP / MySQL Server)
- npm

## Setup Development

### 1. Clone Repository

```bash
git clone <url-repo>
cd software-agency
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Setup Environment Variables

Copy file `.env.example` ke `.env`:

```bash
cp .env.example .env
```

Edit `.env` sesuai konfigurasi MySQL kamu:

```
DATABASE_URL="mysql://root:@localhost:3306/pandev_db"
DATABASE_USER="root"
DATABASE_PASSWORD=""
DATABASE_NAME="pandev_db"
DATABASE_HOST="localhost"
DATABASE_PORT=3306
```

### 4. Buat Database

Buat database di MySQL:

```sql
CREATE DATABASE pandev_db;
```

### 5. Generate Prisma Client

```bash
npx prisma generate
```

### 6. Jalankan Migrasi

```bash
npx prisma migrate dev
```

### 7. Jalankan Development Server

```bash
npm run dev
```

Buka [http://localhost:3000](http://localhost:3000) di browser.

## Perintah yang Tersedia

| Perintah | Keterangan |
|---|---|
| `npm run dev` | Jalankan dev server |
| `npm run build` | Build untuk production |
| `npm run start` | Jalankan production server |
| `npm run lint` | Jalankan ESLint |

## Struktur Project

```
src/
├── app/
│   ├── (main)/              # Halaman publik (homepage, portfolio, tentang, kontak)
│   ├── dashboard/           # Dashboard admin
│   │   └── portfolio/       # CRUD portfolio
│   ├── actions/             # Server actions (upload, portfolio)
│   └── auth/                # Autentikasi
├── components/
│   └── ui/                  # Komponen shadcn/ui
├── hooks/                   # Custom React hooks
└── lib/                     # Utility functions
```

## Menambah Komponen UI (shadcn)

```bash
npx shadcn@latest add <nama-komponen>
```

Contoh:

```bash
npx shadcn@latest add dialog
npx shadcn@latest add table
```

## Catatan Penting

- Gunakan `@/*` path alias untuk import (maps ke `./src/*`)
- Komponen page-specific diletakkan di folder `_components/` dalam route yang sama
- Komponen global/shared di `src/components/ui/`
- Asset statis di `public/assets/`
- Upload file menggunakan server actions di `src/app/actions/upload.ts`
