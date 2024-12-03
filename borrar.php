<?php

$config = include 'config.php';

$resultado = [
  'error' => false,
  'mensaje' => ''
];

try {
  // Verificar si el parámetro `id` está presente y es numérico
  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id']; // Convertir a entero para mayor seguridad

    // Conexión a la base de datos
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    // Preparar la consulta para evitar inyección SQL
    $consultaSQL = "DELETE FROM aprendices WHERE id = :id";
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->bindParam(':id', $id, PDO::PARAM_INT);

    // Ejecutar la consulta
    $resultadoBorrado = $sentencia->execute();

    if ($resultadoBorrado) {
      // Redirigir si se borra correctamente
      header('Location: /index.php?mensaje=Eliminado correctamente');
      exit;
    } else {
      $resultado['error'] = true;
      $resultado['mensaje'] = 'No se pudo borrar el registro.';
    }
  } else {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'ID no válido.';
  }
} catch (PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<div class="container mt-2">
  <div class="row">
    <div class="col-md-12">
      <?php if ($resultado['error']) : ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($resultado['mensaje']) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require "templates/footer.php"; ?>
