<?php
require_once __DIR__ . '/../../Domain/PrivateMessage/PrivateMessageRepositoryInterface.php';

class EditPrivateMessageUseCase {
    private $repo;

    public function __construct(PrivateMessageRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function execute($id, $userId, $message) {
        return $this->repo->update($id, $userId, $message);
    }
}
