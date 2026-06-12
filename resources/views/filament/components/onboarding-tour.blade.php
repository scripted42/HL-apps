@if(auth()->check() && request()->routeIs('filament.admin.pages.dashboard'))
    <!-- Driver.js CSS & JS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Define the tour function globally
            window.startHLTour = function () {
                const driverObj = window.driver.js.driver({
                    showProgress: true,
                    allowClose: true, // Let them close it freely
                    steps: [
                        {
                            popover: {
                                title: 'Selamat Datang! 👋',
                                description: 'Mari kami pandu untuk mengenal aplikasi pengelolaan Sales & Piutang HL.',
                                side: "left",
                                align: 'start'
                            }
                        },
                        {
                            element: 'a[href*="/admin/products"]',
                            popover: {
                                title: 'Master Produk 📦',
                                description: 'Langkah 1: Daftarkan produk LM & BR Anda di sini beserta harga modal dan harga base.',
                                side: "right"
                            }
                        },
                        {
                            element: 'a[href*="/admin/customers"]',
                            popover: {
                                title: 'Master Pelanggan 👥',
                                description: 'Langkah 2: Daftarkan pelanggan Anda, atur diskon bertingkat (cascading), dan target jatah bonus.',
                                side: "right"
                            }
                        },
                        {
                            element: 'a[href*="/admin/transactions"]',
                            popover: {
                                title: 'Transaksi Bon 🧾',
                                description: 'Langkah 3: Buat transaksi Bon penjualan baru. Diskon bertingkat akan otomatis terhitung secara real-time di sini.',
                                side: "right"
                            }
                        },
                        {
                            element: '.fi-wi-stats-overview',
                            popover: {
                                title: 'Dashboard Statistik 📊',
                                description: 'Pantau total piutang, omzet, laba bersih, dan jatah bonus pelanggan secara real-time di sini.',
                                side: "bottom"
                            }
                        }
                    ],
                    onDestroyStarted: function () {
                        // Mark tour as completed in the database (optional fallback, but no longer blocks UX)
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        fetch('/admin/complete-tour', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        driverObj.destroy();
                    }
                });

                driverObj.drive();
            };

            // Event delegation to capture clicks on the start-tour button
            document.addEventListener('click', function (event) {
                const btn = event.target.closest('#start-tour-btn');
                if (btn) {
                    event.preventDefault();
                    window.startHLTour();
                }
            });

            // Manual trigger only via header button
        });
    </script>
@endif
