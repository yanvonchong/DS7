\# 🧪 Laboratorio #2 - Implementación de Login en Laravel



\## 📌 Desarrollo Web VII



\*\*Universidad Tecnológica de Panamá\*\*

\*\*Instructor:\*\* Ing. Irina Fong



\---



\## 🧾 Descripción del Proyecto



Este laboratorio consiste en la implementación de un sistema de autenticación (Login y Registro) utilizando el framework Laravel bajo la arquitectura \*\*MVC (Modelo - Vista - Controlador)\*\*.



El objetivo principal es comprender el flujo de autenticación de usuarios, la gestión de sesiones y el uso de herramientas modernas de desarrollo web.



\---



\## 🎯 Objetivos



\* Aplicar la arquitectura MVC en Laravel

\* Implementar autenticación de usuarios

\* Configurar base de datos y migraciones

\* Comprender el flujo de login y registro

\* Documentar el proceso de desarrollo



\---



\## ⚙️ Requisitos Previos



Para ejecutar este proyecto se necesita:



\* PHP 8.0 o superior

\* Composer

\* Laravel

\* WampServer / XAMPP / Laragon

\* MySQL o MariaDB

\* Visual Studio Code

\* Node.js y npm



\---



\## 🚀 Instalación del Proyecto



\### 1. Clonar o crear el proyecto



```bash

composer create-project laravel/laravel labLaravelLogin7

```



\---



\### 2. Configurar entorno



Editar el archivo `.env`:



```env

DB\_DATABASE=lablaravellogin7

DB\_USERNAME=root

DB\_PASSWORD=

```



\---



\### 3. Instalar dependencias



```bash

composer install

npm install

```



\---



\### 4. Ejecutar migraciones



```bash

php artisan migrate

```



\---



\### 5. Instalar autenticación



```bash

composer require laravel/ui

php artisan ui bootstrap --auth

```



\---



\### 6. Compilar archivos



```bash

npm run dev

```



\---



\### 7. Ejecutar servidor



```bash

php artisan serve

```



\---



\## 🌐 Acceso al Sistema



Abrir en el navegador:



👉 http://127.0.0.1:8000



\---



\## 🧠 Arquitectura MVC en Laravel



\### 📁 Modelos



Representan la base de datos y la lógica de negocio.



\### 📁 Vistas



Son las interfaces que ve el usuario (Blade).



\### 📁 Controladores



Gestionan la lógica entre modelos y vistas.



\---



\## 🗄️ Base de Datos



\* Nombre: `lablaravellogin7`

\* Motor: MySQL

\* Configuración realizada en `.env`



\### 📌 Migraciones utilizadas:



```bash

php artisan migrate

php artisan migrate:fresh

```



\---



\## 📸 Resultados



El sistema permite:



\* Registro de usuarios

\* Inicio de sesión

\* Cierre de sesión



\*(Aquí debes colocar capturas de pantalla del sistema funcionando)\*



\---



\## ⚠️ Dificultades y Soluciones



\### ❌ Error: "Specified key was too long"



✔ Solución:

Se agregó en `AppServiceProvider.php`:



```php

Schema::defaultStringLength(191);

```



\---



\### ❌ Error: "sessions table doesn't exist"



✔ Solución:



```bash

php artisan session:table

php artisan migrate

```



\---



\### ❌ Error: axios no encontrado



✔ Solución:



```bash

npm install axios

```



\---



\### ❌ Error: vite no reconocido



✔ Solución:



```bash

npm install

npm run dev

```



\---



\### ❌ Error: estilos no cargaban



✔ Solución:



\* Instalar Bootstrap

\* Configurar correctamente `app.css` y `app.js`



\---



\## 📚 Referencias



\* https://laravel.com/docs

\* https://www.youtube.com

\* https://stackoverflow.com



\---



\## 📅 Fecha de Ejecución



20 de abril de 2026



\---



\## 👨‍💻 Autor



Este laboratorio ha sido desarrollado por el estudiante de la Universidad Tecnológica de Panamá:



\*\*Nombre:\*\* Yan Von Chong

\*\*Correo:\*\* yan.vonchong@utp.ac.pa

\*\*Curso:\*\* Desarrollo Web VII

\*\*Instructor del Laboratorio:\*\* Irina Fong



\---



