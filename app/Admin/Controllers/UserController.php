<?php

namespace App\Admin\Controllers;

use App\Admin\User;
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
//        $grid->password('Password');
//        $grid->remember_token('Remember token');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');
//        $grid->api_token('Api token');
        $grid->mac('Mac');
        $grid->vipTime('会员到期时间');

        $grid->rank('级别')->display(function ($rank) {
            return $rank ? '高清' : '普清';
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
        $show->created_at('Created at');
        $show->updated_at('Updated at');
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
//        $form->password('password', 'Password');
//        $form->text('remember_token', 'Remember token');
//        $form->text('api_token', 'Api token');
        $form->text('mac', 'Mac');
        $form->datetime('vipTime', '会员到期时间');
        $rank = [
            0 => '普清',
            1 => '高清',
        ];
        $form->select('rank', '级别')->options($rank);
        return $form;
    }
}
