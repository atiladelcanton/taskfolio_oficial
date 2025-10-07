<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatusEnum: string
{
    case Backlog = 'backlog';
    case Refinement = 'refinement';
    case Todo = 'todo';
    case Doing = 'doing';
    case Validation = 'validation';
    case ReadyToDeploy = 'ready_to_deploy';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Backlog       => 'Backlog',
            self::Refinement    => 'Refinamento',
            self::Todo          => 'To Do',
            self::Doing         => 'Doing',
            self::Validation    => 'Validação',
            self::ReadyToDeploy => 'Pronto',
            self::Done          => 'Concluído',
            self::Cancelled     => 'Cancelada',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Backlog       => 'heroicon-m-inbox',
            self::Refinement    => 'heroicon-m-magnifying-glass',
            self::Todo          => 'heroicon-m-list-bullet',
            self::Doing         => 'heroicon-m-play',
            self::Validation    => 'heroicon-m-eye',
            self::ReadyToDeploy => 'heroicon-m-rocket-launch',
            self::Done          => 'heroicon-m-check-badge',
            self::Cancelled     => 'heroicon-m-x-circle',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Backlog       => 'gray',
            self::Refinement    => 'info',
            self::Todo          => 'warning',
            self::Doing         => 'primary',
            self::Validation    => 'purple',
            self::ReadyToDeploy => 'success',
            self::Done          => 'success',
            self::Cancelled     => 'danger',
        };
    }

    /** Regras de transição — ajuste se quiser mais rigidez */
    public function allowedTargets(): array
    {
        return match ($this) {
            self::Backlog       => [self::Refinement, self::Todo, self::Cancelled],
            self::Refinement    => [self::Backlog, self::Todo, self::Cancelled],
            self::Todo          => [self::Backlog, self::Doing, self::Cancelled, self::Cancelled],
            self::Doing         => [self::Todo, self::Validation, self::Cancelled, self::Cancelled],
            self::Validation    => [self::Doing, self::ReadyToDeploy, self::Done, self::Cancelled],
            self::ReadyToDeploy => [self::Done, self::Doing, self::Cancelled],
            self::Done, self::Cancelled => [self::Todo, self::Doing, self::Cancelled],
        };
    }

    public function canTransitionTo(self $to): bool
    {
        if ($this === $to) {
            return true;
        }

        return in_array($to, $this->allowedTargets(), true);
    }

    // Helpers para o Filament
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($c) =>[$c->value=>$c->label()])->all();
    }

    public static function icons(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($c) =>[$c->value=>$c->icon()])->all();
    }

    public static function colors(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($c) =>[$c->value=>$c->color()])->all();
    }
}
