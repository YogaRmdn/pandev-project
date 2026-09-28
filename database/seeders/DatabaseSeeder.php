<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\PortfolioStatus;
use App\Enums\Role;
use App\Enums\TransactionType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Portfolio;
use App\Models\PortfolioGalery;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding database...');

        $password = Hash::make('password123');

        $accounts = [
            ['Ijichi Nijika', 'ijichinijika@yopmail.com', Role::ADMIN],
            ['Gotou Hitori', 'gotouhitori@yopmail.com', Role::USER],
            ['Yamada Ryou', 'yamadaryou@yopmail.com', Role::USER],
            ['Kita Ikuyo', 'kitaikuyo@yopmail.com', Role::USER],
        ];

        $users = collect($accounts)->map(function (array $row) use ($password) {
            [$fullname, $email, $role] = $row;

            return User::create([
                'fullname' => $fullname,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => $password,
                'role' => $role,
            ]);
        });

        foreach ($users as $user) {
            $this->command->line("  {$user->email} | password123 | {$user->role->value}");
        }

        $admin = $users->first();

        $portfolios = collect([
            ['Sistem Informasi Sekolah', 'Web App', PortfolioStatus::PUBLISHED, ['Laravel', 'MySQL', 'Tailwind CSS']],
            ['Aplikasi Kasir Kopi', 'Web App', PortfolioStatus::PUBLISHED, ['Next.js', 'TypeScript', 'PostgreSQL']],
            ['Dashboard Analytics Pendapatan', 'Data & GIS', PortfolioStatus::PUBLISHED, ['Python', 'PostgreSQL']],
            ['Sistem Absensi Fingerprint', 'IoT', PortfolioStatus::DRAFT, ['Arduino', 'MySQL']],
            ['Website Company Profile', 'Design & Video', PortfolioStatus::DRAFT, ['Adobe Premiere', 'Photoshop']],
        ])->map(function (array $row, int $i) use ($admin) {
            [$name, $category, $status, $stacks] = $row;

            return Portfolio::create([
                'thumbnail' => "https://picsum.photos/seed/pandev{$i}/800/450",
                'name' => $name,
                'category' => $category,
                'description' => "Contoh proyek {$name} yang dikerjakan oleh tim PanDev. Proyek ini mencakup requirement gathering, desain antarmuka, pengembangan, hingga pengujian dan maintenance.",
                'demo_link' => 'https://demo.pandev.test',
                'repository_link' => 'https://github.com/pandev/proyek-'.($i + 1),
                'status' => $status,
                'tech_stacks' => $stacks,
                'created_by' => $admin->id,
            ]);
        });

        foreach ($portfolios as $portfolio) {
            PortfolioGalery::create([
                'image_url' => $portfolio->thumbnail,
                'portfolio_id' => $portfolio->id,
            ]);
        }

        $this->command->info('Seeded '.count($portfolios).' portfolios.');

        $transactions = collect([
            [TransactionType::INCOME, 4500000, 'Pembayaran proyek website sekolah', '-45 days'],
            [TransactionType::EXPENSE, 750000, 'Sewa hosting VPS', '-40 days'],
            [TransactionType::INCOME, 2750000, 'DP aplikasi kasir', '-20 days'],
            [TransactionType::EXPENSE, 320000, 'Lisensi IDE dan library', '-12 days'],
            [TransactionType::INCOME, 1800000, 'Maintenance bulanan', '-5 days'],
        ])->map(function (array $row) {
            [$type, $amount, $description, $ago] = $row;

            return Transaction::create([
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'date' => now()->modify($ago)->format('Y-m-d'),
            ]);
        });

        $invoice = Invoice::create([
            'description' => 'Pengembangan Sistem Informasi Sekolah',
            'date' => now()->subDays(45)->format('Y-m-d'),
            'status' => InvoiceStatus::UNPAID,
        ]);

        foreach ([['Design UI/UX', 1, 1500000], ['Frontend', 1, 2000000], ['Backend & Database', 1, 1000000]] as [$name, $qty, $price]) {
            InvoiceItem::create([
                'name' => $name,
                'quantity' => $qty,
                'price' => $price,
                'invoice_id' => $invoice->id,
            ]);
        }

        $this->command->info('Seeded '.count($transactions).' transactions and 1 invoice.');
        $this->command->newLine();
        $this->command->info('Seed completed successfully!');
    }
}
