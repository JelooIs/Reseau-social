<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../models/User.php';

class UserTest extends TestCase {
    private $pdo;
    private $userModel;

    protected function setUp(): void {
        $this->pdo = get_test_pdo();
        $this->userModel = new User($this->pdo);
    }

    public function testCreateAndFindByEmailAndVerify() {
        $email = 'jane@example.test';
        $password = 'secret123';
        $created = $this->userModel->create('Doe', 'Jane', $email, $password, 'user');
        $this->assertTrue((bool)$created, 'User creation should return true');

        $found = $this->userModel->findByEmail($email);
        $this->assertIsArray($found);
        $this->assertEquals('Doe', $found['nom']);

        $verified = $this->userModel->verify($email, $password);
        $this->assertIsArray($verified);
        $this->assertEquals($email, $verified['email']);
    }

    public function testDeleteUser() {
        $email = 'delete@example.test';
        $this->userModel->create('Del', 'User', $email, 'pw', 'user');
        $found = $this->userModel->findByEmail($email);
        $this->assertNotEmpty($found);
        $deleted = $this->userModel->delete($found['id']);
        $this->assertTrue((bool)$deleted);
        $still = $this->userModel->findByEmail($email);
        $this->assertFalse($still);
    }
}
