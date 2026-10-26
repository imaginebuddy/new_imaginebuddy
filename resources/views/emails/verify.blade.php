<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ trans('users.title_email_verify') }} - {{ $settings->title }}</title>

    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;" align="center">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <!-- Logo -->
                    <tr>
                        <td style="padding: 25px 0; text-align: center;">
                            <a style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; font-weight: bold; color: #2F3133; text-decoration: none;" href="{{url('/')}}" target="_blank">
                                {{$settings->title}}
                            </a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td style="width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;" width="100%">
                            <table style="width: auto; max-width: 570px; margin: 0 auto; padding: 0;" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; padding: 35px;">
                                        <!-- Greeting -->
                                        <h1 style="margin-top: 0; color: #111827; font-size: 20px; font-weight: bold; text-align: left;">
                                            {{ trans('emails.verify_email_msg') }} {{$settings->title}}
                                        </h1>

                                        <!-- Intro -->
                                        <p style="margin-top: 15px; color: #4b5563; font-size: 15px; line-height: 1.6em;">
                                            {{ trans('emails.verify_email_msg_2') }}
                                        </p>

                                        <!-- Button -->
                                        <div style="text-align: center; margin: 30px 0;">
                                            <a href="{{url('verify/account', $confirmation_code)}}" class="button" style="background-color: #0f172a; color: #ffffff !important; display: inline-block; padding: 14px 32px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 15px;">
                                                {{ trans('users.title_email_verify') }}
                                            </a>
                                        </div>

                                        <p style="font-size: 13px; color: #9ca3af; line-height: 1.5em; word-break: break-all;">
                                            If the button above does not work, copy and paste this link into your browser:<br>
                                            <a href="{{url('verify/account', $confirmation_code)}}" style="color: #4f46e5;">{{url('verify/account', $confirmation_code)}}</a>
                                        </p>

                                        <!-- Salutation -->
                                        <p style="margin-top: 25px; color: #74787E; font-size: 15px; line-height: 1.5em;">
                                            {{trans('emails.regards')}}<br><strong>{{$settings->title}} Team</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td>
                            <table style="width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #AEAEAE; padding: 35px; text-align: center;">
                                        <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                            &copy; {{ date('Y') }} {{$settings->title}}. {{trans('emails.rights_reserved')}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
