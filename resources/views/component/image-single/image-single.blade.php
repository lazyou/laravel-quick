<script type="text/x-template" id="image-single">
    <div v-loading="loading" class="upload-container">
        <el-upload
            :data="dataObj"
            :multiple="false"
            :show-file-list="false"
            :before-upload="beforeUpload"
            :on-success="handleImageSuccess"
            class="image-uploader"
            :action="qiniuUploadUrl"
            accept="image/*"
            drag
        >
            <i v-if="!imageUrl" class="el-icon-upload"/>
            <div v-if="!imageUrl" class="el-upload__text">
                Drag或<em>点击上传</em>
            </div>
        </el-upload>
        <div v-show="imageUrl" class="image-preview">
            <div v-show="imageUrl" class="image-preview-wrapper">
                <el-image :src="imageUrl" fit="cover" :preview-src-list="[imageUrl]"/>
                <div class="image-delete-action" @click="rmImage">
                    <i class="el-icon-delete"/>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
    Vue.component('image-single', {
        template: '#image-single',
        props: {
            value: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                loading: false,
                tempUrl: '',
                dataObj: {
                    token: '',
                    key: ''
                },
                // 七牛配置: 华南区上传地址
                qiniuUploadUrl: "{{ config('qiniu.upload_url') }}"
            }
        },
        computed: {
            imageUrl() {
                return this.value
            }
        },
        created() {
        },
        methods: {
            rmImage() {
                this.$confirm('是否继续删除该文件?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.emitInput('')
                })
            },
            emitInput(val) {
                this.$emit('input', val)
            },
            handleImageSuccess() {
                this.loading = false
                this.emitInput(this.tempUrl)
            },
            beforeUpload(file) {
                this.loading = true
                const fileInfo = {
                    url: file.name,
                    size: file.size,
                    type: file.type
                }

                const _self = this
                return new Promise((resolve, reject) => {
                    axios.post('/admin/qiniu/token', fileInfo).then(response => {
                        _self._data.dataObj.token = response.token
                        _self._data.dataObj.key = response.key
                        this.tempUrl = response.url
                        resolve(true)
                    }).catch(() => {
                        reject(false)
                    })
                })
            }
        }
    });
</script>

<style>
    .upload-container {
        width: 30%;
        /*height: 100%;*/
        position: relative;
    }

    .upload-container .image-uploader {
        /*height: 100%;*/
    }

    .upload-container .image-preview {
        width: 100%;
        height: 94%;
        position: absolute;
        left: 0px;
        top: 0px;
        border: 1px dashed #d9d9d9;
    }

    .upload-container .image-preview .image-preview-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        /* 上下左右居中 */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-container .image-preview .image-preview-wrapper .el-image {
        max-width: 100%;
        max-height: 100%;
    }

    .upload-container .image-preview .image-delete-action {
        position: absolute;
        width: 100%;
        left: 0;
        top: 80%;
        cursor: default;
        text-align: center;
        color: #fff;
        opacity: 0;
        font-size: 20px;
        background-color: rgba(0, 0, 0, 0.5);
        transition: opacity 0.3s;
        cursor: pointer;
        text-align: center;
        /*line-height: 200px;*/
    }

    .upload-container .image-preview .image-delete-action .el-icon-delete {
        font-size: 36px;
    }

    .upload-container .image-preview:hover .image-delete-action {
        opacity: 1;
    }
</style>
