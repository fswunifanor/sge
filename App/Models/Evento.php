<?php

    namespace App\Models;

    use MF\Model\Model;

    class Evento extends Model {
        private $id;
        private $administradorID = 1;
        private $nome;
        private $local;
        private $respGeralID;
        private $diaInicio;
        private $mesInicio;
        private $anoInicio;    
        private $dataFim;      
        private $cancelado = 0;
        private $descricao;
        private $imgEvento = './img/evento.jpg';     
        
        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        public function adicionarEvento() {
            $query = "
                INSERT INTO evento(administradorID, titulo, local, respGeralID, diaInicio, mesInicio, anoInicio, dataFim, cancelado, descricao, imgEvento) 
                VALUES (:administradorId, :titulo, :local, :respGeralID, :diaInicio, :mesInicio, :anoInicio, STR_TO_DATE(:dataFim, '%d/%m/%Y'), :cancelado, :descricao, :imgEvento);
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':administradorId', $this->__get('administradorID'));
            $stmt->bindValue(':titulo', $this->__get('titulo'));
            $stmt->bindValue(':local', $this->__get('local'));
            $stmt->bindValue(':respGeralID', $this->__get('respGeralID'));
            $stmt->bindValue(':diaInicio', $this->__get('diaInicio'));
            $stmt->bindValue(':mesInicio', $this->__get('mesInicio'));
            $stmt->bindValue(':anoInicio', $this->__get('anoInicio'));
            $stmt->bindValue(':dataFim', $this->__get('dataFim'));
            $stmt->bindValue(':cancelado', $this->__get('cancelado'));
            $stmt->bindValue(':descricao', $this->__get('descricao'));
            $stmt->bindValue(':imgEvento', $this->__get('imgEvento'));
            $stmt->execute();

            return $this;
        }

        public function listarEventos() {
            $query = "
                SELECT e.id, e.titulo, e.local, e.diaInicio, e.mesInicio, e.anoInicio, DATE_FORMAT(e.dataFim, '%d/%m/%Y') as dataFim, e.descricao, e.imgEvento, p.nome 
                FROM evento as e, participante as p, responsavelgeral as rg 
                WHERE p.usuarioID = rg.usuarioID AND e.respGeralID = rg.id 
                ORDER BY e.mesInicio, e.diaInicio;
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function deletarEvento() {
            $query = "
                DELETE FROM evento WHERE id = :id;
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return true;
        }

    }

?>