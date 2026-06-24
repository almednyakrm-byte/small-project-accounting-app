<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\ExpensesController;
use App\Repository\ExpensesRepository;
use App\Service\ExpensesService;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestExpenses extends TestCase
{
    private $controller;
    private $repository;
    private $service;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = $this->createMock(ExpensesRepository::class);
        $this->service = $this->createMock(ExpensesService::class);
        $this->controller = new ExpensesController($this->repository, $this->service);

        $this->pdo->method('prepare')->willReturn($this->createMock(\PDOStatement::class));
        $this->pdo->method('query')->willReturn($this->createMock(\PDOStatement::class));
        $this->pdo->method('exec')->willReturn(1);
        $this->pdo->method('getAttribute')->willReturn(1);
    }

    public function testGetExpenses()
    {
        $this->repository->method('getAll')->willReturn([
            ['id' => 1, 'name' => 'Expense 1'],
            ['id' => 2, 'name' => 'Expense 2'],
        ]);

        $response = $this->controller->getExpenses();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode([
            ['id' => 1, 'name' => 'Expense 1'],
            ['id' => 2, 'name' => 'Expense 2'],
        ]), $response->getBody()->getContents());
    }

    public function testPostExpense()
    {
        $this->service->method('create')->willReturn(1);
        $this->pdo->method('prepare')->willReturn($this->createMock(\PDOStatement::class));

        $data = ['name' => 'New Expense'];
        $response = $this->controller->postExpense($data);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(json_encode(['id' => 1]), $response->getBody()->getContents());
    }

    public function testPutExpense()
    {
        $this->service->method('update')->willReturn(1);
        $this->pdo->method('prepare')->willReturn($this->createMock(\PDOStatement::class));

        $data = ['id' => 1, 'name' => 'Updated Expense'];
        $response = $this->controller->putExpense($data);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['id' => 1]), $response->getBody()->getContents());
    }

    public function testDeleteExpense()
    {
        $this->service->method('delete')->willReturn(1);
        $this->pdo->method('prepare')->willReturn($this->createMock(\PDOStatement::class));

        $id = 1;
        $response = $this->controller->deleteExpense($id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['id' => 1]), $response->getBody()->getContents());
    }
}



// ExpensesController.php
namespace App\Controller;

use App\Repository\ExpensesRepository;
use App\Service\ExpensesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpensesController
{
    private $repository;
    private $service;

    public function __construct(ExpensesRepository $repository, ExpensesService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getExpenses(Request $request)
    {
        $expenses = $this->repository->getAll();
        return new JsonResponse($expenses, 200);
    }

    public function postExpense(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $id = $this->service->create($data);
        return new JsonResponse(['id' => $id], 201);
    }

    public function putExpense(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->service->update($data);
        return new JsonResponse(['id' => $data['id']], 200);
    }

    public function deleteExpense(Request $request, $id)
    {
        $this->service->delete($id);
        return new JsonResponse(['id' => $id], 200);
    }
}