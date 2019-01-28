<?php
	$connect = mysqli_connect("localhost", "admin", "admin", "banco_exporta"); // inicia conexão
	$sql = "SELECT * FROM funcionario";	//código descrito da seleção de SQL

	$result = mysqli_query($connect, $sql);  //une conexão a SQL
	
	$json_array = array();  //cria array
	
	if (mysqli_num_rows($result) != 0){ 
		while($row = mysqli_fetch_assoc($result)){
				
				$json_array["funcionarios"][]	= $row;  
				file_put_contents('db.json',json_encode($json_array, JSON_PRETTY_PRINT));
			
		}
		
		echo file_get_contents('db.json', JSON_PRETTY_PRINT); /*poderia através desse código "file_get_contents" ter colocado uma URL externa por exemplo, mas nesse caso usei uma importação local*/ 
	}else{
		echo "Não há resultados para importar";
	}
	
	mysqli_close($connect); //finaliza conexão
?>  