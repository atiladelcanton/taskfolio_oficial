# Taskfolio [<()>]


# 📋 Componentes Customizados - Filament

Este projeto utiliza componentes customizados do Filament para facilitar o desenvolvimento e garantir consistência em formulários e tabelas.

## 📦 Componentes Disponíveis

- [DocumentInput & DocumentColumn](#-documentinput--documentcolumn) - CPF/CNPJ
- [PhoneInput & PhoneColumn](#-phoneinput--phonecolumn) - Telefone

---

## 🆔 DocumentInput & DocumentColumn

Componentes para entrada e exibição de CPF/CNPJ com formatação automática e validação de dígitos verificadores.

### 📁 Localização

```
app/
├── Forms/Components/DocumentInput.php
└── Tables/Columns/DocumentColumn.php
```

### 🎯 DocumentInput (Formulários)

#### Uso Básico

```php
use App\Forms\Components\DocumentInput;

DocumentInput::make('document')
```

**Funcionalidades automáticas:**
- ✅ Máscara dinâmica (CPF: `000.000.000-00` | CNPJ: `00.000.000/0000-00`)
- ✅ Validação de dígitos verificadores
- ✅ Validação de unicidade (ignora registro atual na edição)
- ✅ Formatação em tempo real
- ✅ Salva apenas números no banco
- ✅ Carrega formatado na edição

#### Variações

```php
// 1. Com validação de unicidade
DocumentInput::make('document')
    ->uniqueInTable(),

// 2. Apenas CPF
DocumentInput::make('cpf')
    ->cpfOnly(),

// 3. Apenas CNPJ
DocumentInput::make('cnpj')
    ->cnpjOnly(),

// 4. Sem validação de dígitos verificadores
DocumentInput::make('document')
    ->withoutDigitsValidation(),

// 5. Customizando
DocumentInput::make('document')
    ->label('Documento da Empresa')
    ->helperText('Informe o CPF ou CNPJ')
    ->required(false),
```

#### Exemplo Completo

```php
use App\Forms\Components\DocumentInput;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('company_name')
                ->label('Empresa')
                ->required(),

            DocumentInput::make('document')
                ->uniqueInTable()
                ->required(),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required(),
        ]);
}
```

### 📊 DocumentColumn (Tabelas)

#### Uso Básico

```php
use App\Tables\Columns\DocumentColumn;

DocumentColumn::make('document')
```

**Funcionalidades automáticas:**
- ✅ Formatação automática (CPF/CNPJ)
- ✅ Searchable
- ✅ Sortable
- ✅ Copyable (copiar com um clique)
- ✅ Tooltip mostrando tipo
- ✅ Tratamento de valores vazios

#### Variações

```php
// 1. Básico (formatado)
DocumentColumn::make('document'),

// 2. Com badge colorido
DocumentColumn::make('document')
    ->withBadge(),
// Resultado: [CPF: 123.456.789-01] (azul) | [CNPJ: 12.345.678/0001-00] (verde)

// 3. Com ícone
DocumentColumn::make('document')
    ->withIcon(),
// 👤 CPF | 🏢 CNPJ

// 4. Com cor
DocumentColumn::make('document')
    ->withColor(),
// CPF (azul) | CNPJ (verde)

// 5. Mascarado (privacidade)
DocumentColumn::make('document')
    ->masked(),
// Resultado: 123.***.** 9-01

// 6. Compacto (mobile)
DocumentColumn::make('document')
    ->compact(),
// Resultado: ***789-01

// 7. Apenas números
DocumentColumn::make('document')
    ->onlyNumbers(),
// Resultado: 12345678901

// 8. Apenas CPF
DocumentColumn::make('cpf')
    ->cpfOnly(),

// 9. Apenas CNPJ
DocumentColumn::make('cnpj')
    ->cnpjOnly(),

// 10. Combinação
DocumentColumn::make('document')
    ->withIcon()
    ->withColor()
    ->copyable()
    ->searchable()
    ->sortable(),
```

#### Exemplo Completo

```php
use App\Tables\Columns\DocumentColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('company_name')
                ->label('Empresa')
                ->searchable(),

            DocumentColumn::make('document')
                ->withIcon()
                ->withColor(),

            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->copyable(),
        ]);
}
```

### 🔒 Validações

O `DocumentInput` valida automaticamente:

- ✅ Formato correto (11 ou 14 dígitos)
- ✅ Dígitos verificadores válidos
- ✅ CPF não pode ser sequencial (111.111.111-11)
- ✅ CNPJ não pode ser sequencial (11.111.111/1111-11)
- ✅ Unicidade na tabela (opcional)

**Mensagens de erro:**
- "O CPF informado é inválido."
- "O CNPJ informado é inválido."
- "O documento já está em uso."
- "O documento deve ter 11 dígitos (CPF) ou 14 dígitos (CNPJ)."

---

## 📱 PhoneInput & PhoneColumn

Componentes para entrada e exibição de telefone com detecção automática de fixo/celular.

### 📁 Localização

```
app/
├── Forms/Components/PhoneInput.php
└── Tables/Columns/PhoneColumn.php
```

### 🎯 PhoneInput (Formulários)

#### Uso Básico

```php
use App\Forms\Components\PhoneInput;

PhoneInput::make('phone')
```

**Funcionalidades automáticas:**
- ✅ Máscara dinâmica (Fixo: `(00) 0000-0000` | Celular: `(00) 00000-0000`)
- ✅ Validação de DDD
- ✅ Validação de formato
- ✅ Formatação em tempo real
- ✅ Salva apenas números no banco
- ✅ Carrega formatado na edição

#### Variações

```php
// 1. Básico (fixo ou celular)
PhoneInput::make('phone')
    ->required(),

// 2. Apenas celular (11 dígitos)
PhoneInput::make('cellphone')
    ->mobileOnly()
    ->required(),

// 3. Apenas fixo (10 dígitos)
PhoneInput::make('landline')
    ->landlineOnly(),

// 4. Com ícone WhatsApp
PhoneInput::make('whatsapp')
    ->mobileOnly()
    ->withWhatsApp()
    ->required(),

// 5. Customizando
PhoneInput::make('phone')
    ->label('Telefone Principal')
    ->placeholder('(00) 00000-0000')
    ->helperText('Telefone com DDD'),
```

#### Exemplo Completo

```php
use App\Forms\Components\PhoneInput;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required(),

            PhoneInput::make('phone')
                ->label('Telefone Principal')
                ->required(),

            PhoneInput::make('whatsapp')
                ->label('WhatsApp')
                ->mobileOnly()
                ->withWhatsApp(),

            PhoneInput::make('commercial_phone')
                ->label('Telefone Comercial')
                ->landlineOnly(),
        ]);
}
```

### 📊 PhoneColumn (Tabelas)

#### Uso Básico

```php
use App\Tables\Columns\PhoneColumn;

PhoneColumn::make('phone')
```

**Funcionalidades automáticas:**
- ✅ Formatação automática (Fixo/Celular)
- ✅ Searchable
- ✅ Sortable
- ✅ Copyable
- ✅ Tooltip mostrando tipo
- ✅ Tratamento de valores vazios

#### Variações

```php
// 1. Básico (formatado)
PhoneColumn::make('phone'),

// 2. Com ícone
PhoneColumn::make('phone')
    ->withIcon(),
// 📱 Celular | ☎️ Fixo

// 3. Com cor
PhoneColumn::make('phone')
    ->withColor(),
// Celular (verde) | Fixo (azul)

// 4. Com badge
PhoneColumn::make('phone')
    ->withBadge(),
// [Cel: (11) 91234-5678] | [Fixo: (11) 1234-5678]

// 5. Com WhatsApp (clicável)
PhoneColumn::make('whatsapp')
    ->withWhatsApp(),
// Link: https://wa.me/5511912345678

// 6. Mascarado (privacidade)
PhoneColumn::make('phone')
    ->masked(),
// Resultado: (11) *****-5678

// 7. Compacto (mobile)
PhoneColumn::make('phone')
    ->compact(),
// Resultado: ****-5678

// 8. Apenas números
PhoneColumn::make('phone')
    ->onlyNumbers(),
// Resultado: 11912345678

// 9. Apenas celular
PhoneColumn::make('cellphone')
    ->mobileOnly(),

// 10. Apenas fixo
PhoneColumn::make('landline')
    ->landlineOnly(),

// 11. Combinação
PhoneColumn::make('phone')
    ->withIcon()
    ->withColor()
    ->copyable(),
```

#### Exemplo Completo

```php
use App\Tables\Columns\PhoneColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),

            PhoneColumn::make('phone')
                ->withIcon()
                ->withColor(),

            PhoneColumn::make('whatsapp')
                ->label('WhatsApp')
                ->withWhatsApp(),

            Tables\Columns\TextColumn::make('email')
                ->copyable(),
        ]);
}
```

### 🔒 Validações

O `PhoneInput` valida automaticamente:

- ✅ DDD válido (11 a 99)
- ✅ Mínimo 10 dígitos (fixo)
- ✅ Máximo 11 dígitos (celular)
- ✅ Celular deve começar com 9 (3º dígito)
- ✅ Formato correto

**Mensagens de erro:**
- "O telefone deve ter no mínimo 10 dígitos."
- "O telefone deve ter no máximo 11 dígitos."
- "DDD inválido."
- "O telefone fixo deve ter 10 dígitos (DDD + 8 números)."
- "O celular deve ter 11 dígitos (DDD + 9 números)."
- "O número de celular deve começar com 9."

---

## 🚀 Exemplo Real Completo

### ClientResource.php

```php
<?php

namespace App\Filament\Resources;

use App\Forms\Components\DocumentInput;
use App\Forms\Components\PhoneInput;
use App\Tables\Columns\DocumentColumn;
use App\Tables\Columns\PhoneColumn;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Empresa')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nome da Empresa')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('personal_name')
                            ->label('Nome do Responsável')
                            ->required()
                            ->maxLength(255),

                        DocumentInput::make('document')
                            ->uniqueInTable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Contato')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        PhoneInput::make('phone')
                            ->label('Telefone Principal')
                            ->required(),

                        PhoneInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->mobileOnly()
                            ->withWhatsApp(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('personal_name')
                    ->label('Responsável')
                    ->searchable(),

                DocumentColumn::make('document')
                    ->withIcon()
                    ->withColor(),

                PhoneColumn::make('phone')
                    ->withIcon()
                    ->withColor(),

                PhoneColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->withWhatsApp(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClients::route('/create'),
            'edit' => Pages\EditClients::route('/{record}/edit'),
        ];
    }
}
```

---

## 📚 Benefícios

### ✅ Reutilização
- Código em um único lugar
- Fácil manutenção
- Consistência em todo o sistema

### ✅ Produtividade
```php
// ❌ Antes (código repetitivo)
TextInput::make('document')
    ->label('CPF/CNPJ')
    ->required()
    ->maxLength(18)
    ->unique(ignoreRecord: true)
    ->live(onBlur: true)
    ->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state))
    ->formatStateUsing(function ($state) {
        // 20 linhas de código...
    })
    ->extraInputAttributes([
        // 30 linhas de código...
    ])
    ->rules([
        // 40 linhas de código...
    ]);

// ✅ Depois (uma linha!)
DocumentInput::make('document')->uniqueInTable()
```

### ✅ Validações Consistentes
- Mesmas regras em todo o sistema
- Mensagens de erro padronizadas
- Validações testadas e confiáveis

### ✅ UX Melhorada
- Formatação em tempo real
- Feedback visual imediato
- Máscaras intuitivas
- Validação ao sair do campo

---

## 🔧 Instalação

Os componentes já estão criados no projeto. Não é necessária instalação adicional.

### Estrutura de Pastas

```
app/
├── Forms/
│   └── Components/
│       ├── DocumentInput.php
│       └── PhoneInput.php
└── Tables/
    └── Columns/
        ├── DocumentColumn.php
        └── PhoneColumn.php
```

---

## 🎨 Customização

Todos os componentes podem ser customizados através de métodos fluentes:

```php
// Exemplo: Document
DocumentInput::make('document')
    ->label('Meu Label Customizado')
    ->helperText('Minha ajuda customizada')
    ->placeholder('Meu placeholder')
    ->required(false)
    ->uniqueInTable('minha_tabela', 'minha_coluna');

// Exemplo: Phone
PhoneInput::make('phone')
    ->label('Meu Telefone')
    ->placeholder('(00) 00000-0000')
    ->helperText('Digite seu telefone')
    ->required(false);
```
## Criando um Relation Manager
```shell
php artisan make:filament-relation-manager CollaboratorResource projects project_name --attac
```
---

## 🐛 Solução de Problemas

### Campo não formata ao editar
**Solução:** Certifique-se de que o valor está sendo salvo sem formatação no banco (apenas números).

### Validação não funciona
**Solução:** Verifique se o campo no banco aceita strings (VARCHAR) com tamanho suficiente.

### Ícone não aparece
**Solução:** Certifique-se de que os ícones do Heroicons estão disponíveis no Filament.

### WhatsApp não abre
**Solução:** Verifique se o número tem 11 dígitos (DDD + 9 números).

---

## 📖 Referências

- [Documentação Filament](https://filamentphp.com/docs)
- [Custom Form Components](https://filamentphp.com/docs/forms/fields/custom)
- [Custom Table Columns](https://filamentphp.com/docs/tables/columns/custom)

---

## 📝 Licença

Estes componentes fazem parte do projeto Taskfolio e seguem a mesma licença do projeto principal.

---

## 👥 Contribuindo

Para sugerir melhorias ou reportar bugs nos componentes, abra uma issue no repositório do projeto.

---

**Desenvolvido com ❤️ para o Taskfolio**
