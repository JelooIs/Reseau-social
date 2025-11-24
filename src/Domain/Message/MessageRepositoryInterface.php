<?php
interface MessageRepositoryInterface {
    public function create($userId, $message, $image = null);
    public function update($id, $message, $image = null);
    public function delete($id);
    public function all($limit = 10, $offset = 0);
    public function count();
}
