<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1>Editar Productos</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST" class="card p-4 shadow">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Descripcion</label>
            <input type="text" name="description" value="{{ $product->description }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Precio</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="stock" value="{{ $product->stock }}" class="form-control">
        </div>

        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Regresar</a>
    </form>
</div>

</body>
</html>