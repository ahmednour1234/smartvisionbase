
<?php $name = $client->name ?? 'Guest'; ?>
<!DOCTYPE html>
<html lang="en">
  <body style="margin:0;padding:0;background:#f5f7fb;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:24px 0;">
      <tr>
        <td align="center">
          <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;padding:24px;">
            <tr>
              <td align="left" style="font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
                <h2 style="margin:0 0 12px;">Verify your email</h2>
                <p style="margin:0 0 16px;">Hi <?php echo e($name); ?>, use the code below to verify your email:</p>
                <div style="font-size:28px; font-weight:700; letter-spacing:6px; padding:16px 0;"><?php echo e($code); ?></div>
                <p style="margin:0 0 12px; color:#475569;">This code expires in 20 minutes.</p>
                <p style="margin:0; color:#475569;">If you didn’t request this, you can ignore this email.</p>
                <hr style="border:0; border-top:1px solid #e2e8f0; margin:24px 0;">
                <p style="font-size:12px; color:#94a3b8;">© <?php echo e(date('Y')); ?> Forex Traders Summit</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/emails/verify_code.blade.php ENDPATH**/ ?>