@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
    <div id="action">
    </div>

    <div id="dialog">
    </div>

    <div id="table">
        <h1 style="text-align: center;">使用方法</h1>

        <div style="height: 300px;">
            <el-steps direction="vertical" :active="3">
                <el-step title="步骤 1" description="设计好栏目名称，在栏目管理录入；"></el-step>
                <el-step title="步骤 2" description="到站点管理录入文章来源的站点，可利用备注来标记当天的录入情况；"></el-step>
                <el-step title="步骤 3" description="开始文章录入，后台会自动提取文章标题和正文，若不正确请手工修改；"></el-step>
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
