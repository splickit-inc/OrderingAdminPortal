<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <style>
        body {
            width: 100%;
        }

        table {
            margin: 0 auto;
            width: 602px;
        }

        @media (max-width: 610px) {
            table {
                width: 100%;
            }

            .banner {
                width: 98%
            }
        }

        tr, td {
            border: 1px solid #efefef;
            margin: 0;
            padding: 0;
        }

        .body, .footer {
            padding: 20px;
        }

        .body-content {
            clear: both;
            margin-top: 150px;
            margin-bottom: 20px;
            line-height: 25px;
        }

        .footer-content {
            font-size: 11px;
        }

        .name {
            float: left;
        }

        .logo {
            float: right;
        }

        img {
            margin: 0 0 -4px;
            padding: 0;
        }
    </style>
</head>
<body>
<table cellspacing="0" cellpadding="0" height="800" class="table" border="0">
    <tr>
        <td class="body" height="50">
            <h2>Order140 Notification</h2>
        </td>
    </tr>
    <tr>
        <td class="body">
            <div class="name">
                <img src="https://d38o1hjtj2mzwt.cloudfront.net/admin_portal/merchant_form_1/img/logo.png"
                     alt="logo" width="332" height="78">
                <br/><br/>
            </div>
            <div class="body-content">
                Dear Order140 Team,<br/>
                A new client has finished their sign up process, and is ready for setup
                immediately.<br/>
                <h3>Customer Info</h3>
                <ul>
                    <li>Brand: <b>{!! $brand['brand_name'] !!}</b></li>
                    <li>Merchant ID: <b>{!! $merchant['merchant_id'] !!}</b></li>
                    <li>Contact Name: <b>{!! $lead['contact_name'] !!}</b></li>
                    <li>Skin ID: <b>{!! $skin['external_identifier'] !!}</b></li>
                    <li>Contact Phone: <b>{!! $lead['contact_phone_no'] !!}</b></li>
                    <li>Contact Email: <b>{!! $lead['contact_email'] !!}</b></li>
                </ul>
            </div>
            <div>Sincerely,<br/>
                Order140 Bot
            </div>
        </td>
    </tr>
    <tr>
        <td class="footer">
            <div class="footer-content">::::: Merchant Form #2 Email Order140 :::::</div>
        </td>
    </tr>
</table>
</body>

</html>