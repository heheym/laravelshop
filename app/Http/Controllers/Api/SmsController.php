<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; //缓存
use App\Api\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SmsController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        $chars = "/^1[0-9]{10}$/";
        if(!preg_match($chars, $data['phone']))
        {
            return response()->json(['Code'=>500,'Msg'=>'手机号不正确','Data'=>null]);
        }

        $exist = DB::table('users')->where('phone', $data['phone'])->exists();
        if($exist){
            return response()->json(['Code'=>500,'Msg'=>'手机号已注册','Data'=>null]);
        }

        $key = 'LTAI7KmN71HdgyOf';
        $secret = 'xVH7omCKjvQdM0osFdcKTNNy0bEC1d';
        $signName = 'KTV卫士';
        $template = 'SMS_166779983';
        $phone = $data['phone'];
        $code = rand(1000,9999);

        $smsModel = new Sms($key,$secret);

        $result = $smsModel->sendSms($signName, // 短信签名
            $template, // 短信模板编号
            $phone, // 短信接收者
            Array("code"=>$code)  // 短信模板中字段的值
        );

        if($result->Code=='OK'){
            Cache::put($phone, $code, 5);
            return response()->json(['Code'=>200]);
        }else{
            return response()->json(['Code'=>500,'Data'=>$result]);
        }
    }

}
