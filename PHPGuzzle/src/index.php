<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

// Configuration
$BASE_API_URL      = "https://api.tyrads.com";
$API_KEY           = "YOUR_API_KEY";      // Replace with your actual API key
$API_SECRET        = "YOUR_API_SECRET";   // Replace with your actual API secret
$SDK_VERSION       = "3.0";
$SDK_PLATFORM      = "Web";
$LANGUAGE          = "en";
$AGE               = 18;                  // Replace with actual age
$GENDER            = 1;                   // 1 for male, 2 for female
$PUBLISHER_USER_ID = "PUBLISHER_USER_ID"; // Replace with actual publisher user ID

$client = new Client([
    'base_uri' => $BASE_API_URL,
]);

try {
    $response = $client->post("/v$SDK_VERSION/auth", [
        'query' => [
            'lang' => $LANGUAGE,
        ],
        'json' => [
            'publisherUserId' => $PUBLISHER_USER_ID,
            'age'             => $AGE,
            'gender'          => $GENDER,
        ],
        'headers' => [
            "X-Api-Key"      => $API_KEY,
            "X-Api-Secret"   => $API_SECRET,
            "X-User-ID"      => $PUBLISHER_USER_ID,
            "X-SDK-Version"  => $SDK_VERSION,
            "X-SDK-Platform" => $SDK_PLATFORM,
            "Accept"         => "*/*",
            "Content-Type"   => "application/json",
        ],
        'http_errors' => false,
    ]);

    $body    = $response->getBody()->getContents();
    $data    = json_decode($body, true);
    $SUCCESS = isset($data['data']['token']);
    $TOKEN   = $SUCCESS ? $data['data']['token'] : '';
    $IFRAME_URL = "https://sdk.tyrads.com?token=" . urlencode($TOKEN);
} catch (\Exception $e) {
    $SUCCESS = false;
    $body = $e->getMessage();
    $TOKEN = '';
    $IFRAME_URL = '';
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
            <iframe id="tyrads_iframe" src="<?php echo htmlspecialchars($IFRAME_URL); ?>" height="650" width="300" frameborder="0" allowfullscreen style="display: block; margin: 0 auto; border-radius: 8px; border: 1px solid #e2e8f0;"></iframe>
        <?php else: ?>
            <h1 style="color: #e53e3e; font-size: 1.5rem; margin-bottom: 16px;">Error Loading SDK Iframe</h1>
            <p style="color: #718096; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px; padding: 16px;"><?php echo htmlspecialchars($body); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>