<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
                    'estoque restante' => $p->stock - $product['quantity'],
                ], [
                    'estoque restante' => 'required|numeric|min:0',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
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

    //     emitirNotaFiscal POST http://localhost:9000/notas-fiscais
    // consultarNotaFiscal GET http://localhost:9000/notas-fiscais/{id}
    // cancelarNotaFiscal DELETE http://localhost:9000/notas-fiscais/{id}

    public function emitirNotaFiscal(Order $order)
    {
        // a nota fiscal fica no CABEÇALHO do pedido
        // se já tem nota fiscal não cancelada ou rejeitada, retorna erro
        if ($order->nota_fiscal_id && $order->nota_fiscal_status !== null && $order->nota_fiscal_status < 2) {
            return redirect()->back()->withErrors(['error' => 'Nota fiscal já emitida para este pedido.']);
        }

        // chama a API externa para emitir a nota fiscal
        // a chamada vai retornar um ID da nota fiscal emitida
        // o status deve ser salvo como "pendente" (0)
        $resp = Http::Post('http://localhost:9000/notas-fiscais', [
            'customer_name' => $order->customer_name,
            'products_base_total' => $order->products->sum('original_price'),
            'products_total' => $order->products->sum('unit_price'),
            'total_discount' => $order->products->sum(function ($product) {
                return $product->original_price - $product->unit_price;
            }),
            'produtos' => $order->products->map(function ($product) {
                return [
                    'product_identifier' => $product->product_id.'',
                    'product_name' => $product->product_name,
                    'gtin' => '', // TODO: adicionar GTIN se necessário
                    'base_price' => $product->original_price,
                    'discount' => $product->original_price - $product->unit_price,
                    'price' => $product->unit_price,
                    'quantity' => $product->quantity,
                    'total' => $product->total,
                ];
            })->toArray(),
        ]);

        if ($resp->successful()) {
            // se a nota fiscal foi emitida com sucesso, salva o ID e o status
            $order->nota_fiscal_id = $resp->json('id');
            $order->nota_fiscal_status = 0; // pendente
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Nota fiscal emitida com sucesso.');
        } else {
            // se houve erro, retorna o erro
            return redirect()->back()->withErrors(['error' => 'Erro ao emitir nota fiscal: ' . $resp->body()]);
        }
    }

    public function consultarNotaFiscal(Order $order)
    {
        // consulta a nota fiscal na API externa
        if (!$order->nota_fiscal_id) {
            return redirect()->back()->withErrors(['error' => 'Nenhuma nota fiscal emitida para este pedido.']);
        }

        $resp = Http::get("http://localhost:9000/notas-fiscais/{$order->nota_fiscal_id}");

        if ($resp->successful()) {
            // se a nota fiscal foi consultada com sucesso, atualiza o status
            $order->nota_fiscal_status = $resp->json('status');
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Nota fiscal consultada com sucesso.');
        } else {
            // se houve erro, retorna o erro
            return redirect()->back()->withErrors(['error' => 'Erro ao consultar nota fiscal: ' . $resp->body()]);
        }
    }

    public function cancelarNotaFiscal(Order $order)
    {
        // cancela a nota fiscal na API externa
        if (!$order->nota_fiscal_id) {
            return redirect()->back()->withErrors(['error' => 'Nenhuma nota fiscal emitida para este pedido.']);
        }

        // se o status da nota fiscal for diferente de autorizada (1), retorna erro
        if ($order->nota_fiscal_status !== 1) {
            return redirect()->back()->withErrors(['error' => 'Nota fiscal não autorizada ou já cancelada/rejeitada.']);
        }

        $resp = Http::delete("http://localhost:9000/notas-fiscais/{$order->nota_fiscal_id}");

        if ($resp->successful()) {
            // se a nota fiscal foi cancelada com sucesso, atualiza o status
            $order->nota_fiscal_status = 2; // cancelada
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Nota fiscal cancelada com sucesso.');
        } else {
            // se houve erro, retorna o erro
            return redirect()->back()->withErrors(['error' => 'Erro ao cancelar nota fiscal: ' . $resp->body()]);
        }
    }
}
