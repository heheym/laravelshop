<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use zgldh\QiniuStorage\QiniuStorage;

class SongController extends Controller
{
    //用户获取歌曲数据接口
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->all();
        $data['isbver'] = isset($data['isbver'])?$data['isbver']:0;
        $data['isApp'] = isset($data['isApp'])?$data['isApp']:0;
        if(!empty($data['starttime'])){
            //普清
            if($user->rank==0){
                $subQuery = DB::table(DB::raw('songs'))
                    ->where('uploadDateStr','>',$data['starttime'])
                    ->where('isbver',$data['isbver'])
                    ->where('isApp',$data['isApp'])
                    ->orderBy('videoClass', 'asc')
                    ->limit(9999999999);
                $data = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
                    ->mergeBindings($subQuery)
                    ->groupBy('t.musicMid')
                    ->paginate(20);
            }
            //高清
            if($user->rank==1){
                //普清
                $subQuer = DB::table(DB::raw('songs'))
                    ->where('uploadDateStr','>',$data['starttime'])
                    ->where('isbver',$data['isbver'])
                    ->where('isApp',$data['isApp'])
                    ->orderBy('videoClass', 'desc')
                    ->limit(9999999999);
                $data = DB::table(DB::raw("({$subQuer->toSql()}) as t"))
                    ->mergeBindings($subQuer)
                    ->groupBy('t.musicMid')
                    ->paginate(20);
            }
        }else{
            //普清
            $subQuery = DB::table(DB::raw('songs'))
                ->where('isbver',$data['isbver'])
                ->where('isApp',$data['isApp'])
                ->orderBy('videoClass', 'asc')
                ->limit(9999999999);
            $data = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
                ->mergeBindings($subQuery)
                ->groupBy('t.musicMid')
                ->paginate(20);
            //高清
            if($user->rank==1){
                //普清
                $subQuery = DB::table(DB::raw('songs'))
                    ->where('isbver',$data['isbver'])
                    ->where('isApp',$data['isApp'])
                    ->orderBy('videoClass', 'desc')
                    ->limit(9999999999);
                $data = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
                    ->mergeBindings($subQuery)
                    ->groupBy('t.musicMid')
                    ->paginate(20);
            }
        }

        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //用户获得歌曲下载地址接口
    public function download(Request $request)
    {
        $Songid = $request->input('Songid');
        if(!$Songid){
            return response()->json(['Code'=>500,'Msg'=>'id不能为空','Data'=>null]);
        }

        $disk = QiniuStorage::disk('qiniu');
        $fileName = DB::table('songs')->where('Songid',$Songid)->value('Filename');

        $exist = $disk->exists($fileName);
        if(!$exist){
            return response()->json(['Code'=>500,'Msg'=>'文件不存在','Data'=>null]);
        }
        $data = $disk->privateDownloadUrl($fileName,['expires'=>3600]);

        return response()->json(['Code'=>200,'Data'=>$data]);

    }

    //用户成功下载歌曲接口
    public function downloadReturn(Request $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->all();
        if(!$data['Songid']){
            return response()->json(['Code'=>500,'Msg'=>'id不能为空','Data'=>null]);
        }
        $exists=DB::table('users_songs')->where(['Songid'=>$data['Songid'],
            'userid'=>$user->id])->exists();

        //成功下载
        if($data['param']==1){
            if($exists){
                $result = DB::table('users_songs')->where(['Songid'=>$data['Songid'],
                    'userid'=>$user->id])->increment('totalDownload');
            }else{
                $result = DB::table('users_songs')->insert(['Songid'=>$data['Songid'],
                    'userid'=>$user->id,'totalDownload'=>1]);
            }
            if($result){
                return response()->json(['Code'=>200,'Msg'=>'下载成功','Data'=>null]);
            }
        }
        //成功入库
        if($data['param']==2){
            if($exists){
                $result = DB::table('users_songs')->where(['Songid'=>$data['Songid'],
                    'userid'=>$user->id])->update(['inWarehouse'=>1]);
            }else{
                $result = DB::table('users_songs')->insert(['Songid'=>$data['Songid'],
                    'userid'=>$user->id,'inWarehouse'=>1]);
            }
            if($result){
                return response()->json(['Code'=>200,'Msg'=>'入库成功','Data'=>null]);
            }
            return response()->json(['Code'=>200,'Msg'=>'已入库','Data'=>null]);
        }
    }

    //前台上传歌曲文件更新数据库
    public function upload(Request $request)
    {
        $get = $_GET;

        $timestamp = !empty($get['timestamp'])?$get['timestamp']:'';
        $signature = !empty($get['signature'])?$get['signature']:'';
        if(md5($timestamp.'1f2606123d0b5f8282561cf5e0d049ab')!=$signature){
            return response()->json(['Code'=>500,'Msg'=>'加密出错','Data'=>null]);
        }
        $post = json_decode(urldecode(file_get_contents("php://input")),true);
        if($get && $post){
            $exists = DB::table('songs')->where('musicdbpk',$post['musicdbpk'])->exists();
            if($exists){
                if($post['isApp']>0){

                }else{
                    $result = DB::table('songs')->where('musicdbpk',$post['musicdbpk'])->update($post);
                }

            }else{
                $result = DB::table('songs')->insert($post);
            }

            return response()->json(['Code'=>200,'Msg'=>'已更新歌曲数据','Data'=>null]);


        }else{
            return response()->json(['Code'=>500,'Msg'=>'数据不正确','Data'=>null]);
        }

    }

    //添加/更新禁播
    public function ban_songs(Request $request)
    {
        $get = $_GET;

        $timestamp = !empty($get['timestamp'])?$get['timestamp']:'';
        $signature = !empty($get['signature'])?$get['signature']:'';
        if(md5($timestamp.'1f2606123d0b5f8282561cf5e0d049ab')!=$signature){
            return response()->json(['Code'=>500,'Msg'=>'加密出错','Data'=>null]);
        }
        $post = json_decode(urldecode(file_get_contents("php://input")),true);
        if($get && $post){
            $exists = DB::table('ban_songs')->where('musicdbpk',$post['musicdbpk'])->exists();
            if($exists){
                $result = DB::table('ban_songs')->where('musicdbpk',$post['musicdbpk'])->update($post);
            }else{
                $result = DB::table('ban_songs')->insert($post);
            }

            return response()->json(['Code'=>200,'Msg'=>'已更新禁播歌曲数据','Data'=>null]);


        }else{
            return response()->json(['Code'=>500,'Msg'=>'数据不正确','Data'=>null]);
        }

    }

    //添加/更新高危
    public function danger_songs(Request $request)
    {
        $get = $_GET;

        $timestamp = !empty($get['timestamp'])?$get['timestamp']:'';
        $signature = !empty($get['signature'])?$get['signature']:'';
        if(md5($timestamp.'1f2606123d0b5f8282561cf5e0d049ab')!=$signature){
            return response()->json(['Code'=>500,'Msg'=>'加密出错','Data'=>null]);
        }
        $post = json_decode(urldecode(file_get_contents("php://input")),true);
        if($get && $post){
            $exists = DB::table('danger_songs')->where('musicdbpk',$post['musicdbpk'])->exists();
            if($exists){
                $result = DB::table('danger_songs')->where('musicdbpk',$post['musicdbpk'])->update($post);
            }else{
                $result = DB::table('danger_songs')->insert($post);
            }

            return response()->json(['Code'=>200,'Msg'=>'已更新高危歌曲数据','Data'=>null]);


        }else{
            return response()->json(['Code'=>500,'Msg'=>'数据不正确','Data'=>null]);
        }

    }

    //获取禁播列表
    public function get_ban_songs()
    {
        $data = DB::table('ban_songs')->paginate(10);
        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //获取高危列表
    public function get_danger_songs()
    {
        $data = DB::table('danger_songs')->paginate(10);
        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //提交补歌
    public function add_songs()
    {
        $user = Auth::guard('api')->user();
        $post = $_GET;
        unset($post['api_token']);
        if($post){
            DB::table('add_songs')->insert($post);
            $exists = DB::table('songs')->where(['name'=>$post['songname'],'singer'=>$post['singer']])
                ->get();
            if($exists){

            }
            return response()->json(['Code'=>200,'Msg'=>'已更新补歌数据','Data'=>null]);
        }else{
            return response()->json(['Code'=>500,'Msg'=>'数据不正确','Data'=>null]);
        }

    }

}
