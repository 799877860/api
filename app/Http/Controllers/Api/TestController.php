<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;

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
        $data = [
            'user_name' => 'XXX',
            'email'     => 'XXX @qq.com',
            'amount'    => 10000
        ];

        echo json_encode($data);
    }

    public function md5()
    {
        $data = "Hello world";      //要发送的数据
        $key = "1905";              //计算签名的key  发送端与接收端拥有相同的key

        //计算签名  MD5($data . $key)
        //$signature = md5($data . $key);
        $signature = 'sdlfkjsldfkjsfd';

        echo "待发送的数据：". $data;echo '</br>';
        echo "签名：". $signature;echo '</br>';

        //发送数据
        $url = "http://passport.1905.com/test/check?data=".$data . '&signature='.$signature;
        echo $url;echo '<hr>';

        $response = file_get_contents($url);
        echo $response;
    }

    public function sign2()
    {
        $key = "1905";          // 签名使用key  发送端与接收端 使用同一个key 计算签名

        //待签名的数据
        $order_info = [
            "order_id"          => 'LN_' . mt_rand(111111,999999),
            "order_amount"      => mt_rand(111,999),
            "uid"               => 12345,
            "add_time"          => time(),
        ];

        $data_json = json_encode($order_info);

        //计算签名
        $sign = md5($data_json.$key);

        // post 表单（form-data）发送数据
        $client = new Client();
        $url = 'http://passport.1905.com/test/check2';
        $response = $client->request("POST",$url,[
            "form_params"   => [
                "data"  => $data_json,
                "sign"  => $sign
            ]
        ]);

        //接收服务器端响应的数据
        $response_data = $response->getBody();
        echo $response_data;

    }
}
