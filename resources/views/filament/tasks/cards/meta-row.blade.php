@php
    /** @var \App\Models\Task $record */
    $record = $getRecord();

    // ===== Mapas iguais aos ToggleButtons =====
    $priorityLabels = ['low'=>'Baixa','medium'=>'Média','high'=>'Alta','urgent'=>'Urgente'];
    $priorityIcons  = ['low'=>'heroicon-m-arrow-down','medium'=>'heroicon-m-minus','high'=>'heroicon-m-arrow-up','urgent'=>'heroicon-m-fire'];
    $priorityColors = ['low'=>'gray','medium'=>'info','high'=>'warning','urgent'=>'danger'];

    $typeLabels = ['epic'=>'Epic','task'=>'Tarefa','bug'=>'Bug','feature'=>'Feature','improvement'=>'Melhoria'];
    $typeIcons  = ['epic'=>'heroicon-m-sparkles','task'=>'heroicon-m-clipboard-document-check','bug'=>'heroicon-m-bug-ant','feature'=>'heroicon-m-sparkles','improvement'=>'heroicon-m-wrench-screwdriver'];
    $typeColors = ['epic'=>'info','task'=>'primary','bug'=>'danger','feature'=>'success','improvement'=>'warning'];

    // ===== Helpers p/ cores (bg/text/ring) =====
    $tone = fn(string $color) => match($color) {
        'gray'    => 'bg-gray-50 text-gray-700 ring-gray-200 dark:bg-gray-900/30 dark:text-gray-300 dark:ring-gray-800',
        'info'    => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/30 dark:text-sky-300 dark:ring-sky-800',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800',
        'danger'  => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800',
        'primary' => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-800',
        default   => 'bg-gray-50 text-gray-700 ring-gray-200 dark:bg-gray-900/30 dark:text-gray-300 dark:ring-gray-800',
    };

    // ===== PRIORITY =====
    $pKey   = $record->priority ?? 'low';
    $pLab   = $priorityLabels[$pKey] ?? ucfirst((string)$pKey);
    $pIcon  = $priorityIcons[$pKey]  ?? null;
    $pCol   = $priorityColors[$pKey] ?? 'gray';
    $pClass = $tone($pCol);

    // ===== TYPE =====
    $tKey   = $record->type_task ?? 'task';
    $tLab   = $typeLabels[$tKey] ?? ucfirst((string)$tKey);
    $tIcon  = $typeIcons[$tKey]  ?? null;
    $tCol   = $typeColors[$tKey] ?? 'gray';
    $tClass = $tone($tCol);

    // ===== TEMPO =====
    // total_spent_seconds / total_spent_formatted (accessors do model)
    $baseSeconds = (int) ($record->total_spent_seconds ?? 0);
    $baseFmt     = $record->total_spent_formatted ?? '00:00';
    $running     = (bool) ($record->activeTracking ?? null);
@endphp

<div class="flex  justify-between gap-3">
    {{-- ESQUERDA: Prioridade + Tempo --}}
    <div class="flex items-center gap-4">
        {{-- Prioridade (label + badge) --}}
        <div class="flex flex-col items-center gap-2">
            <span class="text-[11px] uppercase tracking-wide text-gray-500">Prioridade</span>
            <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 {{ $pClass }}">
                @if ($pIcon)
                    <x-filament::icon :icon="$pIcon" class="h-3.5 w-3.5" />
                @endif
                {{ $pLab }}
            </span>
        </div>
    </div>

    {{-- DIREITA: Tipo (label + badge) --}}
    <div class="flex flex-col items-center gap-2">
        <span class="text-[11px] uppercase tracking-wide text-gray-500">Tipo</span>
        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 {{ $tClass }}">
            @if ($tIcon)
                <x-filament::icon :icon="$tIcon" class="h-3.5 w-3.5" />
            @endif
            {{ $tLab }}
        </span>
    </div>
</div>
