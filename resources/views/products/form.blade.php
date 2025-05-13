@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-semibold mb-6">
            {{ isset($product) ? 'Editar Produto' : 'Novo Produto' }}
        </h2>

        <form method="POST" action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if (isset($product))
                @method('PUT')
            @endif

            <!-- Name Field -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                    class="w-full px-3 py-2 border rounded-md @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price Field -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço</label>
                <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}"
                    class="w-full px-3 py-2 border rounded-md @error('price') border-red-500 @enderror" step="any"
                    required>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock Quantity Field -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estoque</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? '') }}"
                    class="w-full px-3 py-2 border rounded-md @error('stock') border-red-500 @enderror" step="any"
                    required>
                @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo Field -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Foto
                </label>

                <div class="flex items-center gap-4">
                    <!-- Preview Container -->
                    <div class="relative">
                        @if (isset($product) && $product->photo)
                            <img src="data:image/jpeg;base64,{{ $product->photo }}" alt="Current product photo"
                                class="w-32 h-32 object-contain border rounded-md" id="photo-preview">
                        @else
                            <div class="w-32 h-32 border-2 border-dashed rounded-md bg-gray-50 flex items-center justify-center"
                                id="photo-preview">
                                <span class="text-gray-400 text-sm">Sem foto</span>
                            </div>
                        @endif
                    </div>

                    <!-- Hidden File Input -->
                    <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/webp"
                        class="hidden">

                    <!-- Custom Upload Button -->
                    <div class="flex flex-col gap-2">
                        <button type="button" onclick="document.getElementById('photo-input').click()"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            📷 {{ isset($product) && $product->photo ? 'Modificar Foto' : 'Escolher Foto' }}
                        </button>

                        <span class="text-sm text-gray-500" id="file-name">
                            {{ isset($product) && $product->photo ? 'Arquivo selecionado: foto salva' : 'Nenhum arquivo selecionado' }}
                        </span>
                    </div>
                </div>

                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Salvar
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('photo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photo-preview');
            const fileName = document.getElementById('file-name');

            if (file) {
                // Update file name display
                fileName.textContent = `Arquivo selecionado: ${file.name}`;

                // Update preview image
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        preview.innerHTML =
                            `<img src="${e.target.result}" class="w-32 h-32 object-contain rounded-md">`;
                    }
                }
                reader.readAsDataURL(file);

                // Change button text if it's the first selection
                const button = document.querySelector('button[onclick*="photo-input"]');
                if (button.textContent.includes('Choose')) {
                    button.textContent = '📷 Modificar Foto';
                }
            }
        });
    </script>
@endpush
