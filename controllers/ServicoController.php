<?php
require_once __DIR__ . '/../models/Servico.php';

class ServicoController {
    private $model;

    public function __construct() {
        $this->model = new Servico();
    }

    public function index() {
        return $this->model->getAll();
    }

    public function show($id) {
        return $this->model->getById($id);
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

    public function count() {
        return $this->model->count();
    }
}
?>
