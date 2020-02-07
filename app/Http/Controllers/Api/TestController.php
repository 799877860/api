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
    public function reg0(Request $request)
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
    public function login0(Request $request)
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
                    'err_num' => 0,
                    'err_msg'   => '登录成功',
                    'data'  => [
                        'token' => $token
                    ]
                ];
            }else {
                $response = [
                    'err_num' => 40003,
                    'err_msg'   => '密码不正确'
                ];
            }

        }else {
            $response = [
                'err_num' => 40004,
                'err_msg'   => '没有此用户'
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

    /**
     * APP注册
     * @return bool|string
     */
    public function reg()
    {
        //请求passport
        $url = 'http://passport.1905.com/user/reg';
        $response = UserModel::curlPost($url,$_POST);
        return $response;
    }
    /**
     * APP 登录
     */
    public function login()
    {
        //请求passport
        $url = 'http://passport.1905.com/user/login';
        $response = UserModel::curlPost($url,$_POST);
        return $response;
    }

    public function showData()
    {
        // 收到 token
        $uid = $_SERVER['HTTP_UID'];
        $token = $_SERVER['HTTP_TOKEN'];
        // 请求passport鉴权
        $url = 'http://passport.1905.com/user/auth';         //鉴权接口
        $response = UserModel::curlPost($url,['uid'=>$uid,'token'=>$token]);
        $status = json_decode($response,true);
        //处理鉴权结果
        if($status['err_num']==0)     //鉴权通过
        {
            $data = "wenlafaxin";
            $response = [
                'err_num' => 0,
                'err_msg'   => 'ok',
                'data'  => $data
            ];
        }else{          //鉴权失败
            $response = [
                'err_num' => 40003,
                'err_msg'   => '授权失败'
            ];
        }
        return $response;
    }

    /**
     * 接口test
     */
    public function postman()
    {
//        //获取用户标识
//        $token = $_SERVER['HTTP_TOKEN'];
//        // 当前url
//        $request_uri = $_SERVER['REQUEST_URI'];
//
//        $url_hash = md5($token . $request_uri);
//
//
//        //echo 'url_hash: ' .  $url_hash;echo '</br>';
//        $key = 'count:url:'.$url_hash;
//        //echo 'Key: '.$key;echo '</br>';
//
//        //检查 次数是否已经超过限制
//        $count = Redis::get($key);
//        echo "当前接口访问次数为：". $count;echo '</br>';
//
//        if($count >= 5){
//            $time = 10;     // 时间秒
//            echo "请勿频繁请求接口, $time 秒后重试";
//            Redis::expire($key,$time);
//            die;
//        }
//
//        // 访问数 +1
//        $count = Redis::incr($key);
//        echo 'count: '.$count;

        $data = [
            'user_name' => 'XXX',
            'email'     => 'XXX @qq.com',
            'amount'    => 10000
        ];

        echo json_encode($data);
    }

}
