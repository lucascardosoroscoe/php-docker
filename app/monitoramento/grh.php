<?php 

    include('../apiSIRIEMA/configuracoes/config.php');
    include('../apiSIRIEMA/conexao/consulta.php');
    include('../includes/config.php'); include('../includes/header.php');

    $idDeclaracao = $_GET["id"];
    
    //cho json_encode($array);

    echo('<div class="container-fluid fh" style="margin-top: 20px;">');
        getVariaveisSIRIEMA($idDeclaracao);
        $idFormulario = getIdFormulario();
        $idResFormulario = getResFormulario();
        $_SESSION['idResFormulario'] = $idResFormulario;
        // echo $_SESSION['idResFormulario'];
        $_SESSION['idFormulario'] = $idFormulario;
        criarCard();
        formulariosAnteriores($idDeclaracao);
    echo('</div>');

    function getVariaveisSIRIEMA($idDeclaracao){
        $consulta= "SELECT f_DeclaracaoUso.numero AS DURH, f_DeclaracaoUso.tipoPontoInterferencia_id AS idtipo,
        f_TipoFinalidadeUso.valor AS tipoFinalidadeUso,
        f_PontoInterferencia.latitude, f_PontoInterferencia.longitude,
        f_pessoa.cnpj, f_pessoa.cpf, f_pessoa.nomePessoa as requerente,
        f_TipoPontoInterferencia.valor AS tipoPontoInterferencia,
        f_ProcessoOutorga.situacaoOutorga_id,f_ProcessoOutorgaDeclaracaoUso.processoOutorga_id,
        f_PortariaOutorga.dataEmissao, f_PortariaOutorga.dataValidade
        FROM f_DeclaracaoUso 
        JOIN f_TipoFinalidadeUso ON f_TipoFinalidadeUso.id = f_DeclaracaoUso.tipoFinalidadeUso_id
        JOIN f_PontoInterferencia ON f_PontoInterferencia.id = f_DeclaracaoUso.pontoInterferencia_id
        JOIN f_pessoa ON f_pessoa.pkpessoa = f_DeclaracaoUso.requerente_id
        JOIN f_TipoPontoInterferencia ON f_TipoPontoInterferencia.id = f_DeclaracaoUso.tipoPontoInterferencia_id
        JOIN f_ProcessoOutorgaDeclaracaoUso ON f_ProcessoOutorgaDeclaracaoUso.declaracaoUso_id = f_DeclaracaoUso.id 
        JOIN f_ProcessoOutorga ON f_ProcessoOutorga.id =  f_ProcessoOutorgaDeclaracaoUso.processoOutorga_id
        JOIN f_PortariaOutorga ON f_PortariaOutorga.outorga_id = f_ProcessoOutorga.id
        WHERE f_DeclaracaoUso.id = '$idDeclaracao'
        ORDER BY dataAprovacao DESC";
        $array = selec($consulta);
        global  $DURH, $idTipo, $dataEmissao, $dataValidade, $tipoFinalidadeUso, $latitude, $longitude, $requerente,$cp, $tipoPontoInterferencia,$tInicial, $tFinal, $dataInicial,$dataFinal, $prazo;
        $declaracaoUso = $array[0];
        $DURH = $declaracaoUso['DURH'];
        $idTipo = $declaracaoUso['idtipo'];
        $dataEmissao = $declaracaoUso['dataEmissao'];
        $dataValidade = $declaracaoUso['dataValidade'];
        $dataEmissao = date('d-m-Y', strtotime($dataEmissao));
        $dataValidade = date('d-m-Y', strtotime($dataValidade));
        $tipoFinalidadeUso = $declaracaoUso['tipoFinalidadeUso'];
        $latitude = $declaracaoUso['latitude'];
        $longitude = $declaracaoUso['longitude'];
        $requerente = $declaracaoUso['requerente'];
        $cp = $declaracaoUso['cpf'];
        if ($cp == ""){
            $cp = $declaracaoUso['cnpj'];
        }
        $tipoPontoInterferencia = $declaracaoUso['tipoPontoInterferencia'];
        $tInicial = strtotime($dataEmissao);
        $tOutorga = round((strtotime($dataValidade) - $tInicial)/(60*60*24*365));
        $hoje = time();
        $tInicialtemporatio = $tInicial;
        for ($i = 1; $i <= $tOutorga; $i++){
            $tFinaltemporatio = $tInicial + ($i * 60*60*24*365);
            if($tInicialtemporatio <= $hoje && $hoje <= ($tFinaltemporatio + 60*60*24*10)){
                $tInicial = $tInicialtemporatio;
                $tFinal = $tFinaltemporatio;
            } 
            $tInicialtemporatio = $tFinaltemporatio;
        }
        $dataInicial =  date('d-m-Y', $tInicial);
        $dataFinal = date('d-m-Y', $tFinal);
        $prazo = date('d-m-Y', $tFinal + (60*60*24*10));
    }

    function getIdFormulario(){
        global $idTipo;
        switch ($idTipo) {
            case 0:
                $idFormulario = "6";
                break;
            case 1:
                $idFormulario = "8,9";
                break;
            case 2:
                $idFormulario = getFormSuperficial();
                break;
            case 3:
                $idFormulario = "7";
                break;
        }
        return $idFormulario;
    }

    function getResFormulario(){
        global $idFormulario, $idDeclaracao, $tInicial, $tFinal;
        $consulta = "SELECT `id` FROM `resFormulario` WHERE `idFormulario` = '$idFormulario' AND `declaracaoUso_id`=$idDeclaracao AND `tipo`='GRH' AND `tInicio`= $tInicial AND `tFim` =$tFinal";
        $array = selecionar($consulta);
        $idResFormulario = $array[0]['id'];
        if ($idResFormulario == ""){
            $consulta = "INSERT INTO `resFormulario`(`idFormulario`, `declaracaoUso_id`, `tipo`, `tInicio`, `tFim`) VALUES ('$idFormulario', '$idDeclaracao', 'GRH', '$tInicial', '$tFinal')";
            $msg = executar($consulta);
            $consulta = "SELECT `id` FROM `resFormulario` WHERE `idFormulario` = '$idFormulario', `declaracaoUso_id`='$idDeclaracao', `tipo`='GRH', `tInicio`= '$tInicial',`tFim` ='$tFinal'";
            $array = selecionar($consulta);
            $idResFormulario = $array[0]['id'];
        }
        return $idResFormulario;
    }

    function criarCard(){
        global $idDeclaracao, $DURH, $idTipo, $dataEmissao, $dataValidade, $tipoFinalidadeUso, $latitude, $longitude, $requerente,$cp, $tipoPontoInterferencia, $dataInicial,$dataFinal, $prazo, $idResFormulario;
        echo('<div class="card mb-4">');
            echo('<div class="card-header">');
                echo('<h5 style="float: left;">Declaração de Uso nº: '.$idDeclaracao.'. '.$DURH.'</h5>');
                echo('<p style="float: right;"><i class="fas fa-calendar-day"></i> '.$dataEmissao.' -> '.$dataValidade.'</p>');
                echo('</div>');
            echo('<div class="card-body">');
                echo('<div style="float: left;">');
                    echo('<p>'.$tipoPontoInterferencia.'</p>');
                    echo('<p>Finalidade de Uso: '.$tipoFinalidadeUso.'</p>');
                    echo('<p><i class="fas fa-user"></i> '.$requerente.' ('.$cp.')</p>');
                    echo('<p><i class="fas fa-map-marked-alt"></i> '.$latitude.' , '.$longitude.'</p>');
                    echo('<p><i class="fas fa-exclamation-triangle"></i> Prazo máximo de resposta até ' . $prazo .' </p>');
                echo('</div>');
                echo('<a href="../formulario/" class="btn btn-primary" style="float: right;float: bottom;">Reponder Questionário '. $dataInicial .' -> '. $dataFinal .'</a>');
            echo('</div>');
        echo('</div>');
    }

    function getFormSuperficial(){
        return "1,2,3,4,5";
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