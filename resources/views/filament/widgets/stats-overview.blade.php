<x-filament-widgets::widget>
    <div class="space-y-6">
        
        <!-- ==================== Row 1: Welcome Name Card ==================== -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-950 via-indigo-900 to-slate-900 p-6 sm:p-8 shadow-md z-0 transition-transform duration-300 hover:scale-[1.002]">
            <!-- Background decorative elements -->
            <div class="absolute -top-12 -right-12 h-48 w-48 rounded-full bg-indigo-500/10 blur-2xl"></div>
            <div class="absolute -bottom-10 right-24 h-36 w-36 rounded-full bg-blue-500/10 blur-xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-white leading-tight tracking-tight">
                        Selamat Datang Kembali, {{ $welcomeName }}!
                    </h2>
                    <p class="text-indigo-200/80 text-sm mt-2 max-w-xl font-medium">
                        Aplikasi HL Sales & Receivables siap membantu mengaktifkan piutang, memantau omzet lunas, laba bersih, serta status bonus pelanggan hari ini.
                    </p>
                </div>
                <!-- Quick Navigation Buttons (Senior Friendly, Clear Icons) -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ url('/admin/transactions/create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-indigo-950 hover:bg-slate-50 hover:scale-[1.03] active:scale-[0.98] font-bold text-sm rounded-xl shadow-md transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Buat Bon Baru
                    </a>
                    <a href="{{ url('/admin/customers/create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/30 text-white border border-white/20 hover:bg-indigo-600/40 hover:scale-[1.03] active:scale-[0.98] font-semibold text-sm rounded-xl transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                        Tambah Pelanggan
                    </a>
                </div>
            </div>
        </div>

        <!-- ==================== Row 2: Stats Cards with Sparklines ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Card 1: Total Piutang -->
            <div class="premium-card card-piutang flex flex-col justify-between min-h-[155px]">
                <div class="card-glow glow-piutang"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Piutang</span>
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold {{ $piutangDiff <= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                            @if($piutangDiff <= 0)
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m19 12-7 7-7-7"/><path d="M12 19V5"/></svg>
                                {{ abs(round($piutangDiff, 1)) }}%
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"/><path d="M12 5v14"/></svg>
                                {{ round($piutangDiff, 1) }}%
                            @endif
                        </span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-2 tracking-tight">
                        Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-semibold mt-1">
                        {{ $piutangCount }} Bon belum lunas (outstanding)
                    </div>
                </div>
                <div class="w-full mt-4 h-8 sparkline-container relative z-10">
                    <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                        <polyline points="{{ $piutangPoints }}" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Total Omzet Lunas -->
            <div class="premium-card card-omzet flex flex-col justify-between min-h-[155px]">
                <div class="card-glow glow-omzet"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Omzet Lunas</span>
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold {{ $omzetDiff >= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                            @if($omzetDiff >= 0)
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"/><path d="M12 5v14"/></svg>
                                {{ round($omzetDiff, 1) }}%
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m19 12-7 7-7-7"/><path d="M12 19V5"/></svg>
                                {{ abs(round($omzetDiff, 1)) }}%
                            @endif
                        </span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-2 tracking-tight">
                        Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-semibold mt-1">
                        LM: {{ number_format($omzetLM / 1000000, 1, ',', '.') }}jt &bull; BR: {{ number_format($omzetBR / 1000000, 1, ',', '.') }}jt
                    </div>
                </div>
                <div class="w-full mt-4 h-8 sparkline-container relative z-10">
                    <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                        <polyline points="{{ $omzetPoints }}" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Total Laba HL Lunas -->
            <div class="premium-card card-laba flex flex-col justify-between min-h-[155px]">
                <div class="card-glow glow-laba"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Laba HL Lunas</span>
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold {{ $labaDiff >= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                            @if($labaDiff >= 0)
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"/><path d="M12 5v14"/></svg>
                                {{ round($labaDiff, 1) }}%
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m19 12-7 7-7-7"/><path d="M12 19V5"/></svg>
                                {{ abs(round($labaDiff, 1)) }}%
                            @endif
                        </span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-2 tracking-tight">
                        Rp {{ number_format($totalLaba, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-semibold mt-1">
                        LM: {{ number_format($labaLM / 1000000, 1, ',', '.') }}jt &bull; BR: {{ number_format($labaBR / 1000000, 1, ',', '.') }}jt
                    </div>
                </div>
                <div class="w-full mt-4 h-8 sparkline-container relative z-10">
                    <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                        <polyline points="{{ $labaPoints }}" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Bonus Eligibility -->
            <div class="premium-card card-bonus flex flex-col justify-between min-h-[155px]">
                <div class="card-glow glow-bonus"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Bonus Eligibility</span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/50">
                            Aktif
                        </span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-2 tracking-tight">
                        {{ $customersWithBonusCount }} Pelanggan
                    </div>
                    <div class="text-[11px] text-slate-500 font-semibold mt-1">
                        Berhak klaim barang gratis (omzet tercapai)
                    </div>
                </div>
                <div class="w-full mt-4 h-8 sparkline-container relative z-10">
                    <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                        <polyline points="{{ $bonusPoints }}" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- ==================== Row 3: Split Sections (Activity, Transactions, Invoices) ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Column: Last Activity Timeline (5 Columns) -->
            <div class="lg:col-span-5 premium-section-card">
                <div class="border-b border-slate-100 pb-4 mb-5 flex items-center gap-2">
                    <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Aktivitas Terakhir</h3>
                </div>
                
                <div class="relative pl-6 border-l-2 border-slate-100 space-y-6">
                    @forelse($recentActivities as $act)
                        <div class="relative transition-all duration-200 hover:translate-x-1">
                            <!-- Bullet Indicator Icon -->
                            <span class="absolute -left-[31px] top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-white border-2 border-indigo-600 shadow-sm z-10">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span>
                            </span>
                            <!-- Time -->
                            <span class="text-[10px] text-slate-400 font-bold block mb-0.5">
                                {{ $act['time']->diffForHumans() }}
                            </span>
                            <!-- Description -->
                            <p class="text-xs text-slate-700 leading-relaxed">
                                {!! $act['description'] !!}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">Belum ada aktivitas terbaru.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Last Transactions & Last Invoices (7 Columns) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Card 1: Last Transactions -->
                <div class="premium-section-card">
                    <div class="border-b border-slate-100 pb-4 mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-blue-50 rounded-xl text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Transaksi Terakhir</h3>
                        </div>
                        <a href="{{ url('/admin/transactions') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 pr-2">Nomor Bon</th>
                                    <th class="py-3 px-2">Pelanggan</th>
                                    <th class="py-3 px-2 text-center">Status</th>
                                    <th class="py-3 px-2 text-right">Total Tagihan</th>
                                    <th class="py-3 pl-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $t)
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition duration-150">
                                        <td class="py-3.5 pr-2 font-mono font-bold text-slate-900">{{ $t->nomor_bon }}</td>
                                        <td class="py-3.5 px-2 font-semibold text-slate-700">{{ $t->customer->name }}</td>
                                        <td class="py-3.5 px-2 text-center">
                                            @if($t->status === 'Lunas')
                                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">LUNAS</span>
                                            @else
                                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">PIUTANG</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-2 text-right font-bold text-slate-900">Rp {{ number_format($t->total_owed, 0, ',', '.') }}</td>
                                        <td class="py-3.5 pl-2 text-center">
                                            <a href="{{ url("/admin/transactions/{$t->id}/edit") }}" class="inline-flex items-center justify-center p-1.5 bg-slate-50 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 rounded-lg border border-slate-200 shadow-sm transition duration-150" title="Edit Transaksi">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card 2: Last Invoices (Download PDFs) -->
                <div class="premium-section-card">
                    <div class="border-b border-slate-100 pb-4 mb-4 flex items-center gap-2">
                        <div class="p-2 bg-emerald-50 rounded-xl text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Unduh Invoice (Bon) PDF Terbaru</h3>
                    </div>
                    
                    <div class="space-y-3">
                        @forelse($recentInvoices as $inv)
                            <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-indigo-50/20 hover:border-indigo-100 hover:scale-[1.01] transition duration-200">
                                <div class="flex items-center gap-3">
                                    <div class="p-2.5 bg-white text-indigo-600 rounded-xl border border-slate-100 shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-mono font-bold text-slate-900">{{ $inv->nomor_bon }}</span>
                                        <span class="block text-[10px] text-slate-400 font-bold mt-0.5">{{ $inv->customer->name }} &bull; {{ $inv->tanggal->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <a href="{{ url("/admin/transactions/{$inv->id}/pdf") }}" class="inline-flex items-center gap-1.5 px-4.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md hover:scale-[1.03] active:scale-[0.98] transition duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    Unduh PDF
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">Belum ada invoice untuk diunduh.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-filament-widgets::widget>
