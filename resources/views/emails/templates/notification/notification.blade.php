<html>
  <head>
    <title>{{ $partner->title ?? null }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">{{ file_get_contents(app_path('../resources/views/emails/templates/notification/notification.css')) }}</style>
    @if(isset($css))
    <style type="text/css">
      {{ $css }}
    </style>
    @endif

    <style>
      <?php
      $primaryColor = '#FA6155';

      if (isset($partner)) {
          $primaryColor = \HCustomizations::primaryColor($partner, $primaryColor);
      }
      ?>

      .content-wrapper * {
        color: inherit;
      }

      .page-title span {
        color: <?= $primaryColor; ?>;
      }

      .article-content a {
        color: <?= $primaryColor; ?>;
      }

      .article-content li a {
        color: <?= $primaryColor; ?>;
      }

      .button-content .button {
        background: <?= $primaryColor; ?>;
      }

      .button-content.inversed .button {
        color: <?= $primaryColor; ?>;
        border-color: <?= $primaryColor; ?>;
      }

      .color-highlighted {
        color: <?= $primaryColor; ?>
      }

    </style>

  </head>
  <body>
  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #fff">
    <tbody>
    <tr>
      <td align="center">
        <table class="w600" border="0" cellpadding="0" cellspacing="0" width="600">
          <tbody>
            <tr class="large_only">
              <td height="65"></td>
            </tr>

            <tr class="mobile_only">
              <td height="15"></td>
            </tr>
            
            <?php if (isset($logo) && !empty($logo['path'])) {
          ?>
            <tr>
              <td align="left">
                <img 
                  border="0"
                  src="{{ $logo['path'] }}"
                  alt="{{ $partner->title ?? null }}"
                  width="{{ $logo['width'] ?? null}}"
                  height="{{ $logo['height'] ?? null}}"
                />
              </td>
            </tr>
            <?php
      } ?>

          <tr>
            <td class="w600" bgcolor="#ffffff" width="600">
              <table class="w600" border="0" cellpadding="0" cellspacing="0" width="600">
                <tbody>

                  @section('content')
                  @show

                </tbody>
              </table>
            </td>
          </tr>

          <tr>
            <td class="w600 article-content" width="600" align="left" style="color: #a1a1a1;">

              @section('footer')
              @show

              <?php if ($isUnsubscribable ?? null) {
          ?>
                <?=
                  __link(
                    $user->partner->isFiatReferralEnabled() ? 'You can always {unsubscribe} from the notifications of the referral program' : 'You can always {unsubscribe} from the notifications of the rewards program',
                    app(\App\Services\PartnerService::class)->getUrlAutologin($user, 'client.unsubscribe'),
                    [
                        'style' => 'font-weight: normal; text-decoration: underline; color: inherit;',
                    ]
                  ); ?>.
              <?php
      } ?>

            </td>
          </tr>
          <tr class="large_only">
            <td class="w600" width="600" colspan="3" height="70"></td>
          </tr>
          <tr class="mobile_only">
            <td class="w600" width="600" colspan="3" height="40"></td>
          </tr>
          </tbody>
        </table>
      </td>
    </tr>
    </tbody>
  </table>
  </body>
</html>
