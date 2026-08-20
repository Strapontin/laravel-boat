<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration | {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-10 border-b border-stone-200 pb-6 dark:border-stone-800">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-sky-700 dark:text-sky-400">Boat club
            </p>
            <h1 class="text-3xl font-semibold tracking-tight">Administration des bateaux</h1>
        </header>

        <section>
            <h2 class="text-xl font-semibold">Bateaux dans l'entrepôt :</h2>
            <div class="mt-5 flex gap-4 overflow-x-auto pb-3">
                @forelse ($boats as $boat)
                <div
                    class="flex min-w-32 shrink-0 flex-col items-center gap-3 border border-stone-200 bg-white px-5 py-5 shadow-sm dark:border-stone-800 dark:bg-stone-900">
                    <img src="{{ asset('images/Boat-' . ucfirst($boat->color) . '.png') }}"
                        alt="Bateau {{ $boat->color }}" class="h-16 w-24 object-contain">
                    <span class="text-sm font-semibold">Bateau {{ $boat->color }}</span>
                    @php($nextReservation = $boat->reservations->first())
                    @if ($nextReservation)
                        <img src="{{ asset('images/Lock.png') }}" alt="Réservation" class="h-8 w-8 object-contain">
                        <span class="text-center text-xs text-stone-600 dark:text-stone-400">
                            {{ \Carbon\Carbon::parse($nextReservation->date)->locale('fr')->isoFormat('D MMMM YYYY') }}
                            <br>
                            {{ $nextReservation->slot === 'morning' ? 'Matin' : 'Après-midi' }}
                        </span>
                    @endif
                    <form class="reorganization-action hidden" method="POST"
                        action="{{ route('admin.boats.move-outside', $boat) }}">
                        @csrf
                        <button type="submit"
                            class="cursor-pointer border border-rose-600 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:text-rose-400 dark:hover:bg-rose-950">
                            Sortir
                        </button>
                    </form>
                </div>
                @empty
                <p class="text-sm text-stone-500 dark:text-stone-400">Aucun bateau réservé.</p>
                @endforelse
            </div>
        </section>

        <section class="mt-12 border-t border-stone-200 pt-8 dark:border-stone-800">
            <h2 class="text-xl font-semibold">Bateaux en dehors de l'entrepôt</h2>
            <ul
                class="mt-5 divide-y divide-stone-200 border-y border-stone-200 dark:divide-stone-800 dark:border-stone-800">
                @forelse ($boatsOutsideWarehouse as $boat)
                    <li class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/Boat-' . ucfirst($boat->color) . '.png') }}"
                                alt="Bateau {{ $boat->color }}" class="h-10 w-16 object-contain">
                            <span class="text-sm font-medium">Bateau {{ $boat->color }}</span>
                        </div>
                        <form class="reorganization-action hidden mt-3" method="POST"
                            action="{{ route('admin.boats.move-inside', $boat) }}">
                            @csrf
                            <button type="submit"
                                class="cursor-pointer border border-emerald-600 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:text-emerald-400 dark:hover:bg-emerald-950">
                                Rentrer
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="px-4 py-4 text-sm text-stone-500 dark:text-stone-400">Aucun bateau en dehors de l'entrepôt.
                    </li>
                @endforelse
            </ul>
            <button id="reorganize-boats" type="button"
                class="mt-6 cursor-pointer border border-sky-600 bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:ring-offset-stone-950">
                Réorganiser les bateaux
            </button>
        </section>
    </main>
    <script>
        document
            .getElementById('reorganize-boats')
            .addEventListener('click', function () {
                document
                    .querySelectorAll('.reorganization-action')
                    .forEach(function (action) {
                        action.classList.toggle('hidden');
                    });
            });
    </script>
</body>

</html>