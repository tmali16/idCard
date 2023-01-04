<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
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
        $users = User::query()->with('roles')->orderBy('id')->get();

        return response()->view('auth.users',['users'=>$users]);
    }

    public function crud(Request $request, $id)
    {
        $method = $request->get('method');
        switch ($method){
            case "edit":
                $user = User::where('id', $id)->first();
                break;
            case "delete":
                $user = User::where('id', $id)->first();
                $user->delete();
                break;
            default:
                $user = new User();
                break;
        }
        $roles = Role::query()->select('id', 'name as title')->get();
        return response()->view('auth.register', ['user'=>$user, 'method'=>$method, 'roles'=>$roles]);
    }
}
