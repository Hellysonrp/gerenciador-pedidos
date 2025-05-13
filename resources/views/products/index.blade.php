@extends('layout')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header Section -->
        <div class="border-b border-gray-100 p-6 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Produtos</h1>
            <a href="{{ route('products.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Adicionar Produto
            </a>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <x-sortable-header column="id" title="ID" />
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Foto</th>
                        <x-sortable-header column="name" title="Nome" />
                        <x-sortable-header column="price" title="Preço" />
                        <x-sortable-header column="stock_quantity" title="Estoque" />
                        <th class="px-6 py-3 text-right text-sm font-medium text-gray-500">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->id }}</td>

                            <td class="px-6 py-4">
                                @if ($product->photo)
                                    <img src="data:image/jpeg;base64,{{ $product->photo }}" alt="{{ $product->name }}"
                                        class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                R$ {{ number_format($product->price, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 font-mono">
                                    {{ rtrim(rtrim(number_format($product->stock, 5, ',', ''), '0'), ',') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('products.delete', $product->id) }}" method="POST"
                                        class="flex justify-end items-center gap-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Você tem certeza que deseja excluir este produto?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Nenhum produto encontrado. Comece
                                <a href="{{ route('products.create') }}"
                                    class="text-primary-600 hover:underline">adicionando um novo produto</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
