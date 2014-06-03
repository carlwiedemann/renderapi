<!DOCTYPE html>
<html>
  <head>
    <title><?php print $head_title; ?></title>
    <style type="text/css" media="screen">
      body {
        font-family: "Verdana";
        max-width: 1200px;
        margin: 0 auto;
      }
      header,
      footer {
        background-color: #DDD;
        min-height: 10em;
      }

      #main:before,
      #main:after {
        content: "";
        display: table;
      }
      #main:after {
        clear: both;
      }

      #content {
        float: left;
        width: 50%;
        margin-left: 25%;
        margin-right: -75%;
      }
      #sidebar-first,
      #sidebar-second {
        background-color: cadetblue;
        float: left;
        width: 25%;
      }
      #sidebar-first {
        margin-left: 0;
        margin-right: -25%;
      }
      #sidebar-second {
        margin-left: 75%;
        margin-right: -100%;
      }
      .inner {
        padding: 1em;
      }
    </style>
  </head>
  <body>
    <header><div class="inner">
      <h1><?php print $header; ?></h1>
    </div></header>
    <div id="main">
      <section id="content"><div class="inner">
        <?php print $content; ?>
      </div></section>
      <aside>
        <section id="sidebar-first"><div class="inner"><?php print $sidebar_first; ?></div></section>
        <section id="sidebar-second"><div class="inner"><?php print $sidebar_second; ?></div></section>
      </aside>
    </div>
    <footer><div class="inner">
      <?php print $footer; ?>
    </div></footer>
  </body>
</html>
