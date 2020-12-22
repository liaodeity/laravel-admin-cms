let Main = {
    data() {
        return {
            logo: "https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
            chinaImg: "https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
            americaImg: "https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
            wechat: {
                wechatImg: "https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
                isWechat: false
            },
            qq: {
                qqImg: "https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png",
                isQq: false,
            },
            menu: {
                userBgcolor: '#f0f2f5'
            },
            github: 'https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png',
            langLogo: 'https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png',
            avatar: 'https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png',
            tabDefaultValue: 'index',
            filterVisible: false,
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
    created: function () {
        console.log(222);
    },
    methods: {
        onSearch() {

        },
        handleTabClick() {

        },
        handleSizeChange(val) {
            this.formData.pageSize = val;
            this.getList();
        },
        handleCurrentChange(val) {
            this.formData.currentPage = val;
            this.getList();
        }
    }
}
var Ctor = Vue.extend(Main)
Vue.prototype.$ELEMENT = {size: 'small'};
new Ctor().$mount('#app')
