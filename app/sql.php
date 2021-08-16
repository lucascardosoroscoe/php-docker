<?php
     
     $servidor = '185.201.11.228:3306';
     $senha ='@Baba123258';
     $usuario ='u989688937_sigma';
     $bdados ='u989688937_sigma';

     
     function verificar($consulta){
          global $servidor, $usuario, $senha, $bdados;
          $conexao = mysqli_connect($servidor, $usuario, $senha, $bdados);
          $gravacoes = mysqli_query($conexao, $consulta);
          $dados = array();
          while($linha = mysqli_fetch_assoc($gravacoes)){
              $dados[] = $linha; 
          }
          if (empty ($dados)){
               $msg = "Sucesso!";
          }else{
               $msg = "Já cadastrado!";
          }
          mysqli_close($conexao);
          $json = json_encode($dados); 
          echo $json;
          return $msg;
     }
     
     function executar($consulta){
          global $servidor, $usuario, $senha, $bdados;
          $conexao = mysqli_connect($servidor, $usuario, $senha, $bdados);
          if(mysqli_query($conexao, $consulta))
          {
               $msg = "Sucesso!";
          }
          else
          {
               $msg = "Falha!";
          }
          mysqli_close($conexao);
          return $msg;
     }
     
     function selecionar($consulta){
          global $servidor, $usuario, $senha, $bdados;
          //echo $servidor
          $conexao = mysqli_connect($servidor, $usuario, $senha, $bdados);

          $gravacoes = mysqli_query($conexao, $consulta);

          $dados = array();
          while($linha = mysqli_fetch_assoc($gravacoes))
          {
          $dados[] = $linha; 
          }
          mysqli_close($conexao);
          return $dados;
     }
/*

     function getIdManejo($consulta){
          global $servidor, $usuario, $senha, $bdados;
          $conexao = mysqli_connect($servidor, $usuario, $senha, $bdados);
          $query = "SELECT (max(id) + 1) from Manejos;";
          $gravacoes = mysqli_query($conexao, $query);

          $dados = array();
          while($linha = mysqli_fetch_assoc($gravacoes))
          {
          $dados[] = $linha; 
          }
          if(mysqli_query($conexao, $consulta))
          {
               $msg = "Sucesso!";
          }
          else
          {
               $msg = "Falha!";
          }
          $dados2 = $dados[0];
          $id = $dados2['(max(id) + 1)'];
          
          return $id;
          mysqli_close($conexao);
     }


     function getIdSafra($consulta){
          global $servidor, $usuario, $senha, $bdados;
          $conexao = mysqli_connect($servidor, $usuario, $senha, $bdados);
          $query = "SELECT (max(id) + 1) from Safra;";
          $gravacoes = mysqli_query($conexao, $query);

          $dados = array();
          while($linha = mysqli_fetch_assoc($gravacoes))
          {
          $dados[] = $linha; 
          }
          if(mysqli_query($conexao, $consulta))
          {
               $msg = "Sucesso!";
          }
          else
          {
               $msg = "Falha!";
          }
          $dados2 = $dados[0];
          $id = $dados2['(max(id) + 1)'];
          
          return $id;
          mysqli_close($conexao);
     }
     
*/
     

?>