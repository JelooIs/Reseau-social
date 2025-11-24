<?php
require_once __DIR__ . '/../../Domain/Message/MessageRepositoryInterface.php';
require_once __DIR__ . '/../../../models/Message.php';

class MessageRepositoryAdapter implements MessageRepositoryInterface {
    private $model;

    public function __construct() {
        $this->model = new Message();
    }

    public function create($userId, $message, $image = null) {
        return $this->model->create($userId, $message, $image);
    }

    public function update($id, $message, $image = null) {
        return $this->model->update($id, $message, $image);
    }

    public function delete($id) {
        return $this->model->delete($id);
    }

    public function all($limit = 10, $offset = 0) {
        return $this->model->all($limit, $offset);
    }

    public function count() {
        return $this->model->count();
    }
}
