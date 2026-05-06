\# 📘 Laboratorio PSR-4 con Composer



\## 🏫 Universidad Tecnológica de Panamá

\*\*Facultad de Ingeniería de Sistemas Computacionales\*\*  

\*\*Desarrollo de Software VII\*\*  



\*\*Laboratorio:\*\* Implementación de la Carga Automática (Autoload) bajo el estándar PSR-4 con Composer.



\---



\# 📌 Descripción



Este proyecto demuestra el uso del estándar \*\*PSR-4\*\* en PHP utilizando \*\*Composer Autoload\*\* para la carga automática de clases mediante Namespaces.



El laboratorio permite comprender cómo organizar aplicaciones PHP modernas sin utilizar múltiples instrucciones `include` o `require`.



\---



\# 🎯 Objetivos



\- Aplicar el estándar PSR-4.

\- Configurar Composer para carga automática.

\- Organizar clases mediante Namespaces.

\- Utilizar `composer dump-autoload`.

\- Eliminar dependencias manuales con `include` y `require`.



\---



\# 🛠️ Tecnologías Utilizadas



\- PHP

\- Composer

\- PSR-4

\- Apache (WAMP)



\---



\# 📂 Estructura del Proyecto



```plaintext

lab\_psr4/

│

├── composer.json

├── index.php

├── README.md

├── .gitignore

├── vendor/

└── src/

&#x20;   ├── Models/

&#x20;   │   └── Usuario.php

&#x20;   └── Services/

&#x20;       └── Saludo.php

