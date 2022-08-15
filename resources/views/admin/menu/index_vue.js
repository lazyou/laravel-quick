let id = 1000;
let obj = {
    el: appEl,
    data: () => {
        return {
            ...appData,

            // 仓库创建相关
            dialog: false,
            dialog_title: '',
            form_loading: false,
            menu_form: null,
            parent_options: [],
            menu_rules: {
                parent_id: [
                    { required: true, message: '请选择上级菜单', trigger: 'blur' },
                ],
                name: [
                    { required: true, message: '请输入名称', trigger: 'blur' },
                    { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
                ],
                url: [
                    { required: true, message: '请输入链接', trigger: 'blur' },
                    { min: 1, max: 30, message: '长度在 1 到 30 个字符', trigger: 'blur' }
                ],
                sort: [
                    { required: true, message: '请输入排序', trigger: 'blur' },
                    { type: 'number', message: '排序必须为数字值'},
                ],
                icon: [
                    { required: false, message: '请输入图标', trigger: 'blur' },
                    { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
                ],
            },

            //
            tree_loading: false,
            tree: [],
        }
    },
    created() {
        this.getTree();
    },
    methods: {
        menuInit() {
            return {
                id: 0,
                parent_id: 0,
                name: null,
                url: null,
                icon: '',
                sort: 0,
                status: 1,
            }
        },
        setRootOptions() {
            this.parent_options = [
                {
                    id: 0,
                    name: '根节点',
                },
            ];
        },
        createTopMenu() {
            this.menu_form = this.menuInit();
            this.setRootOptions();
        },
        createSecondMenu() {
            this.menu_form = this.menuInit();
            this.menu_form.parent_id = null;
            this.setParentOptions();
        },
        setParentOptions() {
            this.form_loading = true;
            axios.get('/admin/menu/top_options')
                .then((res) => {
                    this.parent_options = res;
                })
                .finally(() => {
                    this.form_loading = false;
                });
        },
        getTree() {
            this.tree_loading = true;
            axios.get('/admin/menu/tree_menus')
                .then((res) => {
                    this.tree = res;
                })
                .finally(() => {
                    this.tree_loading = false;
                });
        },
        treeNodeClick(menu) {
            this.menu_form = menu;
            if (menu.parent_id) {
                this.setParentOptions();
            } else {
                this.setRootOptions();
            }
        },
        remove(node, data) {
            confirmWarning(_ => {
                axios.delete(`/admin/menu/${data.id}`).then((res) => {
                    this.getTree();
                });
            });
        },
        rowEdit() {
            this.$refs['menuForm'].validate((valid) => {
                if (valid) {
                    this.form_loading = true;
                    axios.post('/admin/menu', { ...this.menu_form })
                        .then((res) => {
                            this.getTree();
                        })
                        .finally(() => {
                            this.form_loading = false;
                        });
                }
            });
        },
        resetEdit() {
            this.$refs['menuForm'].resetFields();
        },
    },
};

