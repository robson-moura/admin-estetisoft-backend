<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota de login
Route::post('/login', [AuthController::class, 'login']);

// Rota de documentação
Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});

// Rotas protegidas pelo middleware 'auth:sanctum'
Route::middleware('auth:sanctum')->group(function () {
    // Rota para obter o usuário autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rota de logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rotas relacionadas a usuários
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']); // Listar todos os usuários
        Route::post('/', [UserController::class, 'store']); // Criar um novo usuário
        Route::get('/{id}', [UserController::class, 'show']); // Obter um usuário específico
        Route::put('/{id}', [UserController::class, 'update']); // Atualizar um usuário
        Route::delete('/{id}', [UserController::class, 'destroy']); // Deletar um usuário
    });

    // Rotas relacionadas a perfis
    Route::prefix('profiles')->group(function () {
        Route::get('/combo', [ProfileController::class, 'combo']); // Perfis ativos para select/combo
        Route::get('/', [ProfileController::class, 'index']);      // Listar todos os perfis
        Route::post('/', [ProfileController::class, 'store']);     // Criar um novo perfil
        Route::get('/{id}', [ProfileController::class, 'show']);   // Obter um perfil específico
        Route::put('/{id}', [ProfileController::class, 'update']); // Atualizar um perfil
        Route::delete('/{id}', [ProfileController::class, 'destroy']); // Deletar um perfil
    });

    // Rotas relacionadas a clients
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);      // Listar todos os clientes
        Route::post('/', [ClientController::class, 'store']);     // Criar um novo cliente
        Route::get('/{id}', [ClientController::class, 'show']);   // Obter um cliente específico
        Route::put('/{id}', [ClientController::class, 'update']); // Atualizar um cliente
        Route::delete('/{id}', [ClientController::class, 'destroy']); // Deletar um cliente
    });

    // Rotas relacionadas a atendimentos
    Route::prefix('appointments')->group(function () {
        Route::get('/export', [AppointmentController::class, 'exportXls']); // Exportar atendimentos para XLS
        Route::get('/', [AppointmentController::class, 'index']);      // Listar todos os atendimentos
        Route::post('/', [AppointmentController::class, 'store']);     // Criar um novo atendimento
        Route::get('/{id}', [AppointmentController::class, 'show']);   // Obter um atendimento específico
        Route::put('/{id}', [AppointmentController::class, 'update']); // Atualizar um atendimento
        Route::delete('/{id}', [AppointmentController::class, 'destroy']); // Deletar um atendimento
    });

    // Rotas relacionadas a produtos
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);      // Listar todos os produtos
        Route::post('/', [ProductController::class, 'store']);     // Criar um novo produto
        Route::get('/{id}', [ProductController::class, 'show']);   // Obter um produto específico
        Route::put('/{id}', [ProductController::class, 'update']); // Atualizar um produto
        Route::delete('/{id}', [ProductController::class, 'destroy']); // Deletar um produto
    });

    // Rotas relacionadas a serviços
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);      // Listar todos os serviços
        Route::post('/', [ServiceController::class, 'store']);     // Criar um novo serviço
        Route::get('/{id}', [ServiceController::class, 'show']);   // Obter um serviço específico
        Route::put('/{id}', [ServiceController::class, 'update']); // Atualizar um serviço
        Route::delete('/{id}', [ServiceController::class, 'destroy']); // Deletar um serviço
    });
});

// Rota para validar o token de redefinição de senha
Route::post('/validate-reset-token', [UserController::class, 'validateResetToken']);

// Rota para redefinir a senha
Route::post('/reset-password', [UserController::class, 'resetPassword']);