<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<title>Confirmação dos dados atualizados.</title>
	</head>
<body>
<div class="container">
<?php
error_reporting(0);
if(empty($_POST) && isset($_SERVER["REQUEST_METHOD"])){ 
	setcookie("erro_1", "<h4 class='text-center'>Erro ao atualizar seus dados tamanho máximo para arquivos é 2MB</h4>");
	print "<meta HTTP-EQUIV='Refresh' CONTENT='0;URL=atual_dados_cad.php'>";
}
else{
	include_once('valida_cookies.php');
	include_once('valida_cpf.php');
	include_once('conn.php');	
	$mensagem_erro = "";
	$erro = 0;

	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
	$CPF = filter_input(INPUT_POST, 'CPF', FILTER_SANITIZE_STRING);
	$data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
	$cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
	$estado = filter_input(INPUT_POST, 'uf', FILTER_SANITIZE_STRING);
	$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
	$obs = filter_input(INPUT_POST, 'obs', FILTER_SANITIZE_STRING);
	$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
	
	if (empty($nome) || trim($nome) == null){
		$mensagem_erro=$mensagem_erro."<p>Favor digitar seu nome</p>"; 
		$erro = $erro+1;  
    }
	else{
		if (isset($_COOKIE["nome_usuario"])){
			setcookie("nome_usuario");
			setcookie("nome_usuario", $nome);
		}
		else{
			$mensagem_erro=$mensagem_erro."<p>Favor digitar seu nome</p>"; 
			$erro = $erro+1; 
		}
	}
	
	if (empty($CPF) || verifica($CPF)){ 
		$mensagem_erro=$mensagem_erro."<p>Erro ao validar CPF</p>"; 
		$erro = $erro+1;	
	}
	
	$data =  date("Y/m/d H:i",strtotime($data));
	implode('-', array_reverse(explode('/', $data)));
	
	if (empty($endereco) || strstr($endereco, ' ') == true){
		$mensagem_erro = $mensagem_erro."<p>Favor digitar seu endereço</p>"; 
		$erro = $erro+1;  
    }
	
	if(empty($cidade) || trim($cidade) == null){
		$mensagem_erro = $mensagem_erro."<p>Favor digitar a cidade</p>"; 
		$erro = $erro+1;  
    }
	
	//arquivo imagem
	$destino = "";
	$nomeImage = "";
	$rowimg = "SELECT imagem FROM cadastro2 WHERE id = '$id'";
	$diretorio_antigo_img = mysqli_query($conn, $rowimg);
	$rows_img = mysqli_fetch_array($diretorio_antigo_img);
	$destinoimg = $rows_img["imagem"];
	
	if(isset($_FILES["arquivo"] ["name"]) && $_FILES["arquivo"]["error"]==0){
		$arquivo_tmp=$_FILES["arquivo"]["tmp_name"];
        $nomeImage=$_FILES["arquivo"]["name"];
		$extensao = pathinfo($nomeImage, PATHINFO_EXTENSION);
		$extensao = strtolower($extensao);
		if(strstr(".jpg;.jpeg;.gif;.png", $extensao)){
			$novoNome= uniqid(time()).'.'.$extensao;
			$destino = "imagens/".$novoNome;
		}
		else{
			$mensagem_erro = $mensagem_erro."<p>Apenas arquivos .jpg .jpeg .gif .png</p>";
			$erro = $erro+1;  
		}
	}
	else{
		$destino = $destinoimg;
		$nomeImage = $destino;
	}
	
	//arquivo pdf
	$destinodoc = "";
	$namedoc = "";
	$row = "SELECT pdf FROM cadastro2 WHERE id = '$id' ";
	$diretorio_antigo_pdf = mysqli_query($conn, $row);
	$rows_pdf = mysqli_fetch_array($diretorio_antigo_pdf);
	$destinodocant = $rows_pdf["pdf"];
	
	if(isset($_FILES["pdf"] ["name"]) && $_FILES["pdf"]["error"]==0){
		$arquivo_tmp_doc=$_FILES["pdf"]["tmp_name"];
		$namedoc= $_FILES["pdf"] ["name"];
		$extensao_doc= pathinfo($namedoc, PATHINFO_EXTENSION);
		$extensao_doc= strtolower($extensao_doc);
		if(strstr(".pdf", $extensao_doc)){
			$novoNomeDoc= uniqid(time()).'.'.$extensao_doc;
			$destinodoc = "documentos/".$novoNomeDoc;
		}
		else{
			$mensagem_erro = $mensagem_erro."<p>Apenas arquivo .pdf</p>";
			$erro = $erro+1;
		}
	}
	else{
		$destinodoc = $destinodocant;
		$namedoc = $destinodoc;
	}
	
	$area = "";
	if (isset($_POST["interesse"])){  
		foreach($_POST["interesse"] as $areas){    
			$area .= $areas.",";
		}
		$area = substr($area, 0, -1);
	}
	else{
        $mensagem_erro = $mensagem_erro."<p>Favor selecionar área de interesse</p>"; 
		$erro = $erro+1;
    }
	
	if (empty($senha) || trim($senha) == null){
		$mensagem_erro = $mensagem_erro."<p>Favor digitar sua senha</p>"; 
		$erro = $erro+1; 
    }
	else if (!password_verify($senha, $senha_usuario)){
		$mensagem_erro = $mensagem_erro."<p>Favor digitar sua senha corretamente</p>"; 
		$erro = $erro+1;  
	}
?>
<div class="row">
<div class="col-12 col-lg-6 order-2 order-lg-1">
<?php
	echo '<pre>';
	echo "Aqui está mais informações de debug:!\n";
	print_r($_FILES);
	print "</pre>";	
?>
</div>
<div class="col-12 col-lg-6 order-1 order-lg-2">
<?php
if ($erro >= 1)
{
	print "<b>Seu formulário contém $erro erro(s) descritos (s) a seguir:<b>";
	print $mensagem_erro;
	print "<INPUT TYPE='button' VALUE='Corrigir erros' class='btn btn-sm btn-primary' ONCLICK='javascipt:history.go(-1)'>";
}
else{
	if($_FILES["arquivo"]["type"] == "image/jpeg"){
		unlink("$destinoimg");
		@move_uploaded_file($arquivo_tmp, $destino);
	}
	else if($_FILES["arquivo"]["type"] == "image/gif"){
		print unlink("$destinoimg");
		@move_uploaded_file($arquivo_tmp, $destino);
	}
	else if($_FILES["arquivo"]["type"] == "image/png"){
		print unlink("$destinoimg");
		@move_uploaded_file($arquivo_tmp, $destino);
	}
	
	if($_FILES["pdf"]["type"] == "application/pdf"){
		unlink("$destinodocant");
		@move_uploaded_file($arquivo_tmp_doc, $destinodoc);
	}
	
	echo "<h4>Dados cadastrados com sucesso</h4><br>";
    echo "<b>Nome: </b>$nome<br>";
	echo "<b>CPF: </b>$CPF<br>";
	echo "<b>Data de nascimento: </b>" . date("d/m/Y H:i",strtotime($data))."<br>";
	echo "<b>CPF: </b>$email<br>";
    echo "<b>Endereço: </b>$endereco<br>";
    echo "<b>Cidade: </b>$cidade<br>";
    echo "<b>Estado: </b>$estado<br>";
	echo "<b>Nome Imagem: </b>$nomeImage<br>";
	echo "<b>Arquivo texto: </b>$namedoc<br>";
	echo "<b>Sexo: </b>$sexo<br>";
    echo "<b>Área: </b>$area<br>";
    echo "<b>Observações: </b>$obs<br>";
	$query = mysqli_query($conn, "UPDATE cadastro2 SET nome = '$nome', CPF = '$CPF', Data = '$data', email = '$email', endereco = '$endereco', cidade = '$cidade', estado = '$estado', imagem = '$destino', pdf = '$destinodoc', sexo = '$sexo', area = '$area', obs = '$obs' WHERE id = '$id'");
	mysqli_close($conn);
	echo "<br><a href='dados_cadastrados.php'><button type='button' class='btn btn-sm btn-primary'>Visualizar</button>";
	}
}
?>
</div>
</div>
</div>
</body>
</html>