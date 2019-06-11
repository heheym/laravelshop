<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use zgldh\QiniuStorage\QiniuStorage;

class SongController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->all();
        if(!empty($data['starttime'])){
            //普清
            if($user->rank==0){
                $subQuery = DB::table(DB::raw('songs'))
                    ->where('UploadDate','>',$data['starttime'])
                    ->orderBy('videoClass', 'asc')
                    ->limit(9999999999);
                $data = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
                    ->mergeBindings($subQuery)
                    ->groupBy('t.SongMid')
                    ->paginate(20);
            }
            //高清
            if($user->rank==1){
                //普清
                $subQuer = DB::table(DB::raw('songs'))
                    ->where('UploadDate','>',$data['starttime'])
                    ->orderBy('videoClass', 'desc')
                    ->limit(9999999999);
                $data = DB::table(DB::raw("({$subQuer->toSql()}) as t"))
                    ->mergeBindings($subQuer)
                    ->groupBy('t.SongMid')
                    ->paginate(20);
            }
        }else{
            //普清
            $subQuery = DB::table(DB::raw('songs'))
                ->orderBy('videoClass', 'asc')
                ->limit(9999999999);
            $data = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
                ->mergeBindings($subQuery)
                ->groupBy('t.SongMid')
                ->paginate(20);
            //高清
            if($user->rank==1){
                //普清
                $subQuery = DB::table(DB::raw('songs'))
                    ->orderBy('videoClass', 'desc')
                    ->limit(9999999999);
                $data = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
                    ->mergeBindings($subQuery)
                    ->groupBy('t.SongMid')
                    ->paginate(20);
            }
        }

        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

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

}
