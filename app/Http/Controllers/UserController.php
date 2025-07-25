<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ValidateResetTokenRequest;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="CRUD operations for users"
 * )
 */
class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);

        $filters = [
            'name' => $request->get('name'),
            'cpf' => $request->get('cpf'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'status' => $request->get('status'),
        ];

        $result = $this->userRepository->getFilteredUsers($filters, $limit, $offset);

        $columns = [
            ['label' => 'Nome', 'field' => 'name'],
            ['label' => 'CPF', 'field' => 'cpf'],
            ['label' => 'Telefone', 'field' => 'phone'],
            ['label' => 'E-mail', 'field' => 'email'],
            ['label' => 'Status', 'field' => 'status'],
            ['label' => 'Data Cadastro', 'field' => 'created_at'],
        ];

        return response()->json([
            'data' => $result['users'],
            'columns' => $columns,
            'total' => $result['total'],
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function store(UserRequest $request)
    {        
   
        $validatedData = $request->validated(); // Obtém os dados validados

        // Usa o repositório para criar o usuário
        $user = $this->userRepository->create($validatedData);

        return response()->json(['message' => 'Usuário criado com sucesso!', 'user' => $user], 201);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get a user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function show($id)
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function update(UserRequest $request, $id)
    {
        Log::debug($request); // Log para depuração
        $validatedData = $request->validated(); // Obtém os dados validados

        // Usa o repositório para atualizar o usuário
        $updated = $this->userRepository->update($id, $validatedData);

        if (!$updated) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json(['message' => 'Usuário atualizado com sucesso!'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuário Removido com sucesso"
     *     )
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->userRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json(['message' => 'Usuário Removido com sucesso!'], 200);
    }

    /**
     * @OA\Post(
     *     path="/users/reset-password",
     *     summary="Validate password reset token",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email"},
     *             @OA\Property(property="token", type="string", example="reset_token_example"),
     *             @OA\Property(property="email", type="string", format="email", example="email@email.com") 
     *            )        
    *     ),            
    *     @OA\Response(     
    *         response=200,
    *         description="Token is valid",
    *         @OA\JsonContent(
    *             type="object",

    *             @OA\Property(property="valid", type="boolean", example=true)  
    *         )     
    *     ),            
    *     @OA\Response(         
    *         response=400, 
    *         description="Invalid token or user not found",    
    *         @OA\JsonContent(
    *             type="object",    
    *             @OA\Property(property="valid", type="boolean", example=false),    
    *             @OA\Property(property="message", type="string", example="Token inválido ou expirado.")    
    *         )             
    *     )     
    * ) 
    */  
    public function validateResetToken(ValidateResetTokenRequest $request)
    {
        $result = $this->userRepository->validateResetToken($request->email, $request->token);

        if ($result['valid']) {
            return response()->json(['valid' => true]);
        } else {
            return response()->json(['valid' => false, 'message' => $result['message']], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/users/reset-password",
     *     summary="Reset user password",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="reset_token_example"),
     *             @OA\Property(property="email", type="string", format="email", example="email@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="new_password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="new_password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *        @OA\JsonContent(
     *           type="object",
     *          @OA\Property(property="message", type="string", example="Senha redefinida com sucesso!")
     *       )
     *   ), 
     *   @OA\Response(
     *       response=400,
     *     description="Invalid token or user not found",
     *      @OA\JsonContent(
     *      
     *      type="object",  
     *    @OA\Property(property="message", type="string", example="Token inválido ou expirado.")
     *      )
     *  )
     *      
     *        )
     */ 
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->userRepository->resetPassword($request->all());

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Senha redefinida com sucesso!']);
        } else {
            return response()->json(['message' => __($status)], 400);
        }
    }
}