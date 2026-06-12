<x-filament-panels::page>
    <style>
        /* From Uiverse.io by Javierrocadev */ 
        .uiverse-card {
            width: 100%;
            height: 125px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0,0,0,0.06), 0 1px 2px -1px rgba(0,0,0,0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .uiverse-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px -3px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.05);
        }

        .uiverse-card::before {
            content: "";
            height: 70px;
            width: 70px;
            position: absolute;
            top: -30%;
            left: -15%;
            border-radius: 50%;
            border: 20px solid rgba(255, 255, 255, 0.2);
            transition: all .6s ease;
            filter: blur(.3rem);
            z-index: 1;
        }

        .uiverse-card:hover::before {
            width: 100px;
            height: 100px;
            top: -20%;
            left: 60%;
            filter: blur(0rem);
        }

        .uiverse-card .text {
            flex-grow: 1;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            z-index: 2;
            height: 100%;
        }

        .uiverse-card .subtitle {
            font-size: .7rem;
            font-weight: 700;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .uiverse-card .value {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            margin-top: 1px;
        }

        .uiverse-card .desc-text {
            font-size: .75rem;
            font-weight: 500;
            margin-top: auto;
        }

        /* CARD THEMES (Soft Pastel Colors for Light Mode) */
        .card-piutang { 
            background: #ffe4e6; 
            border: 1px solid #fecdd3;
        }
        .card-piutang .subtitle { color: #e11d48; }
        .card-piutang .value { color: #9f1239; }
        .card-piutang .desc-text { color: #be123c; }
        .card-piutang::before { border-color: rgba(225, 29, 72, 0.15); }

        .card-terbayar, .card-bonus { 
            background: #d1fae5; 
            border: 1px solid #a7f3d0;
        }
        .card-terbayar .subtitle, .card-bonus .subtitle { color: #059669; }
        .card-terbayar .value, .card-bonus .value { color: #065f46; }
        .card-terbayar .desc-text, .card-bonus .desc-text { color: #047857; }
        .card-terbayar::before, .card-bonus::before { border-color: rgba(16, 185, 129, 0.15); }

        .card-omzet { 
            background: #e0e7ff; 
            border: 1px solid #c7d2fe;
        }
        .card-omzet .subtitle { color: #4f46e5; }
        .card-omzet .value { color: #3730a3; }
        .card-omzet .desc-text { color: #4338ca; }
        .card-omzet::before { border-color: rgba(99, 102, 241, 0.15); }

        .card-laba { 
            background: #e0f2fe; 
            border: 1px solid #bae6fd;
        }
        .card-laba .subtitle { color: #0284c7; }
        .card-laba .value { color: #075985; }
        .card-laba .desc-text { color: #0369a1; }
        .card-laba::before { border-color: rgba(14, 165, 233, 0.15); }

        /* Dark Mode overrides (Subtle/Dark Pastel Glassmorphism) */
        .dark .card-piutang {
            background: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.25);
        }
        .dark .card-piutang .subtitle { color: #fda4af; }
        .dark .card-piutang .value { color: #fecdd3; }
        .dark .card-piutang .desc-text { color: rgba(254, 205, 211, 0.85); }
        .dark .card-piutang::before { border-color: rgba(244, 63, 94, 0.2); }

        .dark .card-terbayar, .dark .card-bonus {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.25);
        }
        .dark .card-terbayar .subtitle, .dark .card-bonus .subtitle { color: #a7f3d0; }
        .dark .card-terbayar .value, .dark .card-bonus .value { color: #d1fae5; }
        .dark .card-terbayar .desc-text, .dark .card-bonus .desc-text { color: rgba(209, 250, 229, 0.85); }
        .dark .card-terbayar::before, .dark .card-bonus::before { border-color: rgba(16, 185, 129, 0.2); }

        .dark .card-omzet {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.25);
        }
        .dark .card-omzet .subtitle { color: #c7d2fe; }
        .dark .card-omzet .value { color: #e0e7ff; }
        .dark .card-omzet .desc-text { color: rgba(224, 231, 255, 0.85); }
        .dark .card-omzet::before { border-color: rgba(99, 102, 241, 0.2); }

        .dark .card-laba {
            background: rgba(14, 165, 233, 0.1);
            border-color: rgba(14, 165, 233, 0.25);
        }
        .dark .card-laba .subtitle { color: #bae6fd; }
        .dark .card-laba .value { color: #e0f2fe; }
        .dark .card-laba .desc-text { color: rgba(224, 242, 254, 0.85); }
        .dark .card-laba::before { border-color: rgba(14, 165, 233, 0.2); }
    </style>

    <div class="space-y-6">
        <!-- Customer Info Card -->
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-950 dark:text-white">{{ $record->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detail Aktivitas & Rekap Pelunasan Transaksi</p>
                </div>
                <div class="flex items-center gap-3" style="padding: 10px 16px; border-radius: 12px; background-color: rgba(99, 102, 241, 0.06); border: 1px solid rgba(99, 102, 241, 0.15);">
                    <div class="rounded-lg p-2 text-indigo-600 dark:text-indigo-400" style="background-color: rgba(99, 102, 241, 0.15);">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 8c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/></svg>
                    </div>
                    <div class="text-left">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold uppercase tracking-wider">Bonus Eligibility Threshold</span>
                        <span class="text-base font-extrabold text-indigo-600 dark:text-indigo-400 block mt-0.5">
                            Rp {{ number_format($record->bonus_threshold, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month Selector & Settle Header Action -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-200 dark:border-gray-800/80">
            <div class="flex items-center gap-3">
                <label for="month-select" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pilih Periode:</label>
                <select id="month-select" wire:model.live="selectedMonth" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                    @foreach ($this->getMonths() as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Header Action Buttons using Filament Native Components -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- PDF Monthly Recap Button -->
                <x-filament::button 
                    tag="a"
                    href="{{ route('admin.customers.pdf-recap', ['customer' => $record->id, 'month' => $selectedMonth]) }}"
                    color="gray"
                    icon="heroicon-m-arrow-down-tray"
                    size="sm"
                >
                    Unduh Rekap (PDF)
                </x-filament::button>

                <!-- Settle Whole Month Modal Dialog -->
                <x-filament::modal id="settle-month-modal" width="md">
                    <x-slot name="trigger">
                        @if ($totalPiutang > 0)
                            <x-filament::button 
                                color="success" 
                                icon="heroicon-m-check-circle"
                                size="sm"
                            >
                                Lunasi Satu Bulan
                            </x-filament::button>
                        @else
                            <x-filament::button 
                                color="gray" 
                                icon="heroicon-m-check-circle"
                                size="sm"
                                disabled
                            >
                                Semua Bon Lunas
                            </x-filament::button>
                        @endif
                    </x-slot>
                    
                    <x-slot name="heading">
                        Pelunasan Bulan Ini
                    </x-slot>
                    
                    <div class="space-y-4 py-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Semua transaksi outstanding pada bulan <strong>{{ Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }}</strong> untuk pelanggan ini akan diubah statusnya menjadi <strong>Lunas</strong>.
                        </p>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Pilih Tanggal Pelunasan</label>
                            <input type="date" wire:model="paymentDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>
                    
                    <x-slot name="footer">
                        <div class="flex justify-end gap-3">
                            <x-filament::button color="gray" x-on:click="close">
                                Batal
                            </x-filament::button>
                            <x-filament::button color="success" wire:click="settleMonth" x-on:click="close">
                                Lunasi Bulan
                            </x-filament::button>
                        </div>
                    </x-slot>
                </x-filament::modal>
            </div>
        </div>

        <!-- Monthly Statistics Grid (Uiverse Sunset Vector Theme) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            
            <!-- Card 1: Piutang -->
            <div class="uiverse-card card-piutang">
                <div class="text">
                    <span class="subtitle">Total Piutang</span>
                    <span class="value">Rp {{ number_format($totalPiutang, 2, ',', '.') }}</span>
                    <span class="desc-text">Outstanding: {{ $piutangCount }} Bon</span>
                </div>
            </div>

            <!-- Card 2: Terbayar -->
            <div class="uiverse-card card-terbayar">
                <div class="text">
                    <span class="subtitle">Sudah Dibayar</span>
                    <span class="value">Rp {{ number_format($totalPaid, 2, ',', '.') }}</span>
                    <span class="desc-text">Lunas: {{ $paidCount }} Bon</span>
                </div>
            </div>

            <!-- Card 3: Total Omzet -->
            <div class="uiverse-card card-omzet">
                <div class="text">
                    <span class="subtitle">Total Omzet Lunas</span>
                    <span class="value">Rp {{ number_format($omzetLM + $omzetBR, 2, ',', '.') }}</span>
                    <span class="desc-text">LM: Rp {{ number_format($omzetLM, 0, ',', '.') }} | BR: Rp {{ number_format($omzetBR, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card 4: Laba HL -->
            <div class="uiverse-card card-laba">
                <div class="text">
                    <span class="subtitle">Laba HL Lunas</span>
                    <span class="value">Rp {{ number_format($labaLM + $labaBR, 2, ',', '.') }}</span>
                    <span class="desc-text">LM: Rp {{ number_format($labaLM, 0, ',', '.') }} | BR: Rp {{ number_format($labaBR, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

        <!-- Transactions Table Card -->
        <div class="border border-gray-200 dark:border-gray-800 rounded-xl bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-850">
                <h3 class="font-bold text-base text-gray-950 dark:text-white">Daftar Transaksi Bon</h3>
                <span class="text-xs font-bold px-2.5 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 rounded-full">
                    {{ count($this->getTransactions()) }} Transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-800/40 border-b border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 font-bold text-xs uppercase tracking-wider">
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Nomor Bon</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Jenis</th>
                            <th class="py-3.5 px-6 text-right">Jumlah Piutang</th>
                            <th class="py-3.5 px-6 text-right">Tgl Lunas</th>
                            <th class="py-3.5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($this->getTransactions() as $transaction)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 text-gray-700 dark:text-gray-300 transition duration-150">
                                <td class="py-4 px-6 whitespace-nowrap">{{ Carbon\Carbon::parse($transaction->tanggal)->format('d M Y') }}</td>
                                <td class="py-4 px-6 font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ $transaction->nomor_bon }}</td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if ($transaction->status === 'Lunas')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/40">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40 animate-pulse">
                                            Piutang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if ($transaction->is_bonus)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/30">
                                            🎁 Bonus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            Sales
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right font-extrabold text-gray-900 dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($transaction->total_owed, 2, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $transaction->tanggal_pelunasan ? Carbon\Carbon::parse($transaction->tanggal_pelunasan)->format('d M Y') : '-' }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-x-3">
                                        <!-- Edit Action -->
                                        <x-filament::link 
                                            color="indigo" 
                                            href="{{ App\Filament\Resources\TransactionResource::getUrl('edit', ['record' => $transaction->id]) }}"
                                            size="sm"
                                        >
                                            Edit
                                        </x-filament::link>

                                        <!-- PDF Action -->
                                        <x-filament::link 
                                            color="gray" 
                                            href="{{ route('admin.transactions.pdf', $transaction->id) }}" 
                                            target="_blank"
                                            size="sm"
                                            icon="heroicon-m-arrow-down-tray"
                                        >
                                            PDF
                                        </x-filament::link>

                                        @if ($transaction->status === 'Piutang')
                                            <!-- Single Settle Modal -->
                                            <x-filament::modal id="settle-single-modal-{{ $transaction->id }}" width="sm">
                                                <x-slot name="trigger">
                                                    <x-filament::link 
                                                        color="success" 
                                                        tag="button"
                                                        size="sm"
                                                    >
                                                        Lunasi
                                                    </x-filament::link>
                                                </x-slot>
                                                
                                                <x-slot name="heading">
                                                    Lunasi Bon
                                                </x-slot>
                                                
                                                <div class="space-y-4 py-2 text-left">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        Konfirmasi pelunasan untuk nomor bon <strong>{{ $transaction->nomor_bon }}</strong>.
                                                    </p>
                                                    <div class="space-y-1.5" x-data="{ pDate: '{{ now()->toDateString() }}' }">
                                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Pilih Tanggal Pelunasan</label>
                                                        <input type="date" x-model="pDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                                        
                                                        <div class="flex justify-end gap-2 pt-4">
                                                            <x-filament::button color="gray" x-on:click="close">
                                                                Batal
                                                            </x-filament::button>
                                                            <x-filament::button color="success" x-on:click="$wire.settleSingleTransaction({{ $transaction->id }}, pDate); close()">
                                                                Lunasi
                                                            </x-filament::button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </x-filament::modal>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                    Tidak ada data transaksi untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
