<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class DocumentColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('CPF/CNPJ');

        $this->searchable();

        $this->sortable();

        $this->copyable();

        $this->copyMessage('Documento copiado!');

        // Formatação automática de CPF/CNPJ
        $this->formatStateUsing(function ($state) {
            if (!$state) {
                return '-';
            }

            // Remove tudo que não é número
            $state = preg_replace('/\D/', '', $state);

            // Se estiver vazio após limpar, retorna traço
            if (empty($state)) {
                return '-';
            }

            if (strlen($state) <= 11) {
                // CPF: 000.000.000-00
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $state);
            }

            // CNPJ: 00.000.000/0000-00
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $state);
        });

        // Tooltip mostrando o tipo
        $this->tooltip(function ($state) {
            if (!$state) return null;

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) == 11) {
                return 'CPF';
            } elseif (strlen($state) == 14) {
                return 'CNPJ';
            }

            return 'Documento';
        });
    }

    /**
     * Mostrar apenas números (sem formatação)
     */
    public function Onlynumber(): static
    {
        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            return preg_replace('/\D/', '', $state) ?: '-';
        });

        return $this;
    }

    /**
     * Adicionar badge de tipo (CPF/CNPJ)
     */
    public function withBadge(): static
    {
        $this->badge()
            ->color(fn ($state) => strlen(preg_replace('/\D/', '', $state ?? '')) == 11 ? 'info' : 'success');

        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 11) {
                return 'CPF: ' . preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $state);
            }

            return 'CNPJ: ' . preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $state);
        });

        return $this;
    }

    /**
     * Apenas CPF
     */
    public function cpfOnly(): static
    {
        $this->label('CPF');

        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (empty($state) || strlen($state) != 11) {
                return '-';
            }

            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $state);
        });

        $this->tooltip('CPF');

        return $this;
    }

    /**
     * Apenas CNPJ
     */
    public function cnpjOnly(): static
    {
        $this->label('CNPJ');

        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (empty($state) || strlen($state) != 14) {
                return '-';
            }

            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $state);
        });

        $this->tooltip('CNPJ');

        return $this;
    }

    /**
     * Mascarar parcialmente (ocultar dígitos do meio)
     */
    public function masked(): static
    {
        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 11) {
                // CPF: 123.***.**9-01
                return preg_replace('/(\d{3})(\d{3})(\d{2})(\d{1})(\d{2})/', '$1.***.** $4-$5', $state);
            }

            // CNPJ: 12.***.***/**** -01
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.***.***/****-$5', $state);
        });

        return $this;
    }

    /**
     * Adicionar ícone
     */
    public function withIcon(): static
    {
        $this->icon(fn ($state) => strlen(preg_replace('/\D/', '', $state ?? '')) == 11
            ? 'heroicon-o-user'
            : 'heroicon-o-building-office-2'
        );

        $this->iconPosition('before');

        return $this;
    }

    /**
     * Adicionar cor baseada no tipo
     */
    public function withColor(): static
    {
        $this->color(fn ($state) => strlen(preg_replace('/\D/', '', $state ?? '')) == 11
            ? 'info'   // CPF = azul
            : 'success' // CNPJ = verde
        );

        return $this;
    }

    /**
     * Compacto (para mobile)
     */
    public function compact(): static
    {
        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 11) {
                // CPF compacto: ***789-01
                return '***' . substr($state, -6, 3) . '-' . substr($state, -2);
            }

            // CNPJ compacto: ***0000-01
            return '***' . substr($state, -6, 4) . '-' . substr($state, -2);
        });

        return $this;
    }
}
