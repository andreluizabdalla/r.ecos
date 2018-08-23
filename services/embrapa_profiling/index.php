<?php

//mostra os erros
// ini_set('display_errors', 1);
// ini_set('display_startup_erros', 1);
// error_reporting(E_ALL);


include("../../classes/RecosService.php");






class EmbrapaProfiling extends RecosService{

	function __construct(){

		if( isset($_GET['key']) ){
			$token = $_GET['key'];
		} else {
			//token inválido
			echo '[{"error": "token invalid"}]';
			exit;
		}
		

		/* PEGA INFORMAÇÕES SOBRE ESTE PROJETO */ 
		$this->conn = conectar();
		$aux = sql_select($this->conn,'recos_tb_projects',NULL,'txt_hash_pro = \''.$token.'\' ');
		$project = sql_fetch_array($aux);
		$id_project = $project['pk_cod_project'];

		$this->__token = $token;

		if( !$this->checkToken() ){

			//token inválido
			echo '[{"error": "token invalid profiling"}]';

		} else {
 
			if( isset($_GET['userid']) ){
				$userid = $_GET['userid'];
			} else {
				//user inválido
				echo '[{"error": "user invalid"}]';
				exit;
			}

			//simulando dados usuário solicitante dos recursos
			//o CORRETO é pegar pela API DA EMBRAPA =>> /pesquisaCadastroUsuario
			//http://hereford.cnpgl.embrapa.br/AppLeiteWebService/recomendaappleite/pesquisaCadastroUsuario?idUsuario=1
			$infoUsuarioSolicitante = array('codAinfo' => $userid, 'idNivelLetramento' => 1, 'idRegiao' => 4, 'profissao' => 1);

			//simulando dados dos outros usuários
			//o CORRETO é pegar pela API DA EMBRAPA =>> /todosUsuarios (ainda não existe, então devemos solicitar à EMBRAPA)
			$infoTodosUsuarios = array(
				array('codAinfo' => 'U0001', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0002', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0003', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0004', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0005', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0006', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0007', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0008', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0009', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) ),
				array('codAinfo' => 'U0010', 'idNivelLetramento' => rand(1,4), 'idRegiao' => rand(0,4), 'profissao' => rand(0,20) )
			);

			$result = json_encode($infoTodosUsuarios);

			echo $result;


			//aqui chama o próximo service - neste caso, METHOD, passando como parâmetro o $result
			//ou não?
			




		}//else

	}
	


}//class


$service = new EmbrapaProfiling();



?>