{{-- resources/views/filament/tasks/backlog-tree.blade.php --}}
<x-filament-panels::page>
    <x-filament::section class="mb-3">
        {{ $this->form }}

        <div class="mt-3 flex items-center gap-2">
            <x-filament::button size="sm" x-on:click="$dispatch('tree-expand-all')">
                <x-filament::icon icon="heroicon-m-arrows-pointing-out" class="w-4 h-4 mr-1" />
                Expandir tudo
            </x-filament::button>
            <x-filament::button color="gray" size="sm" x-on:click="$dispatch('tree-collapse-all')">
                <x-filament::icon icon="heroicon-m-arrows-pointing-in" class="w-4 h-4 mr-1" />
                Contrair tudo
            </x-filament::button>
        </div>
    </x-filament::section>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        {{-- Cabeçalho da “tabela” --}}
        <div class="grid items-center gap-3 px-4 py-2 text-xs font-medium uppercase tracking-wide text-gray-500
                    dark:text-gray-400"
             style="grid-template-columns: 28px 1.2fr 0.7fr 0.7fr 0.8fr 0.6fr 0.5fr 120px;">
            <div></div>
            <div>Título</div>
            <div>Tipo</div>
            <div>Status</div>
            <div>Responsável</div>
            <div>Prioridade</div>
            <div>Previsão</div>
            <div class="text-right">Ações</div>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse ($this->getTree() as $root)
                @include('filament.pages.tasks.tree-row', ['node' => $root, 'level' => 0])
            @empty
                <div class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">Nenhum item encontrado.</div>
            @endforelse
        </div>
    </div>

    <style>
        /* gutter esquerdo com conector vertical */
        .tree-gutter { position: relative; width: 28px; }
        .tree-gutter.has-children::before{
            content:''; position:absolute; left:13px; top:18px; bottom:8px;
            border-left:1px solid rgba(229,231,235,.9);
        }
        @media (prefers-color-scheme: dark){
            .tree-gutter.has-children::before{ border-left-color: rgba(31,41,55,.9); }
        }
    </style>
</x-filament-panels::page>
