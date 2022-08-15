@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
{{--    <div id="action">--}}
{{--        <el-button @click="openEdit(permission_init)" type="primary">导入权限</el-button>--}}
{{--        <el-input v-model="params.name" @keyup.enter.native="getPermissionList" clearable placeholder="名字" style="width: 180px;"></el-input>--}}
{{--        <el-button @click="getPermissionList" icon="el-icon-search" type="primary"></el-button>--}}
{{--    </div>--}}

    <div id="table">
        <el-table :data="list" border fit highlight-current-row>
{{--            <el-table-column prop="id" label="ID" width="60"></el-table-column>--}}
            <el-table-column label="所属菜单" width="150">
                <template slot-scope="{row}">
                    <el-select v-model="row.parent_id" @change="rowEdit(row)" placeholder="请选择">
                        <el-option
                            v-for="item in menu_options"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </template>
            </el-table-column>
            <el-table-column label="名字" width="150">
                <template slot-scope="{row}">
                    <el-input
                        v-model="row.name"
                        @change="rowEdit(row)"
                        placeholder="请输入权限名字"
                        maxlength="20"
                       >
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column prop="as" label="别名"></el-table-column>
            <el-table-column prop="url" label="地址"></el-table-column>
            <el-table-column prop="controller" label="控制器" width="550"></el-table-column>
{{--            <el-table-column prop="sort" label="排序" width="80"></el-table-column>--}}
{{--            <el-table-column label="操作" width="180">--}}
{{--                <template slot-scope="{row}">--}}
{{--                    <el-button @click="openEdit(row)" type="primary" plain>编辑</el-button>--}}
{{--                    <el-button @click="rowDelete(row)" type="warning" plain>删除</el-button>--}}
{{--                </template>--}}
{{--            </el-table-column>--}}
        </el-table>
    </div>
@endsection

@section('component-vue')
    @include('quick::component.pagination.pagination')
@endsection

@section('content-vue')
    @include('quick::admin.permission.index_vue')
@endsection
