<html>
	<head>
		<title>{{ $partner->title ?? null }}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style type="text/css">{{ file_get_contents(app_path('../resources/views/emails/templates/sunny.css')) }}</style>
		@if(isset($css))
		<style type="text/css">
			{{ $css }}
		</style>
		@endif

		<style>
			blockquote {
				background: #f9f9f9;
				border-left: 10px solid #ccc;
				margin: 1.5em 10px;
				padding: 0.5em 10px;
				quotes: "\201C""\201D""\2018""\2019";
			}
			blockquote:before {
				color: #ccc;
				content: open-quote;
				font-size: 4em;
				line-height: 0.1em;
				margin-right: 0.25em;
				vertical-align: -0.4em;
			}
			blockquote p {
				display: inline;
			}

			<?php
            $primaryColor = '#EFA947';

            if (isset($partner)) {
                $primaryColor = \HCustomizations::primaryColor($partner, $primaryColor);
            }
            ?>
			.button-content .button {
				background: {{ $primaryColor }};
			}

			.content-wrapper * {
				color: inherit;
			}
		</style>

	</head>
	<body>
	<table id="background-table" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody>
		<tr>
			<td align="center">
				<table class="w640" border="0" cellpadding="0" cellspacing="0" width="640">
					<tbody>
					<tr class="large_only">
						<td class="w640" height="20" width="640"></td>
					</tr>
					<tr class="mobile_only">
						<td class="w640" height="10" width="640"></td>
					</tr>
					<tr class="mobile_only">
						<td class="w640" height="10" width="640"></td>
					</tr>
					<?php if (isset($logo)) {
                ?>
					<tr class="mobile_only">
						<td class="w640" width="640" align="center">
							<img class="mobile_only mobile-logo" border="0" src="{{ $logo['path'] }}" alt="{{ $partner->title ?? null }}" width="{{ $logo['width'] }}" height="{{ $logo['height'] }}" />
						</td>
					</tr>
					<?php
            } ?>

					<tr class="mobile_only">
						<td class="w640" height="20" width="640"></td>
					</tr>
					<tr class="large_only">
						<td class="w640"  height="20" width="640"></td>
					</tr>

					<tr>
						<td class="w640" width="640" colspan="3" height="20"></td>
					</tr>

					<tr class="mobile_only">
						<td class="w640"  height="10" width="640"></td>
					</tr>
					<tr class="mobile_only">
						<td class="w640"  height="20" width="640"></td>
					</tr>
					<?php if (isset($logo)) {
                ?>
					<tr>
						<td id="header" class="w640" align="center" width="640">
							<table class="w640" border="0" cellpadding="0" cellspacing="0" width="640">
								<tr>
									<td class="w30" width="30"></td>
									<td id="logo" width="{{ $logo['width'] }}" valign="top">
										<img border="0" src="{{ $logo['path'] }}" alt="{{ $partner->title ?? null }}" width="{{ $logo['width'] }}" height="{{ $logo['height'] }}" />
									</td>
									<td class="w30" width="30"></td>
								</tr>
								<tr>
									<td colspan="3" height="20" class="large_only"></td>
								</tr>
								<tr>
									<td colspan="3" height="20" class="large_only"></td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
            } ?>
					<tr>
						<td class="w640" bgcolor="#ffffff" width="640">
							<table class="w640 content-wrapper" border="0" cellpadding="0" cellspacing="0" width="640">
								<tbody>

									@section('content')
									@show

								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td class="w640" bgcolor="#ffffff" width="640" colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="w640" bgcolor="#ffffff" width="640" colspan="3" height="20"></td>
					</tr>
					<tr>
						<td>
							<table width="640" class="w640" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td class="w50" width="50"></td>
									<td class="w410" width="410">
										@if (isset($reminder))
											<p id="permission-reminder" class="footer-content-left" align="left">{!! $reminder !!}</p>
										@endif
									</td>
									<td valign="top">
										<table align="right">
											<tr>
												<td colspan="2" height="10"></td>
											</tr>
											<tr>
												@if (isset($twitter))
													<td><a href="https://twitter.com/{{ $twitter }}"><img src="{{ Request::getSchemeAndHttpHost() }}/vendor/beautymail/assets/images/ark/twitter.png" alt="Twitter" height="25" width="25" style="border:0" /></a></td>
												@endif

												@if (isset($facebook))
													<td><a href="https://facebook.com/{{ $facebook }}"><img src="{{ Request::getSchemeAndHttpHost() }}/vendor/beautymail/assets/images/ark/fb.png" alt="Facebook" height="25" width="25" style="border:0" /></a></td>
												@endif
											</tr>
										</table>
									</td>
									<td class="w15" width="15"></td>
								</tr>

							</table>
						</td>
					</tr>
					<tr>
						<td class="w640" width="640" colspan="3" height="20"></td>
					</tr>
					<tr>
						<td id="footer" class="w640" height="60" width="640" align="center">

							@section('footer')
							@show

              <a href="https://bitrewards.com/" target="_blank"><?= __('Made with %heart% on BitRewards', '❤'); ?></a>
							<?php if ($isUnsubscribable ?? null) {
                ?>
							    <br><br>
								<a target="_blank" href="<?= app(App\Services\PartnerService::class)->getUrlAutologin($user, 'client.unsubscribe'); ?>" style="font-weight: normal; font-size: smaller;">
									<?= __('Unsubscribe'); ?>
								</a>
							<?php
            } ?>

						</td>
					</tr>
					<tr>
						<td class="w640" width="640" colspan="3" height="40"></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		</tbody>
	</table>
	</body>
</html>
