@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
    <div id="action">
    </div>

    <div id="dialog">
    </div>

    <div id="table">
        <h1 style="text-align: center;">{{ config('quick.admin_title') }} 使用文档</h1>

        <div style="height: 300px;">
            <el-steps direction="vertical" :active="99">
                <el-step title="步骤 0 -- 后台路由" >
                    <div slot="description">
                        <p>请参考 README.md 设置后台路由文件 <code>routes/quick_admin.php</code></p>
                    </div>
                </el-step>

                <el-step title="步骤 1 -- 创建菜单" >
                    <div slot="description">
                        <p>系统管理 -> 菜单管理 -> 创建菜单</p>
                    </div>
                </el-step>

                <el-step title="步骤 2 -- 路由与控制器">
                    <div slot="description">
                        <p>填写路由地址 -> 创建对应的控制器</p>
                    </div>
                </el-step>

                <el-step title="步骤 3 -- 权限管理">
                    <div slot="description">
                        <p>系统管理 -> 权限管理 -> 设置所属菜单，填写权限名称</p>
                        <p>模板中使用权限验证 <code>v-show="{{ 'p(\'admin.xxx.delete\')' }}"</code></p>
                    </div>
                </el-step>
            </el-steps>
        </div>
    </div>
@endsection


@push('css')
    <style>
        .el-step__description.is-finish{
            color: black;
        }
    </style>
@endpush


@section('component-vue')
    @include('quick::component.pagination.pagination')
@endsection

@section('content-vue')
    @include('quick::admin.home.index_vue')
@endsection
