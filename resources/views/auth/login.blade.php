<x-guest-layout>
    <div
        class="min-h-screen flex items-center justify-center bg-sky-200 py-12 px-4 sm:px-6 lg:px-8 w-full"
    >
        <div
            class="max-w-md w-full bg-white rounded-xl shadow-sm p-8 space-y-8 border border-gray-100"
        >
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">
                    Rekrutmen Ormawa
                </h2>
                <p class="mt-2 text-sm text-gray-600">Politeknik Statistika STIS</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            @if (session('error'))
                <div
                    class="bg-red-50 text-red-600 p-4 rounded-lg text-sm text-center"
                >
                    {{
                        session(
                            'error',
                        )
                    }}
                </div>
            @endif

            <div class="mt-8">
                <a
                    href="{{ route('google.login') }}"
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                >
                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                        alt="Google Logo"
                        class="h-5 w-5 mr-3"
                    />
                    Masuk dengan Akun Google
                </a>
            </div>

            <div class="text-center mt-4">
                <p class="text-xs text-gray-500">*Gunakan email kampus (<span class="font-bold text-gray-700">@stis
                    .ac.id</span>)</p>
            </div>
        </div>
    </div>
</x-guest-layout>
