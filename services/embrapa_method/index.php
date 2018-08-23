<?php

//mostra os erros
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);


include("../../classes/RecosService.php");
// exit('deu certo');




class EmbrapaMethod extends RecosService{

	function __construct(){
	
		// echo 'criou embrapaRepository <Br />';


		if( isset($_GET['key']) ){
			$token = $_GET['key'];
		} else {
			//token inválido
			echo '[{"error": "token invalid"}]';
			exit;
		}

		if( isset($_GET['userid']) ){
			$userid = $_GET['userid'];
		} else {
			//user inválido
			echo '[{"error": "user invalid"}]';
			exit;
		}
		
		// var_dump($_GET);

		/* PEGA INFORMAÇÕES SOBRE ESTE PROJETO */ 
		$this->conn = conectar();
		$aux = sql_select($this->conn,'recos_tb_projects',NULL,'txt_hash_pro = \''.$token.'\' ');
		$project = sql_fetch_array($aux);
		$id_project = $project['pk_cod_project'];
		
		// echo $token;
		// echo $this->__conn;

		$this->__token = $token;

		// echo '<br />XX>'.$this->checkToken().'<';

		// echo '123';

		if( !$this->checkToken() ){

			//token inválido
			echo '[{"error": "token invalid method"}]';

		} else {

			// echo '[{"status": "token ok"}]';

			//com os dados de todos usuários, basta agora realizar o cálculo
			// if( isset($_GET['profiling']) ){
				// $profiling = urldecode($_GET['profiling']);
			// }

			// echo $_GET['profiling'];

			$profiling_service = sql_fetch_array(sql_select($this->conn,'recos_tb_services',NULL,"pk_cod_service = ".$project['fk_cod_profiling_pro']." "));
			$profiling_url = URL_PADRAO.'services/'.$profiling_service['txt_url_ser'].'/?key='.$token.'&userid='.$userid;
			$profiling_result = file_get_contents($profiling_url);

			$profiling = json_decode($profiling_result);

			// echo '<pre>';
			// var_dump($profiling);
			// echo '</pre>';
			// exit($profiling);

			//simulando dados usuário solicitante dos recursos
			//o CORRETO é pegar pela API DA EMBRAPA =>> /pesquisaCadastroUsuario
			if( isset($_GET['userid']) ){
				$userid = $_GET['userid'];
			}
			$infoUsuarioSolicitante = array('codAinfo' => 'U9999', 'idNivelLetramento' => 1, 'idRegiao' => 4, 'profissao' => 1);



			// exit('<br />->1');

			//itera no retorno do serviço anterior, para calcular distâncias do usuário atual para todos os outros
			foreach($profiling as $value){
				
				$infoUsuario = (array) $value;

				// echo '<pre>';
				// var_dump($infoUsuario);
				// echo '</pre>';
				
				$arr_distancias[$value->codAinfo] = $this->distanciaEntreUsuarios($infoUsuarioSolicitante, $infoUsuario);
				
				/*echo ' <br /> >>DISTANCIA ENTRE 
				'.$infoUsuarioSolicitante['codAinfo'].' 
				e '.$infoUsuario['codAinfo'].': 
				'.$this->distanciaEntreUsuarios($infoUsuarioSolicitante, $infoUsuario);*/
				
				// echo 'X ';
			}

			// exit('<br />->2');

			//ordena este array, por distâncias do menor para maior
			asort($arr_distancias);

			//mantém somente os 5 usuários mais similares. pode mudar este valor a vontade
			$arr_distancias_cortado = array_slice($arr_distancias,0,5);
			
			$result = json_encode($arr_distancias_cortado);

			echo $result;

		}//else

	}

	function distanciaEntreUsuarios($usuario1, $usuario2){
		
		// echo 'entrou!';

		//CADA USUÁRIO TEM ESTAS CARACTERÍSTICAS, POR ENQUANTO
		//array('codAinfo' => 'U0999', 'idNivelLetramento' => 1, 'idRegiao' => 4, 'profissao' => 1);

		// echo '<pre>';
		// var_dump($usuario1);
		// var_dump($usuario2);
		// echo '</pre>';

		//CONVERSÕES caso os parâmetros cheguem aqui como OBJETOS, e não como ARRAYS
		$usuario1 = (array) $usuario1;
		$usuario2 = (array) $usuario2;

		$distancia = 0;

		//itera nas características dos usuários
		foreach($usuario1 as $key=>$value){
			
			if( $key != 'codAinfo' ){//desconsidera o ID do usuário

				if( $key == 'idRegiao' ){ //peso 3 para REGIÃO, por parecer mais importante. fique a vontade para mexer nisto!
				
					$distancia += 3*( (int) $value - (int) $usuario2[$key] )*( (int) $value - (int) $usuario2[$key] ) ;

				} else {

					$distancia += ( (int) $value - (int) $usuario2[$key] )*( (int) $value - (int) $usuario2[$key] ) ;

				}

			}

			// echo 'iterou! ';

		}

		$distancia = sqrt($distancia);

		return $distancia;

	}//distancia
	



}//class


$service = new EmbrapaMethod();



?>