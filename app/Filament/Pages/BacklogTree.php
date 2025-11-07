<?php

namespace App\Filament\Pages;


use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Collaborator;
use App\Models\Sprint;
use App\Models\Task;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use UnitEnum;

class BacklogTree extends Page implements HasForms
{

    use InteractsWithForms;

    protected static string $resource = TaskResource::class;
    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Backlog (Árvore)';
    protected static ?string $title = 'Backlog (Árvore)';
    protected static string|null|UnitEnum $navigationGroup = 'Tasks';
    public ?int $sprintId = null;
    public ?string $status = null;
    public ?int $assigneeId = null;
    public string $search = '';
    public ?array $data = [
        'search' => '',
        'sprintId' => null,
        'status' => '',
        'assigneeId' => null,
    ];
    protected string $view = 'filament.pages.tasks.backlog-tree';

    public function mount(): void
    {

        $this->form->fill($this->data);
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('search')
                    ->label('Pesquisar')
                    ->placeholder('Título ou código...')
                    ->live(debounce: 400),

                Select::make('sprintId')
                    ->label('Sprint')
                    ->options(fn() => Sprint::query()->orderBy('title')->pluck('title', 'id'))
                    ->searchable()
                    ->native(false)
                    ->placeholder('Todas')
                    ->live(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Backlog' => 'Backlog',
                        'To Do' => 'To Do',
                        'Doing' => 'Doing',
                        'Review' => 'Review',
                        'Done' => 'Done',
                    ])
                    ->native(false)
                    ->placeholder('Todos')
                    ->live(),

                Select::make('assigneeId')
                    ->label('Responsável')
                    ->options(fn() => Collaborator::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->placeholder('Todos')
                    ->live(),
            ])
            ->columns(4)
            ->statePath('data'); // importante
    }

    public function getTree(): Collection
    {
        $d = $this->data ?? [];

        return Task::query()
            ->when($d['sprintId'] ?? null, fn($q, $v) => $q->where('sprint_id', $v))
            ->when(($d['status'] ?? '') !== '', fn($q, $v) => $q->where('status', $v))
            ->when($d['assigneeId'] ?? null, fn($q, $v) => $q->where('assignee_id', $v))

            ->whereNull('parent_id')
        ->with('children.children.children')
        ->orderBy('type_task')->orderBy('id')
        ->get();
    }
}
