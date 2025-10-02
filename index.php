<?php
//define("RUTA", "http://localhost/lab01_p02_msez/");
define("RUTA", "http://localhost/CICLO8_Desarrollo_Web_Multiplataforma/lab01_p02_msez/");
//archivos de configuracion
require_once "config/rutas.php";
session_start();

// Redirige al login si no está autenticado y no está accediendo al login
if (
    !isset($_SESSION['usuario']) &&
    (!isset($_GET['url']) || strpos($_GET['url'], 'login') !== 0)
) {
    header("Location: " . RUTA . "login");
    exit;
}

//objetos 

$contenido = new Contenido();

?>

<!doctype html>
<html lang="es">

<head>
    <title>Refugio de Mascotas - Encuentra tu compañero perfecto</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e8098;
            --secondary-color: #f4a261;
            --accent-color: #2a9d8f;
        }

        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #248277;
            border-color: #248277;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?= RUTA; ?>">
                    <i class="fas fa-paw me-2"></i>
                    <span>Refugio Amigos Fieles</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavId">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RUTA; ?>estadisticas">
                                <i class="fas fa-chart-pie me-1"></i> Estadísticas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RUTA; ?>mascota">
                                <i class="fas fa-dog me-1"></i> Mascotas en Adopción
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RUTA; ?>tipomascota">
                                <i class="fas fa-tags me-1"></i> Categorías
                            </a>
                        </li>
                        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Administrador'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RUTA; ?>adopcion">
                                    <i class="fas fa-heart me-1"></i> Solicitudes de Adopción
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <li class="nav-item">
                                <span class="nav-link">Hola, <?= $_SESSION['usuario']['nombre']; ?> (<?= $_SESSION['usuario']['rol']; ?>)</span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RUTA; ?>login/logout">
                                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RUTA; ?>login">
                                    <i class="fas fa-sign-in-alt me-1"></i> Iniciar sesión
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

    </header>
    <main>

        <div class="container mt-4">
            <!-- place main content here -->
            <?php
            if (isset($_GET["url"])) {

                $datos = explode("/", $_GET["url"]);
                $pagina = $datos[0];
                $accion = $datos[1] ?? "index";


                //return;
                require_once $contenido->obtenerContenido($pagina);

                $nombreClase = $pagina . "controller";
                if (class_exists($nombreClase)) {
                    $controlador = new $nombreClase();

                    if (method_exists($controlador, $accion)) {

                        if (isset($datos[2])) {
                            $controlador->{$accion}($datos[2]);
                        } else {
                            $controlador->{$accion}();
                        }
                    }
                } else {
                    require_once "vistas/404.php";
                }
            } else {
                require_once "vistas/inicio.php";
            }
            ?>
        </div>



    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>