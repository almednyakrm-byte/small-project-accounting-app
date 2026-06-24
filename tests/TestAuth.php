<?php

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Service\Auth;
use App\Repository\UserRepository;
use App\Repository\TokenRepository;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class TestAuth extends TestCase
{
    /**
     * @var LegacyMockInterface|UserRepository
     */
    protected $userRepository;

    /**
     * @var LegacyMockInterface|TokenRepository
     */
    protected $tokenRepository;

    /**
     * @var Auth
     */
    protected $authService;

    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->tokenRepository = Mockery::mock(TokenRepository::class);
        $this->authService = new Auth($this->userRepository, $this->tokenRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testLoginSuccess()
    {
        // Mock user data
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Mock user repository to return user data
        $this->userRepository->shouldReceive('getUserByEmail')->with($userData['email'])->andReturn($userData);

        // Mock token repository to save token
        $this->tokenRepository->shouldReceive('saveToken')->with(Mockery::type('string'))->andReturn(true);

        // Call login method
        $result = $this->authService->login($userData['email'], $userData['password']);

        // Assert result
        $this->assertTrue($result);
    }

    public function testRegisterSuccess()
    {
        // Mock user data
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Mock user repository to save user
        $this->userRepository->shouldReceive('saveUser')->with($userData)->andReturn(true);

        // Mock token repository to save token
        $this->tokenRepository->shouldReceive('saveToken')->with(Mockery::type('string'))->andReturn(true);

        // Call register method
        $result = $this->authService->register($userData['email'], $userData['password']);

        // Assert result
        $this->assertTrue($result);
    }

    public function testLoginFailure()
    {
        // Mock user data
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Mock user repository to return null
        $this->userRepository->shouldReceive('getUserByEmail')->with($userData['email'])->andReturn(null);

        // Call login method
        $result = $this->authService->login($userData['email'], $userData['password']);

        // Assert result
        $this->assertFalse($result);
    }

    public function testRegisterFailure()
    {
        // Mock user data
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Mock user repository to save user but return false
        $this->userRepository->shouldReceive('saveUser')->with($userData)->andReturn(false);

        // Call register method
        $result = $this->authService->register($userData['email'], $userData['password']);

        // Assert result
        $this->assertFalse($result);
    }
}


This test file covers the following scenarios:

- `testLoginSuccess`: Tests successful login with valid credentials.
- `testRegisterSuccess`: Tests successful registration with valid credentials.
- `testLoginFailure`: Tests failed login with invalid credentials.
- `testRegisterFailure`: Tests failed registration with invalid credentials.

Each test method uses Mockery to mock the `UserRepository` and `TokenRepository` classes, allowing us to isolate the `Auth` service and test its behavior in isolation. The `shouldReceive` method is used to specify the expected behavior of the mocked objects, and the `andReturn` method is used to specify the return values of the mocked methods. The `assertTrue` and `assertFalse` assertions are used to verify the expected results of the `Auth` service methods.