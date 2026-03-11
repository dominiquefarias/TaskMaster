# Task Master 🎓 

<p align="center">
  <img src="https://img.shields.io/badge/Status-En%20Desarrollo-green?style=for-the-badge&logo=github" />
  <img src="https://img.shields.io/badge/Version-2.0-blue?style=for-the-badge" />
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" />
</p>

**Task Master** es una aplicación de gestión de tareas diseñada específicamente para estudiantes que necesitan organizar su carga académica de manera eficiente. El proyecto permite gestionar asignaturas, realizar un seguimiento de tareas pendientes con contadores en tiempo real y visualizar fechas clave en un calendario interactivo.

---

## 🚀 Características Principales

* **🌐 Sistema Multi-idioma**: Soporte completo para Español e Inglés mediante sesiones de usuario.
* **📚 Gestión de Asignaturas**: Permite crear y eliminar contenedores personalizados para organizar las tareas por materias.
* **⏳ Dashboard Dinámico**: Visualización de tareas con un **cronómetro en tiempo real** que calcula días, horas, minutos y segundos para la entrega.
* **🚥 Semáforo de Prioridad**: Clasificación de tareas en niveles 'Alta', 'Media' y 'Baja' con estilos visuales diferenciados.
* **📅 Calendario Académico**: Vista mensual que resalta exámenes y entregas de proyectos para una mejor planificación.
* **🔒 Seguridad**: Autenticación de usuarios con encriptación de contraseñas mediante `password_hash`.

---

## 🛠️ Tecnologías Utilizadas

<p align="left">
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/html5/html5-original-wordmark.svg" alt="html5" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/css3/css3-original-wordmark.svg" alt="css3" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/javascript/javascript-original.svg" alt="javascript" width="40" height="40"/>
</p>

* **Backend**: PHP 8.x.
* **Base de Datos**: MySQL / MariaDB.
* **Frontend**: HTML5, CSS3 y JavaScript para el contador dinámico.
* **Tipografías**: Inter, Cousine y Quicksand vía Google Fonts.

---

## 📂 Estructura del Proyecto

El código está organizado de forma modular para facilitar el mantenimiento:

* **/Inicio de sesion**: Gestión de conexión a la DB, login, registro y cierre de sesión.
* **/Pantalla principal**: Dashboard, gestión de calendario y formularios de inserción.
* **/Pantalla trasera**: Lógica interna para editar y eliminar registros.
* **/css**: Estilos independientes para cada módulo de la interfaz.
* **/database**: Script SQL para la creación de la base de datos y usuarios.
* **/idiomas.php**: Diccionario centralizado para la traducción de la interfaz.

---

## ⚙️ Instalación y Configuración

1.  **Base de Datos**: Importa el archivo `/database/db.sql` en tu servidor MySQL. Este creará la base de datos `task_master` y el usuario.
2.  **Servidor Web**: Aloja la carpeta del proyecto en tu servidor local.
3.  **Acceso**: Entra a través de `index.php` para iniciar el flujo de la aplicación.

---

<p align="center">
  <b>Desarrollado por Dominique Farías Osorio</b><br>
  <i>Proyecto Final DAM - CEAC FP Valencia</i>
</p>