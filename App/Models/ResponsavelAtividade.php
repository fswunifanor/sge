<?php

    namespace App\Models;

    use MF\Model\Model;

    class ResponsavelAtividade extends Model {

        private $id;
        private $usuarioId;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        public function criarResponsavelAtividade() {
            $query = "
                INSERT INTO responsavelatividade(usuarioID) SELECT id FROM usuario WHERE login = :login;
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':login', $this->__get('login'));
            $stmt->execute();

            return $this;
        }

        public function listarResponsavelAtividade() {
            $query = "
                SELECT ra.id, p.nome, u.login 
                FROM responsavelatividade as ra, participante as p, usuario as u
                WHERE ra.usuarioID = p.usuarioID AND u.id = p.usuarioID;
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function deletarResponsavelAtividade() {
            $query = "
                DELETE FROM responsavelatividade WHERE id = :id;
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $this;
        }
    }

?>