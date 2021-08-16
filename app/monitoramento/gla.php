<?php 

    include('../apiSIRIEMA/configuracoes/config.php');
    include('../apiSIRIEMA/conexao/consulta.php');
    include('../includes/config.php'); include('../includes/header.php');

    $id = $_GET["id"];
    
    //cho json_encode($array);

    echo('<div class="container-fluid fh" style="margin-top: 20px;">');
        $_SESSION['idFormulario'] = $idFormulario;
        $consultaGLA= "SELECT * FROM gla WHERE id = '$id'";
        $GLA = selecionar($consultaGLA);
        if($GLA[0] != ""){
            echo('<h2 style="text-align: center;">Monitoramento de Licenças Ambientais</h2>');
        }
        criarGLA($GLA);
        formulariosAnteriores($idDeclaracao); 
    echo('</div>');


    function criarGLA($array){
        foreach ($array as $monitoramento) {
            $id = $monitoramento['id'];

            // Calculo de Validade e Prazo
            $dataCriacao = date('d/m/Y', strtotime($monitoramento["dataCriacao"]) - 60*60*24 );
            $dataValidade = date('d/m/Y', strtotime($monitoramento["dataValidade"]));

            $tInicial = strtotime($monitoramento["dataCriacao"]) - 60*60*24;
            $frequencia = intval($monitoramento["frequencia"]);
            $tOutorga = round((strtotime($dataValidade) - $tInicial)/($frequencia*60*60*24*364));
            $hoje = time();
            $tInicialTemporario = $tInicial;
            for ($i = 1; $i <= $tOutorga; $i++){
                $tFinalTemporario = $tInicial+ ($i * 60*60*24*365);
                if($tInicialTemporario <= $hoje && $hoje <= ($tFinalTemporario + 60*60*24*15)){
                    $tInicial = $tInicialTemporario;
                    $tFinal = $tFinalTemporario;
                    // echo date('d/m/Y', $tFinal);
                } 
                // echo date('d/m/Y',$tInicialTemporario).' -> '.date('d/m/Y',$tFinalTemporario).'<br>';
                $tInicialTemporario = $tFinalTemporario;
            }
            $dataInicial =  date('d-m-Y', $tInicial);
            $dataFinal = date('d-m-Y', $tFinal);
            $prazo = date('d-m-Y', $tFinal + (60*60*24*10));
            

            echo('<div class="card mb-4">');
                echo('<div class="card-header">');
                    echo('<h5 style="float: left;">Licença nº: '.$monitoramento["licenca"].'</h5>');
                    echo('<p style="float: right;"><i class="fas fa-calendar-day"></i> '.$dataCriacao.' -> '.$dataValidade.'</p>');
                echo('</div>');
                echo('<div class="card-body">');
                    echo('<div style="float: left;">');
                        echo('<p>Prazo: '. $prazo .'</p>');
                        echo "<h5>Formulários: </h5>";
                        $consulta = "SELECT * FROM formulariosGLA JOIN formularios ON formularios.idFormularios = formulariosGLA.idFormulario WHERE `idGLA` = '$id'";
                        $formularios = selecionar($consulta);
                        $idFormulario = "";
                        foreach ($formularios as $formulario) {
                            echo "- " . $formulario['nomeFormulario'] . "<br>";
                            if($idFormulario == ""){
                                $idFormulario = $formulario['idFormulario'];
                            }else{
                                $idFormulario = $idFormulario . "," . $formulario['idFormulario'];
                            }
                        }
                        $_SESSION['idFormulario'] = $idFormulario;
                    echo('</div>');
                    echo('<a href="../formulario/" class="btn btn-primary" style="float: right;float: bottom;">Reponder Questionário '. $dataInicial .' -> '. $dataFinal .'</a>');
                echo('</div>');
            echo('</div>');
        }
    }

    function formulariosAnteriores($idDeclaracao){
        echo('<div class="card mb-4">');
            echo('<div class="card-header">');
                echo('<h5 style="float: left;">Formulários de Anos anteriores</h5>');
            echo('</div>');
            $consulta= "SELECT * FROM resFormulario WHERE declaracaoUso_id = '$idDeclaracao' ORDER BY data DESC";
            $array = selecionar($consulta);
            for($i = 1; $i < sizeof($array); $i++){
                $resFormulario = $array[$i];
                $tInicio       = $resFormulario['tInicio'];
                $tFim          = $resFormulario['tFim'];
                $dataInicio    = date('d-m-Y', $tInicio);
                $dataFinal     = date('d-m-Y', $tFim);
                echo('<div class="card-body" style="border-bottom: solid 1px #000;">');
                    echo('<p><i class="fas fa-calendar-day"></i> '. $dataInicio .' -> '. $dataFinal .'</p>');
                    echo('<a href="#" class="btn btn-primary" style="float: right;float: bottom;">Visualizar Formulário</a>');
                echo('</div>');
            }
        echo('</div>');
    }
    

    include('../includes/footer.php');
?>