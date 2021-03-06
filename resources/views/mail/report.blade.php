<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,500" rel="stylesheet">
    <!--[if gte mso 9]>
    <style type="text/css">
        h1, h2, h3, h4, h5, h6, p, a, span, td, strong {
            font-weight: 300;
            font-family: 'Segoe UI', Helvetica, Verdana, sans-serif !important;
            font-size: 13px;
        }
    </style>
    <![endif]-->
    <!-- / Outlook -->
</head>
<body>
<table width="100%" cellspacing="0" cellpadding="0" border="0"
       style="margin:0 auto; width: 100%;background: #ffffff;max-width: 600px;
       font-family: 'Roboto', 'Open Sans', sans-serif;font-size: 14px;font-weight: 300;">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #eee;">
                <tr>
                    <td width="600">
                        <div style="float: left; padding-left: 25px; padding-top: 25px">
                            <img src="https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/logo.png"
                                 alt="header" style="width: 40%; max-width: 100%;">
                        </div>
                        <div style="clear: both;margin-bottom: 20px;line-height: 20px;padding: 20px;">
                            <!--[if gte mso 9]>
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr style="height: 20px"></tr>
                                <tr>
                                    <td style="width: 20px"></td>
                                    <td>
                            <![endif]-->
                            <div style="text-align: center;">
                                <h3>{!! ucfirst($frequency) !!} Report</h3>
                            </div>
                            <p>Report name: {!! $report_name !!}</p>
                            <p>{!! $content !!}</p>
                            @if(!empty($errors))
                                <p>{!! $errors !!}</p>
                            @endif
                            <p>To make changes, please log in to portal.order140.com.</p>
                            <p style="line-height: 17px;">Your Order140 Team,<br/>
                                <a style="color: #000; text-decoration: none;">support@yourcompany.com</a><br/>
                                1-800-775-4254
                            </p>

                            <!--[if gte mso 9]>
                            </td>
                            <td style="width: 20px"></td>
                            </tr>
                            <tr style="height: 20px"></tr>
                            </table>
                            <![endif]-->
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>