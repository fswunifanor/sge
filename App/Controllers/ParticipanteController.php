<?php

    namespace App\Controllers;

    use MF\Controller\Action;
    use MF\Model\Container;

    class ParticipanteController extends Action {

        public function cadastroParticipante() {
            $this->view->participante = [
                'nome' => '',
                'instituicao' => '',
                'curso' => '',
                'login' => '',
                'senha' => ''
            ];
            $this->view->erroCadastro = false;
            $this->render('cadastroParticipante');
        }

        public function cadastrar() {

            $participante = Container::getModel('Participante');
            $participante->__set('nome', $_POST['nome']);
            $participante->__set('apelido', explode(" ", $_POST['nome'])[0]);
            $participante->__set('instituicao', $_POST['instituicao']);
            $participante->__set('curso', $_POST['curso']);
            $participante->__set('login', $_POST['login']);
            $participante->__set('senha', md5($_POST['senha']));

            if(count($participante->getUsuarioLogin()) == 0) {
                $participante->criarParticipante();
                $this->render('sucessoCadastro');
            } else {
                $this->view->participante = [
                    'nome' => $_POST['nome'],
                    'instituicao' => $_POST['instituicao'],
                    'curso' => $_POST['curso'],
                    'login' => $_POST['login'],
                    'senha' => $_POST['senha']
                ];
                $this->view->erroCadastro = true;
            }

        }

        public function indexParticipante() {
            $listaEvento = Container::getModel('Evento');
            $listaEvento->__set('respGeralID', base64_decode($_GET['dXNlcklE']));
            $this->view->eventos = $listaEvento->listarEventos();
            $this->view->inscrito = $listaEvento->inscritoEvento();

            $this->render('indexParticipante');
        }

        public function acaoParticipanteEvento() {

            if(isset($_POST['visualizarAtividades'])) {
                $id = $_POST['visualizarAtividades'];
                header('Location: /atividades_evento?idEvt=' . base64_encode($id));
            }

            if(isset($_POST['inscreverEvento'])) {
                $eventoID = $_POST['inscreverEvento'];
                $usuarioID = base64_decode($_GET['user']);

                $inscricaoEvento = Container::getModel('InscricaoEvento');
                $inscricaoEvento->__set('eventoID', $eventoID);
                $inscricaoEvento->__set('usuarioID', $usuarioID);
                $inscricaoEvento->inscreverEvento();

                header('Location: /index_participante');
            }

            if(isset($_POST['cancelarInscricaoEvento'])) {
                $eventoID = $_POST['cancelarInscricaoEvento'];
                $usuarioID = base64_decode($_GET['user']);

                $inscricaoEvento = Container::getModel('InscricaoEvento');
                $inscricaoEvento->__set('eventoID', $eventoID);
                $inscricaoEvento->__set('usuarioID', $usuarioID);
                $inscricaoEvento->cancelarInscricaoEvento();

                header('Location: /index_participante');
            }
        }

        public function atividadesEvento() {
            $listaAtividade = Container::getModel('Atividade');
            $eventoID = base64_decode($_GET['idEvt']);
            $listaAtividade->__set('eventoID', $eventoID);
            $this->view->titulo = $listaAtividade->getTituloEvento();
            $this->view->atividades = $listaAtividade->listarAtividades();

            $this->render('atividadesEvento');
        }

        public function acaoParticipanteAtividade() {
            if(isset($_POST['inscreverAtividade'])) {
                $atividadeID = $_POST['inscreverAtividade'];
                $usuarioID = base64_decode($_GET['user']);

                $inscricaoAtividade = Container::getModel('InscricaoAtividade');
                $inscricaoAtividade->__set('atividadeID', $atividadeID);
                $inscricaoAtividade->__set('usuarioID', $usuarioID);
                $inscricaoAtividade->inscreverAtividade();

                header('Location: /atividades_evento?idEvt=' . $_GET['idEvt']);
            }

            if(isset($_POST['cancelarInscricaoAtividade'])) {
                $atividadeID = $_POST['cancelarInscricaoAtividade'];
                $usuarioID = base64_decode($_GET['user']);

                $inscricaoAtividade = Container::getModel('InscricaoAtividade');
                $inscricaoAtividade->__set('atividadeID', $atividadeID);
                $inscricaoAtividade->__set('usuarioID', $usuarioID);
                $inscricaoAtividade->cancelarInscricaoAtividade();

                header('Location: /atividades_evento?idEvt=' . $_GET['idEvt']);
            }

            if(isset($_POST['participantes'])) {
                print_r($_POST['participantes']);
                header('Location: /listar_participante?idEvt=' . $_GET['idEvt'] . '&idAtv=' . base64_encode($_POST['participantes']));
            }
        }

        public function adicionarParticipante() {
            $adicionar = Container::getModel('InscricaoAtividade');
            $adicionar->__set('login', $_POST['login']);
            $adicionar->__set('atividadeID', base64_decode($_GET['idAtv']));
            $adicionar->adicionarInscricao();

            header('Location: /listar_participante?idEvt=' . $_GET['idEvt'] . '&idAtv=' . $_GET['idAtv']);
        }

        public function removerParticipante() {
            $remover = Container::getModel('InscricaoAtividade');
            $remover->__set('login', $_POST['remover']);
            $remover->removerInscricao();

            header('Location: /listar_participante?idEvt=' . $_GET['idEvt'] . '&idAtv=' . $_GET['idAtv']);
        }

        public function gerarCertificado() {
            $gerarCertificado = Container::getModel('Participante');
            $gerarCertificado->__set('eventoID', base64_decode($_GET['idEvt']));
            $gerarCertificado->__set('usuarioID', base64_decode($_GET['ui']));
            $this->view->somatorio = $gerarCertificado->somatorioPex();
            $this->view->relatorio = $gerarCertificado->relatorioParticipacao();

            $this->render('gerarCertificado');
        }
    }

?>