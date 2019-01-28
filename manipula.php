<?php
	if(!array_key_exists('path', $_GET)){ // verifica se existe o path na URL
		echo 'Erro,caminho não encontrado';
		exit;
	}
	
	$path = explode('/', $_GET['path']);//pega resultados de GET
	
	if(count($path)==0 || $path[0] == ""){ //caso path não encontrar indicador não retorna resultados
		echo 'Erro, caminho não encontrado';
		exit;
	}
	
	$parametro = "";
	
	if(count($path)>1){
		$parametro = $path[1];
	}
  
	$contents = file_get_contents('db.json');
	
	$json = json_decode($contents, true);
	
	$metodo = $_SERVER['REQUEST_METHOD']; //metodo a ser retornado em HTML
	//header('Content-type: application/json'); //carrega json_server (servidor json)
	$body = file_get_contents('php://input'); 
  
	function findById($vector, $parametro){//encontrando ID e atribuindo a var $encontrado
		$encontrado = -1;
		foreach($vector as $key => $obj){          
			if($obj['id'] == $parametro){
				$encontrado = $key;
				break;
			}
		}
		return $encontrado;
	}
	
	if($metodo === 'GET'){//Busca resultados tudo ou ID especifico
		if($json[$path[0]]){
			if($parametro==""){
				echo json_encode($json[$path[0]],JSON_PRETTY_PRINT);//caso não especificado qual id retorna todo o db.json
			}else{
				$encontrado = findById($json[$path[0]], $parametro); 
				if($encontrado>=0){
					echo json_encode($json[$path[0]][$encontrado],JSON_PRETTY_PRINT);//caso $encontrado tiver resultados para a chave id retorna especifico
				}else{
					echo 'GET: id não existe ou parametro pesquisado não esperada.';
					exit;
				}
			}
		}else{
			echo '[]';//db.json não tem dados
		}
	}
	
	if($metodo === 'POST'){ //adiciona um novo registro
		$jsonBody = json_decode($body, true);
		$jsonBody['id'] = time(); //
    
		if(!$json[$path[0]]){
			$json[$path[0]] = [];
		}
    
		$json[$path[0]][] = $jsonBody;
		echo json_encode($jsonBody);
		file_put_contents('db.json', json_encode($json, JSON_PRETTY_PRINT));//insere no db.json
	}
	
	if($metodo === 'DELETE'){//Deleta um registro existente se encontrado o ID
		if($json[$path[0]]){
			if($parametro==""){
				echo 'DELETE: Não foi passado id para deletar.';
			}else{
				$encontrado = findById($json[$path[0]], $parametro);
				if($encontrado>=0){
					echo json_encode($json[$path[0]][$encontrado]);
					unset($json[$path[0]][$encontrado]);
					file_put_contents('db.json', json_encode($json, JSON_PRETTY_PRINT));
				}else{
					echo 'DELETE: Id não encontrado ou parametro pesquisado não esperado.';
					exit;
				}
			}
		}else{
			echo 'error.';
		}
	}
  
	if($metodo === 'PUT'){//Alterar um resultado existente se encontrado o ID
		if($json[$path[0]]){
			if($parametro==""){
				echo 'PUT: Não foi passado id para alterar.';
			}else{
				$encontrado = findById($json[$path[0]], $parametro);
				if($encontrado>=0){
					$jsonBody = json_decode($body, true);
					$jsonBody['id'] = $parametro;
					$json[$path[0]][$encontrado] = $jsonBody;
					echo json_encode($json[$path[0]][$encontrado]);
					file_put_contents('db.json', json_encode($json,JSON_PRETTY_PRINT));
				}else{
					echo 'PUT: Id não encontrado ou parametro pesquisado não esperado.';
					exit;
				}
			}
		}else{
			echo 'PUT: algo deu errado ocorreu um erro.';
		}
	}
 ?>