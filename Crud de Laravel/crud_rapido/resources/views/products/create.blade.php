<!DOCTYPE html>
<html>
<head>
    <title>Editar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1>Crear Producto</h1>

    <form action="{{ route('products.store') }}" method="POST" class="card p-4 shadow">
        @csrf

        <div class="mb-3">
            <label>Descripcion</label>
            <input type="text" name="description" class="form-control">
        </div>

        <div class="mb-3">
            <label>Precio</label>
            <input type="number" step="0.01" name="price" class="form-control">
        </div>

        <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="stock" class="form-control">
        </div>

       <button class="btn btn-success w-100 mb-2">Guardar</button>
<a href="{{ route('products.index') }}" class="btn btn-secondary w-100">Regresar</a>
    </form>
</div>

</body>
</html>