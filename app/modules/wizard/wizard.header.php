<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Install and Update Script - BrightGamePanel V2</title>
    <!--Powered By Bright Game Panel-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- JS -->
    <script src="./gui/wizard/js/jquery.js"></script>
    <script src="./gui/wizard/js/bootstrap.js"></script>
    <!-- Style -->
    <link href="./gui/wizard/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
    </style>
    <link href="./gui/wizard/css/bootstrap-responsive.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Favicon -->
    <link rel="shortcut icon" href="bootstrap/img/favicon.ico">
</head>

<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="#">Bright Game Panel V2</a>
        </div>
    </div>
</div>
<div class="container">
    <div class="page-header">
        <h1>Install and Update Script&nbsp;<small>Bright Game Panel V2 - <?php echo LASTBGPVERSION; ?></small></h1>
    </div>
    <ul class="breadcrumb">
        <?php

        //---------------------------------------------------------+

        if (!isset($_GET['step'])) // Step == 'zero'
        {
            ?>
            <li class="active">License</li>
            <?php
        }
        else if ($_GET['step'] == 'one')
        {
            ?>
            <li>
                <a href="index.php">License</a> <span class="divider">/</span>
            </li>
            <li class="active">Check Requirements</li>
            <?php
        }
        else if ($_GET['step'] == 'two')
        {
            ?>
            <li>
                <a href="index.php">License</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="index.php?step=one">Check Requirements</a> <span class="divider">/</span>
            </li>
            <li class="active">Select Database Update</li>
            <?php
        }
        else if ($_GET['step'] == 'three')
        {
            ?>
            <li>
                <a href="index.php">License</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="index.php?step=one">Check Requirements</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="index.php?step=two">Select Database Update</a> <span class="divider">/</span>
            </li>
            <li class="active">Install Database</li>
            <?php
        }

        //---------------------------------------------------------+

        ?>
    </ul>
