@extends('quick::layout.app-simple')

@section('content')
    <el-form ref="loginForm" :model="form" :rules="rules" class="login-box" v-loading="loading">
        <h3 class="login-title">欢迎登录</h3>
        <el-form-item prop="email">
            <el-input type="text" placeholder="请输入账号" v-model="form.email"></el-input>
        </el-form-item>
        <el-form-item prop="password">
            <el-input type="password" placeholder="请输入密码" v-model="form.password"></el-input>
        </el-form-item>
        <el-form-item prop="captcha">
            <el-row :gutter="20">
                <el-col :span="12">
                    <el-input type="text" placeholder="请输入验证码" v-model="form.captcha" @keyup.enter.native="onSubmit('loginForm')"></el-input>
                </el-col>
                <el-col :span="12">
                    <img :src="captchaSrc" @click="changeCaptcha" alt="点击图片重新获取验证码" style="border-radius: 4px;">
                </el-col>
            </el-row>
        </el-form-item>
        <el-form-item style="width:100%; padding-top: 10px;">
            <el-button type="primary" @click="onSubmit('loginForm')" style="width:100%;">登录</el-button>
        </el-form-item>
    </el-form>
@endsection

@push('css')
    <style>
        .login-box {
            border: 1px solid #DCDFE6;
            width: 350px;
            margin: 180px auto;
            padding: 35px 35px 15px 35px;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            box-shadow: 0 0 25px #909399;
        }

        .login-title {
            text-align: center;
            margin: 0 auto 30px auto;
            color: #303133;
        }
    </style>
@endpush

@section('content-vue')
    @include('quick::admin.auth.login_vue')
@endsection
