<?php

namespace App\Admin\Controllers;

use App\Admin\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
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
        $grid = new Grid(new User);

        $grid->id('Id');
        $grid->name('用户名');
        $grid->email('邮箱');
        $grid->phone('手机号');
        $grid->mac('Mac');

        $grid->vipState('会员状态')->display(function ($vipState) {
            $arra = [0=>'试用会员',1=>'付费会员',2=>'已过期会员'];
            return $arra[$vipState];
        });

        $grid->vipXgStartDay('可浏览天数');
        $grid->vipStartTime('会员开始时间');
        $grid->vipTime('会员到期时间');

        $grid->download('可下载次数');
        $grid->add('是否可以补歌')->display(function ($add) {
            return $add?'是':'否';
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
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->email('Email');
        $show->phone('Phone');
        $show->password('Password');
        $show->remember_token('Remember token');
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');
        $show->api_token('Api token');
        $show->mac('Mac');
        $show->vipTime('VipTime');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('name', '用户名');
        $form->email('email', '邮箱');
        $form->mobile('phone', '手机号');
        $form->text('mac', 'Mac');

        $form->select('vipState', '会员状态')->options([0=>'试用会员',1=>'付费会员',2=>'已过期会员']);
        $form->text('vipXgStartDay', '可浏览天数');
        $form->datetime('vipStartTime', '会员开始时间');
        $form->datetime('vipTime', '会员到期时间');
        $form->text('download', '可下载次数');
        $form->switch('add', '是否可以补歌');

        return $form;
    }
}
