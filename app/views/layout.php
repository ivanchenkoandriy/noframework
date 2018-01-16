<?php
/**
 * Site layout template
 *
 * @var $this \app\View View
 * @var string $title Page title
 * @var string $description Page description
 * @var array $breadcrumbs Links of the bread crumbs
 * @var String $content Page content
 */
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?= APP_CHARSET; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="<?= $description; ?>">
        <?php //<link rel="icon" href="../../favicon.ico"> ?>

        <title><?= $title; ?></title>

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/styles.css" rel="stylesheet">
    </head>

    <body>

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="/">BeeGee test</a>
                </div>

                <div id="navbar" class="navbar-collapse collapse">
                    <?php if (!\app\Auth::autorized()) { ?>
                        <form class="navbar-form navbar-right" id="form-auth" action="/auth/login" method="post">
                            <div class="form-group">
                                <input type="text" placeholder="Login" name="user" id="auth-login" class="form-control">
                            </div>

                            <div class="form-group">
                                <input type="password" placeholder="Password" name="password" id="auth-password" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-success">Sign in</button>
                        </form>
                    <?php } else { ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="/auth/logout">Logout</a></li>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </nav>

        <?= ($breadcrumbs ? $this->render('blocks/breadcrumbs.php', ['links' => $breadcrumbs]) : ''); ?>

        <?= $content; ?>

        <script src="/js/jquery.min.js"></script>
        <script src="/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <script src="/js/app.js"></script>
    </body>
</html>