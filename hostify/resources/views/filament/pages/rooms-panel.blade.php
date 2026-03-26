<x-filament-panels::page>

{{-- ── KPI CARDS ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-emerald-400 to-emerald-600
                dark:from-emerald-600 dark:to-emerald-800 shadow-lg shadow-emerald-100 dark:shadow-none text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['libre'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-emerald-100 mt-1">Habitaciones Libres</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5 mt-0.5">
                <x-heroicon-o-check-circle class="w-5 h-5 text-white" />
            </div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-400 to-rose-600
                dark:from-rose-600 dark:to-rose-800 shadow-lg shadow-rose-100 dark:shadow-none text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['ocupada'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-rose-100 mt-1">Ocupadas</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5 mt-0.5">
                <x-heroicon-o-user class="w-5 h-5 text-white" />
            </div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-amber-400 to-orange-500
                dark:from-amber-500 dark:to-orange-700 shadow-lg shadow-amber-100 dark:shadow-none text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['sucia'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-amber-100 mt-1">Limpieza Requerida</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5 mt-0.5">
                <x-heroicon-o-sparkles class="w-5 h-5 text-white" />
            </div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-slate-400 to-slate-600
                dark:from-slate-600 dark:to-slate-800 shadow-lg shadow-slate-100 dark:shadow-none text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['no_disponible'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-slate-200 mt-1">No Disponibles</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5 mt-0.5">
                <x-heroicon-o-wrench class="w-5 h-5 text-white" />
            </div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

</div>

{{-- ── HABITACIONES POR PISO ─────────────────────────────────────── --}}
@forelse ($pisos as $floor => $rooms)

    <div class="mb-10">

        {{-- Encabezado del piso --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-1 h-6 bg-blue-500 dark:bg-blue-400 rounded-full"></div>
                <h2 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest">
                    Piso {{ $floor }}
                </h2>
            </div>
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500
                         bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                {{ str_pad(count($rooms), 2, '0', STR_PAD_LEFT) }} habitaciones
            </span>
        </div>

        {{-- Grid de habitaciones --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">

            @foreach ($rooms as $room)
                @php
                    $activeReservation = $room->reservations->first();

                    $topBar = match($room->status) {
                        'libre'         => 'from-emerald-400 to-emerald-500',
                        'ocupada'       => 'from-rose-400 to-rose-500',
                        'sucia'         => 'from-amber-400 to-orange-400',
                        'no_disponible' => 'from-slate-300 to-slate-400',
                        default         => 'from-gray-200 to-gray-300',
                    };

                    $cardBg = match($room->status) {
                        'libre'         => 'bg-white dark:bg-gray-900 border-slate-100 dark:border-slate-700',
                        'ocupada'       => 'bg-white dark:bg-gray-900 border-slate-100 dark:border-slate-700',
                        'sucia'         => 'bg-white dark:bg-gray-900 border-slate-100 dark:border-slate-700',
                        'no_disponible' => 'bg-slate-50 dark:bg-gray-850 border-slate-200 dark:border-slate-700 opacity-70',
                        default         => 'bg-white dark:bg-gray-900 border-slate-100 dark:border-slate-700',
                    };

                    $badge = match($room->status) {
                        'libre'         => 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
                        'ocupada'       => 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
                        'sucia'         => 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
                        'no_disponible' => 'bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:border-slate-600',
                        default         => '',
                    };

                    $label = match($room->status) {
                        'libre'         => 'Libre',
                        'ocupada'       => 'Ocupada',
                        'sucia'         => 'Sucia',
                        'no_disponible' => 'No disponible',
                        default         => $room->status,
                    };

                    $statusIcon = match($room->status) {
                        'libre'         => 'heroicon-o-check-circle',
                        'ocupada'       => 'heroicon-o-user',
                        'sucia'         => 'heroicon-o-sparkles',
                        'no_disponible' => 'heroicon-o-wrench',
                        default         => 'heroicon-o-question-mark-circle',
                    };

                    $numberColor = match($room->status) {
                        'no_disponible' => 'text-slate-400 dark:text-slate-500',
                        default         => 'text-slate-800 dark:text-white',
                    };

                    $menuOptions = [
                        'libre'         => ['icon' => 'heroicon-o-check-circle', 'label' => 'Libre',         'color' => 'text-emerald-500'],
                        'ocupada'       => ['icon' => 'heroicon-o-user',          'label' => 'Ocupada',       'color' => 'text-rose-500'],
                        'sucia'         => ['icon' => 'heroicon-o-sparkles',      'label' => 'Sucia',         'color' => 'text-amber-500'],
                        'no_disponible' => ['icon' => 'heroicon-o-wrench',        'label' => 'No disponible', 'color' => 'text-slate-400'],
                    ];
                @endphp

                <div class="group rounded-2xl border {{ $cardBg }} shadow-sm
                    hover:shadow-xl hover:-translate-y-1
                    transition-all duration-200 flex flex-col">

                    {{-- Barra de color superior --}}
                    <div class="h-1.5 bg-gradient-to-r {{ $topBar }} rounded-t-2xl"></div>

                    <div class="p-4 flex flex-col gap-2 flex-1">

                        {{-- Número + badge --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-extrabold {{ $numberColor }}">
                                {{ $room->number }}
                            </span>
                            <span class="text-[10px] font-semibold {{ $badge }} rounded-xl px-2 py-0.5 flex items-center gap-1">
                                <x-dynamic-component :component="$statusIcon" class="w-3 h-3" />
                                {{ $label }}
                            </span>
                        </div>

                        {{-- Tipo de habitación --}}
                        <p class="text-xs font-medium text-slate-600 dark:text-slate-300 truncate">
                            {{ $room->roomType->name ?? '—' }}
                        </p>

                        {{-- Info dinámica según estado --}}
                        <div class="min-h-[18px]">
                            @if ($activeReservation)
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate flex items-center gap-1">
                                    <x-heroicon-o-user-circle class="w-3.5 h-3.5 shrink-0 text-rose-400" />
                                    {{ $activeReservation->guest->full_name ?? '—' }}
                                </p>
                            @elseif ($room->status === 'sucia' && $room->status_changed_at)
                                <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5 shrink-0" />
                                    {{ $room->status_changed_at->diffForHumans() }}
                                </p>
                            @elseif ($room->status === 'libre')
                                <p class="text-xs text-emerald-500 dark:text-emerald-400 flex items-center gap-1">
                                    <x-heroicon-o-check-circle class="w-3.5 h-3.5 shrink-0" />
                                    Disponible
                                </p>
                            @endif
                        </div>

                        {{-- ── BOTÓN GESTIONAR + DROPDOWN ──────────── --}}
                        <div class="relative mt-auto pt-1">
                            <button
                                wire:click="toggleMenu('{{ $room->id }}')"
                                class="w-full text-xs font-semibold rounded-xl px-3 py-2
                                    flex items-center justify-center gap-1.5 transition-all duration-150
                                    bg-slate-100 dark:bg-slate-700 hover:bg-blue-500 dark:hover:bg-blue-600
                                    text-slate-600 dark:text-slate-300 hover:text-white
                                    border border-slate-200 dark:border-slate-600 hover:border-blue-500">
                                <x-heroicon-o-ellipsis-horizontal class="w-4 h-4" />
                                Gestionar
                            </button>

                            {{-- Dropdown de estados --}}
                            @if(isset($openMenus[$room->id]))
                               <div class="absolute bottom-full left-0 right-0 mb-1.5 z-[999]
                                            bg-white dark:bg-gray-800
                                            border border-slate-200 dark:border-slate-600
                                            rounded-2xl shadow-2xl overflow-hidden">

                                    <div class="px-3 pt-2.5 pb-1">
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                            Cambiar estado
                                        </p>
                                    </div>

                                    @foreach($menuOptions as $status => $option)
                                        @if($status !== $room->status)
                                            <button
                                                wire:click="changeStatus('{{ $room->id }}', '{{ $status }}')"
                                                class="w-full text-left px-3 py-2.5 text-sm flex items-center gap-2.5
                                                       text-slate-700 dark:text-slate-200
                                                       hover:bg-slate-50 dark:hover:bg-slate-700
                                                       transition-colors duration-100
                                                       border-t border-slate-100 dark:border-slate-700">
                                                <x-dynamic-component
                                                    :component="$option['icon']"
                                                    class="w-4 h-4 shrink-0 {{ $option['color'] }}" />
                                                <span>{{ $option['label'] }}</span>
                                            </button>
                                        @endif
                                    @endforeach

                                    <button
                                        wire:click="toggleMenu('{{ $room->id }}')"
                                        class="w-full text-center px-3 py-2 text-xs text-slate-400 dark:text-slate-500
                                               hover:bg-slate-50 dark:hover:bg-slate-700
                                               transition-colors duration-100
                                               border-t border-slate-100 dark:border-slate-700
                                               flex items-center justify-center gap-1">
                                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                        Cancelar
                                    </button>

                                </div>
                            @endif
                        </div>

                    </div>
                </div>

            @endforeach
        </div>
    </div>

@empty
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <x-heroicon-o-building-office-2 class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" />
        <p class="text-slate-500 dark:text-slate-400 font-medium">No hay habitaciones activas registradas.</p>
        <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Agrega habitaciones desde el panel de administración.</p>
    </div>
@endforelse

</x-filament-panels::page>
