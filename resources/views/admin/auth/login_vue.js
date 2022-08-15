let obj = {
    el: '#app',
    data: () => {
        return {
            loading: false,
            captchaSrc: '{{ captcha_src("flat") }}',
            form: {
                email: null,
                password: null,
                captcha: null,
            },

            // 表单验证，需要在 el-form-item 元素中增加 prop 属性
            rules: {
                email: [
                    {required: true, message: '账号不可为空', trigger: 'blur'}
                ],
                password: [
                    {required: true, message: '密码不可为空', trigger: 'blur'}
                ],
                captcha: [
                    {required: true, message: '验证码不可为空', trigger: 'blur'}
                ],
            },
        }
    },
    created() {
    },
    methods: {
        // 验证码
        changeCaptcha() {
            this.captchaSrc = '/captcha/flat?' + Math.random();
        },
        // 前端登录验证
        onSubmit(formName) {
            // 为表单绑定验证功能
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.loginPost();
                } else {
                    this.$message.error('请输入账号密码');
                    return false;
                }
            });
        },
        // 后端登录验证
        loginPost() {
            this.loading = true;

            axios.post('/admin/auth/login', {
                email: this.form.email,
                password: this.form.password,
                captcha: this.form.captcha,
            }).then((res) => {
                window.location.href = res.url;
            }).catch((error) => {
                this.loading = false;
                this.changeCaptcha();
            }).finally(() => {
                // this.loading = false;
            });
        },
    },
};
