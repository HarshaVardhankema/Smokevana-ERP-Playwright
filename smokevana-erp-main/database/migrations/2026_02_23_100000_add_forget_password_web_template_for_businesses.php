<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Insert forget_password_web notification template for every business that has forget_password.
     * Used by B2B forgot-password when X-Platform: web (link-only email, no OTP).
     *
     * @return void
     */
    public function up()
    {
        $businessIds = DB::table('notification_templates')
            ->where('template_for', 'forget_password')
            ->distinct()
            ->pluck('business_id');

        $existingWeb = DB::table('notification_templates')
            ->where('template_for', 'forget_password_web')
            ->pluck('business_id')
            ->flip();

        $defaultEmailBody = ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
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
                Click the button below to set a new password:
              </p>
              <p style="text-align:center; margin:20px 0;">
                <a href="{url_complete}" style="background-color:#004aad; color:#ffffff; padding:12px 20px; border-radius:4px; text-decoration:none; font-size:16px;">
                  Reset Password
                </a>
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                If you did not request this, you can safely ignore this email.
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

        $now = now()->toDateTimeString();

        foreach ($businessIds as $businessId) {
            if ($existingWeb->has($businessId)) {
                continue;
            }

            DB::table('notification_templates')->insert([
                'business_id' => $businessId,
                'template_for' => 'forget_password_web',
                'email_body' => $defaultEmailBody,
                'sms_body' => 'Hi {contact_name}, we received a request to reset your {business_name} password. Reset it using this link: {url_complete}',
                'subject' => 'Forgot Your Password?',
                'cc' => null,
                'bcc' => null,
                'whatsapp_text' => '',
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('notification_templates')
            ->where('template_for', 'forget_password_web')
            ->delete();
    }
};
