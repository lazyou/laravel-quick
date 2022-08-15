let obj = {
    el: '#app',
    data: () => {
        return {
            visible: false,
            loading: false,
            tableData: [],
        }
    },
    created() {
        this.getList();
    },
    methods: {
        getList() {
            this.loading = true;

            axios.get('/admin/vue?t=1')
                .then((response) => {
                    // handle success
                    console.log(response);
                    this.tableData = response;
                    console.log(this.tableData);
                })
                .catch((error) => {
                    // handle error
                    console.log(error);
                })
                .then(() => {
                    // always executed
                }).finally(() => {
                this.loading = false;
            });
        },
    },
};

