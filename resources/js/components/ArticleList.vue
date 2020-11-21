<template>
    <div class="">
        <el-tabs value="index" v-model="tabDefaultValue" type="border-card" @tab-click="handleTabClick">
            <el-tab-pane name="index" label="登记竣工记录">
                <el-form :label-position="right" :inline="true" :model="formData" class="search-form-inline">
                    <el-form-item label="登记时间">
                        <el-date-picker
                            v-model="formData.addtime"
                            type="datetimerange"
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期" class="width-100p">
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item label="待办单编号">
                        <el-input v-model="formData.serial_no" placeholder=""></el-input>
                    </el-form-item>
                    <el-form-item label="名称/身份证">
                        <el-input v-model="formData.keyword" placeholder=""></el-input>
                    </el-form-item>
                    <el-form-item label="推送状态">
                        <el-select v-model="formData.if_send" placeholder="请选择" clearable="" class="width-100p">
                            <el-option
                                v-for="item in selectVal.if_send"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item >
                        <el-button type="primary" @click="onSearch">查询</el-button>
                        <el-button type="primary" @click="dialogSearchVisible = true">登记竣工</el-button>
                    </el-form-item>
                </el-form>
                <el-table
                    :data="tableData"
                    style="width: 100%">
                    <el-table-column
                        prop="date"
                        label="日期"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="name"
                        label="姓名"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="address"
                        label="地址">
                    </el-table-column>
                </el-table>
                <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="pager.current"
                    :page-sizes="pager.sizeList"
                    :page-size="pager.size"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="pager.total">
                </el-pagination>
            </el-tab-pane>
        </el-tabs>
    </div>

</template>

<script>
    export default {
        name: "ArticleList",
        data() {
            return {
                tabDefaultValue: 'index',
                formData: {},
                selectVal: {},
                pager: {
                    current: 1,
                    total: 0,
                    size: 10,
                    sizeList: [10, 20, 30, 40],
                },
                tableData: [{
                    date: '2016-05-02',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄'
                }, {
                    date: '2016-05-04',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1517 弄'
                }, {
                    date: '2016-05-01',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1519 弄'
                }, {
                    date: '2016-05-03',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1516 弄'
                }]
            }
        },
        methods: {
            handleTabClick() {

            },
            handleSizeChange(val) {
                this.formData.pageSize = val;
                this.getList();
            },
            handleCurrentChange(val) {
                this.formData.currentPage = val;
                this.getList();
            },
        }
    }
</script>

<style scoped>

</style>
