<?php
class MessageEntity {
    public $id;
    public $userId;
    public $message;
    public $image;
    public $createdAt;

    public function __construct($id = null, $userId = null, $message = null, $image = null, $createdAt = null) {
        $this->id = $id;
        $this->userId = $userId;
        $this->message = $message;
        $this->image = $image;
        $this->createdAt = $createdAt;
    }
}
