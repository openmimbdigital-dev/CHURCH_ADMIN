<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }} — {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-initial.svg') }}">
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .grid-dot {
            background-image: radial-gradient(rgba(255,255,255,.1) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-primary-950 to-slate-900 text-slate-100 antialiased">
    <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6">
        <div class="pointer-events-none absolute inset-0 grid-dot opacity-40"></div>
        <div class="pointer-events-none absolute -top-32 left-1/4 h-72 w-72 rounded-full bg-primary-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 right-1/4 h-64 w-64 rounded-full bg-primary-700/15 blur-3xl"></div>

        <div class="relative w-full max-w-lg">
            <div class="mb-8 flex flex-col items-center text-center">
                <img
                    src="{{ asset('images/logo-initial.svg') }}"
                    alt="{{ config('app.name') }}"
                    class="mb-5 h-16 w-16 rounded-2xl bg-white/5 p-1.5 shadow-lg ring-1 ring-white/10 sm:h-20 sm:w-20"
                >
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-primary-300/90">Church CFE</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-primary-200/20 bg-white/95 shadow-2xl shadow-slate-900/30 ring-1 ring-white/10 backdrop-blur-sm">
                <div class="border-b border-primary-100 bg-primary-50/80 px-6 py-4 text-center sm:px-8">
                    <p class="text-5xl font-extrabold tracking-tight text-primary-600 sm:text-6xl">{{ $code }}</p>
                </div>

                <div class="px-6 py-8 text-center sm:px-8">
                    <h1 class="text-xl font-bold text-slate-900 sm:text-2xl">{{ $heading }}</h1>
                    <p class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-slate-600">
                        {{ $message }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                            >
                                Ir al panel
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                            >
                                Ir al inicio de sesión
                            </a>
                        @endauth

                        <button
                            type="button"
                            onclick="window.history.back()"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-primary-200/80 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-primary-50/50"
                        >
                            Volver atrás
                        </button>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-slate-400">
                © {{ date('Y') }} {{ config('app.name') }} · Gestión educativa y administrativa
            </p>
        </div>
    </div>
</body>
</html>
