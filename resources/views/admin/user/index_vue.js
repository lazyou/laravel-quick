let obj = {
    el: appEl,
    data: () => {
        return {
            ...appData,

            // 用户创建相关
            dialog: false,
            dialog_title: '',
            dialog_loading: false,
            user_init: {
                id: 0,
                name: null,
                status: 1,
                is_admin: 0,
                email: null,
                password: null,
            },
            user_form: {},
            user_rules: {
                name: [
                    { required: true, message: '请输入名称', trigger: 'blur' },
                    { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
                ],
                role_id: [
                    { required: true, message: '请选择角色', trigger: 'blur' },
                ],
                email: [
                    { required: true, message: '请输入邮箱', trigger: 'blur' },
                    { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
                ],
                password: [
                    { message: '请输入密码', trigger: 'blur' },
                    { min: 6, max: 20, message: '长度在 6 到 20 个字符', trigger: 'blur' }
                ],
            },
            role_options: [],

            // 列表
            list: [],
            total: 0,
            params: {
                page: 1,
                limit: 15,
                name: null,
            },
        }
    },
    created() {
        this.getRoleOptions();
        this.getWarehouseList();
    },
    methods: {
        getRoleOptions() {
            axios.get('/admin/user/roles')
                .then((res) => {
                    this.role_options = res;
                })
        },
        openEdit(user) {
            this.user_form = user;
            this.dialog_title = user.id ? '编辑用户' : '添加用户';
            this.dialog = true;
        },
        rowEdit() {
            this.$refs['userForm'].validate((valid) => {
                if (valid) {
                    this.dialog_loading = true;
                    axios.post('/admin/user', { ...this.user_form })
                        .then((res) => {
                            this.dialog = false;
                            this.getWarehouseList();
                        }).finally(() => {
                            this.dialog_loading = false;
                        });
                }
            });
        },
        rowDelete(user) {
            confirmWarning(_ => {
                axios.delete(`/admin/user/${user.id}`).then((res) => {
                    this.main_loading = true;
                    this.getWarehouseList();
                })
            });
        },
        getWarehouseList() {
            this.main_loading = true;
            axios.get('/admin/user', { params: this.params })
                .then((res) => {
                    this.list = res.data;
                    this.total = res.total;
                }).finally(() => {
                    this.main_loading = false;
                });
        },
        // 每页数量变化
        paginationChange() {
            this.getWarehouseList();
        },
    },
};

