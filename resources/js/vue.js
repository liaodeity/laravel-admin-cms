import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI);
Vue.component('article-list', require('./components/ArticleList.vue').default)
const app = new Vue({
    el: '#vue-app',
});
