<x-filament-widgets::widget>
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

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Card 1: Piutang -->
        <div class="uiverse-card card-piutang">
            <div class="text">
                <span class="subtitle">Total Piutang</span>
                <span class="value">Rp {{ number_format($totalPiutang, 2, ',', '.') }}</span>
                <span class="desc-text">Outstanding: {{ $piutangCount }} Transaksi</span>
            </div>
        </div>

        <!-- Card 2: Total Omzet -->
        <div class="uiverse-card card-omzet">
            <div class="text">
                <span class="subtitle">Total Omzet Lunas</span>
                <span class="value">Rp {{ number_format($totalOmzet, 2, ',', '.') }}</span>
                <span class="desc-text">LM: Rp {{ number_format($omzetLM, 0, ',', '.') }} | BR: Rp {{ number_format($omzetBR, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Card 3: Total Laba HL -->
        <div class="uiverse-card card-laba">
            <div class="text">
                <span class="subtitle">Total Laba HL Lunas</span>
                <span class="value">Rp {{ number_format($totalLaba, 2, ',', '.') }}</span>
                <span class="desc-text">LM: Rp {{ number_format($labaLM, 0, ',', '.') }} | BR: Rp {{ number_format($labaBR, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Card 4: Bonus Eligibility -->
        <div class="uiverse-card card-bonus">
            <div class="text">
                <span class="subtitle">Bonus Eligibility</span>
                <span class="value">{{ $customersWithBonusCount }} Pelanggan</span>
                <span class="desc-text">Pelanggan dengan bonus tersedia</span>
            </div>
        </div>

    </div>
</x-filament-widgets::widget>
