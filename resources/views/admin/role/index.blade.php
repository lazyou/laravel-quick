@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
    <div id="action">
        <el-button @click="openEdit(role_init)" v-show="{{ p('admin.role.edit') }}" type="primary">添加角色</el-button>
        <el-input v-model="params.name" @keyup.enter.native="getRoleList" clearable placeholder="名字" style="width: 180px;"></el-input>
        <el-button @click="getRoleList" icon="el-icon-search" type="primary"></el-button>
    </div>

    <div id="dialog" v-loading="dialog_loading">
        <el-dialog :visible.sync="dialog" :title="dialog_title" :fullscreen="true">
            <el-form :model="role_form" :rules="role_rules" ref="roleForm" label-width="100px" class="demo-ruleForm">
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="名称" prop="name">
                            <el-input v-model="role_form.name"></el-input>
                        </el-form-item>
                        <el-form-item label="标识" prop="flag">
                            <el-input v-model="role_form.flag"></el-input>
                        </el-form-item>
                        <el-form-item label="备注" prop="remark">
                            <el-input type="textarea" v-model="role_form.remark"></el-input>
                        </el-form-item>
                    </el-col>

                    <el-col :span="12">
                        <el-form-item label="权限">
                            <el-tree
                                ref="tree"
                                node-key="id"
                                :data="tree"
{{--                                :default-checked-keys="[]"--}}
                                :default-checked-keys="permission_ids"
                                :expand-on-click-node="false"
{{--                                @check-change="treeCheckChange"--}}
{{--                                @node-click="treeNodeClick"--}}
                                show-checkbox
                                highlight-current
                                default-expand-all>
                        <span class="custom-tree-node" slot-scope="{ node, data }">
                            <i :class="node.data.icon">@{{ node.label }}</i>
                        </span>
                            </el-tree>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="dialog = false">取消</el-button>
                <el-button @click="rowEdit" v-show="{{ p('admin.role.edit') }}" type="primary" >提交</el-button>
            </span>
        </el-dialog>
    </div>

    <div id="table">
        <el-table :data="list" border fit highlight-current-row>
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column prop="name" label="名字"></el-table-column>
            <el-table-column prop="flag" label="标识"></el-table-column>
            <el-table-column prop="remark" label="备注"></el-table-column>
            <el-table-column prop="created_at" label="创建时间"></el-table-column>
            <el-table-column label="操作">
                <template slot-scope="{row}">
                    <el-button @click="openEdit(row)" v-show="{{ p('admin.role.edit') }}" type="primary" plain>编辑</el-button>
                    <el-button @click="rowDelete(row)" v-show="{{ p('admin.role.delete') }}" type="warning" plain>删除</el-button>
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
    @include('quick::admin.role.index_vue')
@endsection
