<style>
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translate3d(-15px, 0, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translate3d(15px, 0, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 12px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    .animate-fade-in-left {
        animation: fadeInLeft 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: transform, opacity;
    }

    .animate-fade-in-right {
        animation: fadeInRight 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: transform, opacity;
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: transform, opacity;
    }

    .anim-delay-150 {
        animation-delay: 100ms;
    }

    .anim-delay-300 {
        animation-delay: 200ms;
    }
</style>

<div class="flex min-h-screen items-stretch bg-gray-50 font-sans overflow-hidden">
    <!-- Left Column: Form -->
    <div class="relative flex flex-col justify-between w-full lg:w-[460px] xl:w-[520px] shrink-0 p-8 sm:p-12 md:p-16 bg-white border-r border-gray-150 shadow-xl z-10 animate-fade-in-left">
        <!-- Loading Overlay -->
        <div wire:loading.flex wire:target="authenticate" class="absolute inset-0 bg-white/80 backdrop-blur-[2px] flex flex-col items-center justify-center z-50 transition-all duration-300">
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin h-9 w-9 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-semibold text-indigo-950 tracking-wide">Signing in...</span>
            </div>
        </div>

        <!-- Top branding -->
        <div class="flex items-center gap-3 animate-fade-in-up">
            <div class="p-2.5 bg-indigo-50 rounded-xl text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M14 8H8"/><path d="M16 12H8"/><path d="M13 16H8"/></svg>
            </div>
            <div>
                <span class="text-lg font-black tracking-tight text-gray-950">HL SALES</span>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mt-[-2px]">Receivables Mgmt</span>
            </div>
        </div>

        <!-- Form main container -->
        <div class="my-auto py-8 animate-fade-in-up anim-delay-150">
            <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 mb-2">
                Sign in to your account
            </h1>
            <p class="text-sm text-gray-500 mb-8">
                Welcome back! Please enter your email and password to access the dashboard.
            </p>

            <x-filament-panels::form wire:submit.prevent="authenticate" class="animate-fade-in-up anim-delay-300">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="true"
                    class="mt-6"
                />
            </x-filament-panels::form>
        </div>

        <!-- Bottom footer -->
        <div class="text-xs text-gray-400 pt-6 border-t border-gray-100 animate-fade-in-up anim-delay-300">
            &copy; {{ date('Y') }} HL Sales & Receivables. All rights reserved.
        </div>
    </div>

    <!-- Right Column: Illustration -->
    <div class="hidden lg:flex flex-col justify-center items-center flex-1 bg-gradient-to-br from-indigo-50 via-blue-50 to-slate-100 relative p-16 overflow-hidden animate-fade-in-right">
        <!-- Background Grid Pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808008_1px,transparent_1px),linear-gradient(to_bottom,#80808008_1px,transparent_1px)] bg-[size:24px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)]"></div>
        
        <div class="relative w-full max-w-[520px] z-10">
            <img src="{{ asset('images/login-illustration.png') }}" alt="HL Sales Illustration" class="w-full h-auto drop-shadow-xl">
        </div>
        <div class="mt-8 text-center z-10">
            <h2 class="text-2xl font-bold text-indigo-900">HL Sales & Receivables</h2>
            <p class="text-slate-500 text-sm mt-2">Manage your sales, receivables & bonuses with ease</p>
        </div>
    </div>
</div>
