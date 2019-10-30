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
        $map = array();
        if(isset($data['isbver'])){
            $map['isbver'] = $data['isbver'];
        }
        if(isset($data['isApp'])){
            $map['isApp'] = $data['isApp'];
        }
        if(isset($data['musicdbpk'])){
            $map['musicdbpk'] = $data['musicdbpk'];
        }
        if(!empty($data['starttime'])){
            //普清
            if($user->rank==0){
                $subQuery = DB::table(DB::raw('songs'))
                    ->where('uploadDateStr','>',$data['starttime'])
                    ->where($map)
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
                    ->where($map)
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
                ->where($map)
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
                    ->where($map)
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


        $disk = \Storage::disk('qiniu');

        
        $fileName = DB::table('songs')->where('musicdbpk',$Songid)->value('localPath');

        $exist = $disk->exists($fileName);

        if(!$exist){
            return response()->json(['Code'=>500,'Msg'=>'文件不存在','Data'=>null]);
        }
        $data = $disk->downloadUrl($fileName);


        return response()->json(['Code'=>200,'Data'=>$data]);

    }

    //用户成功下载/入库歌曲接口
    public function downloadReturn(Request $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->all();
        if(empty($data['Songid'])||empty($data['param'])){
            return response()->json(['Code'=>500,'Msg'=>'参数错误','Data'=>null]);
        }
        $date = date('Y-m-d H:i:s');
        //成功下载
        if($data['param']==1){
            if(!empty($data['bugeId'])){
                DB::table('add_songs')->where('bugeId',$data['bugeId'])->update(['state'=>3]);
            }
            $result = DB::table('users_songs')->insert(['musicdbpk'=>$data['Songid'],
                'userid'=>$user->id,'date'=>$date] );
            if($result){
                return response()->json(['Code'=>200,'Msg'=>'下载成功','Data'=>['date'=>$date]]);
            }
        }
        //成功入库
        if($data['param']==2){
            $result = DB::table('user_warehouse')->insert(['musicdbpk'=>$data['Songid'],
                'userid'=>$user->id,'date'=>$date,'path'=>$data['path'],'songnum'=>$data['songnum']]);
            if($result){
                return response()->json(['Code'=>200,'Msg'=>'入库成功','Data'=>['date'=>$date]]);
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
        $date = date('Y-m-d H:i:s');
        if(md5($timestamp.'1f2606123d0b5f8282561cf5e0d049ab')!=$signature){
            return response()->json(['Code'=>500,'Msg'=>'加密出错','Data'=>null]);
        }
        $post = json_decode(urldecode(file_get_contents("php://input")),true);
        if($get && $post){
            $exists = DB::table('songs')->where('musicdbpk',$post['musicdbpk'])->exists();
            if($exists){
                if(!empty($post['bugeId'])){
                    DB::table('add_songs')->where('bugeId',$post['bugeId'])->update(['state'=>2,'musicdbpk'=>$post['musicdbpk'],'recordDate'=>$date]);
                }else{
                    $result = DB::table('songs')->where('musicdbpk',$post['musicdbpk'])->update($post);
                }

            }else{
                if(!empty($post['bugeId'])){
                    DB::table('add_songs')->where('bugeId',$post['bugeId'])->update(['state'=>2,'musicdbpk'=>$post['musicdbpk'],'recordDate'=>$date]);
                    unset($post['bugeId']);
                }
                $result = DB::table('songs')->insert($post);

                //消息推送的，加1
                $time1 = date('Y-m-d');
                $time2 = $time1.' 09:00:00';
                $time3 = $time1.' 16:30:00';
                $time4 = date("Y-m-d",strtotime("+1 day"));            
                if($time1<=$date && $date<$time2){
                    $starttime = date("Y-m-d",strtotime("-1 day")).' 16:30:00';
                    $endtime = date("Y-m-d").' 09:00:00';
                }elseif($time2<=$date && $date<$time3){
                    $starttime = date("Y-m-d").' 09:00:00';
                    $endtime = date("Y-m-d").' 16:30:00';                    
                }elseif($time3<=$date && $date<$time4){
                    $starttime = date("Y-m-d").' 16:30:00';
                    $endtime = date("Y-m-d",strtotime("+1 day")).' 09:00:00';
                }
                
                $exist = DB::table('song_record')->where(['time','>=',$starttime],['time','<',$endtime])->exists();
                if($exist){
                    DB::table('song_record')->where(['time','>=',$starttime],['time','<',$endtime])->increment('total');
                }else{
                    DB::table('song_record')->insert(['time'=>$date,'total'=>1]);
                }     
                
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
        $date = date('Y-m-d H:i:s');
        if(md5($timestamp.'1f2606123d0b5f8282561cf5e0d049ab')!=$signature){
            return response()->json(['Code'=>500,'Msg'=>'加密出错','Data'=>null]);
        }
        $post = json_decode(urldecode(file_get_contents("php://input")),true);
        if($get && $post){
            $exists = DB::table('ban_songs')->where('musicdbpk',$post['musicdbpk'])->exists();
            if($exists){
                $result = DB::table('ban_songs')->where('musicdbpk',$post['musicdbpk'])->update($post);
            }else{
                $post['recordDate'] = $date;
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
        $date = date('Y-m-d H:i:s');
        if(md5($timestamp.'1f2606123d0b5f8282561cf5e0d049ab')!=$signature){
            return response()->json(['Code'=>500,'Msg'=>'加密出错','Data'=>null]);
        }
        $post = json_decode(urldecode(file_get_contents("php://input")),true);
        if($get && $post){
            $exists = DB::table('danger_songs')->where('musicdbpk',$post['musicdbpk'])->exists();
            if($exists){
                $result = DB::table('danger_songs')->where('musicdbpk',$post['musicdbpk'])->update($post);
            }else{
                $post['recordDate'] = $date;
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
        if(!empty($_GET['starttime'])){
            $data = DB::table('ban_songs')->where('uploadDateStr','>',$_GET['starttime'])->paginate(60);
        }else{
            $data = DB::table('ban_songs')->paginate(60);
        }
        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //获取高危列表
    public function get_danger_songs()
    {
        if(!empty($_GET['starttime'])){
            $data = DB::table('danger_songs')->where('uploadDateStr','>',$_GET['starttime'])->paginate(60);
        }else{
            $data = DB::table('danger_songs')->paginate(60);
        }
        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //用户提交补歌
    public function add_songs()
    {
        $user = Auth::guard('api')->user();
        $get = $_GET;
        if($get){
            if(empty($get['singer']) || empty($get['songname'])){
                return response()->json(['Code'=>500,'Msg'=>'数据不正确','Data'=>null]);
            }
            $data = ['singer'=>$get['singer'],
                    'songname'=>$get['songname'],
                    'userid'=>$user->id,
                    'state'=>0,
                    'date'=>date('Y-m-d H:i:s')
            ];
            $id = DB::table('add_songs')->insertGetId($data);
            $exists = DB::table('songs')->where(['name'=>$get['songname'],'singer'=>$get['singer']])
                ->first();
            if(empty($id)){
                return response()->json(['Code'=>500,'Msg'=>'更新失败','Data'=>null]);
            }
            if(!empty($exists->musicdbpk)){
                $result = DB::table('add_songs')->where('bugeId',$id)->update(['state'=>2,'musicdbpk'=>$exists->musicdbpk]);
                if($result){
                    return response()->json(['Code'=>200,'Msg'=>'已更新补歌数据','Data'=>null]);
                }else{
                    return response()->json(['Code'=>500,'Msg'=>'更新失败','Data'=>null]);
                }
            }
            return response()->json(['Code'=>200,'Msg'=>'已更新补歌数据','Data'=>null]);

        }else{
            return response()->json(['Code'=>500,'Msg'=>'数据不正确','Data'=>null]);
        }

    }

    //获取补歌列表
    public function get_add_songs()
    {
        $user = Auth::guard('api')->user();
        $get = $_GET;
        $map = array();
        if(isset($get['state'])){
            $map['state'] = $get['state'];
        }
        if(!empty($get['starttime'])){
            $data = DB::table('add_songs')->where('date','>',$get['starttime'])
                ->where('userid',$user->id)->where($map)->orderBy('date','desc')->paginate(60);
        }else{
            $data = DB::table('add_songs')->where('userid',$user->id)->where($map)->orderBy('date','desc')->paginate(60);
        }
        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //获取用户已下载歌曲列表
    public function get_users_songs()
    {
        $user = Auth::guard('api')->user();
        $get = $_GET;
        if(!empty($get['starttime'])){
            $data = DB::table('users_songs')->where('date','>',$get['starttime'])
                ->where('userid',$user->id)->paginate(60);
        }else{
            $data = DB::table('users_songs')->where('userid',$user->id)->paginate(60);
        }

        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //获取用户已入库歌曲列表
    public function get_user_warehouse()
    {
        $user = Auth::guard('api')->user();
        $get = $_GET;
        if(!empty($get['starttime'])){
            $data = DB::table('user_warehouse')->where('date','>',$get['starttime'])
                ->where('userid',$user->id)->paginate(60);
        }else{
            $data = DB::table('user_warehouse')->where('userid',$user->id)->paginate(60);
        }

        if($data){
            $data= $data->toArray();
            $data = array_merge(['Code'=>200],$data);
            return response()->json($data);
        }
    }

    //用户删除禁播歌曲
    public function delete_ban()
    {
        $user = Auth::guard('api')->user();
        $get = $_GET;
        $get['userId'] = $user->id;
        $time = date('Y-m-d H:i:s');
        unset($get['api_token']);
        $get['time'] = $time;
        $result = DB::table('delete_ban')->insert($get); 
        if($result){
            return response()->json(['Code'=>200,'Msg'=>'记录删除禁播歌曲成功','Data'=>$time]);
        }
        return response()->json(['Code'=>500,'Msg'=>'记录删除禁播歌曲失败','Data'=>null]);
    }

    //用户删除高危歌曲
    public function delete_danger()
    {
        $user = Auth::guard('api')->user();
        $get = $_GET;
        $get['userId'] = $user->id;
        $time = date('Y-m-d H:i:s');
        unset($get['api_token']);
        $get['time'] = $time;
        $result = DB::table('delete_danger')->insert($get);
        if($result){
            return response()->json(['Code'=>200,'Msg'=>'记录删除高危歌曲成功','Data'=>$time]);
        }
        return response()->json(['Code'=>500,'Msg'=>'记录删除高危歌曲失败','Data'=>null]);
    }

    //消息推送
    public function get_record()
    {
        $user = Auth::guard('api')->user();
        $date = date('Y-m-d H:i:s');
        $time1 = date('Y-m-d');
        $time2 = $time1.' 09:00:00';
        $time3 = $time1.' 16:30:00';
        $time4 = date("Y-m-d",strtotime("+1 day"));            
        if($time1<=$date && $date<$time2){
            $starttime = date("Y-m-d",strtotime("-1 day")).' 09:00:00';
            $endtime = date("Y-m-d",strtotime("-1 day")).' 16:30:00';
        }elseif($time2<=$date && $date<$time3){
            $starttime = date("Y-m-d",strtotime("-1 day")).' 16:30:00';
            $endtime = date("Y-m-d").' 09:00:00';
        }elseif($time3<=$date && $date<$time4){
            $starttime = date("Y-m-d").' 09:00:00';
            $endtime = date("Y-m-d").' 16:30:00';
        }
        
        $newRecord = DB::table('song_record')->where([['time','>',$starttime],['time','<',$endtime]])->value('total');
        if(!isset($newRecord)){
            $newRecord = 0;
        }
        $addRecord = DB::table('add_songs')->where([['recordDate','>',$starttime],['recordDate','<',$endtime]],['state','=',2],['userid'=>$user->id])->count();
        $banRecord = DB::table('ban_songs')->where([['recordDate','>',$starttime],['recordDate','<',$endtime]])->count();
        $dangerRecord = DB::table('danger_songs')->where([['recordDate','>',$starttime],['recordDate','<',$endtime]])->count();

        $data = ['newRecord'=>$newRecord,'addRecord'=>$addRecord,'banRecord'=>$banRecord,'dangerRecord'=>$dangerRecord,'starttime'=>$starttime,'endtime'=>$endtime];

        //记录用户获取推送的时间
        // $exist = DB::table('user_record')->where('userid',$user->id)->exists();
        // if(!$exist){
        //     DB::table('user_record')->insert(['userid'=>$user->id,'date'=>$date]);
        // }else{          
        //     if($time1<=$date && $date<$time2){
        //         $starttime = date("Y-m-d",strtotime("-1 day")).' 16:30:00';
        //         $endtime = date("Y-m-d").' 09:00:00';
        //     }elseif($time2<=$date && $date<$time3){
        //         $starttime = date("Y-m-d").' 09:00:00';
        //         $endtime = date("Y-m-d").' 16:30:00';                    
        //     }elseif($time3<=$date && $date<$time4){
        //         $starttime = date("Y-m-d").' 16:30:00';
        //         $endtime = date("Y-m-d",strtotime("+1 day")).' 09:00:00';
        //     }
        //     $existence = DB::table('user_record')->where([['date','>',$starttime],['date','<',$endtime]])->exists();
        //     if($existence){
        //         return response()->json(['Code'=>200,'Msg'=>'','Data'=>['newRecord'=>0,'addRecord'=>0,'banRecord'=>0,'dangerRecord'=>0]]);
        //     }else{
        //         DB::table('user_record')->where('userid',$user->id)->update(['date'=>$date]);
        //     }
        // }

        return response()->json(['Code'=>200,'Msg'=>'','Data'=>$data]);
    }

}
