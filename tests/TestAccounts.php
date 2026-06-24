<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use PDOStatement;

class TestAccounts extends TestCase
{
    private $pdo;
    private $accounts;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->accounts = new Accounts($this->pdo);
    }

    public function testGetAllAccounts()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([]);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Account 1'],
                ['id' => 2, 'name' => 'Account 2'],
            ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM accounts')
            ->willReturn($stmt);

        $response = $this->accounts->getAllAccounts();
        $this->assertEquals([
            ['id' => 1, 'name' => 'Account 1'],
            ['id' => 2, 'name' => 'Account 2'],
        ], $response);
    }

    public function testGetAccountById()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'Account 1']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM accounts WHERE id = ?')
            ->willReturn($stmt);

        $response = $this->accounts->getAccountById(1);
        $this->assertEquals(['id' => 1, 'name' => 'Account 1'], $response);
    }

    public function testCreateAccount()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['name' => 'New Account']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO accounts (name) VALUES (?)')
            ->willReturn($stmt);

        $response = $this->accounts->createAccount(['name' => 'New Account']);
        $this->assertTrue($response);
    }

    public function testUpdateAccount()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1, 'Updated Account']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE accounts SET name = ? WHERE id = ?')
            ->willReturn($stmt);

        $response = $this->accounts->updateAccount(1, ['name' => 'Updated Account']);
        $this->assertTrue($response);
    }

    public function testDeleteAccount()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM accounts WHERE id = ?')
            ->willReturn($stmt);

        $response = $this->accounts->deleteAccount(1);
        $this->assertTrue($response);
    }
}

class Accounts
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllAccounts()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM accounts');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAccountById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM accounts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createAccount($data)
    {
        $stmt = $this->pdo->prepare('INSERT INTO accounts (name) VALUES (?)');
        return $stmt->execute([$data['name']]);
    }

    public function updateAccount($id, $data)
    {
        $stmt = $this->pdo->prepare('UPDATE accounts SET name = ? WHERE id = ?');
        return $stmt->execute([$data['name'], $id]);
    }

    public function deleteAccount($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM accounts WHERE id = ?');
        return $stmt->execute([$id]);
    }
}