<html>
<head>
  <title>{{ config('backpack.base.project_name') }} Unsubscribe</title>

  <link href='//fonts.googleapis.com/css?family=Open+Sans:300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

  <style>
    body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      color: #B0BEC5;
      display: table;
      font-weight: 300;
      font-family: 'Open Sans';
    }

    .container {
      text-align: center;
      display: table-cell;
      vertical-align: middle;
    }

    .content {
      text-align: center;
      display: inline-block;
    }

    .title {
      font-size: 8vw;
    }

    .quote {
      font-size: 4vw;
    }

    .explanation {
      font-size: 3vw;
    }

    a {
      text-decoration: none;
      border-bottom: 1px solid #999;
      color: #999;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="content">
    <div class="title"><?= __("See you again!") ?></div>
    <br>
    <div class="quote"><?= __("You are unsubscribed from \"%s\" rewards program notifications", $partner->title) ?></div>
    <div class="explanation">
      <br>
      <small>
        <?= __link("Proceed to your {dashboard}", routePartner($partner, 'client.index')) ?>
      </small>
    </div>
  </div>
</div>
</body>
</html>
