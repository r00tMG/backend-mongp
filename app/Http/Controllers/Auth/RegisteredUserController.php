<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'photo_profile' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg'],
            'password' => 'required|same:password_confirmation',
            //'roles' => 'required'
        ]);
        //dd($validated->fails());
        logger($validated->fails());
        if ($validated->fails())
        {
            return response()->json([
                'status' => \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST,
                'message' => "Erreur de requête",
                'errors' => $validated->errors()
            ]);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        //dd($input);
        if ($request->hasFile('photo_profile')){
            $input['photo_profile'] = $request->file('photo_profile')->store('profiles');
        }

        $user = User::create($input);
        logger(!Role::where('name', $request->input('roles'))->exists());

        if (!Role::where('name', $request->input('roles'))->exists()) {
            return response()->json(['error' => 'Role does not exist'], 400);
        }
        //dd($request->input('roles'));
        $user->assignRole(explode(',', $request->input('roles')));
        //dd($user);
        logger('check assign role', [$user]);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => "L'utilisateur a été créé avec succés",
            'storage' => asset('storage'),
            '_user' => $user,
            'user' => new UserResource($user)
        ]);


    }
    public function login(LoginRequest $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]);
            logger('Validation'. $validateUser->fails());

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'email' => [
                            'Invalid credentials'
                        ],
                    ]
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            logger('Recupération de l\'utilisateeur authentifié' . $user);

            //$roles = $user->getRoleNames();
            #dd($roles);
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'storage' => asset('storage/'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'photo_profile' => $user->photo_profile,
                    'role' => RoleResource::collection(
                        $user->roles
                    ),
                ]
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
