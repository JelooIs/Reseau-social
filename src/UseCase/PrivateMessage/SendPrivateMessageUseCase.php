<?php
require_once __DIR__ . '/../../Domain/PrivateMessage/PrivateMessageRepositoryInterface.php';

class SendPrivateMessageUseCase {
    private $repo;

    public function __construct(PrivateMessageRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function execute($senderId, $receiverId, $message) {
        return $this->repo->send($senderId, $receiverId, $message);
    }
}
