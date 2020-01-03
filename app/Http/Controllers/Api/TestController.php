<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

use App\Model\UserModel;
class TestController extends Controller
{
    /**
     * @param Request $request
     * 用户注册
     */
    public function reg(Request $request)
    {
        echo '<pre>';
        print_r($request->input());
        echo '</pre>';

        $pass1 = $request->input('pass1');
        $pass2 = $request->input('pass2');

        if ($pass1 != $pass2) {
            die("两次密码不一致");
        }
        $password = password_hash($pass1, PASSWORD_BCRYPT);

        $data = [
            'email'      => $request->input('email'),
            'name'       => $request->input('name'),
            'mobile'     => $request->input('mobile'),
            'password'   => $password,
            'last_login' => time(),
            'last_ip'    => $_SERVER['REMOTE_ADDR'],      // 获取远程ip

        ];

        $uid = UserModel::insertGetId($data);
        var_dump($uid);
    }

    /**
     * 用户登录接口
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $name = $request->input('name');
        $pass = $request->input('pass');

        $u = UserModel::where(['name' => $name])->first();
        if ($u) {
            // 验证密码
            if (password_verify($pass, $u->password)) {
                // 登录成功
                // 生成token
                $token    = Str::random(32);
                $response = [
                    'errno' => 0,
                    'msg'   => '登录成功',
                    'data'  => [
                        'token' => $token
                    ]
                ];
            }else {
                $response = [
                    'errno' => 40003,
                    'msg'   => '密码不正确'
                ];
            }

        }else {
            $response = [
                'errno' => 40004,
                'msg'   => '没有此用户'
            ];
        }

        return $response;

    }

    /**
     * 获取用户列表
     */
    public function userList()
    {
        $list = UserModel::all();
        echo '<pre>';print_r($list->toArray());echo '</pre>';
    }
}
