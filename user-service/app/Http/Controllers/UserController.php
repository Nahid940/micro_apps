<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    //

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $request)
    {
        try{
            $data = [
                'name'  => $request->name,
                'email' => $request->email,
            ];
            $response = $this->userService->create($data);

            if($response){
                return response()->json(["status" => 201, "message" => "Successful!"]);
            }

        }catch(Exception $ex)
        {
            Log::info($ex->getMessage());
            return response()->json(["status" => 200, "message" => "Error!"]);
        }
    }
}
