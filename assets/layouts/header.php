<?php
require_once("../assets/includes/config.php");


if (isset($_SESSION['auth']))
    $_SESSION['expire'] = ALLOWED_INACTIVITY_TIME;

generate_csrf_token();
check_remember_me();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_DESCRIPTION;  ?>">
    <meta name="author" content="<?php echo APP_OWNER;  ?>">

    <title><?php echo TITLE . ' | ' . APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">

    <link rel="stylesheet" href="../assets/vendor/bootstrap-4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/vendor/fontawesome-5.12.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/vendor/theme/theme/bootstrap.css" media="screen">
    <link rel="stylesheet" href="../assets/vendor/theme/theme/usebootstrap.css">

    <!-- Custom styles -->
    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="stylesheet" href="custom.css" >

<!--  Ckeditor or other header stuff -->
<?php if(!empty($css)){echo $css;} ?>

</head>

<body>

    <?php require 'navbar.php'; ?>
