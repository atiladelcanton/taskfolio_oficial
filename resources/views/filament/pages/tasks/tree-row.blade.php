{{-- resources/views/filament/tasks/partials/tree-row.blade.php --}}
@php
    // Mapas iguais aos da sua Table
    $typeMap = [
        'epic' => ['color' => 'purple', 'icon' => 'heroicon-m-trophy', 'label' => 'Epic'],
        'feature' => ['color' => 'success','icon' => 'heroicon-m-sparkles','label' => 'Feature'],
        'task' => ['color' => 'info','icon' => 'heroicon-m-clipboard-document-list','label' => 'Task'],
        'bug' => ['color' => 'danger','icon' => 'heroicon-m-bug-ant','label' => 'Bug'],
        'improvement' => ['color' => 'warning','icon' => 'heroicon-m-arrow-trending-up','label' => 'Melhoria'],
    ];
    $statusMap = [
        'backlog'=>['color'=>'gray','label'=>'Backlog'],
        'refinement'=>['color'=>'info','label'=>'Refinamento'],
        'todo'=>['color'=>'warning','label'=>'To Do'],
        'doing'=>['color'=>'primary','label'=>'Doing'],
        'validation'=>['color'=>'purple','label'=>'Validação'],
        'ready_to_deploy'=>['color'=>'success','label'=>'Pronto'],
        'done'=>['color'=>'success','label'=>'Concluído'],
    ];
    $prioMap = [
        'low'=>['color'=>'gray','icon'=>'heroicon-m-arrow-down','label'=>'Baixa'],
        'medium'=>['color'=>'info','icon'=>'heroicon-m-minus','label'=>'Média'],
        'high'=>['color'=>'warning','icon'=>'heroicon-m-arrow-up','label'=>'Alta'],
        'urgent'=>['color'=>'danger','icon'=>'heroicon-m-fire','label'=>'Urgente'],
    ];
    $type   = strtolower($node->type_task ?? 'task');
    $status = strtolower($node->status ?? 'backlog');
    $prio   = strtolower($node->priority ?? 'medium');

    $typeCfg   = $typeMap[$type] ?? ['color'=>'gray','icon'=>'heroicon-m-question-mark-circle','label'=>ucfirst($type)];
    $statusCfg = $statusMap[$status] ?? ['color'=>'gray','label'=>ucfirst($status)];
    $prioCfg   = $prioMap[$prio] ?? ['color'=>'gray','icon'=>'heroicon-m-flag','label'=>ucfirst($prio)];
@endphp

<div
    x-data="{ open: true }"
    x-on:tree-expand-all.window="open = true"
    x-on:tree-collapse-all.window="open = false"
>
    {{-- linha atual --}}
    <div class="grid items-center gap-3 px-2 py-2 hover:bg-gray-50 dark:hover:bg-gray-800/60"
         style="grid-template-columns: 28px 1.2fr 0.7fr 0.7fr 0.8fr 0.6fr 0.5fr 120px;">
        {{-- gutter + chevron --}}
        <div class="tree-gutter {{ $node->children->isNotEmpty() ? 'has-children' : '' }}">
            @if($node->children->isNotEmpty())
                <button type="button" x-on:click="open=!open"
                        class="rounded p-1 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4 transition"
                                      x-bind:class="open ? 'rotate-90' : ''"/>
                </button>
            @endif
        </div>

        {{-- título com indentação por nível --}}
        <div class="min-w-0">
            <div class="flex items-center gap-2" style="padding-left: {{ max($level,0)*14 }}px;">
                <span class="truncate font-medium text-sm">
                        {{ $node->title }}
                </span>
            </div>
            <div class="pl-[{{ max($level,0)*14 }}px] text-xs text-gray-500">
                @if($node->assignee) {{ $node->assignee->name }} • @endif
                @if($node->spent_hours) {{ number_format($node->spent_hours,1,',','.') }}h • @endif
                @if($node->due_at) prev: {{ optional($node->due_at)->format('d/m/Y') }} @endif
            </div>
        </div>

        {{-- Tipo --}}
        <div>
            <x-filament::badge
                color="{{ $typeCfg['color'] }}"
                class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[11px] font-medium rounded-md bg-opacity-10"
            >
               <div class="flex px-1.5 py-0.5">
                   <x-filament::icon :icon="$typeCfg['icon']" class="w-3 h-3" />
                   {{ $typeCfg['label'] }}
               </div>
            </x-filament::badge>
        </div>

        {{-- Status --}}
        <div>
            <x-filament::badge
                color="{{ $statusCfg['color'] }}"
                class="text-[11px] font-medium rounded-md bg-opacity-10"
            >
                {{ $statusCfg['label'] }}
            </x-filament::badge>
        </div>

        {{-- Responsável --}}
        <div class="truncate text-sm">
            {{ $node->assignee->name ?? '—' }}
        </div>

        {{-- Prioridade --}}
        <div>
            <x-filament::badge
                color="{{ $prioCfg['color'] }}"
                class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[11px] font-medium rounded-md bg-opacity-10"
            >
                <div class="flex px-1.5 py-0.5">
                <x-filament::icon :icon="$prioCfg['icon']" class="w-3 h-3" />
                {{ $prioCfg['label'] }}
                </div>
            </x-filament::badge>
        </div>

        {{-- Previsão --}}
        <div class="text-sm">
            {{ $node->due_at ? $node->due_at->format('d/m/Y') : '—' }}
        </div>

        {{-- Ações --}}

    </div>

    {{-- filhos --}}
    @if($node->children->isNotEmpty())
        <div x-show="open" x-collapse>
            @foreach ($node->children as $child)
                @include('filament.pages.tasks.tree-row', ['node' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
