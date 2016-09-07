let installPage = new Vue({
    el: '#installPage',

    data: {
        steps: {
            environment: {
                passedChecks: 0,
                class: "active",
                description: "Check that required dependencies are met",
                checks: [
                    {
                        id: 'php_version',
                        state: 'pending',
                        description: 'PHP version >= 5.5.9'
                    },
                    {
                        id: 'openssl_module',
                        state: 'pending',
                        description: 'PHP OpenSSL module available'
                    },
                    {
                        id: 'mysql_module',
                        state: 'pending',
                        description: 'PHP MySQL module available'
                    },
                    {
                        id: 'pdo_module',
                        state: 'pending',
                        description: 'PHP PDO module available'
                    },
                    {
                        id: 'mbstring_module',
                        state: 'pending',
                        description: 'PHP MBString module available'
                    },
                    {
                        id: 'tokenizer_module',
                        state: 'pending',
                        description: 'PHP Tokenizer module available'
                    },
                    {
                        id: 'storage_writable',
                        state: 'pending',
                        description: 'storage folder writable'
                    }
                ]
            },
            database: {
                class: "",
                passedChecks: 0,
                description: "Setup and check database connection",
                checks: [
                    {
                        id: 'check',
                        method: 'get',
                        state: 'pending',
                        description: 'Check database connection'
                    },
                    {
                        id: 'install',
                        method: 'post',
                        state: 'pending',
                        description: 'Install migrations table'
                    },
                    {
                        id: 'migrate',
                        method: 'post',
                        state: 'pending',
                        description: 'Run migrations'
                    },
                    {
                        id: 'seed',
                        method: 'post',
                        state: 'pending',
                        description: 'Seed initial data'
                    }
                ]
            },
            administrator: {
                created: false,
                class: "",
                description: "Create the administrator user",
                user: {
                    firstName: '',
                    lastName: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                }
            },
            ready: {
                class: "",
                description: "Setup complete"
            }
        }
    },

    methods: {
        runChecks() {
            this.steps.environment.checks.forEach(check => check.state = 'pending');
            this.steps.environment.passedChecks = 0;

            this.runCheck(0);
        },

        runCheck(pos) {
            let self = this;
            let check = self.steps.environment.checks[pos];

            $q.get(`/api/install/check/${check.id}`)
                .success(({data}) => {
                    self.steps.environment.checks[pos].state = data ? 'ok' : 'failed';

                    if (++pos < self.steps.environment.checks.length) {
                        self.runCheck(pos);
                        self.steps.environment.passedChecks++;
                    } else if (self.steps.environment.passedChecks == self.steps.environment.checks.length - 1)
                        toastr.success('All checks passed! Please click the next button to continue');
                });
        },

        completeEnvironmentStep() {
            if (this.steps.environment.passedChecks != this.steps.environment.checks.length - 1) {
                toastr.warning('Please click the Start button to run checks before continuing');
                return;
            }

            this.steps.environment.class = 'completed';
            this.steps.database.class = 'active';
        },


        runDbChecks() {
            this.steps.database.checks.forEach(check => check.state = 'pending');
            this.steps.database.passedChecks = 0;

            this.runDbCheck(0);
        },

        runDbCheck(pos) {
            let self = this;
            let check = self.steps.database.checks[pos];

            $q[check.method](`/api/install/database/${check.id}`)
                .success(({data}) => {
                    check.state = data ? 'ok' : 'failed';

                    if (check.state == 'failed') {
                        toastr.error('Something went wrong! Please check the applications log file for more information', 'Database Error');
                    } else if (++pos < self.steps.database.checks.length) {
                        self.runDbCheck(pos);
                        self.steps.database.passedChecks++;
                    } else if (self.steps.database.passedChecks == self.steps.database.checks.length - 1)
                        toastr.success('All steps complete! Please click the next button to continue');
                });
        },

        completeDatabaseStep() {
            if (this.steps.database.passedChecks != this.steps.database.checks.length - 1) {
                toastr.warning('Please click the Check Connection button to run the connection test before continuing');
                return;
            }
            this.steps.database.class = 'completed';
            this.steps.administrator.class = 'active';
        },

        createAdministrator() {
            let self = this;
            $q.put('/api/install/administrator', this.steps.administrator.user)
                .success(() => {
                    toastr.success('Administrator user successfully');
                    self.steps.administrator.created = true;
                });
        },

        completeAdministratorStep() {
            this.steps.administrator.class = "completed";
            this.steps.ready.class = "completed";
        }
    }

});