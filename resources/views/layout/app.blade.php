<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ $_title }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        @include('quick::layout.common')
        <style>
            {{-- element-ui css 重写, 通用 css --}}
            .el-aside {
                min-height: 720px;
            }
            .el-menu a {
                text-decoration: unset;
            }
            .el-header{
                color: #333;
                font-size: 12px;
                text-align: right;
                line-height: 60px;
                background-color: #f7f8fa!important;
                border-color: #f7f8fa!important;
            }
            .el-main{
                padding: 15px
            }
            .pagination-container{
                padding-top: 15px;
            }
            #action{
                padding-bottom: 15px;
            }
            .el-radio-button__inner, .el-radio-group{
                height: 40px;
                line-height: 50px;
            }
            .el-form, .el-form-item{
                margin-bottom: 0px !important;
            }
        </style>
        @stack('css')
    </head>

    <body>
        <div id="app" v-loading.fullscreen.lock="loading">
            <el-container>
                <el-aside width="200px" style="background-color: rgb(238, 241, 246);">
                    <el-menu default-active="{{ $_menu_active }}">
                        <el-menu-item index="0" disabled style="text-align: center; font-size: 28px; line-height: 52px;">
{{--                            <i class="{{ $menu['icon'] }}"></i>--}}
                            <span slot="title" >
                                <strong>{{ config('quick.admin_name', '') }}</strong>
                            </span>
                        </el-menu-item>

                        @foreach($_menus as $key => $menu)
                            @if(isset($menu['children']))
                                <el-submenu index="{{ $menu['url'] }}">
                                    <template slot="title">
                                        <i class="{{ $menu['icon'] }}"></i>{{ $menu['name'] }}
                                    </template>

                                    @foreach($menu['children'] as $subKey => $subMenu)
                                        <a href="{{ $subMenu['url'] }}">
                                            <el-menu-item index="{{ $subMenu['url'] }}">{{ $subMenu['name'] }}</el-menu-item>
                                        </a>
                                    @endforeach
                                </el-submenu>
                            @else
                                <a href="{{ $menu['url'] }}">
                                    <el-menu-item index="{{ $menu['url'] }}">
                                        <i class="{{ $menu['icon'] }}"></i>
                                        <span slot="title">{{ $menu['name'] }}</span>
                                    </el-menu-item>
                                </a>
                            @endif
                        @endforeach

                    </el-menu>
                </el-aside>

                <el-container>
                    <el-header style="height: 56px;">
                        <el-dropdown>
                          <span class="el-dropdown-link">
                              {{ $_auth->name }}
                              <i class="el-icon-arrow-down el-icon--right"></i>
                          </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-link href="/{{config('quick.admin_path', 'admin')}}/auth/logout">
                                <el-dropdown-item>注销</el-dropdown-item>
                            </el-link>
                        </el-dropdown-menu>
                        </el-dropdown>
                    </el-header>

                    <el-main v-loading.fullscreen.lock="main_loading">
                        @yield('content')
                    </el-main>
                </el-container>
            </el-container>
        </div>

        {{-- vue 的组件 --}}
        @yield('component-vue')

        <script>
            // TODO: 如何添加全局的vue属性方法
            let appEl = '#app';
            let appData = {
                loading: false,
                main_loading: false,
            };
            @yield('content-vue')
            // let app = {
            //     el: '#app',
            //     data: () => {
            //         return {
            //             ...obj.data(),
            //             mainLoading: false,
            //         };
            //     },
            //     // // TODO: 以下行不通
            //     // created() {
            //     //     obj.created();
            //     // },
            //     methods: {
            //         ...obj.methods,
            //     },
            // };
            // element-ui全局设置组件大小 https://blog.csdn.net/weixin_44795287/article/details/113841395
            Vue.use(ElementUI, {size: 'small'})
            new Vue(obj);
        </script>

    @stack('js')
    </body>
</html>
