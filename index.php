<?php
session_start(); // Iniciar a sessão
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar *.cvs salvar Mysql</title>
</head>

<body>
    <h1>Importar *.CSV</h1>
    <?php
    // Apresentar a mensagem de erro ou sucesso
    if (isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
    ?>
    <form action="importcsv.php" method="post" enctype="multipart/form-data">
        <input type="file" name="arquivo" id="arquivo"><br><br>

        <input type="submit" value="Enviar">
    </form>
</body>

</html>