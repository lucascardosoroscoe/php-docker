<?php 
    include('../apiSIRIEMA/configuracoes/config.php');
    include('../apiSIRIEMA/conexao/consulta.php');
    include('../includes/config.php'); 
    include('../includes/header.php');

    //Tornar dinâmica com os dados do requerente
    $requerente = '5185';
    $requerenteCP = '03.982.931/0001-20'; 
    //Data mínima
    $dataMinima = '2019-01-01 00:00:00';
    $consultaGRH= "SELECT 
    f_DeclaracaoUso.id AS idDeclaracao, f_DeclaracaoUso.numero AS DURH, f_DeclaracaoUso.tipoPontoInterferencia_id AS idtipo,
    f_TipoFinalidadeUso.valor AS tipoFinalidadeUso,
    f_PontoInterferencia.latitude, f_PontoInterferencia.longitude,
	f_pessoa.cnpj, f_pessoa.cpf, f_pessoa.nomePessoa as requerente,
    f_TipoPontoInterferencia.valor AS tipoPontoInterferencia,
    f_ProcessoOutorga.situacaoOutorga_id,
    f_PortariaOutorga.dataEmissao, f_PortariaOutorga.dataValidade
    FROM f_DeclaracaoUso 
    JOIN f_TipoFinalidadeUso ON f_TipoFinalidadeUso.id = f_DeclaracaoUso.tipoFinalidadeUso_id
    JOIN f_PontoInterferencia ON f_PontoInterferencia.id = f_DeclaracaoUso.pontoInterferencia_id
    JOIN f_pessoa ON f_pessoa.pkpessoa = f_DeclaracaoUso.requerente_id
    JOIN f_TipoPontoInterferencia ON f_TipoPontoInterferencia.id = f_DeclaracaoUso.tipoPontoInterferencia_id
    JOIN f_ProcessoOutorgaDeclaracaoUso ON f_ProcessoOutorgaDeclaracaoUso.declaracaoUso_id = f_DeclaracaoUso.id 
    JOIN f_ProcessoOutorga ON f_ProcessoOutorga.id =  f_ProcessoOutorgaDeclaracaoUso.processoOutorga_id
    JOIN f_PortariaOutorga ON f_PortariaOutorga.outorga_id = f_ProcessoOutorga.id
	WHERE f_ProcessoOutorga.situacaoOutorga_id = '2' AND f_ProcessoOutorga.dataAprovacao > '$dataMinima' AND f_DeclaracaoUso.requerente_id = '$requerente'
    ORDER BY dataAprovacao DESC";
    $consultaGLA= "SELECT * FROM gla WHERE idEmpresa = '$requerenteCP'";
    
    
    echo('<div class="container-fluid fh" style="margin-top: 20px;">');
        // $GLA = selecionar($consultaGLA);
        // if($GLA[0] != ""){
        //     echo('<h2 style="text-align: center;">Monitoramento de Licenças Ambientais</h2>');
        // }
        // criarGLA($GLA);
        $GRH = selec($consultaGRH);
        if($GRH[0] != ""){
            echo('<h2 style="text-align: center;">Monitoramento de Recursos Hídricos</h2>');
        }
        criarGRH($GRH);
    echo('</div>');

    function criarGLA($array){
        foreach ($array as $monitoramento) {
            $id = $monitoramento['id'];
            echo('<div class="card mb-4">');
                echo('<div class="card-header">');
                    echo('<h5 style="float: left;">Licença nº: '.$monitoramento["licenca"].'</h5>');
                    echo('<p style="float: right;"><i class="fas fa-calendar-day"></i> '.$monitoramento["dataCriacao"].' -> '.$monitoramento["dataCriacao"].'</p>');
                echo('</div>');
                echo('<div class="card-body">');
                    echo('<div style="float: left;">');
                    echo "<h5>Formulários: </h5>";
                        $consulta = "SELECT * FROM formulariosGLA JOIN formularios ON formularios.idFormularios = formulariosGLA.idFormulario WHERE `idGLA` = '$id'";
                        $formularios = selecionar($consulta);
                        foreach ($formularios as $formulario) {
                            echo "- " . $formulario['nomeFormulario'] . "<br>";
                        }
                    echo('</div>');
                    echo('<a href="./gla.php?id='.$id.'" class="btn btn-primary" style="float: right;float: bottom;">Questionários de Monitoramento</a>');
                echo('</div>');
            echo('</div>');
        }
    }
    function criarGRH($array){
        foreach($array as $declaracaoUso){
            $idDeclaracao = $declaracaoUso['idDeclaracao'];
            $DURH = $declaracaoUso['DURH'];
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
                    echo('</div>');
                    echo('<a href="./grh.php?id='.$idDeclaracao.'" class="btn btn-primary" style="float: right;float: bottom;">Questionários de Monitoramento</a>');
                echo('</div>');
            echo('</div>');
        }
    }
    include('../includes/footer.php');
?>