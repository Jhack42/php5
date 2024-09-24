<?php
// Cargar el archivo de configuración .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!empty($value)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    return true;
}

$envFile = __DIR__ . '/../.env';
loadEnv($envFile);

// Función para obtener la conexión a la base de datos
function getConnection() {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_DATABASE') ?: 'php5';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

    $conn = mysqli_connect($host, $username, $password, $database, (int)$port);

    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error() . " - Host: $host, Usuario: $username, Puerto: $port");
    }

    return $conn;
}

// Función para cargar las vistas
function load_view($view) {
    // Define la ruta de la vista
    $view_file = __DIR__ . '/../app/Views/' . $view . '.php';
    
    // Verifica si el archivo existe
    if (file_exists($view_file)) {
        include $view_file;
    } else {
        echo "La vista no existe.";
    }
}



// Función para cargar los controladores
function load_controller($controller) {
    $controller_file = __DIR__ . '/../app/Controllers/' . $controller . '.php';
    
    if (file_exists($controller_file)) {
        include $controller_file;
    } else {
        echo "El controlador no existe.";
    }
}

// Función para cargar los modelos
function load_model($model) {
    $model_file = __DIR__ . '/../app/Models/' . $model . '.php';
    
    if (file_exists($model_file)) {
        include $model_file;
    } else {
        echo "El modelo no existe.";
    }
}

// Verifica si se ha enviado la variable 'controller'
if (isset($_GET['controller'])) {
    $controller = $_GET['controller'];
    load_controller($controller);  // Carga el controlador correspondiente
} elseif (isset($_GET['view'])) {
    $view = $_GET['view'];
    load_view($view);  // Carga la vista correspondiente
} else {
    // Controlador o vista por defecto
    load_view('casa'); // Vista predeterminada
}
?>
