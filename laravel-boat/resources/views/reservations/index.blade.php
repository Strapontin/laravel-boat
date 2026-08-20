<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservations | {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <main class="mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8">
        <header
            class="mb-8 flex flex-col gap-4 border-b border-stone-200 pb-6 dark:border-stone-800 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-sky-700 dark:text-sky-400">Boat
                    club</p>
                <h1 class="text-3xl font-semibold tracking-tight">Reserve a boat</h1>
                <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Choose a morning or afternoon slot over the
                    next seven days.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs text-stone-600 dark:text-stone-400">
                <div class="flex gap-3">
                    <span class="inline-flex items-center gap-2">
                        <span class="size-2 rounded-full bg-sky-600"></span>
                        Available
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="size-2 rounded-full bg-stone-300 dark:bg-stone-700"></span>
                        Booked
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="cursor-pointer border border-stone-300 px-3 py-2 font-medium text-stone-700 transition hover:border-stone-400 hover:bg-stone-100 dark:border-stone-700 dark:text-stone-300 dark:hover:border-stone-600 dark:hover:bg-stone-800">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        @if (session('error'))
            <div class="mb-6 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300"
                role="alert">
                {{ session('error') }}
            </div>
        @endif

        <section
            class="min-w-0 overflow-hidden border border-stone-200 bg-white shadow-sm dark:border-stone-800 dark:bg-stone-900">
            <div class="w-full max-w-full overflow-x-auto overflow-y-hidden">
                <div class="w-max min-w-full">
                    <div
                        class="grid grid-cols-[180px_repeat(14,minmax(65px,1fr))] border-b border-stone-200 bg-stone-100/70 dark:border-stone-800 dark:bg-stone-800/60">
                        <div
                            class="border-r border-stone-200 px-4 py-4 text-xs font-semibold uppercase tracking-wider text-stone-500 dark:border-stone-800 dark:text-stone-400">
                            Boat</div>
                        @foreach ($dates as $date)
                            @foreach (['morning' => 'AM', 'afternoon' => 'PM'] as $slot => $label)
                                <div
                                    class="border-r border-stone-200 px-2 py-3 text-center last:border-r-0 dark:border-stone-800">
                                    <div class="text-xs font-semibold text-stone-900 dark:text-stone-100">
                                        {{ \Carbon\Carbon::parse($date)->format('D j') }}</div>
                                    <div
                                        class="mt-1 text-[10px] font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">
                                        {{ $label }}</div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    @forelse ($boats as $boat)
                        @php
                            $reservations = $boat->reservations->keyBy(fn($reservation) => $reservation->date . '_' . $reservation->slot);
                        @endphp
                        <div
                            class="grid grid-cols-[180px_repeat(14,minmax(65px,1fr))] border-b border-stone-200 last:border-b-0 dark:border-stone-800">
                            <div class="flex items-center gap-3 border-r border-stone-200 px-4 py-4 dark:border-stone-800">
                                <img src="{{ asset('images/Boat-' . ucfirst($boat->color) . '.png') }}"
                                    alt="Bateau {{ $boat->color }}" class="h-10 w-14 shrink-0 object-contain">
                                <div>
                                    <div class="font-medium">Bateau {{ $boat->color }}</div>
                                </div>
                            </div>
                            @foreach ($dates as $date)
                                @foreach (['morning', 'afternoon'] as $slot)
                                    @php $isBooked = $reservations->has($date . '_' . $slot); @endphp
                                    <div
                                        class="flex items-center justify-center border-r border-stone-200 px-1.5 py-2 last:border-r-0 dark:border-stone-800">
                                        <form method="POST" action="{{ route('reservations.store') }}" class="w-full">
                                            @csrf
                                            <input type="hidden" name="boat_id" value="{{ $boat->id }}">
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <input type="hidden" name="slot" value="{{ $slot }}">
                                            <button type="submit" @disabled($isBooked)
                                                class="h-10 w-full border px-1 text-[10px] font-semibold uppercase tracking-wide transition {{ $isBooked ? 'cursor-not-allowed border-stone-200 bg-stone-100 text-stone-400 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-500' : 'cursor-pointer border-sky-200 bg-sky-50 text-sky-700 hover:border-sky-500 hover:bg-sky-600 hover:text-white dark:border-sky-900 dark:bg-sky-950/50 dark:text-sky-300 dark:hover:bg-sky-600 dark:hover:text-white' }}"
                                                @if ($isBooked) aria-label="Booked" @endif>
                                                {{ $isBooked ? 'Booked' : 'Book' }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-stone-500 dark:text-stone-400">No boats are
                            available to reserve.</div>
                    @endforelse
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300"
                role="status">
                {{ session('success') }}
            </div>
        @endif
    </main>
</body>

</html>