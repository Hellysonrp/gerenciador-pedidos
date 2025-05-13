{{-- resources/views/orders/show.blade.php --}}
@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-semibold">Pedido #{{ $order->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Criado em: {{ $order->created_at->format('d/m/Y H:i:s') }} |
                    Atualizado em: {{ $order->updated_at->format('d/m/Y H:i:s') }}
                </p>
            </div>
            <a href="{{ route('orders.index') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                Voltar
            </a>
        </div>

        <!-- Order Information -->
        <div class="mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <dl class="grid grid-cols-1 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome do Cliente</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_name }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Products Table -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Produtos</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 ">Nome do Produto</th>
                            <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 ">Valor Unitário</th>
                            <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 ">Quantidade</th>
                            <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 ">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($order->products as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $product->product_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    R$ {{ number_format($product->unit_price, 5) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    {{ number_format($product->quantity, 5) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    R$ {{ number_format($product->total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-500">Total
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                R$ {{ number_format($order->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
