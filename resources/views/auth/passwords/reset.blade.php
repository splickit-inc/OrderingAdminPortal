<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Splickit Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="Admin Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="/img/favicon.ico">

    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/css/app.css" rel="stylesheet" type="text/css">

</head>

<body ng-app="app">

<!-- Begin page -->
<div class="accountbg"></div>
<div class="wrapper-page">
    <div class="panel panel-color panel-primary panel-pages">
        <div class="panel-body" ng-controller="ResetPasswordController">

            <h3 class="text-center m-t-0 m-b-30">
                <span class=""><img src="/img/Order140_Final_Logoflat.png" alt="splickit logo" height="40"></span>
            </h3>

            <form class="form-horizontal m-t-20" name="reset_password_form" novalidate ng-submit="submitNewPassword()">

                <div class="form-group">
                    <div class="col-xs-12">
                        <input type="email" class="form-control" placeholder="Email" required autofocus
                               ng-required="true" ng-model="reset_password.new_password_email" ng-fade>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input type="password" id="inputEmail" class="form-control" placeholder="New Password" required autofocus
                               ng-required="true" ng-model="reset_password.new_password" ng-fade>
                    </div>
                </div>

                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Submit</button>
                    </div>
                </div>

                <div ng-show="password_successfully_reset" class="col-md-12 m-t-15">
                    <div class="alert alert-success alert-reg-position form-submit-success fade-effect"
                         role="alert">
                        <i class="fa fa-info-circle" aria-hidden="true"></i> Your password has successfully been reset.  <a href="/home">Click here to enter application</a>.
                    </div>
                </div>

                <div ng-show="request_processing" class="form-loader">
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
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/modernizr.min.js"></script>
<script src="/assets/js/detect.js"></script>
<script src="/assets/js/fastclick.js"></script>
<script src="/assets/js/jquery.slimscroll.js"></script>
<script src="/assets/js/jquery.blockUI.js"></script>
<script src="/assets/js/waves.js"></script>
<script src="/assets/js/wow.min.js"></script>
<script src="/assets/js/jquery.nicescroll.js"></script>
<script src="/assets/js/jquery.scrollTo.min.js"></script>

<script src="/assets/js/app.js"></script>

<!-- Angular Scripts  -->
<script src="/js/angular.min.1.5.6.js"></script>
<script src="/js/app/login_app.js"></script>
<script src="/js/angular_directives.js"></script>
<script src="/js/factory/user.js"></script>
<script src="/js/service/cookie.js"></script>
<script src="/js/service/utility.js"></script>
<script src="/js/controllers/reset_password.js"></script>


</body>
</html>