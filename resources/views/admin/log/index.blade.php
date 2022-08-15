@extends('quick::layout.app')

@section('content')
    {{-- 标准单页面CURD参考 --}}
    <div id="action">
        <el-input v-model="params.as" @keyup.enter.native="getLogList" @clear="getLogList"  clearable placeholder="别名" style="width: 220px;"></el-input>
        <el-input v-model="params.url" @keyup.enter.native="getLogList" @clear="getLogList"  clearable placeholder="链接" style="width: 220px;"></el-input>
        <el-button @click="getLogList" icon="el-icon-search" type="primary"></el-button>
    </div>

    <div id="dialog" v-loading="dialog_loading">
        <el-dialog :visible.sync="dialog" :title="dialog_title">
            <el-form :model="log_form" :rules="log_rules" ref="logForm" label-width="100px" class="demo-ruleForm">
                <el-form-item label="数据" prop="body_format">
                    <el-input v-model="log_form.body_format" type="textarea" readonly :autosize="{ minRows: 5, maxRows: 10}"></el-input>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="dialog = false">关闭</el-button>
            </span>
        </el-dialog>
    </div>

    <div id="table">
        <el-table :data="list" border fit highlight-current-row>
            <el-table-column prop="id" label="ID" width="80"></el-table-column>
            <el-table-column label="菜单权限">
                <template slot-scope="{row}">
                    @{{ row.permission && row.permission.parent ? row.permission.parent.name + ' -> ' : '' }}
                    @{{ row.permission ? row.permission.name : '' }}
                </template>
            </el-table-column>
            <el-table-column prop="as" label="路由别名"></el-table-column>
            <el-table-column prop="method" label="方法" width="100"></el-table-column>
            <el-table-column prop="url" label="链接" width="400"></el-table-column>
            <el-table-column prop="ip" label="IP地址"></el-table-column>
            <el-table-column label="操作人">
                <template slot-scope="{row}">
                    @{{ row.user ? row.user.name : '' }}
                </template>
            </el-table-column>
            <el-table-column prop="created_at" label="创建时间" width="180"></el-table-column>
            <el-table-column label="操作" width="100">
                <template slot-scope="{row}">
                    <el-button v-if="row.body" @click="logBodyOpen(row)" type="primary" plain>数据</el-button>
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
    @include('quick::admin.log.index_vue')
@endsection
