<?php
require_once __DIR__ . '/../models/Agendamento.php';

class AgendamentoController {
    private $model;

    public function __construct() {
        $this->model = new Agendamento();
    }

    public function index() {
        return $this->model->getAll();
    }

    public function show($id) {
        return $this->model->getById($id);
    }

    public function getByDate($data) {
        return $this->model->getByDate($data);
    }

    public function store($data) {
        return $this->model->create($data);
    }

    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    public function destroy($id) {
        return $this->model->delete($id);
    }

    public function countPorDia($data) {
        return $this->model->countPorDia($data);
    }
}
?>
