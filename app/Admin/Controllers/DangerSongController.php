<?php

namespace App\Admin\Controllers;

use App\Admin\Models\DangerSong;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DangerSongController extends Controller
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
        $grid = new Grid(new DangerSong);

        $grid->name('歌名');
        $grid->singer('歌星名');
        $grid->language('语种')->display(function ($language){
            $languageArray = [0=>'国语',1=>'粤语',2=>'英语',3=>'台语',4=>'日语',5=>'韩语',6=>'其它'];
            return $languageArray[$language];
        });
        $grid->recordcompany('唱片公司');
        $grid->musicdbpk('总库id');
        $grid->uploadDateStr('提交时间');
        
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
        $show = new Show(DangerSong::findOrFail($id));

        $show->name('Name');
        $show->singer('Singer');
        $show->language('Language');
        $show->recordcompany('Recordcompany');
        $show->musicdbpk('Musicdbpk');
        $show->uploadDateStr('UploadDateStr');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DangerSong);

        $form->text('name', '歌名');
        $form->text('singer', '歌星名');
        $form->select('language', '语种')->options([0=>'国语',1=>'粤语',2=>'英语',3=>'台语',4=>'日语',5=>'韩语',6=>'其它']);
        $form->text('recordcompany', '唱片公司');
        $form->datetime('uploadDateStr', '提交时间');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        return $form;
    }
}
