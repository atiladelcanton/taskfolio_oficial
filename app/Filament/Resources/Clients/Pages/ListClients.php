<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'Clientes';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Novo Cliente')->icon('heroicon-s-plus'),
        ];
    }
}
