<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJ Inventory - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .navbar { background-color: #000 !important; }
        .sidebar { height: 100vh; background: #fff; border-right: 1px solid #dee2e6; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">AJ INVENTORY</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="categories.php">Categorías</a></li>
        <li class="nav-item"><a class="nav-link text-warning" href="sales.php">Nueva Venta</a></li>
        <li class="nav-item"><a class="nav-link" href="reports.php">Reportes</a>
        <li class="nav-item"><a class="nav-link" href="customers.php">Clientes</a>
</li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">