/**
 * script.js — Lab 1: CRUD con Fetch API
 * Estudiante: Yan
 * Descripción: Maneja toda la lógica del cliente. Se comunica con
 *              registrar.php usando fetch() sin recargar la página.
 */

"use strict"; // Activa el modo estricto de JavaScript para evitar errores silenciosos

// ── Referencias al DOM ───────────────────────────────────────────────
// Capturamos los elementos HTML que vamos a usar durante la ejecución
const formProducto  = document.getElementById("formProducto");
const btnRegistrar  = document.getElementById("btnRegistrar");
const btnCancelar   = document.getElementById("btnCancelar");
const btnBuscar     = document.getElementById("btnBuscar");
const btnVerTodos   = document.getElementById("btnVerTodos");
const campoBusqueda = document.getElementById("campoBusqueda");
const cuerpoTabla   = document.getElementById("cuerpoTabla");
const contadorBadge = document.getElementById("contadorProductos");
const badgeModo     = document.getElementById("badge-modo");

// ── Cargar tabla al iniciar la página ───────────────────────────────
// DOMContentLoaded se dispara cuando el HTML termina de cargarse
document.addEventListener("DOMContentLoaded", () => {
    ListarProductos(); // Mostramos los productos apenas abre la página
});

// ════════════════════════════════════════════════════════════════════
// EVENTO SUBMIT — Guardar o Modificar
// Se activa cuando el usuario presiona el botón Registrar / Actualizar
// ════════════════════════════════════════════════════════════════════
formProducto.addEventListener("submit", async (e) => {
    e.preventDefault(); // Evita que el formulario recargue la página

    // Si hay un ID oculto en el formulario, es edición; si no, es nuevo registro
    const idProducto = document.getElementById("idProducto").value;
    const accion     = idProducto ? "Modificar" : "Guardar";

    // FormData recoge todos los campos del formulario automáticamente
    const formData = new FormData(formProducto);
    formData.set("Accion", accion); // Agregamos el campo Accion para el switch de PHP

    try {
        // fetch() envía los datos a registrar.php de forma asíncrona (sin recargar)
        const response = await fetch("registrar.php", {
            method: "POST",
            body: formData
        });

        // Si el servidor respondió con error HTTP (500, 404, etc.)
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Convertimos la respuesta de texto a objeto JSON
        const data = await response.json();

        // ── Switch en JavaScript (criterio de rúbrica) ───────────────
        // Evaluamos la acción que devuelve el servidor para saber qué mostrar
        switch (data.accion) {

            case "Guardar":
                if (data.success) {
                    // Producto creado — mostramos alerta de éxito y actualizamos la tabla
                    Swal.fire({
                        icon:  "success",
                        title: "¡Guardado!",
                        text:  data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    resetFormulario(); // Limpiamos el formulario
                    ListarProductos(); // Recargamos la tabla
                } else {
                    mostrarErrores(data); // Mostramos los errores de validación
                }
                break;

            case "Modificar":
                if (data.success) {
                    // Producto actualizado — alerta de éxito
                    Swal.fire({
                        icon:  "success",
                        title: "¡Actualizado!",
                        text:  data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    resetFormulario();
                    ListarProductos();
                } else {
                    mostrarErrores(data);
                }
                break;

            default:
                // Acción desconocida — caso de seguridad
                Swal.fire("Error", data.message || "Acción desconocida.", "error");
                break;
        }

    } catch (error) {
        // Error de red o de parseo JSON
        Swal.fire({
            icon:  "error",
            title: "Error de red",
            text:  error.message
        });
    }
});

// ════════════════════════════════════════════════════════════════════
// BUSCAR — filtra productos mientras el usuario escribe
// ════════════════════════════════════════════════════════════════════
btnBuscar.addEventListener("click", () => {
    const termino = campoBusqueda.value.trim();
    ListarProductos(termino); // Enviamos el término de búsqueda
});

// Si el usuario presiona Enter en el buscador, activa la búsqueda
campoBusqueda.addEventListener("keyup", (e) => {
    if (e.key === "Enter") btnBuscar.click();
});

// Botón "Ver todos" limpia el buscador y recarga toda la lista
btnVerTodos.addEventListener("click", () => {
    campoBusqueda.value = "";
    ListarProductos();
});

// ════════════════════════════════════════════════════════════════════
// CANCELAR — vuelve al modo Registrar sin guardar cambios
// ════════════════════════════════════════════════════════════════════
btnCancelar.addEventListener("click", () => {
    resetFormulario();
});

// ════════════════════════════════════════════════════════════════════
// ListarProductos() — consulta todos o filtra por término
// Parámetro termino: texto a buscar (vacío = traer todos)
// ════════════════════════════════════════════════════════════════════
async function ListarProductos(termino = "") {
    // Construimos el FormData con la acción Buscar
    const formData = new FormData();
    formData.append("Accion",  "Buscar");
    formData.append("termino", termino); // Puede ser vacío para traer todo

    try {
        const response = await fetch("registrar.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (data.success && data.data) {
            renderTabla(data.data); // Dibujamos las filas en la tabla
        } else {
            // Sin resultados — mostramos mensaje en la tabla
            cuerpoTabla.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="bi bi-inbox"></i> No se encontraron productos.
                    </td>
                </tr>`;
            contadorBadge.textContent = "0";
        }
    } catch (error) {
        // Error de conexión con el servidor
        cuerpoTabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">
                    Error al cargar productos: ${error.message}
                </td>
            </tr>`;
    }
}

// ════════════════════════════════════════════════════════════════════
// renderTabla() — construye las filas HTML de la tabla dinámicamente
// Recibe el array de productos que devuelve el servidor
// ════════════════════════════════════════════════════════════════════
function renderTabla(productos) {
    contadorBadge.textContent = productos.length; // Actualiza el contador del badge

    if (productos.length === 0) {
        cuerpoTabla.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i> Sin productos registrados.
                </td>
            </tr>`;
        return;
    }

    let filas = "";
    // Recorremos el array y construimos una fila por producto
    productos.forEach((p, index) => {
        filas += `
        <tr>
            <td>${index + 1}</td>
            <td>${escapeHtml(p.codigo)}</td>
            <td>${escapeHtml(p.producto)}</td>
            <td>$${parseFloat(p.precio).toFixed(2)}</td> <!-- Formato de 2 decimales -->
            <td>${p.cantidad}</td>
            <td class="text-center">
                <!-- Botón Editar: carga los datos del producto en el formulario -->
                <button class="btn btn-edit btn-sm me-1"
                    onclick="cargarEdicion(${p.id}, '${escapeHtml(p.codigo)}',
                    '${escapeHtml(p.producto)}', ${p.precio}, ${p.cantidad})">
                    <i class="bi bi-pencil-fill"></i> Editar
                </button>
                <!-- Botón Borrar: pide confirmación antes de eliminar -->
                <button class="btn btn-danger btn-sm"
                    onclick="eliminarRegistro(${p.id}, '${escapeHtml(p.producto)}')">
                    <i class="bi bi-trash-fill"></i> Borrar
                </button>
            </td>
        </tr>`;
    });

    cuerpoTabla.innerHTML = filas; // Insertamos todas las filas de una vez
}

// ════════════════════════════════════════════════════════════════════
// cargarEdicion() — rellena el formulario con los datos del producto
// para que el usuario pueda modificarlos
// ════════════════════════════════════════════════════════════════════
function cargarEdicion(id, codigo, producto, precio, cantidad) {
    // Rellenamos cada campo del formulario con los datos del producto seleccionado
    document.getElementById("idProducto").value = id;       // ID oculto para el UPDATE
    document.getElementById("Codigo").value     = codigo;
    document.getElementById("Producto").value   = producto;
    document.getElementById("Precio").value     = precio;
    document.getElementById("Cantidad").value   = cantidad;

    // Al editar, la cantidad puede ser 0 (reducir stock)
    document.getElementById("Cantidad").min = "0";

    // Cambiamos la UI para indicar que estamos en modo edición
    btnRegistrar.innerHTML = '<i class="bi bi-arrow-repeat"></i> Actualizar';
    btnRegistrar.classList.replace("btn-primary", "btn-warning");
    btnCancelar.classList.remove("d-none"); // Mostramos el botón Cancelar
    badgeModo.textContent = "Modo: Editar";
    badgeModo.classList.replace("bg-success", "bg-warning");

    // Hacemos scroll suave hacia el formulario
    formProducto.scrollIntoView({ behavior: "smooth" });
}

// ════════════════════════════════════════════════════════════════════
// resetFormulario() — limpia el formulario y regresa al modo Registrar
// ════════════════════════════════════════════════════════════════════
function resetFormulario() {
    formProducto.reset();                               // Limpia todos los campos
    document.getElementById("idProducto").value = "";  // Borra el ID oculto
    document.getElementById("Cantidad").min     = "1"; // Restaura mínimo a 1

    // Restauramos la UI al estado inicial (modo Registrar)
    btnRegistrar.innerHTML = '<i class="bi bi-floppy-fill"></i> Registrar';
    btnRegistrar.classList.replace("btn-warning", "btn-primary");
    btnCancelar.classList.add("d-none");
    badgeModo.textContent = "Modo: Registrar";
    badgeModo.classList.replace("bg-warning", "bg-success");
}

// ════════════════════════════════════════════════════════════════════
// mostrarErrores() — muestra los errores de validación del servidor
// con SweetAlert2 en formato de lista
// ════════════════════════════════════════════════════════════════════
function mostrarErrores(data) {
    let listaErrores = data.message || "";

    // Si hay errores por campo, los mostramos en una lista HTML
    if (data.errors && Object.keys(data.errors).length > 0) {
        listaErrores += "<ul class='text-start mt-2'>";
        for (const campo in data.errors) {
            listaErrores += `<li><strong>${campo}:</strong> ${data.errors[campo]}</li>`;
        }
        listaErrores += "</ul>";
    }

    Swal.fire({
        icon:              "error",
        title:             "Error de validación",
        html:              listaErrores,
        confirmButtonText: "Entendido"
    });
}

// ════════════════════════════════════════════════════════════════════
// eliminarRegistro() — pide confirmación y elimina el producto
// ════════════════════════════════════════════════════════════════════
async function eliminarRegistro(id, nombre) {
    // Mostramos diálogo de confirmación antes de eliminar
    const confirmacion = await Swal.fire({
        icon:               "warning",
        title:              "¿Eliminar producto?",
        html:               `<b>${nombre}</b> será eliminado permanentemente.`,
        showCancelButton:   true,
        confirmButtonText:  "Sí, eliminar",
        cancelButtonText:   "Cancelar",
        confirmButtonColor: "#dc3545"
    });

    // Si el usuario canceló, no hacemos nada
    if (!confirmacion.isConfirmed) return;

    // Enviamos el ID al servidor para ejecutar el DELETE
    const formData = new FormData();
    formData.append("Accion", "Eliminar");
    formData.append("id",     id);

    try {
        const response = await fetch("registrar.php", { method: "POST", body: formData });
        const data     = await response.json();

        if (data.success) {
            Swal.fire({ icon: "success", title: "Eliminado", text: data.message,
                timer: 1800, showConfirmButton: false });
            ListarProductos(); // Actualizamos la tabla después de eliminar
        } else {
            Swal.fire("Error", data.message, "error");
        }
    } catch (error) {
        Swal.fire("Error de red", error.message, "error");
    }
}

// ════════════════════════════════════════════════════════════════════
// escapeHtml() — sanitiza texto antes de insertarlo en el DOM
// Evita ataques XSS (Cross-Site Scripting)
// ════════════════════════════════════════════════════════════════════
function escapeHtml(texto) {
    if (typeof texto !== "string") return texto;
    return texto
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}
