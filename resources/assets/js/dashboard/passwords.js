let Passwords = Vue.extend({
    template: `<section class="password screen">
                    <article class="ui message">
                        <div class="ui big breadcrumb">
                            <a class="section" v-on:click="openBreadcrumbFolder('root')">Root</a>
                            <span v-for="folder in breadcrumb">
                                <i class="right chevron icon divider"></i>
                                <a class="section" v-on:click="openBreadcrumbFolder(folder)">{{ folder.name }}</a>
                            </span>
                        </div>
                    </article>
                    
                    <article class="ui grid">
                        <div class="three wide computer four wide tablet column" v-for="folder in folders">
                            <div class="ui fluid link card" v-on:click="openFolder(folder)">
                                <div class="image">
                                    <img src="/images/folder.png" alt="Folder">
                                </div>
                                <div class="center aligned content">
                                    <div class="header">{{ folder.name }}</div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <div v-if="passwords.length > 0">
                        <hr>
                        <h2>Passwords</h2>
                        <article class="ui grid">
                            <div class="four wide computer sixteen wide tablet column" v-for="password in passwords">
                                <div class="ui fluid card">
                                    <div class="content">
                                        <div class="header">{{ password.name }}</div>
                                        <hr>
                                        <table class="ui table">
                                            <tbody>
                                            <tr>
                                                <td><strong>URL</strong></td>
                                                <td><a href="{{ password.url || '' }}">{{ password.url || 'none' }}</a></td>
                                            </tr>
                                            <tr>
                                                <td><strong>User</strong></td>
                                                <td>{{ password.user || 'none' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Password</strong></td>
                                                <td><span id="password_{{ password.id }}">&ast;&ast;&ast;&ast;&ast;&ast;</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="ui two bottom attached buttons">
                                        <button class="ui blue labeled icon button" v-on:click="showPassword(password.id)">
                                            <i class="search icon"></i>
                                            Show Password
                                        </button>
                                        <button class="ui primary labeled icon button" v-on:click="copyPassword(password.id)">
                                            <i class="copy icon"></i>
                                            Copy Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div id="password_copy_container">
                        <button id="copy_trigger" data-clipboard-text="{{ hiddenPassword }}"></button>
                    </div>
    `,

    data() {
        return {
            hiddenPassword: '',
            breadcrumb: [],
            folders: [],
            passwords: []
        }
    },

    ready() {
        this.loadContents(1);
        setTimeout(() => new Clipboard('#copy_trigger'), 500);
    },

    methods: {

        openFolder(folder) {
            this.loadContents(folder.id);
            this.breadcrumb.push(folder);
        },

        openBreadcrumbFolder(folder) {
            if (folder === 'root') {
                this.breadcrumb = [];
                this.loadContents(1);
            } else {
                if (this.breadcrumb[this.breadcrumb.length - 1] != folder) {
                    let position = this.breadcrumb.indexOf(folder);
                    this.breadcrumb.splice(position + 1);
                }
                this.loadContents(folder.id);
            }
        },

        loadContents(folderId) {
            this.loadFolders(folderId);
            this.loadPasswords(folderId);
        },

        loadFolders(folderId) {
            this.folders = [];
            $q.get(`/api/folder/${folderId}/folders`)
                .success(({data}) => {
                    this.folders = data;
                });
        },

        loadPasswords(folderId) {
            this.passwords = [];
            $q.get(`/api/folder/${folderId}/passwords`)
                .success(({data}) => {
                    this.passwords = data;
                });
        },

        showPassword(id) {
            let el = $(`#password_${id}`);
            let password = '';
            this.passwords.forEach((item) => {
                if (item.id == id)
                    password = item.password;
            });

            el.html(atob(password));

            setTimeout(() => {el.html('&ast;&ast;&ast;&ast;&ast;&ast;')}, 3000);
        },

        copyPassword(id) {
            let password = '';
            this.passwords.forEach((item) => {
                if (item.id == id)
                    password = item.password;
            });

            this.hiddenPassword = atob(password);
            setTimeout(() => {$('#copy_trigger').click()}, 50);
            toastr.info('Copied password to clipboard');
            setTimeout(() => {this.hiddenPassword = ''}, 100);

        }
    }
});