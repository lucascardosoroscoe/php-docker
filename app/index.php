<?php
    include_once './includes/config.php';
    include_once './includes/header.php';
    $msg =  $_GET["msg"];
?>
<div class="container-fluid fundoImg fh">
    <img src="./assets/img/logo.png" alt="Logo SIGMA" srcset="" class="logoIni">
    <div class="card mb-4" style="background-color: transparent;">
        <div class="card-header" style="background-color:rgba(76, 175, 80, 1); color: #fff;">
            <h5>
                Olá <?php echo " ".$usuario;?>, seja bem vindo ao SIGMA!!!
            </h5>
        </div>
        <div class="card-body" style="background-color: rgba(0, 0, 0, 0.6); color: #fff;">
            <h6 style="text-indent: 1.5em;">
            O Sistema de Gestão e Monitoramento Ambiental (SIGMA) é uma ferramenta que conecta os empreendedores ao Instituto do Meio Ambiente de Mato Grosso do Sul (IMASUL), simplificando e automatizando o processo de Monitoramento Ambiental.
            <?php 
                if($tipoUsuario == ''){
                    echo 'Logado como ';
                }else if($tipoUsuario == 1){
                echo "Gerência: ";
                }else if($tipoUsuario == 2){
                echo "Fiscal: ";
                }else if($tipoUsuario == 3){
                echo "Usuário:";
                }
            ?>
            </h6>
        </div>
    </div>
</div>

<?php
    include_once './includes/footer.php';
?>