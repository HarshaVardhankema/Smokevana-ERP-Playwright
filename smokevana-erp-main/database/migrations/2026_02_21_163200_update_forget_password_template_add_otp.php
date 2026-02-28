<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update forget_password notification template to include {otp} (B2B OTP flow).
     *
     * @return void
     */
    public function up()
    {
        $emailBody = ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
          <tr style="background-color:#004aad;">
            <td style="padding:20px; text-align:center;">
              <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
            </td>
          </tr>
          <tr>
            <td style="padding:30px; color:#333;">
              <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Forgot Your Password?</h2>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Hello {contact_name},
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                We received a request to reset your password for your {business_name} account.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Your password reset code is: <strong>{otp}</strong>. It expires in 15 minutes.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Enter this code on the password reset page to set a new password.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                If you did not request a password reset, you may ignore this message.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                Regards, <br>
                The {business_name} Team
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#eeeeee; padding:20px; text-align:center; font-size:14px; color:#777;">
              &copy; {business_name} | All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>';

        $smsBody = 'Hello {contact_name}, your {business_name} password reset code is: {otp}. It expires in 15 minutes.';

        DB::table('notification_templates')
            ->where('template_for', 'forget_password')
            ->update([
                'email_body' => $emailBody,
                'sms_body' => $smsBody,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reliably restore previous template content; leave as-is or no-op
    }
};
