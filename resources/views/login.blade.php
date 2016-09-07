<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>lockd - Secure password storage</title>
        <link rel="stylesheet" href="/lib/semantic/dist/semantic.min.css">
        <link rel="stylesheet" href="/css/login.min.css">
    </head>
    <body>
        <div class="ui one column stackable relaxed center aligned grid container">
            <div class="column twelve wide">
                <div class="ui very padded segment">
                    <img class="ui centered small image" src="/images/logo.png" alt="lockd Logo">
                    <h2 class="ui header">
                        lockd
                    </h2>
                    <hr>
                    <div class="ui center aligned grid">
                        <div class="twelve wide column">
                            <form class="ui form" action="/login" method="POST">
                                <div class="field">
                                    <label>Email</label>
                                    <input type="email" name="email" placeholder="Email">
                                </div>
                                <div class="field">
                                    <label>Password</label>
                                    <input type="password" name="password" placeholder="Password">
                                </div>
                                <button type="submit" class="ui primary button">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
