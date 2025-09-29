<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class PhoneColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Telefone');

        $this->searchable();

        $this->sortable();

        $this->copyable();

        $this->copyMessage('Telefone copiado!');

        // Formatação automática
        $this->formatStateUsing(function ($state) {
            if (!$state) {
                return '-';
            }

            // Remove tudo que não é número
            $state = preg_replace('/\D/', '', $state);

            if (empty($state)) {
                return '-';
            }

            if (strlen($state) <= 10) {
                // Fixo: (00) 0000-0000
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $state);
            }

            // Celular: (00) 00000-0000
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $state);
        });

        // Tooltip mostrando o tipo
        $this->tooltip(function ($state) {
            if (!$state) return null;

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) == 10) {
                return 'Telefone Fixo';
            } elseif (strlen($state) == 11) {
                return 'Celular';
            }

            return 'Telefone';
        });
    }

    /**
     * Adicionar badge de tipo (Fixo/Celular)
     */
    public function withBadge(): static
    {
        $this->badge()
            ->color(fn ($state) => strlen(preg_replace('/\D/', '', $state ?? '')) == 11 ? 'success' : 'info');

        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 10) {
                return 'Fixo: ' . preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $state);
            }

            return 'Cel: ' . preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $state);
        });

        return $this;
    }

    /**
     * Adicionar ícone baseado no tipo
     */
    public function withIcon(): static
    {
        $this->icon(function ($state) {
            $digits = strlen(preg_replace('/\D/', '', $state ?? ''));

            if ($digits == 11) {
                return 'heroicon-o-device-phone-mobile'; // Celular
            } elseif ($digits == 10) {
                return 'heroicon-o-phone'; // Fixo
            }

            return 'heroicon-o-phone';
        });

        return $this;
    }

    /**
     * Adicionar cor baseada no tipo
     */
    public function withColor(): static
    {
        $this->color(fn ($state) => strlen(preg_replace('/\D/', '', $state ?? '')) == 11
            ? 'success'  // Celular = verde
            : 'info'     // Fixo = azul
        );

        return $this;
    }

    /**
     * Com link para WhatsApp (apenas celular)
     */
    public function withWhatsApp(): static
    {
        $this->url(function ($state) {
            $phone = preg_replace('/\D/', '', $state ?? '');

            // Apenas celular (11 dígitos)
            if (strlen($phone) == 11) {
                return "https://wa.me/55{$phone}";
            }

            return null;
        });

        $this->openUrlInNewTab();

        $this->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->iconColor('success');

        return $this;
    }

    /**
     * Apenas números (sem formatação)
     */
    public function onlyNumbers(): static
    {
        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            return preg_replace('/\D/', '', $state) ?: '-';
        });

        return $this;
    }

    /**
     * Mascarado (ocultar dígitos do meio)
     */
    public function masked(): static
    {
        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 10) {
                // Fixo: (00) ****-0000
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) ****-$3', $state);
            }

            // Celular: (00) *****-0000
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) *****-$3', $state);
        });

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

            // Mostra apenas últimos 4 dígitos
            return '****-' . substr($state, -4);
        });

        return $this;
    }

    /**
     * Apenas telefone fixo
     */
    public function landlineOnly(): static
    {
        $this->label('Telefone Fixo');

        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) != 10) {
                return '-';
            }

            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $state);
        });

        return $this;
    }

    /**
     * Apenas celular
     */
    public function mobileOnly(): static
    {
        $this->label('Celular');

        $this->formatStateUsing(function ($state) {
            if (!$state) return '-';

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) != 11) {
                return '-';
            }

            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $state);
        });

        return $this;
    }
}
