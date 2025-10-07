<div class="space-y-5">

    {{-- STATUS ATUAL --}}
    <div class="rounded-xl border p-4 space-y-1">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold">Status</h3>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                {{ $active ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $active ? 'Em execução' : 'Pausado' }}
            </span>
        </div>

        @if ($active)
            <p class="text-sm text-gray-600">
                Iniciado em: <strong>{{ $currentSince->locale(app()->getLocale())->format('d/m/Y H:i') }}</strong> ({{ $tz }})
            </p>
            <p class="text-2xl font-mono">
                {{ $format($currentSeconds) }}
            </p>
            <p class="text-xs text-gray-500">* O tempo exibido é um snapshot ao abrir o modal.</p>
        @else
            <p class="text-sm text-gray-600">Nenhuma sessão em andamento.</p>
        @endif
    </div>

    {{-- HISTÓRICO POR DIA --}}
    <div class="overflow-hidden rounded-xl border">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold">Dia</th>
                <th class="px-4 py-2 text-right text-sm font-semibold">Total no dia</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($rows as $day => $seconds)
                <tr>
                    <td class="px-4 py-2 text-sm">
                        {{ \Carbon\Carbon::parse($day, $tz)->locale(app()->getLocale())->translatedFormat('d/m/Y') }}
                    </td>
                    <td class="px-4 py-2 text-sm text-right font-mono">
                        {{ $format($seconds) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-3 text-sm text-center text-gray-500" colspan="2">
                        Sem registros ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>

            @if($rows->isNotEmpty())
                <tfoot class="border-t bg-gray-50">
                <tr>
                    <td class="px-4 py-2 text-sm font-semibold">Total acumulado</td>
                    <td class="px-4 py-2 text-sm font-semibold text-right font-mono">
                        {{ $format($grand) }}
                    </td>
                </tr>
                </tfoot>
            @endif
        </table>
    </div>
    {{-- RESPONSÁVEL ATUAL --}}
    <div class="rounded-xl border p-4 mb-4">
        <h3 class="text-sm font-semibold mb-1">Responsável</h3>
        @if ($collaborator)
            <p class="text-sm">Atual: <strong>{{ $collaborator }}</strong></p>
        @else
            <p class="text-sm text-gray-600">Nenhum colaborador definido.</p>
        @endif
        <p class="text-xs text-gray-500">Você pode assumir a tarefa pelo botão “Assumir tarefa”.</p>
    </div>
    <p class="text-xs text-gray-500">
        * Fuso horário: {{ $tz }}
    </p>
</div>
