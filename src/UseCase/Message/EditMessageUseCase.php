<?php
require_once __DIR__ . '/../../Domain/Message/MessageRepositoryInterface.php';

class EditMessageUseCase {
    private $repo;

    public function __construct(MessageRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function execute($id, $message, $image = null) {
        return $this->repo->update($id, $message, $image);
    }
}
