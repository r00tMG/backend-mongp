<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    function __construct()
    {
        /*$this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);*/
    }

    public function getRoles()
    {
        $roles = Role::orderBy('id','DESC')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => "Ceci est la liste des roles",
            'storage' => asset('storage'),
            'roles' => RoleResource::collection($roles)
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        //dd($user);
        if (!$user)
        {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => "Unauthorized",
            ]);
        }
        $roles = Role::orderBy('id','DESC')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => "Ceci est la liste des roles",
            'storage' => asset('storage'),
            'roles' => RoleResource::collection($roles)
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
        logger('Validation', [$validated->fails()]);
        if ($validated->fails())
        {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => "Bad Request",
                'errors' => $validated->errors()
            ]);
        }
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web'
        ]);
        logger('role created', [$role]);
        $permissions = [];

        $post_permissions = $request->input('permission');
        logger('permission post', [$post_permissions]);

        foreach ($post_permissions as $permissionId) {
            $permission = Permission::where('id', intval($permissionId))
                ->where('guard_name', 'web')
                ->first();
            logger('permission récupéré', [$permission]);

            if ($permission) {
                $permissions[] = $permission->id;
            } else {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => "Permission with ID $permissionId not found for guard 'sanctum'.",
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $role->syncPermissions($permissions);
        logger('permission assigné au role', [$role]);


        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => "Le rôle a été ajouté avec succès",
            'role' => new RoleResource($role), // Assuming RoleResource is used correctly
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        //dd(!$user);
        if (!$role)
        {
            return \response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ce role n\'est pas trouvé',
            ]);
        }
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        //dd(RoleResource::collection($role));

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => "Les détails de ce role ",
            'storage' => asset('storage'),
            'role' => new RoleResource($role),
            'rolePermissions' => $rolePermissions

        ]);

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
        //dd(!$user);
        if (!$role)
        {
            return \response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ce role n\'est pas trouvé',
            ]);
        }
        $validated = Validator::make($request->all(),[
            'name' => 'required',
            'permission' => 'required',
        ]);
        logger('Validation', [$validated->fails()]);
        if ($validated->fails())
        {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => "Bad Request",
                'errors' => $validated->errors()
            ]);
        }

        $role = Role::find($id);
        logger($role);
        $role->name = $request->input('name');
        $role->guard_name = "web";
        //dd($role);
        $role->save();
        logger($role);



        $permissions = [];
        $post_permissions = $request->input('permission');
        logger($post_permissions);
        foreach ($post_permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)
                //->where('guard_name', 'web')
                ->first();
            logger('validation permission', $permissions);
            if ($permission) {
                $permissions[] = $permission->id;
            } else {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => "Permission avec le nom '$permissionName' non trouvée pour le guard 'sanctum'.",
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // Vérifier si des permissions ont été récupérées
        if (empty($permissions)) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => "Aucune permission valide n'a été trouvée.",
            ], Response::HTTP_BAD_REQUEST);
        }
        //dd($permissions);
        logger($permission);


        $role->syncPermissions($permissions);
        //dd($role);
        logger($role);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => "Le role a été modifié avec succés",
            'storage' => asset('storage'),
            'role' => new RoleResource($role)
        ]);

        return redirect()->route('roles.index')->with('success','Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        //dd(!$user);
        if (!$role)
        {
            return \response()->json([
               'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ce role n\'est pas trouvé',
            ]);
        }
        DB::table("roles")->where('id',$id)->delete();
        return response()->json([
            'status' => Response::HTTP_NO_CONTENT,
            'message' => "Le role est supprimé avec succés",
        ]);

        return redirect()->route('roles.index')->with('success','Role deleted successfully');
    }
}
