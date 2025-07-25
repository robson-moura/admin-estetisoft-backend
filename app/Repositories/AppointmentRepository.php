<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotificationMail;

class AppointmentRepository
{
    protected $appointmentModel;
    protected $productModel;

    public function __construct(Appointment $appointmentModel, Product $productModel)
    {
        $this->appointmentModel = $appointmentModel;
        $this->productModel = $productModel;
    }

    public function getFilteredAppointments($filters = [], $limit = 10, $offset = 0)
    {
        $query = $this->appointmentModel->with(['client', 'user', 'service']);

        if (!empty($filters['client_name'])) {
            $query->whereHas('client', function ($q) use ($filters) {
                $q->where('full_name', 'like', '%' . $filters['client_name'] . '%');
            });
        }
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['date'])) {
            $query->where('date', $filters['date']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $total = $query->count();
        $appointments = $query->orderBy('date', 'desc')
            ->orderBy('time', 'asc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return [
            'appointments' => $appointments,
            'total' => $total,
        ];
    }

    public function create(array $data)
    {
        Storage::disk('public')->makeDirectory('appointments/before');
        Storage::disk('public')->makeDirectory('appointments/after');

        if (isset($data['before_photo']) && $data['before_photo'] instanceof UploadedFile) {
            $path = $data['before_photo']->store('appointments/before', 'public');
            $data['before_photo'] = '/storage/' . $path;
        }
        if (isset($data['after_photo']) && $data['after_photo'] instanceof UploadedFile) {
            $path = $data['after_photo']->store('appointments/after', 'public');
            $data['after_photo'] = '/storage/' . $path;
        }

        $products = $data['products_ids'] ?? [];
        unset($data['products_ids']);

        // Verifica estoque dos produtos
        foreach ($products as $productId) {
            $product = $this->productModel->find($productId);
            if (!$product || $product->stock === null || $product->stock <= 0) {
                throw new \Exception("O produto '{$product->name}' está sem estoque.");
            }
        }

        $appointment = $this->appointmentModel->create($data);

        if (!empty($products) && $appointment) {
            $appointment->products()->sync($products);

            // Diminui o estoque dos produtos
            foreach ($products as $productId) {
                $product = $this->productModel->find($productId);
                if ($product && $product->stock !== null && $product->stock > 0) {
                    $product->stock -= 1;
                    $product->save();
                }
            }
        } elseif ($appointment) {
            $appointment->products_ids = $products;
            $appointment->save();
        }

        if ($appointment && $appointment->client && $appointment->client->email) {
            Mail::to($appointment->client->email)->send(new AppointmentNotificationMail($appointment, false));
        }

        return $appointment;
    }

    public function findById($id)
    {
        return $this->appointmentModel->with(['client', 'user', 'service', 'products'])->find($id);
    }

    public function update($id, array $data)
    {
        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return null;
        }
        Storage::disk('public')->makeDirectory('appointments/before');
        Storage::disk('public')->makeDirectory('appointments/after');

        if (isset($data['before_photo']) && $data['before_photo'] instanceof UploadedFile) {
            $path = $data['before_photo']->store('appointments/before', 'public');
            $data['before_photo'] = '/storage/' . $path;
        }
        if (isset($data['after_photo']) && $data['after_photo'] instanceof UploadedFile) {
            $path = $data['after_photo']->store('appointments/after', 'public');
            $data['after_photo'] = '/storage/' . $path;
        }

        $products = $data['products_ids'] ?? [];
        unset($data['products_ids']);

        // Verifica estoque dos produtos novos
        $currentProducts = $appointment->products()->pluck('products.id')->toArray();
        $newProducts = $products;
        $addedProducts = array_diff($newProducts, $currentProducts);

        foreach ($addedProducts as $productId) {
            $product = $this->productModel->find($productId);
            if (!$product || $product->stock === null || $product->stock <= 0) {
                throw new \Exception("O produto '{$product->name}' está sem estoque.");
            }
        }

        $appointment->update($data);

        if (!empty($products)) {
            $appointment->products()->sync($products);

            // Baixa estoque apenas dos produtos adicionados
            foreach ($addedProducts as $productId) {
                $product = $this->productModel->find($productId);
                if ($product && $product->stock !== null && $product->stock > 0) {
                    $product->stock -= 1;
                    $product->save();
                }
            }
        } else {
            $appointment->products_ids = $products;
            $appointment->save();
        }

        if ($appointment && $appointment->client && $appointment->client->email) {
            Mail::to($appointment->client->email)->send(new AppointmentNotificationMail($appointment, true));
        }

        return $appointment;
    }

    public function delete($id)
    {
        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return false;
        }
        return $appointment->delete();
    }
}