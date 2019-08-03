<?php

    namespace App\Models;

    use MF\Model\Model;

    class Evento extends Model {
        private $id;
        private $administradorID;
        private $titulo;
        private $local;
        private $respGeralID;
        private $dataInicio;   
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
                INSERT INTO evento(administradorID, titulo, local, respGeralID, dataInicio, dataFim, cancelado, descricao, imgEvento) 
                VALUES (:administradorID, :titulo, :local, :respGeralID, :dataInicio, :dataFim, :cancelado, :descricao, :imgEvento);
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':administradorID', $this->__get('administradorID'));
            $stmt->bindValue(':titulo', $this->__get('titulo'));
            $stmt->bindValue(':local', $this->__get('local'));
            $stmt->bindValue(':respGeralID', $this->__get('respGeralID'));
            $stmt->bindValue(':dataInicio', $this->__get('dataInicio'));
            $stmt->bindValue(':dataFim', $this->__get('dataFim'));
            $stmt->bindValue(':cancelado', $this->__get('cancelado'));
            $stmt->bindValue(':descricao', $this->__get('descricao'));
            $stmt->bindValue(':imgEvento', $this->__get('imgEvento'));
            $stmt->execute();

            return $this;
        }

        public function listarEventos() {

            $administrador = "";
            if($this->__get('administradorID')) {
                $administrador = "e.administradorID = :administradorID";
            }

            $responsavelGeral = "";
            if($this->__get('respGeralID')) {
                $responsavelGeral = "e.respGeralID = :respGeralID";
            }

            // SELECT e.id, e.titulo, e.local, DATE_FORMAT(e.dataInicio, '%d/%m/%Y') as dataInicio, DATE_FORMAT(e.dataFim, '%d/%m/%Y') as dataFim, e.descricao, e.imgEvento, p.nome 
            // FROM evento as e, participante as p, responsavelgeral as rg 
            // WHERE p.usuarioID = rg.usuarioID AND e.respGeralID = rg.id ". $administrador ." ". $responsavelGeral ." 
            // ORDER BY e.dataInicio;
            $query = "
                SELECT DISTINCT e.id, e.titulo, e.local, e.dataInicio, e.dataFim, e.descricao, e.imgEvento, p.nome 
                FROM sge.evento as e 
                    LEFT JOIN sge.responsavelgeral as rg ON e.respGeralID = rg.id 
                    LEFT JOIN sge.participante as p ON p.usuarioID = rg.usuarioID 
                WHERE ". $administrador ." ". $responsavelGeral ."
                ORDER BY e.dataInicio;
            ";

            $stmt = $this->db->prepare($query);
            if($this->__get('administradorID')) {
                $stmt->bindValue(':administradorID', $this->__get('administradorID'));
            }
            if($this->__get('respGeralID')) {
                $stmt->bindValue(':respGeralID', $this->__get('respGeralID'));
            }
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        // SELECT e.id, e.titulo, e.local, e.dataInicio, e.dataFim, e.descricao, e.imgEvento, p.nome 
        // FROM evento as e, participante as p, responsavelgeral as rg 
        // WHERE e.id = :id AND p.usuarioID = rg.usuarioID AND e.respGeralID = rg.id
        public function listarDadosEvento() {
            $query = "
                SELECT e.id, e.titulo, e.local, e.dataInicio, e.dataFim, e.descricao, e.imgEvento, p.nome 
                FROM evento as e 
                    LEFT JOIN responsavelgeral as rg ON e.respGeralID = rg.id
                    LEFT JOIN participante as p ON p.usuarioID = rg.usuarioID
                WHERE e.id = :id 
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function listarDadosResponsavelGeral() {
            $query = "
                SELECT rg.id, p.nome 
                FROM responsavelgeral as rg, participante as p
                WHERE rg.usuarioID = p.usuarioID
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function alterarEvento() {
            $query = "
                UPDATE evento 
                SET titulo = :titulo, 
                    local = :local,
                    respGeralID = :respGeralID,
                    dataInicio = :dataInicio, 
                    dataFim = :dataFim, 
                    descricao = :descricao
                    WHERE id = :id
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->bindValue(':titulo', $this->__get('titulo'));
            $stmt->bindValue(':local', $this->__get('local'));
            $stmt->bindValue(':respGeralID', $this->__get('respGeralID'));
            $stmt->bindValue(':dataInicio', $this->__get('dataInicio'));
            $stmt->bindValue(':dataFim', $this->__get('dataFim'));
            $stmt->bindValue(':descricao', $this->__get('descricao'));
            $stmt->execute();

            return $this;
        }

        public function cancelarEvento() {
            $query = "
                UPDATE evento 
                SET cancelado = true
                WHERE id = :id
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $this;
        }

        public function ativarEvento() {
            $query = "
                UPDATE evento 
                SET cancelado = false
                WHERE id = :id
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $this;
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

        public function inscritoEvento() {
            $query = "
                SELECT usuarioID, eventoID
                FROM inscricaoevento
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

    }

?>