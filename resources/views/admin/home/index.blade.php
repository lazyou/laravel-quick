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
            <el-steps direction="vertical" :active="3">
                <el-step title="步骤 1" description="路由"></el-step>
                <el-step title="步骤 2" description="控制器"></el-step>
                <el-step title="步骤 3" description="模板"></el-step>
            </el-steps>
        </div>
    </div>
@endsection

@section('component-vue')
    @include('quick::component.pagination.pagination')
@endsection

@section('content-vue')
    @include('quick::admin.home.index_vue')
@endsection
