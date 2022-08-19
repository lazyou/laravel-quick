@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
    <div id="action">
        <el-button @click="createTopMenu" type="primary">添加一级菜单</el-button>
        <el-button @click="createSecondMenu" type="primary">添加二级菜单</el-button>
    </div>

    <div id="table">
        <el-row>
            <el-col :span="12" v-loading="tree_loading">
                <div class="block">
                    <el-tree
                        :data="tree"
{{--                        show-checkbox--}}
                        node-key="id"
                        default-expand-all
                        @node-click="treeNodeClick"
                        :expand-on-click-node="false">
                        <span class="custom-tree-node" slot-scope="{ node, data }">
                            <i :class="node.data.icon">@{{ node.label }}</i>
                            <el-button v-show="data.type == 1" type="text" size="mini" @click="() => remove(node, data)">
                            删除
                            </el-button>
                        </span>
                    </el-tree>
                </div>
            </el-col>

            <el-col :span="12" v-if="menu_form" v-loading="form_loading">
                <el-form :model="menu_form" :rules="menu_rules" ref="menuForm" label-width="100px" class="demo-ruleForm">
                    <el-form-item label="上级" prop="parent_id">
                        <el-select v-model="menu_form.parent_id" placeholder="请选择">
                            <el-option
                                v-for="item in parent_options"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="menu_form.name"></el-input>
                    </el-form-item>
                    <el-form-item label="链接" prop="url">
                        <el-input v-model="menu_form.url" placeholder="请以 / 开头"></el-input>
                    </el-form-item>
                    <el-form-item label="排序" prop="sort">
                        <el-input-number controls-position="right" v-model="menu_form.sort" :min="1" :max="999" label="排序"></el-input-number>
                    </el-form-item>
                    <el-form-item label="图标" prop="icon">
                        <el-input v-model="menu_form.icon" :prefix-icon="menu_form.icon"></el-input>
                        <el-link type="primary" href="https://element.eleme.cn/#/zh-CN/component/icon" target="_blank">图标来源</el-link>
                    </el-form-item>
                    <el-form-item label="状态" prop="status">
                        <el-switch v-model="menu_form.status" :active-value="1" :inactive-value="2"></el-switch>
                    </el-form-item>
                    <el-form-item>
                        <el-button @click="rowEdit" type="primary" >提交</el-button>
                        <el-button @click="resetEdit">重置</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
    </div>
@endsection

@push('css')
    <style>
        .custom-tree-node {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            padding-right: 8px;
        }
    </style>
@endpush

@section('component-vue')
    @include('quick::component.pagination.pagination')
@endsection

@section('content-vue')
    @include('quick::admin.menu.index_vue')
@endsection
