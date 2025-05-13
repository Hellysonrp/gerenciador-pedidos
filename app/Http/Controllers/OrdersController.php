<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        // carrega os pedidos com a ordenação selecionada no front
        // e com paginação

        $sortColumn = request('sort');
        $sortOrder = request('order');

        $query = Order::query();

        if ($sortColumn && in_array($sortColumn, ['id', 'name', 'price', 'stock_quantity'])) {
            $validOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';
            $query->orderBy($sortColumn, $validOrder);
        }

        $orders = $query->paginate(10)->withQueryString();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        // carrega todos os produtos para usar no formulário
        // isto é temporário; nas próximas iterações, será trocado para carregar via ajax e provavelmente ter busca
        $products = Product::orderBy('name', 'asc')->get();

        // carrega a página do formulário de pedido
        return view('orders.form', compact('products'));
    }

    public function store(Request $request)
    {
        // valida os dados do pedido
        $validated = $request->validate([
            'customer_name' => 'required|string|max:191',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.product_name' => 'required|string|max:191',
            'products.*.original_price' => 'required|numeric',
            'products.*.unit_price' => 'required|numeric',
            'products.*.quantity' => 'required|numeric|min:0.00001',
        ]);

        // transação para reverter tudo se ocorrer algum problema
        DB::transaction(function () use ($validated) {
            $total = 0;
            $orderProducts = [];
            foreach ($validated['products'] as $product) {
                // calcula o total do produto e o total do pedido
                // arredonda para duas casas decimais
                // TODO round ABNT
                $product['total'] = round($product['unit_price'] * $product['quantity'], 2);
                $total += $product['total'];

                // itera entre os produtos para validar e decrementar o estoque
                // o correto seria centralizar toda a lógica de gerenciamento de estoque, mas não vou me preocupar com isso agora
                $p = Product::findOrFail($product['product_id']);

                $validator = Validator::make([
                    'remaining' => $p->stock - $product['quantity'],
                ], [
                    'remaining' => 'required|numeric|min:0',
                ]);
                if ($validator->fails()) {
                    $validator->throwValidationException($validator);
                }

                $p->decrement('stock', $product['quantity']);

                $orderProducts[] = $product;
            }

            // preenche e salva o pedido
            $order = new Order($validated);
            $order->total = $total;
            $order->save();

            // salva os produtos do pedido
            $order->products()->createMany($orderProducts);
        });

        return redirect()->route('orders.index')->with('success', 'Pedido criado com sucesso.');
    }

    public function edit(Order $order)
    {
        // TODO
        return view('orders.form', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // TODO
        throw new \Exception('Método não implementado');
    }

    public function delete(Order $order)
    {
        // abre uma transação
        // itera entre os produtos do pedido
        // adiciona o estoque novamente dentro do produto do produto do pedido
        // deleta o pedido

        DB::transaction(function () use ($order) {
            foreach ($order->products->all() as $product) {
                $p = Product::findOrFail($product->product_id);
                $p->increment('stock', $product->quantity);
            }

            $order->delete();
        });
        return redirect()->route('orders.index')->with('success', 'Pedido excluído com sucesso.');
    }

    public function show(Order $order)
    {
        // carrega a página de visualização do pedido
        return view('orders.show', compact('order'));
    }
}
