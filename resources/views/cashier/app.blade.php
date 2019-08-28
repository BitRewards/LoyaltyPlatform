<?php
/**
 * @var App\Models\Partner
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $partner->title; ?>: Loyalty Cashier Interface</title>
    <link href='/cashier/style.css?<?= time(); ?>' rel='stylesheet' type='text/css'>
    <link href="/favicon.png" rel="icon" type="image/x-icon" />
    <link href="https://unpkg.com/animate.css@3.5.1/animate.min.css" rel="stylesheet" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <script>
        window.LANGUAGE = "<?= $partner->default_language; ?>";
    </script>
</head>
<body>
    <div id="app-container">
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min.js"></script>

    @if (config('app.env') === 'production')
        <script src="https://cdn.ravenjs.com/3.16.0/raven.min.js" crossorigin="anonymous"></script>
        <script>
            Raven.config('https://d11905a6de164d63a99fae5ebfecfc5f@sentry.io/180373', {
                release: '{{ $sentryFrontendRelease }}'
            }).install();
        </script>
    @endif

    <script src="/cashier/app.js?<?= time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FastClick.attach(document.body);
        }, false);
    </script>
</body>
</html>
