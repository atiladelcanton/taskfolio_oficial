@php
    /** @var \App\Models\Task $record */
    $record = $getRecord();

    $collabName = $record->collaborator ? Str::of($record->collaborator->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('') : null;

    // Accessors no model (já usamos antes)
    $baseSeconds = (int) ($record->total_spent_seconds ?? 0);
    $baseFmt     = $record->total_spent_formatted ?? '00:00';
    $running     = (bool) ($record->activeTracking ?? null);
@endphp

<div class="mt-2 flex items-center justify-between gap-3">

    {{-- Colaborador (esquerda) --}}
    <div class="flex flex-col  gap-2">
        <span class="text-[11px] uppercase tracking-wide text-gray-500">Colaborador</span>

        @if($collabName)
            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                         bg-violet-100 text-violet-700 ring-1 ring-violet-200
                         dark:bg-violet-900/30 dark:text-violet-300 dark:ring-violet-800">
                {{ $collabName }}
            </span>
        @else
            <span class="text-xs text-gray-500">—</span>
        @endif
    </div>

    {{-- Tempo (direita) --}}
    <span
        @if($running)
            x-data="{
                s: {{ $baseSeconds }},
                t(){ this.s++ },
                fmt(){ const h=Math.floor(this.s/3600), m=Math.floor((this.s%3600)/60);
                      return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0'); }
            }"
        x-init="setInterval(()=>t(), 1000)"
        @endif
        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded ring-1
               ring-gray-200 dark:ring-gray-700 font-mono tabular-nums text-[12px]"
        title="Tempo total gasto"
    >
        <x-filament::icon icon="heroicon-m-clock" class="h-3.5 w-3.5 text-gray-500" />
        <span @if($running) x-text="fmt()" @endif>
            @unless($running) {{ $baseFmt }} @endunless
        </span>
    </span>
</div>
