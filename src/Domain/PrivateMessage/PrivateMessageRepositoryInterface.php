<?php
interface PrivateMessageRepositoryInterface {
    public function send($senderId, $receiverId, $message);
    public function messagesBetween($userA, $userB, $limit = 50, $offset = 0);
    public function countBetween($userA, $userB);
    public function threadsForUser($userId);
    public function update($id, $userId, $message);
}
