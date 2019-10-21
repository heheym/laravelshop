<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AddSong;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class AddSongController extends Controller
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
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AddSong);

        $grid->bugeId('ID');
        $grid->singer('歌星名');
        $grid->songname('歌名');
        $grid->userid('用户')->display(function ($userid){
            return DB::table('users')->where('id',$userid)->value('name');
        });
        $grid->musicdbpk('总库id');
        $grid->state('状态')->display(function ($state){
            $stateArray = [0=>'新增',1=>'处理中',2=>'已完成',3=>'已下载',4=>'取消无法处理'];
            return $stateArray[$state];
        });
        $grid->source('来源');
        $grid->explain('说明');
        $grid->date('提交时间');

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
        $show = new Show(AddSong::findOrFail($id));

        $show->bugeId('BugeId');
        $show->singer('Singer');
        $show->songname('Songname');
        $show->userid('Userid');
        $show->musicdbpk('Musicdbpk');
        $show->state('State');
        $show->source('Source');
        $show->explain('Explain');
        $show->date('Date');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AddSong);

//        $form->number('bugeId', 'ID');
        $form->text('singer', '歌星名');
        $form->text('songname', '歌名');

        //更复杂的显示
        $form->display('userid','用户')->with(function ($value) {
            return DB::table('users')->where('id',$value)->value('name');
        });

        $form->text('musicdbpk', '总库id')->readOnly();
        $form->select('state', '状态')->options([0=>'新增',1=>'处理中',2=>'已完成',3=>'已下载',4=>'取消无法处理']);
        $form->text('source', '来源');
        $form->text('explain', '说明');
        $form->datetime('date', '提交时间');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        return $form;
    }
}
