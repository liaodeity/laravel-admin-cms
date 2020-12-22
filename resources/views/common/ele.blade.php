<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- import CSS -->
{{--    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">--}}
    <link rel="stylesheet" href="{{mix_build_dist('/css/admin/vendor.css')}}">
    <link rel="stylesheet" href="{{mix_build_dist('/css/admin/app.css')}}">
    @yield('style')
    <style>
        body {
            margin: 0;
        }

        .el-breadcrumb {
            height: 60px;
            line-height: 60px;
        }

        .el-main {
            padding-top: 0;
        }

        .el-header {
            padding: 0;
        }

    </style>
</head>
<body>
<div id="app">
    <el-container>

        <el-header>
            <div class="right-nav" ref="rightNav">
                <top-menu></top-menu>
                <div class="userinfo-right rflex">
                    <div class="notify-row">
                        <ul class="top-menu">
                            <li class="li-badge">
                                <el-tooltip class="item" effect="dark" content="访问github" placement="top">
                                    <a :href='github' target="_blank">
                                        <icon-svg icon-class="iconGithub"/>
                                    </a>
                                </el-tooltip>
                            </li>
                            <li class="li-badge">
                                <a :href='github' target="_blank" v-popover:qcode>
                                    <icon-svg icon-class="iconwechat"/>
                                    <el-popover
                                        ref="qcode"
                                        popper-class="qcodepopper"
                                        placement="bottom"
                                        trigger="hover">
                                        <div class="wechat-area cflex">
                                            <p class="titles">加我微信</p>
                                            {{--                                            <img :src="wechat.wechatImg" alt="加我微信"  />--}}
                                        </div>
                                    </el-popover>
                                </a>
                            </li>
                            <li class="li-badge">
                                <a :href='github' target="_blank" v-popover:qqcode>
                                    <icon-svg icon-class="iconqq"/>
                                    <el-popover
                                        ref="qqcode"
                                        popper-class="qcodepopper"
                                        placement="bottom"
                                        trigger="hover">
                                        <div class="wechat-area cflex">
                                            <p class="titles">加入qq群</p>
                                            {{--                                            <img :src="qq.qqImg" alt="加入qq群"  />--}}
                                        </div>
                                    </el-popover>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="userinfo">
                        <el-menu
                            class="el-menu-demo"
                            mode="horizontal"
                        >
                            <el-submenu index="1" popper-class="langItem">
                                <template slot="title">
                                    <img :src="langLogo" class='langAvatar' alt="">
                                </template>
                                <el-menu-item index="1-1" @click="changeLocale('zh')">
                                    <img :src="chinaImg" class='langAvatar' alt="">
                                    <span class="intro">中文</span>
                                </el-menu-item>
                                <el-menu-item index="1-2" @click="changeLocale('en')">
                                    <img :src="americaImg" class='langAvatar' alt="">
                                    <span class="intro">EngList</span>
                                </el-menu-item>
                            </el-submenu>

                            <el-submenu index="2" popper-class="infoItem">
                                <template slot="title">
                                    <div class='welcome'>
                                        <span class="name">11111,</span>
                                        <span class='name avatarname'> 2222</span>
                                    </div>
                                    <img :src="avatar" class='avatar' alt="">
                                </template>
                                <el-menu-item index="2-1" @click="setDialogInfo('info')">3333</el-menu-item>
                                <el-menu-item index="2-2" @click="setDialogInfo('pass')">4444</el-menu-item>
                                <el-menu-item index="2-3" @click="setDialogInfo('logout')">5555</el-menu-item>
                            </el-submenu>
                        </el-menu>
                    </div>
                </div>
            </div>
        </el-header>

        <el-container>
            <el-aside width="200px">
                <el-menu
                    default-active="2"
                    class="el-menu-vertical-demo"
                    @open="handleOpen"
                    @close="handleClose">
                    <el-submenu index="1">
                        <template slot="title">
                            <i class="el-icon-location"></i>
                            <span>导航一</span>
                        </template>
                        <el-menu-item-group>
                            <template slot="title">分组一</template>
                            <el-menu-item index="1-1">选项1</el-menu-item>
                            <el-menu-item index="1-2">选项2</el-menu-item>
                        </el-menu-item-group>
                        <el-menu-item-group title="分组2">
                            <el-menu-item index="1-3">选项3</el-menu-item>
                        </el-menu-item-group>
                        <el-submenu index="1-4">
                            <template slot="title">选项4</template>
                            <el-menu-item index="1-4-1">选项1</el-menu-item>
                        </el-submenu>
                    </el-submenu>
                    <el-menu-item index="2">
                        <i class="el-icon-menu"></i>
                        <span slot="title">导航二</span>
                    </el-menu-item>
                    <el-menu-item index="3" disabled>
                        <i class="el-icon-document"></i>
                        <span slot="title">导航三</span>
                    </el-menu-item>
                    <el-menu-item index="4">
                        <i class="el-icon-setting"></i>
                        <span slot="title">导航四</span>
                    </el-menu-item>
                </el-menu>
            </el-aside>
            <el-container>
                <el-main>
                    <el-breadcrumb separator-class="el-icon-arrow-right">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item>活动管理</el-breadcrumb-item>
                        <el-breadcrumb-item>活动列表</el-breadcrumb-item>
                        <el-breadcrumb-item>活动详情</el-breadcrumb-item>
                    </el-breadcrumb>
                    @yield('content')
                </el-main>
                <el-footer>

                </el-footer>
            </el-container>
        </el-container>
    </el-container>
</div>
</body>
<!-- import Vue before Element -->
{{--<script src="https://unpkg.com/vue/dist/vue.js"></script>--}}
<!-- import JavaScript -->
{{--<script src="https://unpkg.com/element-ui/lib/index.js"></script>--}}
<script type="text/javascript" src="{{mix_build_dist('js/manifest.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/vendor.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/app.js')}}"></script>
@yield('footer')
<script>
    // new Vue({
    //     el: '#app',
    //     data: function () {
    //         return {
    //             visible: false,
    //             title: "Hello world",
    //             msg: "Try <em>Element</em> to easy!"
    //         }
    //     }
    // })
</script>
</html>
