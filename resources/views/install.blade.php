<!DOCTYPE html>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>lockd - Installation</title>
    <link rel="stylesheet" href="/lib/toastr/toastr.min.css">
    <link rel="stylesheet" href="/lib/semantic/dist/semantic.min.css">
    <link rel="stylesheet" href="/css/install.css">
</head>
<body>

<header class="ui menu">
    <div class="header item">
        <img src="/images/logo.png" alt="lockd Logo">
        <span class="brand-name">lockd</span>
    </div>
</header>

<main id="installPage">
    <div class="ui container">
        <section class="ui four ordered top attached mini steps">
            <article class="step" :class="step.class" v-for="(name, step) in steps">
                <div class="content">
                    <div class="title">@{{ name | capitalize }}</div>
                    <div class="description">@{{ step.description }}</div>
                </div>
            </article>
        </section>
        <section class="ui attached segment" v-show="steps.environment.class == 'active'">
            <article class="controls">
                <button class="ui green disabled button" v-on:click="completeEnvironmentStep" :class="{'disabled': steps.environment.passedChecks != steps.environment.checks.length - 1}">
                    Next
                </button>
                <button class="ui primary button" v-on:click="runChecks">
                    Start
                </button>
            </article>
            <hr>
            <article class="ui text container">
                <table class="ui center aligned stackable table">
                    <thead>
                    <tr>
                        <th class="collapsing">State</th>
                        <th>Step</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="check in steps.environment.checks" :class="{'negative': check.state == 'failed', 'positive': check.state == 'ok'}">
                        <td class="collapsing">
                            <i class="icon" :class="{
                                'genderless': check.state == 'pending',
                                'check': check.state == 'ok',
                                'close': check.state == 'failed',
                            }"></i>
                        </td>
                        <td>@{{ check.description }}</td>
                    </tr>
                    </tbody>
                </table>
            </article>
        </section>
        <section class="ui attached segment" v-show="steps.database.class == 'active'">
            <article class="controls">
                <button class="ui green button" v-on:click="completeDatabaseStep" :class="{'disabled': steps.database.passedChecks != steps.database.checks.length - 1}">
                    Next
                </button>
                <button class="ui primary button" v-on:click="runDbChecks">
                    Start
                </button>
            </article>
            <hr>
            <article class="ui text container">
                <table class="ui center aligned stackable table">
                    <thead>
                    <tr>
                        <th class="collapsing">State</th>
                        <th>Step</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="check in steps.database.checks" :class="{'negative': check.state == 'failed', 'positive': check.state == 'ok'}">
                        <td class="collapsing">
                            <i class="icon" :class="{
                                'genderless': check.state == 'pending',
                                'check': check.state == 'ok',
                                'close': check.state == 'failed',
                            }"></i>
                        </td>
                        <td>@{{ check.description }}</td>
                    </tr>
                    </tbody>
                </table>
            </article>
        </section>
        <section class="ui attached segment" v-show="steps.administrator.class == 'active'">
            <article class="controls">
                <button class="ui green button" v-on:click="completeAdministratorStep" :class="{'disabled': !steps.administrator.created}">
                    Finish
                </button>
                <button class="ui primary button" v-on:click="createAdministrator" :class="{'disabled': steps.administrator.created}">
                    Create
                </button>
            </article>
            <hr>
            <article class="ui text container">
                <form class="ui form">
                    <div class="two fields">
                        <div class="field" :class="{'disabled': steps.administrator.created}">
                            <label>First Name</label>
                            <input type="text" placeholder="First Name" v-model="steps.administrator.user.firstName">
                        </div>
                        <div class="field" :class="{'disabled': steps.administrator.created}">
                            <label>Last Name</label>
                            <input type="text" placeholder="Last Name" v-model="steps.administrator.user.lastName">
                        </div>
                    </div>
                    <div class="field" :class="{'disabled': steps.administrator.created}">
                        <label>Email Address</label>
                        <input type="email" placeholder="Email Address" v-model="steps.administrator.user.email">
                    </div>
                    <div class="field" :class="{'disabled': steps.administrator.created}">
                        <label>Password</label>
                        <input type="password" placeholder="Password" v-model="steps.administrator.user.password">
                    </div>
                    <div class="field" :class="{'disabled': steps.administrator.created}">
                        <label>Password Confirmation</label>
                        <input type="password" placeholder="Password Confirmation" v-model="steps.administrator.user.password_confirmation">
                    </div>
                </form>
            </article>
        </section>
        <section class="ui attached center aligned very padded segment" v-show="steps.ready.class == 'completed'">
            <article>
                <h2 class="ui icon header">
                    <i class="green check icon"></i>
                    <div class="content">
                        Setup Complete
                        <div class="sub header">You're now setup and ready to securely store your passwords</div>
                    </div>
                </h2>

                <div class="ui message">
                    <div class="header">
                        Re-running Setup
                    </div>
                    <p>If you ever need to run this setup process again, remove the setup.lock file found in {{ storage_path('app') }} directory.</p>
                </div>
            </article>
        </section>
        <a href="/" class="ui bottom attached primary button" v-show="steps.ready.class == 'completed'">
            Login
        </a>
    </div>

    <script src="/lib/jquery/dist/jquery.min.js"></script>
    <script src="/lib/toastr/toastr.min.js"></script>
    <script src="/lib/vue/dist/vue.min.js"></script>
    <script src="/js/install.min.js"></script>
</main>

</body>
</html>