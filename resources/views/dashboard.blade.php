<!DOCTYPE html>
    <html>
        <head>

            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>@yield('title', '.lockd - Secure password storage')</title>

            <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

            <link rel="stylesheet" href="/lib/semantic/dist/semantic.min.css">
            <link rel="stylesheet" href="/lib/toastr/toastr.min.css">
            <link rel="stylesheet" href="/css/dashboard.min.css">

        </head>
        <body id="dashboardPage">

            <aside class="lockd sidebar">

                <img src="/images/logo.png" alt=".lockd Logo" class="ui tiny centered image">
                <h2 class="ui header">.lockd</h2>

                <hr>

                <h4 class="ui header">Navigation</h4>

                <nav class="ui secondary vertical menu">
                    <a class="active item" v-link="{'path': '/passwords'}">
                        Passwords
                    </a>
                    <a class="item" v-link="{'path': '/search'}">
                        Search
                    </a>
                    <a class="item" v-link="{'path': '/account'}">
                        Account
                    </a>
                </nav>



                <h4 class="ui header">Administration</h4>

                <nav class="ui secondary vertical menu">
                    <a class="item" v-link="{'path': '/user-management'}">
                        User Management
                    </a>
                    <a class="item" v-link="{'path': '/group-management'}">
                        Group Management
                    </a>
                </nav>
            </aside>

            <main>
                <router-view></router-view>
            </main>

            <script src="/lib/clipboard/dist/clipboard.js"></script>
            <script src="/lib/vue/dist/vue.min.js"></script>
            <script src="/lib/vue-router/dist/vue-router.min.js"></script>
            <script src="/lib/jquery/dist/jquery.min.js"></script>
            <script src="/lib/toastr/toastr.min.js"></script>
            <script src="/lib/semantic/dist/semantic.min.js"></script>
            <script src="/js/dashboard.min.js"></script>

        </body>
    </html>