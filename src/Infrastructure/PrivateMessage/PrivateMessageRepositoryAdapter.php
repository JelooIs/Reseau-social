<?php
require_once __DIR__ . '/../../../models/PrivateMessage.php';

class PrivateMessageRepositoryAdapter implements PrivateMessageRepositoryInterface {
    private $model;

    public function __construct() {
        $this->model = new PrivateMessage();
    }

    public function send($senderId, $receiverId, $message) {
        return $this->model->send($senderId, $receiverId, $message);
    }

    public function messagesBetween($userA, $userB, $limit = 50, $offset = 0) {
        return $this->model->messagesBetween($userA, $userB, $limit, $offset);
    }

    public function threadsForUser($userId) {
        return $this->model->threadsForUser($userId);
    }

    public function countBetween($userA, $userB) {
        return $this->model->countBetween($userA, $userB);
    }

    public function update($id, $userId, $message) {
        return $this->model->update($id, $userId, $message);
    }
}
