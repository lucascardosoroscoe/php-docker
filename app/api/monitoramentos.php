
<?php

    include('../sql.php');
    $requerente = $_GET['id'];

    $consulta = "SELECT * FROM Monitoramento JOIN Periodicidade ON Monitoramento.periodicidade = Periodicidade.idPeriodicidade WHERE `requerente` = '$requerente' ORDER BY 'dataInicio'";

    $dados = selecionar($consulta);

    echo json_encode($dados);
?>