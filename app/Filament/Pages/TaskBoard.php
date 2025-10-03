<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\TaskTrackingTime;
use Filament\Actions\Action;

use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action as ModalAction;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Phiki\Phast\Text;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardPage;
use Relaticle\Flowforge\Column;

class TaskBoard extends BoardPage
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Task Board';
    protected static ?string $title = 'Task Board';

    /**
     * @throws \Exception
     */
    public function board(Board $board): Board
    {
        return $board
            ->query($this->getEloquentQuery())
            ->recordTitleAttribute('title')
            ->columnIdentifier('status')
            ->positionIdentifier('position')

            ->cardActions([
                Action::make('acoes') // pode manter o name 'toggle_time' se preferir
                ->label('Ações') // antes era "Tempo"
                ->icon('heroicon-m-clock')
                    ->color('gray')
                    ->size('lg')

                    // Abre modal direto com conteúdo (sem requireConfirmation)
                    ->modalHeading('Cronômetro & Histórico')
                    ->modalWidth('xl')
                    ->modalIcon('heroicon-m-clock')

                    // Botões padrão do modal
                    ->modalSubmitActionLabel(fn ($record) => $record->activeTracking ? 'Pausar' : 'Iniciar')
                    ->modalCancelActionLabel('Fechar')

                    // === Botão extra: Assumir tarefa (no mesmo modal, sem abrir outro) ===
                    ->extraModalFooterActions([
                        ModalAction::make('assumir')
                            ->label(function ($record) {
                                $current = $record->collaborator->name ?? null;
                                return $record->collaborator_id === Auth::id()
                                    ? 'Você já é o responsável'
                                    : ($current ? "Assumir tarefa (substituir {$current})" : 'Assumir tarefa');
                            })
                            ->icon('heroicon-m-user-plus')
                            ->color('primary')
                            ->disabled(fn ($record) => $record->collaborator_id === Auth::id())

                            ->action(function ($record) {
                                $record->update(['collaborator_id' => Auth::id()]);
                                Notification::make()
                                    ->success()
                                    ->title('Tarefa assumida')
                                    ->body('Você agora é o colaborador responsável por esta tarefa.')
                                    ->send();
                                $this->dispatch('refresh');
                            }),
                    ])

                    // Conteúdo do modal (status + histórico)
                    ->modalContent(function ($record) {
                        $tz = auth()->user()->timezone ?? config('app.timezone', 'UTC');

                        $rows = TaskTrackingTime::query()
                            ->where('task_id', $record->id)
                            ->orderBy('start_at', 'desc')
                            ->get()
                            ->groupBy(fn ($t) => $t->start_at->timezone($tz)->toDateString())
                            ->map(function ($group) {
                                return $group->sum(function ($t) {
                                    $end = $t->stop_at ?? now();
                                    return $t->start_at->diffInSeconds($end);
                                });
                            })
                            ->sortKeysDesc();

                        $grand = $rows->sum();

                        // Status atual
                        $activeTracking = $record->activeTracking;
                        $now = now();
                        $currentSeconds = 0;
                        $currentSince = null;

                        if ($activeTracking) {
                            $currentSeconds = $activeTracking->start_at->diffInSeconds($now);
                            $currentSince   = $activeTracking->start_at->timezone($tz);
                        }

                        $format = function (int $seconds) {
                            $h = intdiv($seconds, 3600);
                            $m = intdiv(($seconds % 3600), 60);
                            $s = $seconds % 60;
                            return sprintf('%02d:%02d:%02d', $h, $m, $s);
                        };

                        return view('filament/tasks/partials/tracking-playpause-modal', [
                            'rows'           => $rows,              // 'YYYY-MM-DD' => total em segundos
                            'grand'          => $grand,             // total geral em segundos
                            'format'         => $format,
                            'tz'             => $tz,
                            'active'         => (bool) $activeTracking,
                            'currentSince'   => $currentSince,
                            'currentSeconds' => $currentSeconds,
                            'collaborator'   => $record->collaborator->name ?? null,
                            'collaboratorId' => $record->collaborator_id,
                        ]);
                    })

                    // Submit do modal: Iniciar ou Pausar (sem abrir outro modal)
                    ->action(function ($record) {
                        $activeTracking = $record->activeTracking;

                        if ($activeTracking) {
                            // PAUSAR
                            $activeTracking->update(['stop_at' => now()]);
                            $record->updateTotalTimeWorked();

                            $duration = $activeTracking->duration_in_hours ?? round(
                                ($activeTracking->start_at->diffInSeconds($activeTracking->stop_at) / 3600), 2
                            );

                            Notification::make()
                                ->success()
                                ->title('Tempo pausado')
                                ->body("Sessão de {$duration}h registrada. Total acumulado: {$record->total_time_worked}h")
                                ->send();
                        } else {
                            // INICIAR
                            TaskTrackingTime::create([
                                'task_id'         => $record->id,
                                'collaborator_id' => auth()->id(),
                                'start_at'        => now(),
                                'stop_at'         => null,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Tempo iniciado')
                                ->body('Cronômetro em execução')
                                ->send();
                        }

                        $this->dispatch('refresh');
                    })
            ])
            ->cardSchema(fn (Schema $schema) => $schema->components([
                Grid::make()
                    ->extraAttributes(fn ($record) => [
                        'class' => ($record->type_task === 'bug' && in_array($record->priority, ['high','urgent']))
                            ? 'bg-red-50 ring-1 ring-red-200 rounded-xl p-2 dark:bg-red-900/20 dark:ring-red-800'
                            : '',
                    ])
                    ->schema([

                        ViewEntry::make('meta_row')
                            ->view('filament/tasks/cards/meta-row') // Blade logo abaixo
                            ->columnSpanFull(),

                        ViewEntry::make('collab_time_row')
                            ->view('filament/tasks/cards/collab-time-row') // blade abaixo
                            ->columnSpanFull(),
                    ])
            ]))
            ->columns([
                Column::make('backlog')->label('Planejado')->color('gray'),
                Column::make('refinement')->label('Refinamento')->color('primary'),
                Column::make('todo')->label('A Fazer')->color('secondary'),
                Column::make('doing')->label('Executando')->color('info'),
                Column::make('validation')->label('Validação')->color('warning'),
                Column::make('ready_to_deploy')->label('Aguardando Deploy')->color('danger'),
                Column::make('done')->label('Concluído')->color('success'),
            ]);
    }

    public function getEloquentQuery(): Builder
    {
        return Task::query()->with(['collaborator', 'activeTracking']);
    }
}
