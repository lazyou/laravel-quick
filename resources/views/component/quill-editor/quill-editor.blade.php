{{--
    https://github.com/surmon-china/vue-quill-editor
    https://www.bootcdn.cn/quill/1.3.7/
--}}
<link rel="stylesheet" href="{{ asset('quick/vue/quill-editor/css/quill.core.min.css') }}">
<link rel="stylesheet" href="{{ asset('quick/vue/quill-editor/css/quill.snow.min.css') }}">
<link rel="stylesheet" href="{{ asset('quick/vue/quill-editor/css/quill.bubble.min.css') }}">

<script src="{{ asset('quick/vue/quill-editor/js/quill.min.js') }}"></script>
<script src="{{ asset('quick/vue/quill-editor/js/vue-quill-editor.js') }}"></script>

<script type="text/javascript">
    Vue.use(window.VueQuillEditor)
</script>

<style>
    .quill-editor{
        height: 400px;
    }
</style>
