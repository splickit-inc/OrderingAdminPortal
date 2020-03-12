<?php namespace App\Http\Controllers;

use App\BusinessModel\Authentication\Users;
use App\Model\Brand;
use App\Model\Merchant;
use App\Model\Organization;
use App\Model\PortalRoleUserMap;
use App\Model\Role;
use App\Service\UserService;
use App\Service\Utility;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{

    public function index()
    {
        if (App::environment(['local']))
            return view('index');

        return view('index_production');
    }

    public function createUser(Request $request, UserService $userService)
    {
        //$request = Request();
        //$userService = new UserService();
        $data = $request->all();
        $user_count = User::where('email', '=', $data['email'])->count();

        if ($user_count < 1) {
            DB::beginTransaction();
            try {
                $current_user_permissions = session('user_permissions');
                $new_user_organization_id = session('user_organization_id');

                if ($current_user_permissions != null &&
                    $current_user_permissions->contains('create_usr_org_select') &&
                    $request->has('organization')) {

                    $new_user_organization_id = $request->get('organization');
                }

                $user = new User();

                $user->first_name = $data['first_name'];

                $user->last_name = $data['last_name'];

                $user->email = $data['email'];
                $user->organization_id = $new_user_organization_id;

                $user->visibility = $data['visibility'];

                $user->password = bcrypt($data['password']);

                $user->save();

                $role = Role::find($data['role']);

                $user->roles()->save($role);

                if ($data['visibility'] == 'operator') {
                    foreach ($data['merchants'] as $data_merchant) {
                        $merchant = Merchant::find($data_merchant['merchant_id']);

                        $user->merchants()->save($merchant);
                    }
                }

                if ($data['visibility'] == 'brand') {
                    $brand = Brand::find($data['brand']);

                    $user->brand()->save($brand);
                }

                $parent_user = User::find(Auth::user()->id);

                $user->parent()->save($parent_user);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->errorResponse($exception->getMessage(), 404);
            }

            try {
                $userService->sendUserWelcomeEmail($user);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
            return 'success';
        } else {
            DB::rollBack();
            return 'email_registered';
        }

    }

    public function createLoad(Organization $organization, Brand $brand)
    {
        $utility = new Utility();

        $brands = $brand->allBrands();

        $role = new Role();

        $roles = $role->getCreateRoles();

        $lookups = ['portal_visibility'];

        $lookup_values = $utility->getLookupValues($lookups);

        $brands = array_map(function ($brand) {
            $brand['selected'] = false;
            return $brand;
        }, $brands);

        $organizations = $organization->all();
        return [
            'brands' => $brands,
            'roles' => $roles,
            'visibilities' => $lookup_values['portal_visibility'],
            'organizations' => $organizations
        ];
    }

    public function flushEntireExistingSession() {
        Auth::logout();
        Session::flush();
        Cookie::queue(Cookie::forget('user_data'));
    }

    public function loginAttempt(Request $request, Users $users)
    {
        try {
           $this->flushEntireExistingSession();

            $data = $request->all();
            Log::info('Log in attempt with data request : '.json_encode($data));

            $user = $users->loginAttempt($data);

            if ($user == null) {
                Log::info('User with id '.$user->id.' successfully logged in.');
                return response()->json(["errors" => "Invalid credentials", "status" => 0], 401);
            }

            Log::info('User with these credentials successfully logged in: '.json_encode($user));
            $response = $users->setSessionState($user);
            return response()->json($response, 200)->withCookie(cookie('laravel_session'));
        } catch (\Exception $exception) {
            return response()->json(["errors" => $exception->getMessage(), "status" => 0], 401);
        }
    }

    public function logOut()
    {
        Auth::logout();
        Session::flush();
        Cookie::queue(Cookie::forget('user_data'));
        return redirect('/');
    }

    public function home()
    {
        $user = Auth::user();

        if ($user->can('home')) {

            return view('home');
        }
        return view('home');
    }

    public function roles()
    {
        $roles = Role::get();
        return $roles->toArray();
    }

    public function delete(Request $request)
    {
        $data = $request->all();

        User::destroy($data['id']);

        return 1;
    }

    //Returns All Users for the Manage Users View
    public function allUsers(Users $users)
    {
        try {
            \DB::enableQueryLog();
            $result = $users->getAllUsers();
            $q = \DB::getQueryLog();
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['users' => []], 400);
        }
    }

    //View User Permissions View
    public function permissions()
    {
        return session('user_permissions');
    }

    public function getSession(Users $users)
    {
        $response = $users->getSessionState();
        return $response;
    }

    public function editUser(Request $request, User $userModel)
    {
        try {
            $data = $request->all();

            $email_exist = $userModel->where('email', $data['email'])->where('id', '<>', $data['id'])->first();
            if ($email_exist) {
                return response()->json('The email is already taken.', 422);
            }
            if (array_key_exists('id', $data)) {
                $user_id = $data['id'];

                $role_id = $data['role_id'];
                $user_service = new User();

                if ($user_service->checkPermission('change_user_role')) {
                    $role_user_mapping = PortalRoleUserMap::where('user_id', '=', $user_id)->first();
                    $role_user_mapping->role_id = $role_id;
                    $role_user_mapping->save();
                }

                $data = array_intersect_key($data, array_flip($userModel->getFillable()));
                if (array_key_exists('password', $data)) {
                    $data['password'] = bcrypt($data['password']);
                }

                $result = User::find($user_id)
                    ->update($data);
                return response()->json($result, 200);
            }
            throw new BadRequestHttpException('Invalid Request.');
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function manualChangeUserPassword()
    {
        $password = 'Test96321';

        $user = User::find(5048);

        $user->password = bcrypt($password);

        $user->save();

        return $user;
    }

    public function logInAs($user_id)
    {
        $user = new User();

        if ($user->checkPermission('login_as_user')) {
            Auth::logout();
            Session::flush();

            Auth::loginUsingId($user_id);

            $users = new Users();
            $user = User::find($user_id);
            $response = $users->setSessionState($user);

            return response()->json($response, 200)->withCookie(cookie('laravel_session'));
        } else {
            return false;
        }

    }
}