{{-- TODO: vue 相关组件   --}}
{{--<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">--}}
{{--<script src="https://unpkg.com/vue/dist/vue.js"></script>--}}
{{--<script src="https://unpkg.com/element-ui/lib/index.js"></script>--}}
{{--<script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
<link rel="stylesheet" href="{{ asset('quick/vue/element-ui-2.15.1.css') }}">

@if($_debug)
    <script src="{{ asset('quick/vue/vue-2.6.12-dev.js') }}"></script>
@else
    <script src="{{ asset('quick/vue/vue-2.6.12-prod.js') }}"></script>
@endif

<script src="{{ asset('quick/vue/element-ui-2.15.1.js') }}"></script>
<script src="{{ asset('quick/vue/axios-0.21.0.min.js') }}"></script>
<script src="{{ asset('quick/vue/axios-util.js') }}"></script>
