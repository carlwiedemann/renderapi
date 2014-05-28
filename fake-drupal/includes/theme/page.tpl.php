<!DOCTYPE html>
<html>
  <head>
    <title><?php print render($head_title); ?></title>
    <style type="text/css" media="screen">
      body {
        font-family: "Helvetica Neue";
        padding: 2em;
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
        background-color: #FFC7FF;
        float: left;
        width: 50%;
        margin-left: 25%;
        margin-right: -75%;
      }
      #sidebar-first,
      #sidebar-second {
        background-color: #FFFFC7;
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
        padding: 2em;
      }
    </style>
  </head>
  <body>
    <header><div class="inner">
      <?php print render($header); ?>
    </div></header>
    <div id="main">
      <section id="content"><div class="inner">
        <?php print render($content); ?>
      </div></section>
      <aside>
        <section id="sidebar-first"><div class="inner"><?php
        print render($sidebar_first);
        ?></div></section>
        <section id="sidebar-second"><div class="inner"><?php print render($sidebar_second); ?></div></section>
      </aside>
    </div>
    <footer><div class="inner">
      <?php print render($footer); ?>
    </div></footer>
  </body>
</html>
