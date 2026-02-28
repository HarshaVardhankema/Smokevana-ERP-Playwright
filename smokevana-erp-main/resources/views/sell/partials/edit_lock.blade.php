<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice Locked</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- Optional: Some basic reset -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div
        style="max-width: 600px; margin: 40px auto; background: #fff3cd; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="POST"
            action="{{ action([\App\Http\Controllers\OrderfulfillmentController::class, 'releaseModal'], [$modelType, $modelId]) }}"
            id="edit_session_lock">
            @csrf

            <h2 style="font-size: 24px; font-weight: bold; color: #856404; margin-bottom: 20px;">Invoice Locked</h2>

            <div style="text-align: center;">
                <p style="font-size: 64px; color: #ffcc00;">⚠️</p>
                <h4 style="font-size: 18px; font-weight: 600; margin-top: 10px;">
                    {{$user->first_name}} {{$user->last_name}}
                    <span title="{{$user->contact_number}}" style="cursor: pointer; margin-left: 5px;">
                        <i
                            style="font-style: normal; border: 1px solid black; border-radius: 50%; padding: 0 5px;">ℹ</i>
                    </span>
                    is already editing this
                   Transaction. Do you want to take over?
                </h4>

                <div id="password_section" style="margin-top: 20px; display: none;">
                    <input type="hidden" name="order_id" value="{{ $modelId }}" />
                    <input type="password"
                        style="margin-top: 10px; font-size: 18px; padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"
                        name="password" id="password" placeholder="Enter your password" required>
                </div>

                <div style="margin-top: 30px;">
                    <button type="button" id="confirm_takeover"
                        style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Yes
                    </button>

                    <button type="submit" id="submit_form"
                        style="padding: 10px 20px; font-size: 16px; background-color: #28a745; color: white; border: none; border-radius: 4px; display: none; cursor: pointer;">
                        Take Over
                    </button>

                    <a href="{{ url('/sells') }}"
                        style="padding: 10px 20px; font-size: 16px; background-color: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 10px; text-decoration: none;">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- <div style="display: none;">{{ $is_invoice }}</div> --}}
        </form>
    </div>
    @include('layouts.partials.javascripts')

    <script>
        $(document).ready(function () {
            $('#confirm_takeover').on('click', function () {
                $('#password_section').show();
                $('#confirm_takeover').hide();
                $('#submit_form').show();
            });

            $('#edit_session_lock').on('submit', function (e) {
                e.preventDefault();

                $('#submit_form').prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr.success('You can now edit the invoice.', '✅ Success', {
                            timeOut: 3000
                        });
                        window.location.reload();
                    },
                    error: function (xhr) {
                        toastr.error('You are not authorized.', '❌ Error', {
                            timeOut: 3000
                        });
                        $('#submit_form').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>