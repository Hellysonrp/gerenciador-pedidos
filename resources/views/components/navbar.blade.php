<nav class="bg-white shadow-sm">
    <div class="container-xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <a href="/" class="flex items-center text-xl font-bold text-gray-900">
                    {{ config('app.name') }}
                </a>
                <div class="ml-6 flex space-x-8">
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        Produtos
                    </a>
                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
