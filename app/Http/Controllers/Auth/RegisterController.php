<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\events\Registered;

use Illuminate\Support\Facades\Cache; //缓存

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|regex:/1[0-9]{10}/|unique:users',
            'password' => 'required|string|min:5',
//            'code' => "required|in:".Cache::get($data['phone'])
        ],['code.required'=>'验证码不能为空',
            'code.in'=>'验证码不正确']);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $data['vipState'] = 0;
        $data['vipXgStartDay'] = 7;
        $data['vipStartTime'] = date('Y-m-d H:i:s');
        $data['vipTime'] = date('Y-m-d H:i:s',strtotime("+7 days"));
        $data['download'] = 1;
        $data['add'] = 0;
        return User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'mac' => $data['mac'],
            'vipState' => $data['vipState'],
            'vipXgStartDay' => $data['vipXgStartDay'],
            'vipStartTime' => $data['vipStartTime'],
            'vipTime' => $data['vipTime'],
            'download' => $data['download']
        ]);
    }


//api 注册 by ma
    protected function registered(Request $request, $user)
    {
        //$user->generateToken();
        $user->generateToken();
//        $user->expires = 7200;
        return response()->json(['Code'=>200,'Data' => $user->toArray()]);
    }

}
