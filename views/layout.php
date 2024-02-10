<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title> <!-- jQuery -->
    <link rel="icon" type="image/x-icon" href="build/img/Sello_Corte_Suprema_de_Justicia_de_El_Salvador_(2021).png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Salsa&display=swap" rel="stylesheet">

    <!-- Vincular hojas de estilo CSS -->
    <link rel="stylesheet" href="build/css/estilosdatatables.css">
    <link rel="stylesheet" href="build/css/buttons-dataTables.min.css">
    <link rel="stylesheet" href="build/css/font-awesome.min.css">

    <!-- Vincular jQuery -->

    <!-- Vincular DataTables Buttons -->

    <link rel="stylesheet" href="build/css/app.css">

    <link href="build/css/bootstrap.min.css" rel="stylesheet">
    <script src="build/js/bootstrap.bundle.min.js"></script>

    <!-- Vincular jsPDF y jsPDF-AutoTable -->
    <script src="build/js/jspdf.umd.min.js"></script>
    <script src="build/js/jspdf.plugin.autotable.min.js"></script>

</head>

<body>
    <?php
    include_once __DIR__ . '/templates/header.php';
    echo $contenido;
    include_once __DIR__ . '/templates/footer.php';
    ?>
  

</body>

</html>