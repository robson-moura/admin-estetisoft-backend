<?php

namespace App\Repositories;

use App\Models\Service;

class ServiceRepository
{
    protected $model;

    public function __construct(Service $service)
    {
        $this->model = $service;
    }

    public function getFilteredServices($filters = [], $limit = 10, $offset = 0)
    {
        $query = $this->model->newQuery();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['category'])) {
            $query->where('category', 'like', '%' . $filters['category'] . '%');
        }

        $total = $query->count();
        $services = $query->offset($offset)->limit($limit)->orderBy('name')->get();

        return [
            'services' => $services,
            'total' => $total,
        ];
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function update($id, $data)
    {
        $service = $this->model->find($id);
        if ($service) {
            $service->update($data);
        }
        return $service;
    }

    public function delete($id)
    {
        $service = $this->model->find($id);
        if ($service) {
            return $service->delete();
        }
        return false;
    }
}