<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Splickit Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="Admin Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="img/favicon.ico">

    <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="css/app.css" rel="stylesheet" type="text/css">

</head>

<body ng-app="app">

<!-- Begin page -->
<div class="accountbg"></div>
<div class="wrapper-page">
    <div class="panel panel-color panel-primary panel-pages">
        <div class="panel-body" ng-controller="ResetPasswordController">

            <h3 class="text-center m-t-0 m-b-30">
                <span class=""><img src="img/Order140_Final_Logoflat.png" alt="splickit logo" height="40"></span>
            </h3>

            <form class="form-horizontal m-t-20" name="login_form" novalidate ng-submit="sendEmail()">

                <div class="form-group">
                    <div class="col-xs-12">
                        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus
                               ng-required="true" ng-model="reset_password.email" ng-fade>
                    </div>
                </div>


                <div class="form-group text-center m-t-20">


                    <div class="col-xs-12">
                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Submit</button>
                    </div>
                </div>

                <div class="form-group m-t-30 m-b-0">
                    <div class="col-sm-7">
                        <a href="#" class="text-muted"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a>
                    </div>
                </div>

                <div ng-show="login_processing" class="form-loader">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </form>

        </div>
    </div>
</div>



<!-- jQuery  -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/modernizr.min.js"></script>
<script src="assets/js/detect.js"></script>
<script src="assets/js/fastclick.js"></script>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/jquery.blockUI.js"></script>
<script src="assets/js/waves.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>

<script src="assets/js/app.js"></script>

<!-- Angular Scripts  -->
<script src="/js/angular.min.1.5.6.js"></script>
<script src="/js/app/login_app.js"></script>
<script src="/js/angular_directives.js"></script>
<script src="/js/factory/user.js"></script>
<script src="/js/service/cookie.js"></script>
<script src="/js/controllers/reset_password.js"></script>

</body>
</html>