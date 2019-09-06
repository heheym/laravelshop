<?php

namespace App\Admin\Controllers;

use App\Admin\Song;
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
            ->body($this->form()->edit($id));
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

        $grid->Songid('Songid');
        $grid->Songnum('Songnum');
        $grid->SongName('SongName');
        $grid->Singer('Singer');
        $grid->SongNameAlias('SongNameAlias');
        $grid->SingerAlias('SingerAlias');
        $grid->Langtype('Langtype');
        $grid->Songtype('Songtype');
        $grid->Songmark('Songmark');
        $grid->SoundType('SoundType');
        $grid->AlbumName('AlbumName');
        $grid->Pinyin('Pinyin');
        $grid->AllPinyin('AllPinyin');
        $grid->Wordcount('Wordcount');
        $grid->FistWordStrokes('FistWordStrokes');
        $grid->Strokes('Strokes');
        $grid->IssueAreaID('IssueAreaID');
        $grid->SongCustomTypes('SongCustomTypes');
        $grid->RecordCompany('RecordCompany');
        $grid->singerArea('SingerArea');
        $grid->SingerPinyin('SingerPinyin');
        $grid->SingerAllPinyin('SingerAllPinyin');
        $grid->SingerSex('SingerSex');
        $grid->SingerBH('SingerBH');
        $grid->SingerOneWorkBH('SingerOneWorkBH');
        $grid->UploadDate('UploadDate');
        $grid->videoClass('VideoClass');
        $grid->Filename('Filename');
        $grid->FileSize('FileSize');
        $grid->SongMid('SongMid');

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
