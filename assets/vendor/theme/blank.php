<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Theme Preview - Usebootstrap.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="theme/bootstrap.css" media="screen">
    <link rel="stylesheet" href="theme/usebootstrap.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="bootstrap/html5shiv.js"></script>
      <script src="bootstrap/respond.min.js"></script>
    <![endif]-->

  </head>
  <body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("/var/www/samekhi.com/vendor/autoload.php");

$hl = new \Highlight\Highlighter();
$hl->setAutodetectLanguages(array('php', 'css', 'java'));
//$hl->setAutodetectLanguages(array('php', 'css', 'java'));

$html=file_get_contents('/var/www/samekhi.com/theme/blank.php');
$highlighted = $hl->highlightAuto($html);

//Krumo
require_once("/var/www/samekhi.com/admin/krumo/class.krumo.php");
krumo($html);
//Krumo


echo "<pre><code class=\"hljs {$highlighted->language}\">";
echo $highlighted->value;
echo "</code></pre>";

?>


    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap/bootstrap.min.js"></script>
	<script src="bootstrap/usebootstrap.js"></script>


  </body>
</html>
