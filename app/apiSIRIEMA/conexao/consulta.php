<?php
function selec($consulta){
    global $serv, $port, $data, $usua, $senh;
    try
    {
        $conexao = new PDO( "sqlsrv:Server={$serv},{$port};Database={$data};", $usua, $senh );
    }
    catch ( PDOException $e )
    {
        echo "\nErro: " . $e->getMessage();
        exit;
    }
     
    $query = $conexao->prepare($consulta);
    $query->execute();
     
    $resultado = $query->fetchAll();
    return $resultado;
}

?>