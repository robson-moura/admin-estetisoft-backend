<?php

namespace App\Http\Controllers;

use App\Repositories\AppointmentRepository;
use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel; // Se usar Laravel Excel
use Illuminate\Support\Facades\View;

class AppointmentController extends Controller
{
    protected $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    // Listar todos os atendimentos (com paginação e filtro)
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $filters = [
            'client_name' => $request->get('client_name'),
            'date' => $request->get('date'),
            'status' => $request->get('status'),
            'client_id' => $request->get('client_id'),
        ];

        $result = $this->appointmentRepository->getFilteredAppointments($filters, $limit, $offset);

        $columns = [
            ['label' => 'Cliente', 'field' => 'client.full_name'],
            ['label' => 'Profissional', 'field' => 'user.name'],
            ['label' => 'Data', 'field' => 'date_br'],
            ['label' => 'Hora', 'field' => 'time'],
            ['label' => 'Serviço', 'field' => 'service.name'],
            ['label' => 'Status', 'field' => 'status'],
        ];

        return response()->json([
            'data' => $result['appointments'],
            'columns' => $columns,
            'total' => $result['total'],
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    // Criar um novo atendimento
    public function store(AppointmentRequest $request)
    {
        try {
            $appointment = $this->appointmentRepository->create($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'Atendimento criado com sucesso!', 'appointment' => $appointment], 201);
    }

    // Exibir um atendimento específico
    public function show($id)
    {
        $appointment = $this->appointmentRepository->findById($id);
        if (!$appointment) {
            return response()->json(['message' => 'Atendimento não encontrado.'], 404);
        }
        return response()->json($appointment);
    }

    // Atualizar um atendimento
    public function update(AppointmentRequest $request, $id)
    {
        try {
            $updated = $this->appointmentRepository->update($id, $request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        if (!$updated) {
            return response()->json(['message' => 'Atendimento não encontrado.'], 404);
        }
        return response()->json(['message' => 'Atendimento atualizado com sucesso!', 'appointment' => $updated], 200);
    }

    // Remover um atendimento
    public function destroy($id)
    {
        $deleted = $this->appointmentRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Atendimento não encontrado.'], 404);
        }
        return response()->json(['message' => 'Atendimento removido com sucesso!'], 200);
    }

    public function exportXls(Request $request)
    {
        $appointments = \App\Models\Appointment::with(['client', 'user', 'service'])
            ->orderBy('date', 'desc')
            ->get();

        $headers = [
            "Content-type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=atendimentos.xls",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $html = View::make('exports.appointments_xls', compact('appointments'))->render();

        return Response::make($html, 200, $headers);
    }
}