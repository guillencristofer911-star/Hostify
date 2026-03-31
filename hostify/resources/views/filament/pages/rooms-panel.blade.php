<x-filament-panels::page>

{{-- ── FILTRO DE FECHA (solo admin/supervisor/recepcionista) ──────── --}}
@if(! $isHousekeeper)
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <label class="text-sm font-medium text-gray-400">Disponibilidad para:</label>
    <input
        type="date"
        wire:model.live="filterDate"
        class="rounded-lg border border-gray-600 bg-gray-800 px-3 py-1.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
    />
    <button
        wire:click="$set('filterDate', '{{ now()->toDateString() }}')"
        class="text-xs text-gray-400 hover:text-white underline transition-colors">
        Hoy
    </button>
    @if($filterDate && $filterDate !== now()->toDateString())
        <span class="text-xs font-semibold text-primary-400 bg-primary-900/30 border border-primary-700 rounded-full px-3 py-1">
            Viendo: {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') }}
        </span>
    @endif
</div>
@else
{{-- Camarera: encabezado personalizado --}}
<div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 flex items-center gap-3">
    <x-heroicon-o-sparkles class="w-6 h-6 text-amber-500 shrink-0" />
    <div>
        <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Tus habitaciones asignadas hoy</p>
        <p class="text-xs text-amber-600 dark:text-amber-400">{{ \Carbon\Carbon::now()->translatedFormat('l d \d\e F') }}</p>
    </div>
</div>
@endif

{{-- ── KPI CARDS ─────────────────────────────────────────────────── --}}
@if(! $isHousekeeper)
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-emerald-400 to-emerald-600
                dark:from-emerald-600 dark:to-emerald-800 shadow-lg text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['libre'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-emerald-100 mt-1">Habitaciones Libres</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5"><x-heroicon-o-check-circle class="w-5 h-5 text-white" /></div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-400 to-rose-600
                dark:from-rose-600 dark:to-rose-800 shadow-lg text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['ocupada'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-rose-100 mt-1">Ocupadas</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5"><x-heroicon-o-user class="w-5 h-5 text-white" /></div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-amber-400 to-orange-500
                dark:from-amber-500 dark:to-orange-700 shadow-lg text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['sucia'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-amber-100 mt-1">Limpieza Requerida</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5"><x-heroicon-o-sparkles class="w-5 h-5 text-white" /></div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-slate-400 to-slate-600
                dark:from-slate-600 dark:to-slate-800 shadow-lg text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ str_pad($resumen['no_disponible'], 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm font-medium text-slate-200 mt-1">No Disponibles</p>
            </div>
            <div class="bg-white/20 rounded-2xl p-2.5"><x-heroicon-o-wrench class="w-5 h-5 text-white" /></div>
        </div>
        <div class="absolute -bottom-3 -right-3 w-20 h-20 rounded-full bg-white/10"></div>
    </div>

</div>
@endif

{{-- ── HABITACIONES POR PISO ─────────────────────────────────────── --}}
@forelse ($pisos as $floor => $rooms)
    <div class="mb-10">

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-1 h-6 bg-blue-500 dark:bg-blue-400 rounded-full"></div>
                <h2 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest">
                    Piso {{ $floor }}
                </h2>
            </div>
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                {{ str_pad(count($rooms), 2, '0', STR_PAD_LEFT) }} habitaciones
            </span>
        </div>

        {{-- Grid: 1 col móvil, 2 tablet, 3-4 desktop --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

            @foreach ($rooms as $room)
                @php
                    $activeReservation = $room->reservations->first();
                    $session = $this->getSessionForRoom($room->id);

                    $statusValue = $room->status instanceof \App\Enums\RoomStatus
                        ? $room->status->value
                        : (string) $room->status;

                    $topBar = match($statusValue) {
                        'libre'         => 'from-emerald-400 to-emerald-500',
                        'ocupada'       => 'from-rose-400 to-rose-500',
                        'sucia'         => 'from-amber-400 to-orange-400',
                        'no_disponible' => 'from-slate-300 to-slate-400',
                        default         => 'from-gray-200 to-gray-300',
                    };

                    $cardBg = match($statusValue) {
                        'no_disponible' => 'bg-slate-50 dark:bg-gray-850 border-slate-200 dark:border-slate-700 opacity-70',
                        default         => 'bg-white dark:bg-gray-900 border-slate-100 dark:border-slate-700',
                    };

                    $badge = match($statusValue) {
                        'libre'         => 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
                        'ocupada'       => 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
                        'sucia'         => 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
                        'no_disponible' => 'bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:border-slate-600',
                        default         => '',
                    };

                    $label = match($statusValue) {
                        'libre'         => 'Libre',
                        'ocupada'       => 'Ocupada',
                        'sucia'         => 'Sucia',
                        'no_disponible' => 'No disponible',
                        default         => $statusValue,
                    };

                    $statusIcon = match($statusValue) {
                        'libre'         => 'heroicon-o-check-circle',
                        'ocupada'       => 'heroicon-o-user',
                        'sucia'         => 'heroicon-o-sparkles',
                        'no_disponible' => 'heroicon-o-wrench',
                        default         => 'heroicon-o-question-mark-circle',
                    };

                    $menuOptions = [
                        'libre'         => ['icon' => 'heroicon-o-check-circle', 'label' => 'Libre',         'color' => 'text-emerald-500'],
                        'ocupada'       => ['icon' => 'heroicon-o-user',          'label' => 'Ocupada',       'color' => 'text-rose-500'],
                        'sucia'         => ['icon' => 'heroicon-o-sparkles',      'label' => 'Sucia',         'color' => 'text-amber-500'],
                        'no_disponible' => ['icon' => 'heroicon-o-wrench',        'label' => 'No disponible', 'color' => 'text-slate-400'],
                    ];

                    $sessionStatus = $session?->status instanceof \App\Enums\CleaningStatus
                        ? $session->status->value
                        : (string) ($session?->status ?? '');
                @endphp

                <div class="group rounded-2xl border {{ $cardBg }} shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col">

                    {{-- Barra color superior --}}
                    <div class="h-2 bg-gradient-to-r {{ $topBar }} rounded-t-2xl"></div>

                    <div class="p-5 flex flex-col gap-3 flex-1">

                        {{-- Número + badge --}}
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-extrabold text-slate-800 dark:text-white">
                                {{ $room->number }}
                            </span>
                            <span class="text-xs font-semibold {{ $badge }} rounded-xl px-2.5 py-1 flex items-center gap-1">
                                <x-dynamic-component :component="$statusIcon" class="w-3.5 h-3.5" />
                                {{ $label }}
                            </span>
                        </div>

                        {{-- Tipo --}}
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300 truncate">
                            {{ $room->roomType->name ?? '—' }}
                        </p>

                        {{-- Info dinámica --}}
                        <div class="min-h-[20px]">
                            @if ($activeReservation)
                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate flex items-center gap-1">
                                    <x-heroicon-o-user-circle class="w-4 h-4 shrink-0 text-rose-400" />
                                    {{ $activeReservation->guest->full_name ?? '—' }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1 mt-0.5">
                                    <x-heroicon-o-arrow-right-end-on-rectangle class="w-3.5 h-3.5 shrink-0" />
                                    Sale {{ \Carbon\Carbon::parse($activeReservation->check_out_date)->translatedFormat('d M') }}
                                </p>
                            @elseif ($statusValue === 'sucia')
                                <p class="text-sm text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
                                    Pendiente de limpieza
                                </p>
                            @elseif ($statusValue === 'libre')
                                <p class="text-sm text-emerald-500 dark:text-emerald-400 flex items-center gap-1">
                                    <x-heroicon-o-check-circle class="w-4 h-4 shrink-0" />
                                    Disponible
                                </p>
                            @endif
                        </div>

                        {{-- ── BOTONES CAMARERA (RF-18, RF-19) ─────── --}}
                        @if($isHousekeeper && $session)
                            <div class="flex flex-col gap-2 mt-auto pt-1">

                                @if($sessionStatus === 'pendiente')
                                    {{-- Botón táctil grande: Iniciar --}}
                                    <button
                                        wire:click="startCleaning('{{ $session->id }}')"
                                        wire:loading.attr="disabled"
                                        class="w-full min-h-[48px] text-base font-bold rounded-2xl px-4 py-3
                                               bg-amber-500 hover:bg-amber-400 active:bg-amber-600
                                               text-white flex items-center justify-center gap-2
                                               transition-all duration-150 shadow-md shadow-amber-200 dark:shadow-none">
                                        <x-heroicon-o-play class="w-5 h-5" />
                                        Iniciar limpieza
                                    </button>

                                @elseif($sessionStatus === 'en_proceso')
                                    {{-- Timer en proceso --}}
                                    <div class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 font-medium px-1">
                                        <x-heroicon-o-clock class="w-4 h-4 animate-pulse" />
                                        En proceso
                                        @if($session->started_at)
                                            · desde {{ $session->started_at->format('H:i') }}
                                        @endif
                                    </div>

                                    {{-- Botón táctil grande: Terminar --}}
                                    <button
                                        wire:click="finishCleaning('{{ $session->id }}')"
                                        wire:loading.attr="disabled"
                                        class="w-full min-h-[48px] text-base font-bold rounded-2xl px-4 py-3
                                               bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600
                                               text-white flex items-center justify-center gap-2
                                               transition-all duration-150 shadow-md shadow-emerald-200 dark:shadow-none">
                                        <x-heroicon-o-check-circle class="w-5 h-5" />
                                        Marcar como lista
                                    </button>

                                    {{-- Botón nota/foto RF-19 --}}
                                    <button
                                        wire:click="openNoteModal('{{ $session->id }}', '{{ $room->id }}', '{{ $room->number }}')"
                                        class="w-full min-h-[44px] text-sm font-semibold rounded-2xl px-4 py-2.5
                                               bg-slate-100 dark:bg-slate-700 hover:bg-blue-50 dark:hover:bg-blue-900/30
                                               text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400
                                               border border-slate-200 dark:border-slate-600 hover:border-blue-300
                                               flex items-center justify-center gap-2
                                               transition-all duration-150">
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4" />
                                        Agregar nota / foto
                                    </button>

                                @elseif($sessionStatus === 'terminada')
                                    <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 font-semibold px-1">
                                        <x-heroicon-o-check-badge class="w-5 h-5" />
                                        Completada
                                        @if($session->finished_at)
                                            · {{ $session->finished_at->format('H:i') }}
                                        @endif
                                    </div>
                                @endif

                            </div>

                        @else
                            {{-- ── BOTÓN GESTIONAR ADMIN (dropdown) ── --}}
                            <div class="relative mt-auto pt-1">
                                <button
                                    wire:click="toggleMenu('{{ $room->id }}')"
                                    class="w-full min-h-[44px] text-sm font-semibold rounded-2xl px-3 py-2.5
                                           flex items-center justify-center gap-1.5 transition-all duration-150
                                           bg-slate-100 dark:bg-slate-700 hover:bg-blue-500 dark:hover:bg-blue-600
                                           text-slate-600 dark:text-slate-300 hover:text-white
                                           border border-slate-200 dark:border-slate-600 hover:border-blue-500">
                                    <x-heroicon-o-ellipsis-horizontal class="w-4 h-4" />
                                    Gestionar
                                </button>

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
                                            @if($status !== $statusValue)
                                                <button
                                                    wire:click="changeStatus('{{ $room->id }}', '{{ $status }}')"
                                                    class="w-full text-left px-3 py-3 text-sm flex items-center gap-2.5
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
                                            class="w-full text-center px-3 py-2.5 text-xs text-slate-400 dark:text-slate-500
                                                   hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-100
                                                   border-t border-slate-100 dark:border-slate-700
                                                   flex items-center justify-center gap-1">
                                            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                            Cancelar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>

            @endforeach
        </div>
    </div>

@empty
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <x-heroicon-o-building-office-2 class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" />
        @if($isHousekeeper)
            <p class="text-slate-500 dark:text-slate-400 font-medium">No tienes habitaciones asignadas hoy.</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Consulta con tu supervisor.</p>
        @else
            <p class="text-slate-500 dark:text-slate-400 font-medium">No hay habitaciones activas registradas.</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Agrega habitaciones desde el panel de administración.</p>
        @endif
    </div>
@endforelse

{{-- ── MODAL NOTA / FOTO RF-19 ─────────────────────────────────────── --}}
@if($showNoteModal)
<div class="fixed inset-0 z-[1000] flex items-end sm:items-center justify-center px-4 pb-4 sm:pb-0"
     wire:click.self="closeNoteModal">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Sheet / Modal --}}
    <div class="relative w-full max-w-lg bg-white dark:bg-gray-900
                rounded-t-3xl sm:rounded-3xl shadow-2xl
                p-6 flex flex-col gap-5
                animate-slide-up">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-blue-500" />
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                    Nota — Hab. {{ $noteRoomNumber }}
                </h3>
            </div>
            <button
                wire:click="closeNoteModal"
                class="w-10 h-10 flex items-center justify-center rounded-full
                       bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400
                       hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        {{-- Textarea nota --}}
        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                Observación <span class="text-rose-400">*</span>
            </label>
            <textarea
                wire:model="noteText"
                rows="4"
                maxlength="500"
                placeholder="Ej: Falta toalla, daño en baño, cliente solicitó almohada extra..."
                class="w-full rounded-2xl border border-slate-200 dark:border-slate-600
                       bg-slate-50 dark:bg-slate-800
                       text-slate-800 dark:text-slate-200
                       px-4 py-3 text-base
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                       resize-none transition-colors"></textarea>
            @error('noteText')
                <p class="text-sm text-rose-500 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Upload foto --}}
        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                Foto (opcional)
            </label>

            <label class="flex flex-col items-center justify-center w-full min-h-[100px] rounded-2xl
                          border-2 border-dashed border-slate-300 dark:border-slate-600
                          bg-slate-50 dark:bg-slate-800
                          cursor-pointer hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20
                          transition-colors gap-2 p-4">
                @if($notePhoto)
                    <x-heroicon-o-check-circle class="w-8 h-8 text-emerald-500" />
                    <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                        {{ $notePhoto->getClientOriginalName() }}
                    </span>
                @else
                    <x-heroicon-o-camera class="w-8 h-8 text-slate-400" />
                    <span class="text-sm text-slate-500 dark:text-slate-400">Toca para adjuntar foto</span>
                    <span class="text-xs text-slate-400">JPG, PNG · Máx 4MB</span>
                @endif
                <input type="file" wire:model="notePhoto" accept="image/*" capture="environment" class="sr-only" />
            </label>

            @error('notePhoto')
                <p class="text-sm text-rose-500 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-4 h-4" /> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Acciones --}}
        <div class="flex gap-3 pt-1">
            <button
                wire:click="closeNoteModal"
                class="flex-1 min-h-[48px] rounded-2xl border border-slate-200 dark:border-slate-600
                       text-slate-600 dark:text-slate-300 font-semibold text-base
                       hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Cancelar
            </button>
            <button
                wire:click="saveNote"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                class="flex-2 flex-grow min-h-[48px] rounded-2xl
                       bg-blue-600 hover:bg-blue-500 active:bg-blue-700
                       text-white font-bold text-base
                       flex items-center justify-center gap-2
                       transition-all duration-150 shadow-md shadow-blue-200 dark:shadow-none">
                <span wire:loading.remove wire:target="saveNote">
                    <x-heroicon-o-check class="w-5 h-5 inline mr-1" /> Guardar nota
                </span>
                <span wire:loading wire:target="saveNote" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>

    </div>
</div>

<style>
@keyframes slide-up {
    from { transform: translateY(100%); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
.animate-slide-up { animation: slide-up 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
</style>
@endif

</x-filament-panels::page>