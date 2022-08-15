let obj = {
    el: appEl,
    data: () => {
        return {
            ...appData,

            menu_options: [],
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
        this.getMenuOptions();
        this.getPermissionList();
    },
    methods: {
        getMenuOptions() {
            this.main_loading = true;
            axios.get('/admin/permission/menus', { params: this.params })
                .then((res) => {
                    this.menu_options = res;
                })
                .finally(() => {
                    this.main_loading = false;
                });
        },
        getPermissionList() {
            this.main_loading = true;
            axios.get('/admin/permission', { params: this.params })
                .then((res) => {
                    this.list = res;
                })
                .finally(() => {
                this.main_loading = false;
            });
        },
        rowEdit(permission) {
            this.main_loading = true;
            axios.post('/admin/permission', { ...permission })
                .then((res) => {})
                .finally(() => {
                    this.main_loading = false;
                });
        },
        rowDelete(permission) {
            confirmWarning(_ => {
                axios.delete(`/admin/permission/${permission.id}`).then((res) => {
                    this.main_loading = true;
                    this.getPermissionList();
                })
            });
        },

        // 每页数量变化
        paginationChange() {
            this.getPermissionList();
        },
    },
};

