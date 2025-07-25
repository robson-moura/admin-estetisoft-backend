<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $filters = [
            'name' => $request->get('name'),
            'category' => $request->get('category'),
        ];

        $result = $this->serviceRepository->getFilteredServices($filters, $limit, $offset);

        $columns = [
            ['label' => 'Nome', 'field' => 'name'],
            ['label' => 'Categoria', 'field' => 'category'],
            ['label' => 'Preço', 'field' => 'price'],
            ['label' => 'Duração', 'field' => 'duration'],
            ['label' => 'Ativo', 'field' => 'active'],
        ];

        return response()->json([
            'data' => $result['services'],
            'columns' => $columns,
            'total' => $result['total'],
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function store(ServiceRequest $request)
    {
        $service = $this->serviceRepository->create($request->validated());
        return response()->json(['message' => 'Serviço criado com sucesso!', 'service' => $service], 201);
    }

    public function show($id)
    {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            return response()->json(['message' => 'Serviço não encontrado.'], 404);
        }
        return response()->json($service);
    }

    public function update(ServiceRequest $request, $id)
    {
        $service = $this->serviceRepository->update($id, $request->validated());
        if (!$service) {
            return response()->json(['message' => 'Serviço não encontrado.'], 404);
        }
        return response()->json(['message' => 'Serviço atualizado com sucesso!', 'service' => $service], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->serviceRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Serviço não encontrado.'], 404);
        }
        return response()->json(['message' => 'Serviço removido com sucesso!'], 200);
    }
}