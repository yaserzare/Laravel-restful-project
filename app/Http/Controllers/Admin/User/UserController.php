<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\User\UserDetailsApiResource;
use App\Http\Resources\Admin\User\UserListApiResource;
use App\RestfulApi\ApiResponse;
use http\Env\Response;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usersQuery = User::query();

        if($request->has('email')) {
            $usersQuery = $usersQuery->whereEmail($request->email);
        }
        $users = $usersQuery->paginate();
        return  UserListApiResource::collection($users);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'first_name'=>['required','min:1','max:255'],
                'last_name'=>['required','min:1','max:255'],
                'email'=>['required','email','unique:users,email'],
                'password'=>['required','min:8'],
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'errors'=>$validator->errors()
                ],422);
            }

            $inputs = $validator->validated();
            $inputs['password'] = Hash::make($inputs['password']);

            $user = User::create($inputs);

        } catch (\Throwable $th)
        {
            app()[ExceptionHandler::class]->report($th);
            return $this->apiResponse('Something is wrong.try again later', 500);
        }


        $response = new ApiResponse();
        $response->setMessage('User created successfully.');
        $response->setData($user);
        $response->setAppends([
            'fullname'=>'123'
        ]);
        return $response->response();



    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserDetailsApiResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try
        {

            $validator = Validator::make($request->all(),[
                'first_name'=>['required', 'string', 'min:1', 'max:255'],
                'last_name'=>['required', 'string', 'min:1','max:255'],
                'email'=>['required','email', Rule::unique('users', 'email')->ignore($user->id)],
                'password'=>['nullable','min:8'],
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'errors' => $validator->errors()
                ],422);
            }

            $inputs  = $validator->validated();

            if(isset(($inputs['password'])))
            $inputs['password'] = Hash::make($inputs['password']);

            $user->update($inputs);

        } catch(\Throwable $th) {
            app()[ExceptionHandler::class]->report($th);

            return response()->json([
                'message' => 'Something is wrong.try again later',
            ], 500);

        }

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {

            $user->delete();

        } catch (\Throwable $ex) {

            return response()->json([
                'message' => 'Something is wrong.try again later',
            ], 500);
        }

        return response()->json([
            'message' => 'User deleted successfully',
            'data' => $user
        ]);

    }

    private function apiResponse($message = null, $data = null, $status = 200)
    {
        $body = [];
        !is_null($message) && $body['message'] = $message;
        !is_null($data) && $body['data'] = $data;

        return response()->json($body, $status);

    }


}
