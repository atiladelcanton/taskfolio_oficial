<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class DocumentInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('CPF/CNPJ');

        $this->placeholder('000.000.000-00 ou 00.000.000/0000-00');

        $this->helperText('Digite apenas números. A máscara será aplicada automaticamente.');

        $this->maxLength(18);

        $this->required();

        $this->live(onBlur: true);

        // Formatar ao carregar (edição)
        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return $state;
            }

            $state = preg_replace('/\D/', '', $state);

            if (strlen($state) <= 11) {
                // CPF: 000.000.000-00
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $state);
            }

            // CNPJ: 00.000.000/0000-00
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $state);
        });

        // Salvar sem formatação
        $this->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state));

        // Máscara com Alpine.js
        $this->extraInputAttributes([
            'x-data' => new RawJs(<<<'JS'
                {
                    init() {
                        this.formatDocument();
                    },
                    formatDocument() {
                        let value = $el.value.replace(/\D/g, '');

                        if (value.length <= 11) {
                            // CPF: 000.000.000-00
                            if (value.length > 9) {
                                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
                            } else if (value.length > 6) {
                                value = value.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
                            } else if (value.length > 3) {
                                value = value.replace(/(\d{3})(\d{0,3})/, '$1.$2');
                            }
                        } else {
                            // CNPJ: 00.000.000/0000-00
                            if (value.length > 12) {
                                value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
                            } else if (value.length > 8) {
                                value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
                            } else if (value.length > 5) {
                                value = value.replace(/(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
                            } else if (value.length > 2) {
                                value = value.replace(/(\d{2})(\d{0,3})/, '$1.$2');
                            }
                        }

                        $el.value = value;
                    }
                }
            JS),
            'x-on:input' => 'formatDocument()',
            'x-on:blur' => 'formatDocument()',
        ]);

        // Validação de CPF/CNPJ
        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) === 11) {
                    if (! self::isValidCPF($value)) {
                        $fail('O CPF informado é inválido.');
                    }
                } elseif (strlen($value) === 14) {
                    if (! self::isValidCNPJ($value)) {
                        $fail('O CNPJ informado é inválido.');
                    }
                } else {
                    $fail('O documento deve ter 11 dígitos (CPF) ou 14 dígitos (CNPJ).');
                }
            };
        });
    }

    /**
     * Ativar validação de unicidade
     */
    public function uniqueInTable(?string $table = null, ?string $column = null, bool $ignoreRecord = true): static
    {
        // Se não passar a tabela, tenta pegar do contexto do formulário
        if (! $table) {
            // Será resolvido em tempo de execução pelo Filament
            $this->unique(ignoreRecord: $ignoreRecord);
        } else {
            // Tabela específica
            $this->unique(table: $table, column: $column ?? $this->getName(), ignoreRecord: $ignoreRecord);
        }

        return $this;
    }

    /**
     * Validação de CPF
     */
    private static function isValidCPF(string $cpf): bool
    {
        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        $digit1 = 11 - ($sum % 11);
        if ($digit1 > 9) {
            $digit1 = 0;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        $digit2 = 11 - ($sum % 11);
        if ($digit2 > 9) {
            $digit2 = 0;
        }

        return $digit1 == $cpf[9] && $digit2 == $cpf[10];
    }

    /**
     * Validação de CNPJ
     */
    private static function isValidCNPJ(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }

        $length = 12;
        $numbers = substr($cnpj, 0, $length);
        $digits = substr($cnpj, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = $length; $i >= 1; $i--) {
            $sum += (int) $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        if ($result != $digits[0]) {
            return false;
        }

        $length = 13;
        $numbers = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = $length; $i >= 1; $i--) {
            $sum += (int) $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        return $result == $digits[1];
    }

    /**
     * Desabilitar validação de dígitos verificadores
     */
    public function withoutDigitsValidation(): static
    {
        // Remove a validação personalizada
        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) !== 11 && strlen($value) !== 14) {
                    $fail('O documento deve ter 11 dígitos (CPF) ou 14 dígitos (CNPJ).');
                }
            };
        });

        return $this;
    }

    /**
     * Aceitar apenas CPF
     */
    public function cpfOnly(): static
    {
        $this->label('CPF');
        $this->placeholder('000.000.000-00');
        $this->helperText('Digite apenas números do CPF.');
        $this->maxLength(14);

        $this->extraInputAttributes([
            'x-data' => new RawJs(<<<'JS'
                {
                    init() {
                        this.formatCPF();
                    },
                    formatCPF() {
                        let value = $el.value.replace(/\D/g, '');

                        if (value.length > 11) {
                            value = value.slice(0, 11);
                        }

                        if (value.length > 9) {
                            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
                        } else if (value.length > 6) {
                            value = value.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
                        } else if (value.length > 3) {
                            value = value.replace(/(\d{3})(\d{0,3})/, '$1.$2');
                        }

                        $el.value = value;
                    }
                }
            JS),
            'x-on:input' => 'formatCPF()',
            'x-on:blur' => 'formatCPF()',
        ]);

        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return $state;
            }
            $state = preg_replace('/\D/', '', $state);

            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $state);
        });

        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) !== 11) {
                    $fail('O CPF deve ter 11 dígitos.');

                    return;
                }

                if (! self::isValidCPF($value)) {
                    $fail('O CPF informado é inválido.');
                }
            };
        });

        return $this;
    }

    /**
     * Aceitar apenas CNPJ
     */
    public function cnpjOnly(): static
    {
        $this->label('CNPJ');
        $this->placeholder('00.000.000/0000-00');
        $this->helperText('Digite apenas números do CNPJ.');
        $this->maxLength(18);

        $this->extraInputAttributes([
            'x-data' => new RawJs(<<<'JS'
                {
                    init() {
                        this.formatCNPJ();
                    },
                    formatCNPJ() {
                        let value = $el.value.replace(/\D/g, '');

                        if (value.length > 14) {
                            value = value.slice(0, 14);
                        }

                        if (value.length > 12) {
                            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
                        } else if (value.length > 8) {
                            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
                        } else if (value.length > 5) {
                            value = value.replace(/(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
                        } else if (value.length > 2) {
                            value = value.replace(/(\d{2})(\d{0,3})/, '$1.$2');
                        }

                        $el.value = value;
                    }
                }
            JS),
            'x-on:input' => 'formatCNPJ()',
            'x-on:blur' => 'formatCNPJ()',
        ]);

        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return $state;
            }
            $state = preg_replace('/\D/', '', $state);

            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $state);
        });

        $this->rule(function () {
            return function (string $attribute, $value, \Closure $fail) {
                $value = preg_replace('/\D/', '', $value);

                if (strlen($value) !== 14) {
                    $fail('O CNPJ deve ter 14 dígitos.');

                    return;
                }

                if (! self::isValidCNPJ($value)) {
                    $fail('O CNPJ informado é inválido.');
                }
            };
        });

        return $this;
    }
}
