<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Password Reset Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #F0F0F0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Wrapper for the entire email container */
        .email-wrapper {
            width: 600px;
            /* Fixed width for the email content box */
            height: auto;
            background-color: #fff;
            text-align: center;
            margin: 0;
        }

        /* Header strip image */
        .header img {
            width: 100%;
            height: auto;
            display: block;
            /* Remove default space below the image */
        }

        /* Main email content container */
        .email-container {
            width: 100%;
            height: auto;
            background-image: url('{{ asset('img/gsw_emails-07_body.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 30px 0;
        }

        .email-banner {
            display: flex;
            color: white;
            font-size: 24px;
            font-weight: bold;
            flex-direction: column;
            justify-content: center;
            padding: 0 15px;
        }

        .email-content {
            padding: 20px;
            color: #FCFCFC;
            margin-top: 50px;
        }

        .email-content p {
            font-size: 14px;
            line-height: 1.6;
        }

        .matter {
            padding: 0 20px;
        }

        .button-section {
            margin-top: 20px;
        }

        .button-section a {
            color: white;
            text-decoration: none;
        }

        .explore-btn {
            display: inline-block;
            background-color: #FF9929;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .explore-btn:hover {
            background-color: #E07E15;
        }

        .footer {
            font-size: 12px;
            color: #FFFFFF;
            margin-top: 20px;
            padding-bottom: 10px;
        }

        @media (max-width: 600px) {

            /* Make sure content doesn't exceed the mobile screen */
            .email-wrapper {
                width: 90vw;
                /* Reduce width to fit better on mobile devices */
                padding: 0 15px;
            }

            .email-content p {
                font-size: 12px;
            }

            .explore-btn {
                font-size: 14px;
                padding: 10px 20px;
            }

            .footer {
                font-size: 10px;
            }
        }

    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <img src="{{asset('img/gsw_emails-09_header.jpg')}}" alt="Header Image" style="width: 100%; height: auto;" />


            <div class="email-container">
                <div class="email-banner">
                    <div class="email-content">
                        <p class="name-class">Hello {{$first_name}},</p>
                        <p class="matter">
                            Thanks for joining {{ config('app.name') }}! We are currently reveiwing your account and will notify you once it's approved.
                        </p>
                        <p class="matter">
                            If you have any questions or need assistance, don't hesitate to reach out.We are here to help!
                        </p>


                        <div class="footer">
                            &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
