<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\Models\Permission;
use jeremykenedy\LaravelRoles\Models\Role;

class UserController extends Controller
{

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!in_array($request->getClientIp(), ['127.0.0.1','12.3.2.5'])){
            return response()->redirectTo('/');
        }
        $users = User::query()->with('roles')->orderBy('id')->get();

        return response()->view('auth.users',['users'=>$users]);
    }

    function create(Request $request){
        $user = new User();
        $roles = Role::query()->select('id', 'name as title')->orderByDesc('id')->get();
        $permissions = Permission::query()->select('id', 'name as title')->get();
        return response()->view('auth.register', ['user'=>$user, 'roles'=>$roles, 'permissions'=>$permissions]);
    }

    public function rud(Request $request, $id)
    {
        $method = $request->get('method');
        switch ($method){
            case 'pass':
            case "edit" || $id !== 0:
                $user = User::where('id', $id)->first();
                break;
            case "delete" || $id !== 0:
                $user = User::where('id', $id)->first();
                $user->delete();
                break;
            default:
                $user = new User();
                break;
        }
        $roles = Role::query()->select('id', 'name as title')->orderByDesc('id')->get();
        $permissions = Permission::query()->select('id', 'name as title')->get();
        return response()->view('auth.register', ['user'=>$user, 'method'=>$method, 'roles'=>$roles, 'permissions'=>$permissions]);
    }

    function save(UserRequest $request){
        $id = $request->get('id');
        $userData = $request->validated();
        $roles = $request->get('roles');
        if(!empty($id)){
            $user = User::query()->where('id', $id)->first();
            $user->update($userData);
        }else{
//            dd($userData);
            $user = User::create($userData);
        }
        if(!empty($roles)){
            $user->roles()->sync($roles);
        }
        return response()->redirectTo(route('user.index'));
    }
}
