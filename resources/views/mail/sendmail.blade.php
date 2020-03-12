<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
    <style>
        body{
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
        }
        img {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div style="margin: 0 auto; width: 100%;background: #ffffff;border: 1px solid #eee; max-width: 600px;">
    <div style="float: left">
        <img src="https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/email/header.png"
             alt="header" style="width: 100%; max-width: 100%;">
    </div>
    <div style="clear: both;margin-bottom: 20px;line-height: 20px;padding: 20px;">
        <p>Dear {!! $lead['contact_name'] !!},</p>
        <p>Ready to get online in days, not months?</p>
        <p>Our full-featured, easy to manage ordering platform helps grow your business while
            maintaining the direct customer relationships you value.</p>
        <p>Just a few easy steps to go live and begin accepting online orders, with all the great features
            your customers have come to expect.</p>
        <br />
        <p style="line-height: 17px;">Your Order140 Team,<br />
            <a style="color: #000; text-decoration: none;">support@yourcompany.com</a><br />
            1-800-775-4254
        </p>
    </div>
    <div>
        <a href="{}" style="margin-left: 33%;">
            <img src="https://s3.amazonaws.com/com.yourbiz.products/admin_portal/merchant_form_1/img/email/button-1.png"
                 alt="button" style="width: 100%; max-width: 181px;">
        </a>
        <img src="https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/email/footer.png"
             alt="header" style="width: 100%; max-width: 100%;">
    </div>
</div>
</body>

</html>