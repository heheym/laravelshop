<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Song;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use zgldh\QiniuStorage\QiniuStorage;

class SongController extends Controller
{
    use HasResourceActions;

    public function update($id)
    {
        return $this->editForm()->update($id);
    }

    public function destroy($id)
    {
        return $this->editForm()->destroy($id);
    }

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->editForm()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form())
            ->body(view('song.create'));
    }

    /**
     * 导入excel文件
     * @param Request $request
     * @param Content $content
     */
    public function store(Request $request,Content $content)
    {
        //按时间命名文件
        $name = time() . '.xls';
        //文件保存到storage/app/public
        $path = $request->file('文件')->storeAs('public', $name);
        //获取文件url
        $url = storage_path() . '/app/' . $path;

        //读取文件
        $file = \PHPExcel_IOFactory::load($url);
        //文件转数组
        $re = $file->getSheet(0)->toArray(null, false, false, true);

        $re[0] = '';
        $temp = [];
        //获取文件数组条目数
        $recounts = count($re);
        //开始循环写入数据库，为什么$i=2？，因为第一行为标头
        for ($i = 3; $i < $recounts; $i++) {
            foreach($re[$i] as $k=>$v){
                $temp[$i][$re[2][$k]] = $v;
            }
        }

        $result = DB::table('songs')->insert($temp);
        if(!$result){
            admin_toastr(trans('数据库插入失败'),'error');
        }else{
            admin_toastr(trans('数据库录入成功'),'success');
        }
    }

    /**
     * 上传歌曲文件
     * @param Request $request
     */
    public function upload(Request $request)
    {
        
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Song);

        $grid->musicdbpk('总库id');
        $grid->name('歌名');
        $grid->singer('歌星名');
        $grid->singerType('歌星类型')->display(function($singerType){
            $singerTypeArray = [1=>'男',2=>'女',3=>'合唱',4=>'组合',5=>'群星'];
            return $singerTypeArray[$singerType];
        });
        $grid->location('地区')->display(function($location){
            $locationArray = [1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它'];
            return $locationArray[$location];
        });
        $grid->namePingYin('歌曲拼音');
        $grid->nameFullPingYing('歌曲全拼');
        $grid->nameCharacts('歌曲笔画');
        $grid->nameWordLenght('歌曲字数');
        $grid->nameCharactsCount('歌曲的首字笔画数');
        $grid->singerNameFirst('歌手的首字笔画数');
        $grid->singerPingYin('歌手拼音');
        $grid->singerFullPingYing('歌手全拼');
        $grid->singerLocation('歌手别名');
        $grid->singerCharacts('歌星笔画');
        $grid->chineseName('歌曲别名');
        $grid->size('文件大小');
        $grid->language('语种')->display(function($language){
            $languageArray = [0=>'国语',1=>'粤语',2=>'英语',3=>'台语',4=>'日语',5=>'韩语',6=>'其它'];
            return $languageArray[$language];
        });
        $grid->videoClass('视频类型')->display(function($videoClass){
            $videoClassArray = [7=>'HD高清',5=>'DVD 16:9',2=>'DVD 4:3',1=>'DVD 4:3单'];
            return $videoClassArray[$videoClass];
        });;
        $grid->recordCompany('唱片公司');
        $grid->album('专辑');
        $grid->copyRight('是否有授权')->display(function($copyRight){
            return $copyRight?'授权':'非授权';
        });
        $grid->category('歌曲类别')->display(function($category){
            $categoryArray = [1=>'流行歌曲', 2=>'男女对唱',3=>'军旅红歌',4=>'戏曲',5=>'儿童歌曲',6=>'舞曲',7=>'节日祝福',8=>'迪士高',9=>'民歌'];
            return $categoryArray[$category];
        });
        $grid->type('音乐类型')->display(function($type){
            $typeArray = [1=>'普通歌曲',2=>'新歌推荐',4=>'替换歌曲',5=>'网络歌曲'];
            return $typeArray[$type];
        });
        $grid->format('版本格式')->display(function($format){
            $formatArray = [1=>'MTV',2=>'演唱会',3=>'影视剧情',4=>'人物',5=>'风景',6=>'动画',7=>'其他'];
            return $formatArray[$format];
        });
        $grid->uploadDateStr('发布时间');
        $grid->audioClass('音频格式')->display(function($audioClass){            
            return $audioClass==1?'原版伴奏':'消音伴奏';
        });
        $grid->isTaste('是否体验')->display(function($isTaste){            
            return $isTaste==1?'是':'是否热补';
        });
        $grid->isApp('发布类型')->display(function($isApp){      
            $isAppArray = [0=>'新歌', 1=>'补歌', 2=>'又是新歌又是补歌'];
            return $isAppArray[$isApp];
        });
        $grid->sedName('专区名一');
        $grid->thiName('专区名二');
        $grid->localPath('文件路径');
        $grid->bugeId('补歌网id');
        $grid->isRealCopy('曲库类型')->display(function($isRealCopy){
            $isRealCopyArray = [0=>'新歌曲库',1=>'有版权曲库',2=>'无版权曲库',3=>'经典歌曲',4=>'公播新歌'];
            return $isRealCopyArray[$isRealCopy];
        });
        $grid->searchName1('查询名称1');
        $grid->searchName2('查询名称2');
        $grid->word('歌词');
        $grid->introduce('制作类型');
        $grid->hasLogo('是否加标')->display(function($hasLogo){
            return $hasLogo?'是':'否';
        });
        $grid->ranking('排行');
        $grid->musicMid('歌曲mid');
        $grid->bscoin('宝声币');
        $grid->ispf('是否评分')->display(function($ispf){
            return $ispf?'是':'否';
        });
        $grid->isbsHide('是否隐藏')->display(function($isbsHide){
            return $isbsHide?'是':'否';
        });
        $grid->variety('综艺专辑');
        $grid->isbver('是否B版')->display(function($isbver){
            return $isbver?'是':'否';
        });
        $grid->songnum('歌曲编号');



        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Song::findOrFail($id));

        $show->Songid('Songid');
        $show->Songnum('Songnum');
        $show->SongName('SongName');
        $show->Singer('Singer');
        $show->SongNameAlias('SongNameAlias');
        $show->SingerAlias('SingerAlias');
        $show->Langtype('Langtype');
        $show->Songtype('Songtype');
        $show->Songmark('Songmark');
        $show->SoundType('SoundType');
        $show->AlbumName('AlbumName');
        $show->Pinyin('Pinyin');
        $show->AllPinyin('AllPinyin');
        $show->Wordcount('Wordcount');
        $show->FistWordStrokes('FistWordStrokes');
        $show->Strokes('Strokes');
        $show->IssueAreaID('IssueAreaID');
        $show->SongCustomTypes('SongCustomTypes');
        $show->RecordCompany('RecordCompany');
        $show->singerArea('SingerArea');
        $show->SingerPinyin('SingerPinyin');
        $show->SingerAllPinyin('SingerAllPinyin');
        $show->SingerSex('SingerSex');
        $show->SingerBH('SingerBH');
        $show->SingerOneWorkBH('SingerOneWorkBH');
        $show->UploadDate('UploadDate');
        $show->videoClass('VideoClass');
        $show->Filename('Filename');
        $show->FileSize('FileSize');
        $show->SongMid('SongMid');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Song);

        /*$form->number('Songid', 'Songid');
        $form->text('Songnum', 'Songnum');
        $form->text('SongName', 'SongName');
        $form->text('Singer', 'Singer');
        $form->text('SongNameAlias', 'SongNameAlias');
        $form->text('SingerAlias', 'SingerAlias');
        $form->switch('Langtype', 'Langtype');
        $form->switch('Songtype', 'Songtype');
        $form->switch('Songmark', 'Songmark');
        $form->switch('SoundType', 'SoundType');
        $form->text('AlbumName', 'AlbumName');
        $form->text('Pinyin', 'Pinyin');
        $form->text('AllPinyin', 'AllPinyin');
        $form->number('Wordcount', 'Wordcount');
        $form->text('FistWordStrokes', 'FistWordStrokes');
        $form->text('Strokes', 'Strokes');
        $form->switch('IssueAreaID', 'IssueAreaID');
        $form->switch('SongCustomTypes', 'SongCustomTypes');
        $form->text('RecordCompany', 'RecordCompany');
        $form->switch('singerArea', 'SingerArea');
        $form->text('SingerPinyin', 'SingerPinyin');
        $form->text('SingerAllPinyin', 'SingerAllPinyin');
        $form->text('SingerSex', 'SingerSex');
        $form->text('SingerBH', 'SingerBH');
        $form->text('SingerOneWorkBH', 'SingerOneWorkBH');
        $form->datetime('UploadDate', 'UploadDate')->default(date('Y-m-d H:i:s'));
        $form->number('videoClass', 'VideoClass');
        $form->text('Filename', 'Filename');
        $form->decimal('FileSize', 'FileSize')->default(0.00);
        $form->number('SongMid', 'SongMid');*/
        $form->file('文件');
        return $form;
    }

    protected function editForm()
    {
        $form = new Form(new Song);
        // $form->display('musicdbpk','总库id');
        $form->text('name','歌名');
        $form->text('singer','歌星名');
        $form->select('singerType','歌星类型')->options([1=>'男',2=>'女',3=>'合唱',4=>'组合',5=>'群星']);
        $form->select('location','地区')->options([1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它']);
        $form->text('namePingYin','歌曲拼音');
        $form->text('nameFullPingYing','歌曲全拼');
        $form->text('nameCharacts','歌曲笔画');
        $form->text('nameWordLenght','歌曲字数');
        $form->text('nameCharactsCount','歌曲的首字笔画数');
        $form->text('singerNameFirst','歌手的首字笔画数');
        $form->text('singerPingYin','歌手拼音');
        $form->text('singerFullPingYing','歌手全拼');
        $form->text('singerLocation','歌手别名');
        $form->text('singerCharacts','歌星笔画');
        $form->text('chineseName','歌曲别名');
        $form->text('size','文件大小');
        $form->select('language','语种')->options([0=>'国语',1=>'粤语',2=>'英语',3=>'台语',4=>'日语',5=>'韩语',6=>'其它']);
        $form->select('videoClass','视频类型')->options([7=>'HD高清',5=>'DVD 16:9',2=>'DVD 4:3',1=>'DVD 4:3单']);
        $form->text('recordCompany','唱片公司');
        $form->text('album','专辑');
        $form->switch('copyRight', '是否有授权');
        $form->select('category','歌曲类别')->options([1=>'流行歌曲', 2=>'男女对唱',3=>'军旅红歌',4=>'戏曲',5=>'儿童歌曲',6=>'舞曲',7=>'节日祝福',8=>'迪士高',9=>'民歌']);
        $form->select('type','音乐类型')->options([1=>'普通歌曲',2=>'新歌推荐',4=>'替换歌曲',5=>'网络歌曲']);
        $form->select('format','版本格式')->options([1=>'MTV',2=>'演唱会',3=>'影视剧情',4=>'人物',5=>'风景',6=>'动画',7=>'其他']);
        $form->text('uploadDateStr','发布时间');
        $form->select('audioClass','音频格式')->options([0=>'消音伴奏',1=>'原版伴奏']);
        $form->switch('isTaste','是否体验');
        $form->select('isApp','发布类型')->options([0=>'新歌', 1=>'补歌', 2=>'又是新歌又是补歌']);
        $form->text('sedName','专区名一');
        $form->text('thiName','专区名二');
        $form->text('localPath','文件路径');
        $form->text('bugeId','补歌网id');
        $form->select('isRealCopy','曲库类型')->options([0=>'新歌曲库',1=>'有版权曲库',2=>'无版权曲库',3=>'经典歌曲',4=>'公播新歌']);
        $form->text('searchName1','查询名称1');
        $form->text('searchName2','查询名称2');
        $form->text('word','歌词');
        $form->text('introduce','制作类型');
        $form->switch('hasLogo','是否加标');
        $form->text('ranking','排行');
        $form->text('musicMid','歌曲mid');
        $form->text('bscoin','宝声币');
        $form->switch('ispf','是否评分');
        $form->switch('isbsHide','是否隐藏');
        $form->text('variety','综艺专辑');
        $form->switch('isbver','是否B版');
        $form->text('songnum','歌曲编号');
        
        return $form;
    }

    /**
     * 七牛云getToken
     */
    public function getToken()
    {
        $disk = QiniuStorage::disk('qiniu');
        $token = $disk->uploadToken();
        return response()->json(['uptoken'=>$token]);
    }

}
