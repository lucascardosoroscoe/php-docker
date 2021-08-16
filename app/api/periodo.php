
<?php
    
    include('../sql.php');

    $consulta = "SELECT * FROM Requerente JOIN Usuario ON Requerente.usuario = Usuario.id ORDER BY Usuario.nome";

    $dados = selecionar($consulta);

    echo json_encode($dados);
?>