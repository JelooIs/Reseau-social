<?php
require_once __DIR__ . '/../../Domain/Message/MessageRepositoryInterface.php';

class CreateMessageUseCase {
    private $repo;

    public function __construct(MessageRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function execute($userId, $message, $image = null) {
        return $this->repo->create($userId, $message, $image);
    }
}
