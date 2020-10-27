<?php
    require './vendor/autoload.php';
    require_once './db.php';

    use App\appRights;
    $rights = new appRights($conn);

    Mustache_Autoloader::register();
    $loader = new Mustache_Loader_FilesystemLoader("templates",array('extension'=>'.html'));
    $m = new Mustache_Engine;
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>pagesmanager</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
      <a class="navbar-brand" href="#">PagesManager</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="index.php">Управление доступом
<!--              <span class="sr-only">(current)</span>-->
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="users.php">Пользователи</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Выход</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>