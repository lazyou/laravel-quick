@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
    <div id="action">
{{--        @if(permission('admin.user.edit'))--}}
            <el-button v-show="{{ p('admin.user.edit') }}"  @click="openEdit(user_init)" type="primary">添加用户</el-button>
{{--        @endif--}}

        <el-input v-model="params.name" @keyup.enter.native="getWarehouseList" clearable placeholder="名字" style="width: 180px;"></el-input>
        <el-button @click="getWarehouseList" icon="el-icon-search" type="primary"></el-button>
    </div>

    <div id="dialog" v-loading="dialog_loading">
        <el-dialog :visible.sync="dialog" :title="dialog_title">
            <el-form :model="user_form" :rules="user_rules" ref="userForm" label-width="100px" class="demo-ruleForm">
                <el-form-item label="名称" prop="name">
                    <el-input v-model="user_form.name"></el-input>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch v-model="user_form.status" :active-value="1" :inactive-value="2"></el-switch>
                </el-form-item>
                <el-form-item label="超级管理员" prop="is_admin">
                    <el-switch v-model="user_form.is_admin" :active-value="1" :inactive-value="0"></el-switch>
                </el-form-item>
                <el-form-item label="角色" prop="role_id">
                    <el-select v-model="user_form.role_id" placeholder="请选择角色">
                        <el-option
                            v-for="item in role_options"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="邮箱" prop="email">
                    <el-input v-model="user_form.email"></el-input>
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input v-model="user_form.password" show-password></el-input>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="dialog = false">取消</el-button>
                <el-button @click="rowEdit" v-show="{{ p('admin.user.edit') }}" type="primary" >提交</el-button>
            </span>
        </el-dialog>
    </div>

    <div id="table">
        <el-table :data="list" border fit highlight-current-row>
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="name" label="名字"></el-table-column>
            <el-table-column prop="email" label="邮箱"></el-table-column>
            <el-table-column prop="status_name" label="状态"></el-table-column>
            <el-table-column prop="created_at" label="创建时间"></el-table-column>
            <el-table-column label="操作">
                <template slot-scope="{row}">
                    <el-button @click="openEdit(row)" v-show="{{ p('admin.user.edit') }}" type="primary" plain>编辑</el-button>
                    <el-button @click="rowDelete(row)" v-show="{{ p('admin.user.delete') }}" type="warning" plain>删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <vue-pagination
            :total="total"
            :page.sync="params.page"
            :limit.sync="params.limit"
            @pagination="paginationChange">
        </vue-pagination>
    </div>
@endsection

@section('component-vue')
    @include('quick::component.pagination.pagination')
@endsection

@section('content-vue')
    @include('quick::admin.user.index_vue')
@endsection
