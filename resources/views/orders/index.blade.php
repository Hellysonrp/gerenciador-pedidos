@extends('layout')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Pedidos</h1>
                <a href="{{ route('orders.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Novo Pedido
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <x-sortable-header column="id" title="ID" />
                            <x-sortable-header column="customer_name" title="Nome do Cliente" />
                            <x-sortable-header column="total" title="Total" />
                            <th class="px-6 py-3 text-right text-sm font-medium text-gray-500">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->customer_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R$ {{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Visualizar Pedido">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <!-- Botão Nota Fiscal -->
                                        @php
                                            $notaFiscalId = $order->nota_fiscal_id ?? null;
                                            $notaFiscalStatus = $order->nota_fiscal_status ?? null;
                                            $statusColors = [
                                                0 => 'text-gray-400 hover:text-gray-600', // pendente
                                                1 => 'text-green-600 hover:text-green-800', // autorizada
                                                2 => 'text-orange-500 hover:text-orange-700', // cancelada
                                                3 => 'text-red-600 hover:text-red-800', // rejeitada
                                            ];
                                            $statusTitles = [
                                                0 => 'Consultar Nota Fiscal',
                                                1 => 'Nota Fiscal Autorizada',
                                                2 => 'Nota Fiscal Cancelada',
                                                3 => 'Nota Fiscal Rejeitada',
                                            ];
                                            $iconColor = $statusColors[$notaFiscalStatus ?? 0] ?? 'text-gray-400';
                                            $iconTitle = $statusTitles[$notaFiscalStatus ?? 0] ?? 'Emitir Nota Fiscal';
                                        @endphp
                                        @if(!$notaFiscalId || in_array($notaFiscalStatus, [2, 3]))
                                            @php
                                                $emitirTitle = 'Emitir Nota Fiscal';
                                                if (in_array($notaFiscalStatus, [2, 3])) {
                                                    $emitirTitle .= ' (Status: ' . ($notaFiscalStatus === 2 ? 'Cancelada' : ($notaFiscalStatus === 3 ? 'Rejeitada' : '')) . ')';
                                                } elseif ($notaFiscalStatus === 0 && $notaFiscalId) {
                                                    $emitirTitle .= ' (Status: Pendente)';
                                                }
                                            @endphp
                                            <form action="{{ route('orders.nota-fiscal.emitir', $order->id) }}" method="POST" class="flex justify-end items-center gap-2">
                                                @csrf
                                                <button type="submit" title="{{ $emitirTitle }}" class="{{ $iconColor }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19v2m-6-2a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($notaFiscalId && !in_array($notaFiscalStatus, [2, 3]))
                                            <form action="{{ $notaFiscalStatus == 0 ? route('orders.nota-fiscal.consultar', $order->id) : '#' }}" method="POST" class="flex justify-end items-center gap-2">
                                                @csrf
                                                <button type="submit" title="{{ $iconTitle }}" class="{{ $iconColor }}" @if($notaFiscalStatus != 0) disabled @endif>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19v2m-6-2a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12z" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @if($notaFiscalStatus == 1)
                                                <form action="{{ route('orders.nota-fiscal.cancelar', $order->id) }}" method="POST" class="flex justify-end items-center gap-2">
                                                    @csrf
                                                    <button type="submit" title="Cancelar Nota Fiscal" class="text-orange-600 hover:text-orange-800"
                                                        onclick="return confirm('Tem certeza que deseja cancelar a nota fiscal autorizada? Esta ação não pode ser desfeita.');">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        <!-- Botão Delete -->
                                        <form action="{{ route('orders.delete', $order->id) }}" method="POST"
                                            class="flex justify-end items-center gap-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza que deseja excluir este pedido?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Nenhum pedido encontrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
