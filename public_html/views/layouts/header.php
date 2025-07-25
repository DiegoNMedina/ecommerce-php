<?php

use Install\Config; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Config::APP_NAME ?> - Tienda de Computadoras</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/ecommerce-php/assets/css/style.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/ecommerce-php/assets/img/favicon.ico">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/ecommerce-php/">
                    <i class="fas fa-laptop me-2"></i><?= Config::APP_NAME ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/ecommerce-php/products">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ecommerce-php/featured">Destacados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ecommerce-php/calculator">Calculadora de Pagos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ecommerce-php/about">Acerca de</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ecommerce-php/contact">Contacto</a>
                        </li>
                    </ul>

                    <form class="d-flex" action="/ecommerce-php/search" method="GET">
                        <input class="form-control me-2" type="search" name="q" placeholder="Buscar productos..."
                            value="<?= htmlspecialchars($this->getQuery('q', '')) ?>">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">