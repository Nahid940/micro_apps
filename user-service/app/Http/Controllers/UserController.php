<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    //

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);

        } catch (\Throwable $e) {
            Log::error('User creation failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
