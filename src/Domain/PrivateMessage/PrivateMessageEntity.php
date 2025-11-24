<?php
class PrivateMessageEntity {
    public $id;
    public $senderId;
    public $receiverId;
    public $message;
    public $createdAt;

    public function __construct($id = null, $senderId = null, $receiverId = null, $message = null, $createdAt = null) {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->message = $message;
        $this->createdAt = $createdAt;
    }
}
