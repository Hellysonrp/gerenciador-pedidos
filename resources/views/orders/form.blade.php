@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-semibold mb-6">Novo Pedido</h2>

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf

            <!-- Customer Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Cliente *</label>
                <input type="text" name="customer_name" required
                    class="w-full px-3 py-2 border rounded-md @error('customer_name') border-red-500 @enderror"
                    value="{{ old('customer_name') }}">
                @error('customer_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Products Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Produtos *</h3>
                    <button type="button" onclick="addProductRow()"
                        class="px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Adicionar Produto
                    </button>
                </div>

                <div id="products-container">
                    <!-- Product Rows will be added here -->
                </div>
            </div>

            <!-- Order Total -->
            <div class="mb-6">
                <div class="flex justify-end items-center gap-4">
                    <span class="text-lg font-medium">Total:</span>
                    <input type="number" id="order-total" name="total" required min="0.01" step="any"
                        class="w-32 px-3 py-2 border rounded-md text-right font-medium" readonly>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-2">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Salvar
                </button>
            </div>
        </form>
    </div>



    <!-- Template for new product row (hidden) -->
    <div id="product-row-template" class="hidden mb-4 product-row">
        <div class="flex gap-4 items-center">
            <div class="flex-1">
                <select name="products[][product_id]" required class="product-select w-full px-3 py-2 border rounded-md"
                    onchange="updateProductInfo(this)">
                    <option value="">Selecionar Produto *</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}">
                            {{ $product->name }} - R$ {{ number_format($product->price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="products[][product_name]" class="product-name">

            <div class="w-32">
                <input type="number" name="products[][quantity]" required step="any"
                    class="quantity-input w-full px-3 py-2 border rounded-md" placeholder="Quantidade *"
                    oninput="updateRowTotal(this)" onblur="sanitizeNumber(this)">
            </div>

            <div class="w-32">
                <input type="hidden" name="products[][original_price]" class="original-price">
                <input type="number" name="products[][unit_price]" required step="any"
                    class="unit-price w-full px-3 py-2 border rounded-md" placeholder="Valor Unitário *"
                    oninput="updateRowTotal(this)" onblur="sanitizeNumber(this)">
            </div>

            <div class="w-32">
                <input type="number" name="products[][total]" required min="0.01" step="any"
                    class="total-price w-full px-3 py-2 border rounded-md" placeholder="Total *" readonly>
            </div>

            <button type="button" onclick="removeProductRow(this)" class="px-2 text-red-600 hover:text-red-800">
                Remover
            </button>
        </div>
    </div>

    @push('scripts')
        <script>
            // Add initial product row
            document.addEventListener('DOMContentLoaded', () => addProductRow());

            function addProductRow() {
                const template = document.getElementById('product-row-template');
                const clone = template.cloneNode(true);
                clone.classList.remove('hidden');
                clone.removeAttribute('id');

                const container = document.getElementById('products-container');
                container.appendChild(clone);

                // Update names with index
                const index = container.children.length - 1;
                Array.from(clone.querySelectorAll('[name]')).forEach(input => {
                    input.name = input.name.replace('[]', `[${index}]`);
                });

                // Put 1 in the quantity field
                const quantityInput = clone.querySelector('.quantity-input');
                quantityInput.value = '1';
            }

            function removeProductRow(button) {
                const row = button.closest('.product-row');
                row.remove();
                updateOrderTotal();
            }

            function sanitizeNumber(input) {
                const value = parseFloat(input.value.replace(/[^0-9.]/g, ''));
                input.value = isNaN(value) ? '' : value.toFixed(input.step.includes('00001') ? 5 : 2);
            }

            function updateProductInfo(select) {
                const row = select.closest('.product-row');
                const price = parseFloat(select.selectedOptions[0]?.dataset?.price || 0);
                const productName = select.selectedOptions[0]?.dataset?.name || '';
                row.querySelector('.product-name').value = productName;
                row.querySelector('.unit-price').value = price.toFixed(5);
                row.querySelector('.original-price').value = price.toFixed(5);
                updateRowTotal(select);
            }

            function updateRowTotal(input) {
                const row = input.closest('.product-row');
                const unitPrice = parseFloat(row.querySelector('.unit-price').value || 0);
                const quantity = parseFloat(row.querySelector('.quantity-input').value || 0);
                const total = unitPrice * quantity;

                row.querySelector('.total-price').value = Math.round(total * 100) / 100;
                updateOrderTotal();
            }

            function updateOrderTotal() {
                let total = 0;
                document.querySelectorAll('.total-price').forEach(input => {
                    total += parseFloat(input.value || 0);
                });
                document.getElementById('order-total').value = total.toFixed(2);
            }
        </script>
    @endpush
@endsection
