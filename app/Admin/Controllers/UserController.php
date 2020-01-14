<?php

namespace App\Admin\Controllers;

use App\Model\PassUserModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PassUserModel());

        $grid->column('pass_id', __('Pass id'));
        $grid->column('pass_name', __('Pass name'));
        $grid->column('pass_tel', __('Pass tel'));
        $grid->column('pass_email', __('Pass email'));
        $grid->column('pass_pwd', __('Pass pwd'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(PassUserModel::findOrFail($id));

        $show->field('pass_id', __('Pass id'));
        $show->field('pass_name', __('Pass name'));
        $show->field('pass_tel', __('Pass tel'));
        $show->field('pass_email', __('Pass email'));
        $show->field('pass_pwd', __('Pass pwd'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PassUserModel());

        $form->text('pass_name', __('Pass name'));
        $form->text('pass_tel', __('Pass tel'));
        $form->text('pass_email', __('Pass email'));
        $form->text('pass_pwd', __('Pass pwd'));

        return $form;
    }
}
