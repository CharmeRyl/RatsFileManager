<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo $_STATIC_URI; ?>/favicon.ico">

    <title><?php echo $_TITLE; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo $_STATIC_URI; ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $_STATIC_URI; ?>/css/ladda-themeless.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <?php if(in_array('base', $_CSS)): ?>
    <link href="<?php echo $_STATIC_URI; ?>/css/base.css" rel="stylesheet">
    <?php endif; ?>
    <?php if(in_array('floating-labels', $_CSS)): ?>
    <link href="<?php echo $_STATIC_URI; ?>/css/floating-labels.css" rel="stylesheet">
    <?php endif; ?>

    <!--  Font Awesome  -->
    <link href="<?php echo $_STATIC_URI; ?>/css/font-awesome.min.css" rel="stylesheet">

</head>

<body class="bg-light">