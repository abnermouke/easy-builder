{{-- 继承模版 --}}
@extends('pros.console.layouts.master')

{{-- 页面标题 --}}
@section('title', '{__DATA_NAME__}')

{{-- 自定义页面样式 --}}
@section('styles')

@endsection

{{-- 自定义主体内容 --}}
@section('container')
    {!!
            \Abnermouke\Pros\Builders\Table\TableBuilder::BASIC()
            ->setButtons(function (\Abnermouke\Pros\Builders\Table\Tools\TableButtonBuilder $builder) {
                $builder->form(route('pros.console.{__CONSOLE_ROUTE_NODES__}.detail', ['id' => 0]), '添加{__DATA_NAME__}');
            })
            ->setItems(function (\Abnermouke\Pros\Builders\Table\Tools\TableItemBuilder $builder) {
                $builder->string('id', 'ID');

                //TODO : 其他字段

                $builder->switch('status', '状态')->on(\App\Model{__RESOURCE_DICTIONARY__}\{__CASE_NAME__}::STATUS_ENABLED, route('pros.console.{__CONSOLE_ROUTE_NODES__}.enable', ['id' => '__ID__']), 'post', '正常启用')->off(\App\Model{__RESOURCE_DICTIONARY__}\{__CASE_NAME__}::STATUS_DISABLED, route('pros.console.{__CONSOLE_ROUTE_NODES__}.enable', ['id' => '__ID__']), 'post', '禁止使用');
                $builder->string('created_at', '创建时间')->date('friendly')->sorting(true, \App\Model{__RESOURCE_DICTIONARY__}\{__CASE_NAME__}::TABLE_NAME);
                $builder->string('updated_at', '更新时间')->date('Y-m-d H:i:s')->sorting(true, \App\Model{__RESOURCE_DICTIONARY__}\{__CASE_NAME__}::TABLE_NAME);
            })
            ->setActions(function (\Abnermouke\Pros\Builders\Table\Tools\TableActionBuilder $builder) {
                $builder->form(route('pros.console.{__CONSOLE_ROUTE_NODES__}.detail', ['id' => '__ID__']), '编辑');
                $builder->ajax(route('pros.console.{__CONSOLE_ROUTE_NODES__}.delete', ['id' => '__ID__']), '删除')->theme('danger')->confirmed('删除后将不可恢复，是否继续？');
            })
            ->setFilters(function (\Abnermouke\Pros\Builders\Table\Tools\TableFilterBuilder $builder) {
                $builder->input('keyword', '关键词')->placeholder('请输入关键词信息检索');
                $builder->date('created_at', '创建时间');
                $builder->date('updated_at', '更新时间');
            })
            ->setQuery(route('pros.console.{__CONSOLE_ROUTE_NODES__}.lists'))
            ->pagination()
            ->export()
            ->render()
        !!}
@endsection

{{-- 自定义页面弹窗 --}}
@section('popups')

@endsection

{{-- 自定义页面javascript --}}
@section('script')

@endsection
