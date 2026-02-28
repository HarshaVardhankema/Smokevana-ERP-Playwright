
@php
    $business_email=config('business_email')
@endphp
<body
    style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh;">

    {{-- {{$payable}} --}}
    <div style="max-width: 1200px; width: 100%;">
        <div style="display: flex; flex-wrap:wrap-reverse; gap: 24px;">

            <!-- Left: Payment Form -->
            <div
                style="flex: 1 1 55%;  padding: 30px; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); border-radius: 10px; ">
                <p style="color: #6b7280; font-weight: 600; font-size: 14px;">PAYMENT AMOUNT</p>
                <h2 style="font-size: 32px; font-weight: bold; color: #1f2937; margin: 4px 0;">${{ $payable }}</h2>
                {{-- <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Includes a $25.00 convenience fee</p> --}}

                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Pay with Card</h3>

                <form action="/payment-sell-request" method="POST"
                    style="margin: 50px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                    <!-- Normally not a visible input to the user -->
                    @csrf
                    <div class="input">
                        <input type="text" name="amount" value={{ $payable }} hidden />
                        <input type="text" name="token" value={{ $token }} hidden />
                        <input type="text" name="customer_id" value={{ $customerId }} hidden />

                    </div>

                    <!-- Credit card fields -->
                    <div class="input" style="margin-bottom: 20px;">
                        <span style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Card
                            Number</span>
                        <div id="demoCcnumber" style="border: 1px solid #ccc; padding: 10px; border-radius: 6px;"></div>
                    </div>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div class="input" style="margin-bottom: 20px; flex: 1 1 200px; min-width: 200px;">
                            <span style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">
                                Expiration Date
                            </span>
                            <div id="demoCcexp" style="border: 1px solid #ccc; padding: 10px; border-radius: 6px;">
                            </div>
                        </div>

                        <div class="input" style="margin-bottom: 20px; flex: 1 1 200px; min-width: 200px;">
                            <span style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">
                                CVV
                            </span>
                            <div id="demoCvv" style="border: 1px solid #ccc; padding: 10px; border-radius: 6px;">
                            </div>
                        </div>
                    </div>



                    <!-- Submit button -->
                    <button id="demoPayButton" type="button"
                        style="width: 100%; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer;">
                        Pay ${{ $payable }}
                    </button>
                </form>
            </div>

            <!-- Right: Invoice Details -->
            <div style="flex: 1 1 35%; display: flex; flex-direction: column; gap: 16px;">
                <div
                    style="background-color: #ffffff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); border-radius: 10px; padding: 16px;">
                    <h4 style="margin: 0 0 12px 0; font-weight: 600;">{{ config('app.name', 'ultimatePOS') }}</h4>
                    {{-- <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Invoice</span><span>1764</span>
                    </div> --}}
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Due date</span><span>April 7, 2025</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Invoice amount</span><span>{{ $payable }}</span>
                    </div>
                    {{-- <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Online fee</span><span>$25.00</span>
                    </div> --}}
                    <hr style="margin: 12px 0;">
                    {{-- <div style="display: flex; justify-content: space-between; font-weight: bold;">
                        <span>Total</span><span>$94,050.00</span>
                    </div> --}}
                    {{-- <button
                        style="margin-top: 12px; border: 1px solid #d1d5db; padding: 6px 14px; font-size: 13px; border-radius: 6px; background-color: white; cursor: pointer;">
                        View Invoice
                    </button> --}}
                </div>

                <div
                    style="background-color: #ffffff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); border-radius: 10px; padding: 16px;">
                    <h5 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600;">Merchant details</h5>
                    <p style="font-size: 14px; margin: 0;">Email:
                        <a href={{$business_email}}
                            style="color: #2563eb; text-decoration: none;">{{ config('app.name', 'ultimatePOS') }}</a>
                    </p>
                </div>

                <div
                    style="background-color: #ffffff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); border-radius: 10px; padding: 16px;">
                    <h5 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600;">Payment instructions</h5>
                    <p style="font-size: 14px; margin: 0;">We appreciate your business!
                        {{ config('app.name', 'ultimatePOS') }}</p>
                    {{-- <a href="#" style="font-size: 13px; color: #2563eb; text-decoration: none;">See more</a> --}}
                </div>

                {{-- <div style="display: flex; align-items: center; font-size: 12px; color: #6b7280;">
                    <img src="https://static.intuit.com/identity/ux/trust/trust-certified.png"
                        style="width: 24px; margin-right: 8px;">
                    Information is protected and kept confidential.
                </div> --}}
            </div>

        </div>
    </div>
</body>
<div class="loader-overlay" style="display:none;">
    <div class="loader-spinner">
        <div class="loader"></div>
    </div>
</div>
<style>
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* HTML: <div class="loader"></div> */
    .loader {
        font-weight: bold;
        font-family: monospace;
        display: inline-grid;
        font-size: 30px;
    }

    .loader:before,
    .loader:after {
        content: "Payment Processing...";
        grid-area: 1/1;
        -webkit-mask-size: 1.5ch 100%, 100% 100%;
        -webkit-mask-repeat: no-repeat;
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        animation: l36-1 2s infinite;
    }

    .loader:before {
        -webkit-mask-image:
            linear-gradient(#000 0 0),
            linear-gradient(#000 0 0);
    }

    .loader:after {
        -webkit-mask-image: linear-gradient(#000 0 0);
        animation:
            l36-1 2s infinite,
            l36-2 .5s infinite cubic-bezier(0.5, 200, 0.5, -200);
    }

    @keyframes l36-1 {
        0% {
            -webkit-mask-position: 0 0, 0 0
        }

        20% {
            -webkit-mask-position: .5ch 0, 0 0
        }

        40% {
            -webkit-mask-position: 100% 0, 0 0
        }

        60% {
            -webkit-mask-position: 4.5ch 0, 0 0
        }

        80% {
            -webkit-mask-position: 6.5ch 0, 0 0
        }

        100% {
            -webkit-mask-position: 2.5ch 0, 0 0
        }
    }

    @keyframes l36-2 {
        100% {
            transform: translateY(0.2px)
        }
    }
</style>

<script src="https://secure.nmi.com/token/Collect.js" data-tokenization-key="M33w5J-7FXtJJ-EekM7R-rCmV4D"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Loader control functions
        function showLoader() {
            $(".loader-overlay").show();
        }

        function hideLoader() {
            $(".loader-overlay").hide();
        }

        CollectJS.configure({
            paymentSelector: "#demoPayButton",
            variant: "inline",
            googleFont: "Montserrat:400",

            customCss: {
                color: "#333333",
                "background-color": "#ffffff",
                "font-size": "16px",
                "border-radius": "6px",
                padding: "10px",
                border: "0",
            },
            invalidCss: {
                color: "#ffffff",
                "background-color": "#e74c3c",
                border: "0",
            },
            validCss: {
                color: "#2c3e50",
                "background-color": "#eafaf1",
                border: "0",
            },
            placeholderCss: {
                color: "#95a5a6",
                "background-color": "white",
            },
            focusCss: {
                color: "#000000",
                "background-color": "none",
                border: "0",
            },

            fields: {
                ccnumber: {
                    selector: "#demoCcnumber",
                    title: "Card Number",
                    placeholder: "0000 0000 0000 0000",
                },
                ccexp: {
                    selector: "#demoCcexp",
                    title: "Card Expiration",
                    placeholder: "MM / YY",
                },
                cvv: {
                    display: "show",
                    selector: "#demoCvv",
                    title: "CVV Code",
                    placeholder: "***",
                }
            },

            price: "20.00",
            currency: "USD",
            country: "US",

            validationCallback: function(field, status, message) {
                const log = field + (status ? " is now OK: " : " is now Invalid: ") + message;
                // console.log(log);
            },

            timeoutDuration: 10000,
            timeoutCallback: function() {
                hideLoader(); // Hide loader on timeout
                swal({
                    title: "Timeout!",
                    text: "Payment took too long. Please try again.",
                    icon: "error",
                    button: "OK"
                });
            },

            fieldsAvailableCallback: function() {
                // console.log("Collect.js loaded the fields onto the form");
            },

            // This function is called after tokenization
            callback: function(response) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "nounce";
                input.value = response.token;

                const form = document.getElementsByTagName("form")[0];
                form.appendChild(input);
                submitForm(); // AJAX submit
            },
        });

        // Attach click event to start loader before CollectJS starts tokenizing
        $("#demoPayButton").on("click", function() {
            showLoader(); // Start loader when payment button is clicked
        });

        function submitForm() {
            const form = document.getElementsByTagName("form")[0];
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;

            $.ajax({
                url: form.action,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideLoader(); // Hide loader after AJAX completes

                    if (response.status) {
                        swal({
                            title: "Payment Successful!",
                            text: response.message,
                            icon: "success",
                            buttons: false,
                            timer: 2000
                        });
                    } else {
                        swal({
                            title: "Payment Failed!",
                            text: response.message,
                            icon: "error",
                            button: "OK"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    hideLoader(); // Hide loader on AJAX error

                    console.error("Payment error:", xhr.responseText);
                    swal({
                        title: "Payment Failed!",
                        text: "Something went wrong during the payment process.",
                        icon: "error",
                        button: "OK"
                    });
                }
            });
        }

    });
</script>
