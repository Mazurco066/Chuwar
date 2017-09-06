<?php

class Jogo{

  //Atributos
  private $usuario;
  private $computador;
  private $mensagem;
  private $Vpaises = NULL;

  //Método construtor da classe
  function __construct(Usuario $user){

    //Definindo usuário logado e computador que jogará contra o jogador
    $this->usuario = $user;
    $this->computador = new Computador();

    //Recuperando os países do banco de dados
    $this->Vpaises = $this->recuperaPaises();

  }

  //Metodos Getter and Setter()
  function setPaisesUsuario(){
    //Função para sortear países para o usuário
    $this->usuario->setPaises($this->sortearPaises());
  }

  function setPaisesComputador(){
    //Função para sortear paises para o computador
    $this->computador->setPaises($this->sortearPaises());
  }

  function getPaisesUsuario(){
    //Função para retornar paises do usuário
    return $this->usuario->getPaises();
  }

  function getPaisesComputador(){
    //Função para Retornar países do computador
    return $this->computador->getPaises();
  }

  //Métodos de classe

  /**
    * @return retorna 0 se não tiver nenhuma partida e 1 se ja existir pratida
    */
  function checaExistencia(){
    //Método que checa se há um jogo em andamento para esse usuário - OK
    $conexao = new DB;
    $conexao=$conexao->getConnection();
    $rs = $conexao->prepare("SELECT * FROM jogos where id_usuario = ? and emJogo = 1");
    $id = $this->usuario->getId();
    $rs->bindParam(1, $id);
    if($rs->execute()){

        if($rs->rowCount() > 0){

          return 1;

        }
        else{

          return 0;

        }

    }
    else{ //Erro de banco de dados

      $flash="OPS!... ouve algum erro em nosso Sistema. Por Favor contate administrador!";
      echo $flash;

    }

  }

  /**
    * @return retorna os países disponiveis no banco
    */
  function recuperaPaises(){
    //Função que pega os países do banco e jogam no array de países da classe - OK
    $conexao = new DB;
    $conexao=$conexao->getConnection();
    $rs = $conexao->prepare("SELECT * FROM paises");
    if ($rs->execute()){

      $row = $rs->fetchAll(PDO::FETCH_OBJ);
      return $row;

    }
    else{

      $flash="OPS!... ouve algum erro em nosso Sistema. Por Favor contate administrador!";
      echo $flash;

    }

  }

  /**
    * @return retorna um vetor de países para o jogador ou computador
    */
  function sortearPaises(){
    //função que sorteia paises para usuário e computador
    $selecao = array_rand($this->Vpaises, 6);
		$lista = [];

			foreach ($selecao as $key){

				$lista[] = $this->Vpaises[$key];
				unset($this->Vpaises[$key]);

			}

    return $lista;

  }

  function novoJogo($paisesUsuario, $paisesComputador){
    //Função que iniciará um novo jogo , distribuirá os paises e salvara no banco a nova partida
    $conexao = new DB;
    $conexao=$conexao->getConnection();
    $rs = $conexao->prepare("INSERT INTO jogos(id_usuario, emJogo) VALUES(?, 1)");
    $id = $this->usuario->getId();
    $rs->bindParam(1,$id);
    if ($rs->execute()){

      $conexao = new DB;
      $conexao=$conexao->getConnection();
      $rs = $conexao->prepare("Select ID from jogos where id_usuario = ?");
      $rs->bindParam(1,$id);
      $rs->execute();
      $row = $rs->fetch(PDO::FETCH_OBJ);
      $idJogo = $row->ID;

      foreach ($paisesUsuario as $key => $value) {

        $pertence = "Jogador";
        $rs = $conexao->prepare("INSERT INTO status_paises(Jogo_ID, Pais_ID, Nome, tropas, Pertence) VALUES(?,?,?,?,?)");
        $rs->bindParam(1,$idJogo);
        $rs->bindParam(2,$value->ID);
        $rs->bindParam(3,$value->Nome);
        $rs->bindParam(4,$value->tropas);
        $rs->bindParam(5,$pertence);
        $rs->execute();
      }

      foreach ($paisesComputador as $key => $value) {

        $pertence = "Computador";
        $rs = $conexao->prepare("INSERT INTO status_paises(Jogo_ID, Pais_ID, Nome, tropas, Pertence) VALUES(?,?,?,?,?)");
        $rs->bindParam(1,$idJogo);
        $rs->bindParam(2,$value->ID);
        $rs->bindParam(3,$value->Nome);
        $rs->bindParam(4,$value->tropas);
        $rs->bindParam(5,$pertence);
        $rs->execute();
      }

    }
    else{

    }

  }

  function carregaJogo(){
    //função que carrega um jogo existente, restaurando da ultima rodada feita
  }

}

 ?>