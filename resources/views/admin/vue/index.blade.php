@extends('quick::layout.app')

@section('content')
    <el-button @click="visible = true">Button</el-button>
    <el-link href="/admin/vue">默认链接</el-link>

    <el-dialog :visible.sync="visible" title="Hello world">
        <p>Try Element</p>
    </el-dialog>

    <el-table :data="tableData"  v-loading="loading" :data="data" border fit highlight-current-row>
        <el-table-column prop="date" label="日期" width="180"></el-table-column>
        <el-table-column prop="name" label="姓名" width="180"></el-table-column>
        <el-table-column prop="address" label="地址"></el-table-column>
    </el-table>
@endsection

@section('content-vue')
    @include('quick::admin.vue.index_vue')
@endsection
