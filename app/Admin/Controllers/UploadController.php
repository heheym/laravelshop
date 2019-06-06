<?php

namespace App\Admin\Controllers;

use App\Admin\Upload;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Illuminate\Support\Facades\DB;
//use Qiniu\Http\Request;
use Illuminate\Http\Request;
use zgldh\QiniuStorage\QiniuStorage;

class UploadController extends Controller
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
            ->body(view('upload.create'));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Upload());

        $grid->Songid('ID')->sortable();


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
        $show = new Show(Upload::findOrFail($id));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Upload);

        $form->display('id', 'ID');
        $form->file('文件');


        return $form;
    }

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
//        var_dump($re);
//        return;
        //初始化下标0到数组（excel没有下标为0的数组，从1开始），避免后面读取报错
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
        }


    }

    //上传七牛云
    public function store1(Request $request)
    {
        $disk = QiniuStorage::disk('qiniu');
        $token = $disk->uploadToken();
        return response()->json(['uptoken'=>$token]);
    }

}