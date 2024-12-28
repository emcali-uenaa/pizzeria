<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Carpeta donde se guardarán las imágenes
$uploadDir = 'uploads/';

// Crear la carpeta si no existe
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Manejo de solicitudes GET y POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todos los elementos del menú
    $sql = "SELECT * FROM menu_items";
    $result = $conn->query($sql);
    $menu = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Agregar la URL completa de la imagen para el frontend
            if (!empty($row['image'])) {
                $row['image_url'] = $uploadDir . $row['image'];
            }
            $menu[] = $row;
        }
    }
    echo json_encode($menu);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos enviados desde el formulario
    $category = $_POST['category'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $imageName = '';

    // Verifica si se ha subido una imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = uniqid() . '-' . basename($_FILES['image']['name']); // Nombre único
        $imagePath = $uploadDir . $imageName;

        // Mover la imagen a la carpeta de destino
        if (!move_uploaded_file($imageTmp, $imagePath)) {
            echo json_encode(["error" => "Error al guardar la imagen en el servidor."]);
            exit;
        }
    }

    // Insertar datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO menu_items (category, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $category, $name, $description, $price, $imageName);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Elemento agregado con éxito."]);
    } else {
        echo json_encode(["error" => "Error al agregar el elemento."]);
    }
}

$conn->close();
?>
