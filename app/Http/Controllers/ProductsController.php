<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        // carrega os produtos com a ordenação selecionada no front
        // e com paginação

        $sortColumn = request('sort');
        $sortOrder = request('order');

        $query = Product::query();

        if ($sortColumn && in_array($sortColumn, ['id', 'name', 'price', 'stock_quantity'])) {
            $validOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';
            $query->orderBy($sortColumn, $validOrder);
        }

        $products = $query->paginate(10)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        // retorna a página de formulário de produto
        return view('products.form');
    }

    public function store(Request $request)
    {
        // valida se todos os dados são corretos
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'stock' => 'required|numeric',
            'price' => 'required|numeric',
            'photo' => 'nullable|image|max:20480',
        ]);

        // preenche os dados no model e cria o produto
        $product = new Product($validated);

        if ($request->hasFile('photo')) {
            $product->photo = base64_encode($request->file('photo')->getContent());
        }

        $product->save();

        // retorna para a listagem com uma mensagem de sucesso
        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso.');
    }

    public function edit(Product $product)
    {
        // carrega o formulário de cadastro de produtos
        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // valida se todos os dados são corretos
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'stock' => 'required|numeric',
            'price' => 'required|numeric',
            'photo' => 'nullable|image|max:20480',
        ]);

        // preenche o produto
        $product->fill($validated);

        if ($request->hasFile('photo')) {
            $product->photo = base64_encode($request->file('photo')->getContent());
        }

        // salva
        $product->save();

        // volta para a listagem com uma mensagem de sucesso
        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function delete(Product $product)
    {
        // marca o produto como deletado (soft delete)
        $product->delete();

        // volta para a listagem com uma mensagem de sucesso
        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }

    // public function stockManagement(Product $product) {
    //     // gerencia estoque de 1 produto
    //     // carrega a view 'products.manage-stock'
    //     // TODO não utilizado

    //     return view('products.manage-stock', compact('product'));
    // }

    // public function changeStock(Request $request, Product $product) {
    //     // altera o estoque de 1 produto
    //     // TODO não utilizado

    //     $validated = $request->validate([
    //         'stock' => 'required|numeric',
    //     ]);

    //     $product->stock = $validated['stock'];
    //     $product->save();

    //     return redirect()->route('products.index')->with('success', 'Estoque atualizado com sucesso.');
    // }
}
