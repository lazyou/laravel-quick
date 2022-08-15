let obj = {
    el: appEl,
    data: () => {
        return {
            ...appData,

            // 用户创建相关
            dialog: false,
            dialog_title: '',
            dialog_loading: false,
            log_form: {},
            log_rules: {
                title: [
                    { required: true, message: '请输入名称', trigger: 'blur' },
                    { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
                ],
                sort: [
                    { required: true, message: '请输入排序', trigger: 'blur' },
                    { type: 'number', message: '排序必须为数字值'},
                ],
            },

            // 列表
            list: [],
            total: 0,

            // 搜索
            params: {
                page: 1,
                limit: 15,
                as: null,
                url: null,
            },
        }
    },
    created() {
        this.getLogList();
    },
    methods: {
        // 提交内容查看
        logBodyOpen(row) {
            this.log_form = row;
            this.dialog_title = `${row.id} 提交的数据`;
            this.dialog = true;
        },
        getLogList() {
            this.main_loading = true;
            axios.get('/admin/log', { params: this.params })
                .then((res) => {
                    this.list = res.data;
                    this.total = res.total;
                }).finally(() => {
                    this.main_loading = false;
                });
        },
        // 每页数量变化
        paginationChange() {
            this.getLogList();
        },
    },
};

