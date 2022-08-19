<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ $_title }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        @include('quick::layout.common')
        @stack('css')
    </head>

    <body>
        <div id="app">
            @yield('content')
        </div>

        <script>
            @yield('content-vue')
            Vue.use(ElementUI, {size: "{{ config('quick.ui_size')  }}"})
            new Vue(obj);
        </script>

        @stack('js')
    </body>
</html>
