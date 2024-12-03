<?php
$config = include 'config.php'; // Se incluye el archivo 'config.php' que contiene la configuración de la base de datos.

$resultado = [ // Se define un arreglo asociativo '$resultado' para almacenar el estado de la operación y el mensaje relacionado.
  'error' => false, // Inicializa el valor 'error' como false, lo que indica que no ha ocurrido ningún error.
  'mensaje' => '' // Inicializa el mensaje de error como vacío.
];

if (!isset($_GET['id'])) { // Verifica si no se ha proporcionado el parámetro 'id' en la URL.
  $resultado['error'] = true; // Si no existe el 'id', se marca el error como true.
  $resultado['mensaje'] = 'El aprendiz no existe'; // Se establece un mensaje de error.
}

if (isset($_POST['submit'])) { // Si el formulario ha sido enviado (presionando el botón 'submit').
  try {
    // Se establece la conexión con la base de datos usando PDO.
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name']; // Se crea la cadena DSN para la conexión a la base de datos.
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']); // Se establece la conexión usando PDO.

    // Se preparan los datos del aprendiz para la actualización.
    $aprendiz = [
      "id"        => $_GET['id'], // Se obtiene el 'id' del aprendiz desde la URL.
      "nombre"    => $_POST['nombre'], // Se obtiene el nombre desde el formulario.
      "apellido"  => $_POST['apellido'], // Se obtiene el apellido desde el formulario.
      "email"     => $_POST['email'], // Se obtiene el email desde el formulario.
      "edad"      => $_POST['edad'] // Se obtiene la edad desde el formulario.
    ];
    
    // Se prepara la consulta SQL para actualizar los datos del aprendiz en la base de datos.
    $consultaSQL = "UPDATE aprendices SET
        nombre = :nombre,
        apellido = :apellido,
        email = :email,
        edad = :edad,
        updated_at = NOW()
        WHERE id = :id"; // La consulta SQL actualiza los datos del aprendiz y marca la fecha de actualización.

    $consulta = $conexion->prepare($consultaSQL); // Se prepara la consulta SQL.
    $consulta->execute($aprendiz); // Se ejecuta la consulta con los datos proporcionados.

  } catch(PDOException $error) { // Si ocurre un error durante la operación de base de datos, se captura la excepción.
    $resultado['error'] = true; // Se marca el error como true.
    $resultado['mensaje'] = $error->getMessage(); // Se almacena el mensaje de error.
  }
}

try {
  // Se establece la conexión con la base de datos nuevamente.
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name']; // Se crea la cadena DSN.
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']); // Se establece la conexión.

  $id = $_GET['id']; // Se obtiene el 'id' del aprendiz desde la URL.
  $consultaSQL = "SELECT * FROM aprendices WHERE id =" . $id; // Se prepara la consulta SQL para obtener los datos del aprendiz con el 'id' proporcionado.

  $sentencia = $conexion->prepare($consultaSQL); // Se prepara la sentencia SQL.
  $sentencia->execute(); // Se ejecuta la sentencia SQL.

  $aprendiz = $sentencia->fetch(PDO::FETCH_ASSOC); // Se obtiene el primer registro de la consulta, el cual debe ser el aprendiz con el 'id' especificado.

  if (!$aprendiz) { // Si no se encuentra el aprendiz, se marca el error.
    $resultado['error'] = true; // Se marca el error como true.
    $resultado['mensaje'] = 'No se ha encontrado el aprendiz'; // Se establece el mensaje de error.
  }

} catch(PDOException $error) { // Si ocurre un error durante la consulta, se captura la excepción.
  $resultado['error'] = true; // Se marca el error como true.
  $resultado['mensaje'] = $error->getMessage(); // Se almacena el mensaje de error.
}
?>

<?php require "templates/header.php"; ?> <!-- Se incluye el archivo 'header.php' para mostrar el encabezado de la página. -->

<?php
if ($resultado['error']) { // Si se ha producido un error, se muestra un mensaje de error.
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?> <!-- Se muestra el mensaje de error almacenado en la variable $resultado. -->
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) { // Si el formulario ha sido enviado y no hubo errores, se muestra un mensaje de éxito.
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          El aprendiz ha sido actualizado correctamente <!-- Se muestra un mensaje de éxito. -->
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($aprendiz) && $aprendiz) { // Si los datos del aprendiz fueron obtenidos correctamente.
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el aprendiz <?= escapar($aprendiz['nombre']) . ' ' . escapar($aprendiz['apellido'])  ?></h2> <!-- Se muestra el título con el nombre y apellido del aprendiz. -->
        <hr>
        <form method="post"> <!-- Se crea un formulario para editar los datos del aprendiz. -->
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($aprendiz['nombre']) ?>" class="form-control"> <!-- Campo para editar el nombre del aprendiz. -->
          </div>
          <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="<?= escapar($aprendiz['apellido']) ?>" class="form-control"> <!-- Campo para editar el apellido del aprendiz. -->
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= escapar($aprendiz['email']) ?>" class="form-control"> <!-- Campo para editar el email del aprendiz. -->
          </div>
          <div class="form-group">
            <label for="edad">Edad</label>
            <input type="text" name="edad" id="edad" value="<?= escapar($aprendiz['edad']) ?>" class="form-control"> <!-- Campo para editar la edad del aprendiz. -->
          </div>
          <div class="form-group">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar"> <!-- Botón para enviar el formulario y actualizar el aprendiz. -->
            <a class="btn btn-primary" href="index.php">Regresar al inicio</a> <!-- Enlace para regresar a la página de inicio. -->
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php require "templates/footer.php"; ?> <!-- Se incluye el archivo 'footer.php' para mostrar el pie de página de la página. -->
