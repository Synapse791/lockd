let Passwords = Vue.extend({
    template: `<section class="password screen">
                    <article class="ui message">
                        <div class="ui big breadcrumb">
                            <a class="section" @click="openBreadcrumbFolder('root')">Root</a>
                            <span v-for="folder in breadcrumb">
                                <i class="right chevron icon divider"></i>
                                <a class="section" @click="openBreadcrumbFolder(folder)">{{ folder.name }}</a>
                            </span>
                        </div>
                    </article>
                    <article class="ui grid lockd controls">
                        <div class="ten wide column">
                            <div class="ui input">
                                <input type="text" placeholder="Filter" v-model="filter">
                            </div>
                        </div>
                        <div class="six wide column">
                            <div class="ui two basic icon buttons">
                                <button class="ui button" :class="{'active': layout == 'grid'}" @click="setLayout('grid')">
                                    <i class="block layout icon"></i>
                                    Grid
                                </button>
                                <button class="ui button" :class="{'active': layout == 'list'}" @click="setLayout('list')">
                                    <i class="list layout icon"></i>
                                    List
                                </button>
                            </div>
                        </div>
                    </article>

                    <article v-if="layout == 'grid'">

                        <div class="ui grid">
                            <div class="three wide computer four wide tablet column" v-for="folder in folders | filterBy filter in 'name'">
                                <div class="ui fluid link card" @click="openFolder(folder)">
                                    <div class="image">
                                        <img src="/images/folder.png" alt="Folder">
                                    </div>
                                    <div class="center aligned content">
                                        <div class="header">{{ folder.name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ui grid" v-if="passwords.length > 0">
                            <div class="four wide computer sixteen wide tablet column" v-for="password in passwords | filterBy filter in 'name'">
                                <div class="ui fluid card">
                                    <div class="content">
                                        <div class="header">{{ password.name }}</div>
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
                                        <button class="ui blue labeled icon button" @click="showPassword(password.id)">
                                            <i class="search icon"></i>
                                            Show Password
                                        </button>
                                        <button class="ui primary labeled icon button" @click="copyPassword(password.id)">
                                            <i class="copy icon"></i>
                                            Copy Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </article>

                    <article v-if="layout == 'list'" class="ui segment">
                        <div class="ui relaxed divided list">
                            <div class="item" v-for="folder in folders | filterBy filter in 'name'" @click="openFolder(folder)">
                                <i class="large folder middle aligned icon"></i>
                                <div class="content">
                                    <a class="header">{{ folder.name }}</a>
                                    <div class="description">
                                        Contains {{ typeof folder.folder_count === 'undefined' ? '???' : folder.folder_count }} folders and {{ typeof folder.password_count === 'undefined' ? '???' : folder.password_count }} passwords
                                    </div>
                                </div>
                            </div>
                            <div class="item" v-for="password in passwords | filterBy filter in 'name'" @click="showPasswordDetails(password)">
                                <i class="large key middle aligned icon"></i>
                                <div class="content">
                                    <a class="header">{{ password.name }}</a>
                                    <div class="description">User: {{ password.user || 'no user' }}</div>

                                    <div class="details" v-if="password.id == activePassword.id" transition="fade">
                                        <div class="ui attached segment">
                                            <table class="ui table">
                                                <tbody>
                                                <tr>
                                                    <td>User</td>
                                                    <td>{{ password.user }}</td>
                                                </tr>
                                                <tr>
                                                    <td>URL</td>
                                                    <td><a href="{{ password.url }}">{{ password.url }}</a></td>
                                                </tr>
                                                <tr>
                                                    <td>Password</td>
                                                    <td><span id="password_{{ password.id }}">&ast;&ast;&ast;&ast;&ast;&ast;</span></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ui two bottom attached buttons">
                                            <button class="ui blue labeled icon button" @click="showPassword(password.id)">
                                                <i class="search icon"></i>
                                                Show Password
                                            </button>
                                            <button class="ui primary labeled icon button" @click="copyPassword(password.id)">
                                                <i class="copy icon"></i>
                                                Copy Password
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </article>


                    <div id="password_copy_container">
                        <button id="copy_trigger" data-clipboard-text="{{ hiddenPassword }}"></button>
                    </div>
    `,

    data() {
        return {
            activePassword: {},
            layout: 'grid',
            filter: '',
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

        setLayout(layout) {
            this.layout = layout;
        },

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

        showPasswordDetails(password) {
            this.activePassword = password;
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