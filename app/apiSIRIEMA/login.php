<?php

    include('./configuracoes/config.php');
    include('./conexao/consulta.php');
    include('../includes/session.php');
    $cpf = $_POST['cpf'];
    $pass = $_POST['senha'];
    $passShaBase64 = base64_encode(sha1($pass, true));

    $consulta = "SELECT * FROM x_usr WHERE x_usr.login = '$cpf' AND x_usr.password = '$passShaBase64';";
    //echo $consulta . '<br>';
    $array = selec($consulta);
    //echo json_encode($array);
    $array = selec($consulta);
    $user = $array[0];
    if($user != ""){
        $name = $user['name'];
        $email = $user['email'];
        $administrador = $user['administrador'];
        //Descobrir o que é o Tipo e o Status
        $tipo = $user['tipo'];
        $status = $user['status'];
        //...
        $pkpessoa = $user['pkpessoa'];

        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["administrador"] = $administrador;
        $_SESSION["tipo"] = $tipo;
        $_SESSION["status"] = $status;
        $_SESSION["pkpessoa"] = $pkpessoa;
        header('Location: ../index.php');
    }else{
        $msg = "Usuário e senha incorretos";
        header('Location: ../login/index.php?msg='. $msg);
    }
?>