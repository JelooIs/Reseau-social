<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/User.php';

class MessageTest extends TestCase {
    private $pdo;
    private $messageModel;
    private $userModel;

    protected function setUp(): void {
        $this->pdo = get_test_pdo();
        $this->messageModel = new Message($this->pdo);
        $this->userModel = new User($this->pdo);
    }

    public function testCreateAndAllAndCount() {
        // create a user
        $this->userModel->create('Msg', 'Author', 'msgauthor@example.test', 'pw', 'user');
        $user = $this->userModel->findByEmail('msgauthor@example.test');
        $this->assertNotEmpty($user);

        $created = $this->messageModel->create($user['id'], 'Hello world', null);
        $this->assertTrue((bool)$created);

        $all = $this->messageModel->all(10, 0);
        $this->assertIsArray($all);
        $this->assertCount(1, $all);

        $count = $this->messageModel->count();
        $this->assertEquals(1, (int)$count);
    }
}
