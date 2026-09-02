<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>
            {{
                config(
                    'app.name',
                    'Laravel',
                )
            }}
        </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />

        <!-- Scripts -->
        @vite (['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <style>
        /* Styling khusus untuk Scrollbar yang elegan */
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px; /* Mengatur ketebalan scrollbar horizontal */
            width: 6px; /* Mengatur ketebalan scrollbar vertikal */
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc; /* Warna latar (slate-50) */
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* Warna batang scroll (slate-300) */
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* Warna batang saat disorot/hover (slate-400) */
        }
    </style>

    <body class="font-sans antialiased bg-gray-50 text-gray-900 max-w-screen">
        <x-loading-screen />
        <div class="min-h-screen flex">
            @include ('layouts.sidebar')

            <div
                class="flex-1 flex flex-col md:ml-64 transition-all duration-300"
            >
                @include ('layouts.navigation')

                @isset ($header)
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div
                            class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
                        >
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="flex-1">
                    <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                        <x-breadcrumb />
                    </div>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
