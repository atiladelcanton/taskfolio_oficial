<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class PhoneInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Telefone');

        $this->placeholder('(00) 00000-0000');

        $this->helperText('Digite apenas números.');

        $this->maxLength(15);

        $this->tel();

        $this->live(onBlur: true);

        // Formatar ao carregar (edição)
        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return $state;
            }

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 10) {
                // Fixo: (00) 0000-0000
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $state);
            }

            // Celular: (00) 00000-0000
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $state);
        });

        // Salvar sem formatação
        $this->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state));

        // Máscara com Alpine.js
        $this->extraInputAttributes([
            'x-data' => new RawJs(<<<'JS'
                {
                    init() {
                        this.formatPhone();
                    },
                    formatPhone() {
                        let value = $el.value.replace(/\D/g, '');

                        // Limita a 11 dígitos
                        if (value.length > 11) {
                            value = value.slice(0, 11);
                        }

                        if (value.length <= 10) {
                            // Fixo: (00) 0000-0000
                            if (value.length > 6) {
                                value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                            } else if (value.length > 2) {
                                value = value.replace(/(\d{2})(\d{0,4})/, '($1) $2');
                            } else if (value.length > 0) {
                                value = value.replace(/(\d{0,2})/, '($1');
                            }
                        } else {
                            // Celular: (00) 00000-0000
                            if (value.length > 7) {
                                value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                            } else if (value.length > 2) {
                                value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                            } else if (value.length > 0) {
                                value = value.replace(/(\d{0,2})/, '($1');
                            }
                        }

                        $el.value = value;
                    }
                }
            JS),
            'x-on:input' => 'formatPhone()',
            'x-on:blur' => 'formatPhone()',
        ]);

        // Validação de telefone
        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) < 10) {
                    $fail('O telefone deve ter no mínimo 10 dígitos.');

                    return;
                }

                if (strlen($value) > 11) {
                    $fail('O telefone deve ter no máximo 11 dígitos.');

                    return;
                }

                // Validar DDD (11 a 99)
                $ddd = (int) substr($value, 0, 2);
                if ($ddd < 11 || $ddd > 99) {
                    $fail('DDD inválido.');
                }
            };
        });
    }

    /**
     * Apenas telefone fixo (10 dígitos)
     */
    public function landlineOnly(): static
    {
        $this->label('Telefone Fixo');
        $this->placeholder('(00) 0000-0000');
        $this->maxLength(14);

        $this->extraInputAttributes([
            'x-data' => new RawJs(<<<'JS'
                {
                    init() {
                        this.formatLandline();
                    },
                    formatLandline() {
                        let value = $el.value.replace(/\D/g, '');

                        if (value.length > 10) {
                            value = value.slice(0, 10);
                        }

                        if (value.length > 6) {
                            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                        } else if (value.length > 2) {
                            value = value.replace(/(\d{2})(\d{0,4})/, '($1) $2');
                        } else if (value.length > 0) {
                            value = value.replace(/(\d{0,2})/, '($1');
                        }

                        $el.value = value;
                    }
                }
            JS),
            'x-on:input' => 'formatLandline()',
        ]);

        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) !== 10) {
                    $fail('O telefone fixo deve ter 10 dígitos (DDD + 8 números).');
                }
            };
        });

        return $this;
    }

    /**
     * Apenas celular (11 dígitos)
     */
    public function mobileOnly(): static
    {
        $this->label('Celular');
        $this->placeholder('(00) 00000-0000');
        $this->maxLength(15);

        $this->extraInputAttributes([
            'x-data' => new RawJs(<<<'JS'
                {
                    init() {
                        this.formatMobile();
                    },
                    formatMobile() {
                        let value = $el.value.replace(/\D/g, '');

                        if (value.length > 11) {
                            value = value.slice(0, 11);
                        }

                        if (value.length > 7) {
                            value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                        } else if (value.length > 2) {
                            value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                        } else if (value.length > 0) {
                            value = value.replace(/(\d{0,2})/, '($1');
                        }

                        $el.value = value;
                    }
                }
            JS),
            'x-on:input' => 'formatMobile()',
        ]);

        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) !== 11) {
                    $fail('O celular deve ter 11 dígitos (DDD + 9 números).');
                }

                // Validar se começa com 9
                if (substr($value, 2, 1) !== '9') {
                    $fail('O número de celular deve começar com 9.');
                }
            };
        });

        return $this;
    }

    /**
     * Com WhatsApp
     */
    public function withWhatsApp(): static
    {
        $this->suffixIcon('heroicon-o-chat-bubble-bottom-center-text')
            ->suffixIconColor('success');

        return $this;
    }
}
