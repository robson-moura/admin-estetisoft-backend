<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $filters = [
            'name' => $request->get('name'),
            'category' => $request->get('category'),
        ];

        $result = $this->productRepository->getFilteredProducts($filters, $limit, $offset);

        $columns = [
            ['label' => 'Nome', 'field' => 'name'],
            ['label' => 'Categoria', 'field' => 'category'],
            ['label' => 'Preço', 'field' => 'price'],
            ['label' => 'Estoque', 'field' => 'stock'],
            ['label' => 'Ativo', 'field' => 'active'],
        ];

        return response()->json([
            'data' => $result['products'],
            'columns' => $columns,
            'total' => $result['total'],
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function store(ProductRequest $request)
    {
        $product = $this->productRepository->create($request->validated());
        return response()->json(['message' => 'Produto criado com sucesso!', 'product' => $product], 201);
    }

    public function show($id)
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }
        return response()->json($product);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = $this->productRepository->update($id, $request->validated());
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }
        return response()->json(['message' => 'Produto atualizado com sucesso!', 'product' => $product], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->productRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }
        return response()->json(['message' => 'Produto removido com sucesso!'], 200);
    }
}