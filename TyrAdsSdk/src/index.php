<?php
require 'vendor/autoload.php';

use Tyrads\TyradsSdk\Contract\AuthenticationRequest;
use Tyrads\TyradsSdk\TyrAdsSdk;

// Configuration
$API_KEY           = "YOUR_API_KEY";      // Replace with your actual API key
$API_SECRET        = "YOUR_API_SECRET";   // Replace with your actual API secret
$LANGUAGE          = "en";
$AGE               = null;                  // (Optional) Replace with actual age
$GENDER            = null;                  // (Optional) 1 for male, 2 for female
$PUBLISHER_USER_ID = "PUBLISHER_USER_ID"; // Replace with actual publisher user ID

$tyrAdsSdk = TyrAdsSdk::make($API_KEY, $API_SECRET, $LANGUAGE);

$error = "";
try {
    $authRequest = new AuthenticationRequest(
        $PUBLISHER_USER_ID,
        [
            'age' => $AGE,
            'gender' => $GENDER
        ]
    );
    $authSign = $tyrAdsSdk->authenticate(
        $authRequest
    );
    if($authSign === null) {
        throw new \Exception("Authentication failed. Please check your API credentials and parameters.");
    }
    $TOKEN   = $authSign->getToken();
    $SUCCESS = !empty($TOKEN);
    $IFRAME_URL = $tyrAdsSdk->iframeUrl($TOKEN);
    $IFRAME_PREMIUM_URL = $tyrAdsSdk->iframePremiumWidget($TOKEN);
} catch (\Exception $e) {
    $SUCCESS = false;
    $TOKEN = '';
    $IFRAME_URL = '';
    $IFRAME_PREMIUM_URL = '';
    $error = $e->getMessage();
}

// HTML view
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TyrAds SDK Iframe</title>
</head>
<body style="background: #f7f8fa; font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0;">
    <div style="max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 32px;">
        <?php if ($SUCCESS): ?>
            <h1 style="color: #2d3748; font-size: 2rem; margin-bottom: 16px;">TyrAds SDK Iframe</h1>
            <h3 style="color: #4a5568; font-size: 1.1rem; margin-bottom: 8px;">Generated Token</h3>
            <textarea rows="4" cols="50" readonly style="width: 100%; font-size: 1rem; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e0; background: #f1f5f9; margin-bottom: 24px;"><?php echo htmlspecialchars($TOKEN); ?></textarea>
            
            <h3 style="color: #4a5568; font-size: 1.1rem; margin-bottom: 16px;">Offerwall</h3>
            <iframe id="tyrads_iframe" src="<?php echo htmlspecialchars($IFRAME_URL); ?>" height="650" width="300" frameborder="0" allowfullscreen style="display: block; margin: 0 auto 32px auto; border-radius: 8px; border: 1px solid #e2e8f0;"></iframe>
            
            <h3 style="color: #4a5568; font-size: 1.1rem; margin-bottom: 16px;">Premium Widget</h3>
            <iframe id="tyrads_premium_iframe" src="<?php echo htmlspecialchars($IFRAME_PREMIUM_URL); ?>" height="400" width="300" frameborder="0" allowfullscreen style="display: block; margin: 0 auto; border-radius: 8px; border: 1px solid #e2e8f0;"></iframe>
        <?php else: ?>
            <h1 style="color: #e53e3e; font-size: 1.5rem; margin-bottom: 16px;">Error Loading SDK Iframe</h1>
            <p style="color: #4a5568; font-size: 1rem; margin-bottom: 16px;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>