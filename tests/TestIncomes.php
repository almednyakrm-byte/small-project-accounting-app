<?php

namespace App\Tests\Controller;

use App\Controller\IncomesController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use PDOStatement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestIncomes extends TestCase
{
    private $controller;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->controller = new IncomesController($this->pdo);
    }

    public function testGetIncomes()
    {
        $expectedResponse = [
            ['id' => 1, 'amount' => 100],
            ['id' => 2, 'amount' => 200],
        ];

        $this->pdo->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM incomes')
            ->willReturn($this->createMock(PDOStatement::class));

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedResponse);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM incomes')
            ->willReturn($stmt);

        $response = $this->controller->getIncomes();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testCreateIncome()
    {
        $income = ['amount' => 500];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO incomes (amount) VALUES (:amount)')
            ->willReturn($this->createMock(PDOStatement::class));

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':amount' => $income['amount']]);

        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(3);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('request')
            ->with('json')
            ->willReturn(json_encode($income));

        $response = $this->controller->createIncome($request);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(['id' => 3, 'amount' => 500], json_decode($response->getContent(), true));
    }

    public function testUpdateIncome()
    {
        $income = ['id' => 1, 'amount' => 600];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE incomes SET amount = :amount WHERE id = :id')
            ->willReturn($this->createMock(PDOStatement::class));

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':amount' => $income['amount'], ':id' => $income['id']]);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('request')
            ->with('json')
            ->willReturn(json_encode($income));

        $response = $this->controller->updateIncome(1, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['id' => 1, 'amount' => 600], json_decode($response->getContent(), true));
    }

    public function testDeleteIncome()
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM incomes WHERE id = :id')
            ->willReturn($this->createMock(PDOStatement::class));

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 1]);

        $response = $this->controller->deleteIncome(1);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}