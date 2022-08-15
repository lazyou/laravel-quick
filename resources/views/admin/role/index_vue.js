let obj = {
    el: appEl,
    data: () => {
        return {
            ...appData,

            // 角色创建相关
            dialog: false,
            dialog_title: '',
            dialog_loading: false,
            role_init: {
                id: 0,
                name: null,
                flag: null,
                remark: null,
                permission_ids: [],
            },
            permission_ids: [],
            tree: [],
            role_form: {},
            role_rules: {
                name: [
                    { required: true, message: '请输入名称', trigger: 'blur' },
                    { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
                ],
                remark: [
                    { required: false, message: '请输入备忘', trigger: 'blur' },
                    { min: 1, max: 100, message: '长度在 1 到 100 个字符', trigger: 'blur' }
                ],
            },

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
        this.getRoleList();
    },
    methods: {
        getTree() {
            this.dialog_loading = true;
            axios.get('/admin/menu/tree')
                .then((res) => {
                    this.tree = res;
                    this.resolveCheckedKeys(this.tree, this.role_form.permission_ids);
                })
                .finally(() => {
                    this.dialog_loading = false;
                });
        },
        // 提交的tree数据，包含半选key
        getPermissionIds() {
            let ref = this.$refs.tree;
            return ref.getCheckedKeys().concat(ref.getHalfCheckedKeys());
        },
        openEdit(role) {
            this.role_form = role;
            this.dialog_title = role.id ? '编辑角色' : '添加角色';
            this.dialog = true;
            if (this.permission_ids) {
                this.permission_ids = [];
            }

            this.getTree();

            // this.$nextTick(() => {
            //     let checkedKeys = this.resolveCheckedKeys(role.permission_ids);
            //     this.$refs.tree.setCheckedKeys(checkedKeys);
            // });
        },
        // 计算 tree 选中节点id，主要是半选情况的计算
        resolveCheckedKeys(tree, permission_ids) {
            tree.forEach(node => {
               if (! permission_ids.includes(node.id)) {
                   return;
               }

               if (! node.children) {
                   this.permission_ids.push(node.id);
               } else {
                   this.childrenCheckedKeys(node, permission_ids);
               }
            });
        },
        childrenCheckedKeys(node, permission_ids) {
            let allSubCheck = true;
            node.children.forEach(subNode => {
               if (! permission_ids.includes(subNode.id)) {
                   allSubCheck = false;
                   return;
               }

               if (! subNode.children) {
                   this.permission_ids.push(subNode.id);
                   if (allSubCheck) {
                       this.permission_ids.push(node.id);
                   }
               } else {
                   this.childrenCheckedKeys(subNode, permission_ids);
               }
            });
        },
        rowEdit() {
            let permission_ids = this.getPermissionIds();
            if (! permission_ids.length) {
                messageError('请勾选角色权限');
                return false;
            } else {
                this.role_form.permission_ids = permission_ids;
            }

            this.$refs['roleForm'].validate((valid) => {
                if (valid) {
                    this.dialog_loading = true;
                    axios.post('/admin/role', { ...this.role_form })
                        .then((res) => {
                            this.dialog = false;
                            this.getRoleList();
                        }).finally(() => {
                            this.dialog_loading = false;
                        });
                }
            });
        },
        rowDelete(role) {
            confirmWarning(_ => {
                axios.delete(`/admin/role/${role.id}`).then((res) => {
                    this.main_loading = true;
                    this.getRoleList();
                })
            });
        },
        getRoleList() {
            this.main_loading = true;
            axios.get('/admin/role', { params: this.params })
                .then((res) => {
                    this.list = res.data;
                    this.total = res.total;
                }).finally(() => {
                    this.main_loading = false;
                });
        },
        // 每页数量变化
        paginationChange() {
            this.getRoleList();
        },
    },
};

