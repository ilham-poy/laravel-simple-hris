<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Portal Internal - HandToHand Logistics</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <style>
        /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
        @layer theme {

            :root,
            :host {
                --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            }
        }

        @layer base {

            *,
            :after,
            :before {
                box-sizing: border-box;
                border: 0 solid;
                margin: 0;
                padding: 0
            }
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>

<body class="bg-[#F8FAFC] dark:bg-[#090D16] text-[#1E293B] dark:text-[#E2E8F0] min-h-screen flex flex-col justify-between p-6">

    <!-- Header / Status Hub -->
    <header class="w-full max-w-md mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <!-- Icon Brand menggunakan Amber khas Filament -->
            <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center text-white font-bold text-base shadow-sm shadow-amber-500/20">
                H
            </div>
            <div>
                <span class="font-bold text-base tracking-tight block leading-tight">HandToHand<span class="text-amber-500">.</span></span>
                <span class="text-[11px] text-[#64748B] dark:text-[#94A3B8] block -mt-0.5">Logistics Internal System</span>
            </div>
        </div>
        <!-- Status Badge -->
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            System Online
        </span>
    </header>

    <!-- Center Container: Direct Portal Action -->
    <main class="w-full max-w-md mx-auto my-auto">
        <div class="bg-white dark:bg-[#111827] border border-[#E2E8F0] dark:border-[#1F2937] rounded-2xl shadow-xl p-6 sm:p-8">
            <!-- Title & Internal Notification -->
            <div class="mb-6">
                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 text-[11px] font-semibold tracking-wide uppercase">
                    Portal Internal
                </span>
                <h1 class="text-xl font-bold tracking-tight mt-2">Sistem Manajemen SDM & Operasional</h1>
                <p class="text-xs text-[#64748B] dark:text-[#94A3B8] mt-1 leading-relaxed">
                    Akses khusus Karyawan, HRD, dan Tim Operasional PT HandToHand Logistics Indonesia. Silakan masuk dengan akun yang terdaftar.
                </p>
            </div>

            <!-- Direct Button ke Login Filament (Menggunakan warna kuning/amber-500 senada dengan tombol Sign In) -->
            <div class="space-y-3 pt-2">
                <a
                    href="{{ url('/admin') }}"
                    class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-medium text-sm rounded-lg shadow-sm transition-all">
                    <span>Masuk Dashboard Hand To Hand</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>

                <p class="text-[11px] text-center text-[#64748B] dark:text-[#94A3B8]">
                    Anda akan diarahkan ke halaman login.
                </p>
            </div>

            <!-- Help Desk Info -->
            <div class="mt-6 pt-4 border-t border-[#F1F5F9] dark:border-[#1F2937] text-center">
                <p class="text-[11px] text-[#64748B] dark:text-[#94A3B8]">
                    Kendala akses atau akun terkunci? Hubungi Email
                    <br />
                    <span class="font-medium text-[#1E293B] dark:text-[#E2E8F0]">it-support@hand.to.hand.com</span>
                </p>
            </div>
        </div>
    </main>

    <!-- Minimalist Footer -->
    <footer class="w-full max-w-md mx-auto text-center text-xs text-[#64748B] dark:text-[#94A3B8]">
        <p>&copy; {{ date('Y') }} PT HandToHand Logistics Indonesia. All rights reserved.</p>
    </footer>

</body>

</html>