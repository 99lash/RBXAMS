<?php

namespace Tests\Unit;

use App\Utils\AccountId;
use Hidehalo\Nanoid\Client;
use PHPUnit\Framework\TestCase;

class AccountIdTest extends TestCase
{
    public function testGenerate()
    {
        // Create a mock for the Nanoid client
        $nanoidClientMock = $this->createMock(Client::class);

        // Configure the mock to return specific values for each segment
        $nanoidClientMock->method('generateId')
            ->willReturnOnConsecutiveCalls('seg1', 'seg2', 'seg3');

        // Create an instance of AccountId with the mock client
        $accountId = new AccountId($nanoidClientMock);

        // Generate the ID
        $generatedId = $accountId->generate();

        // Assert that the generated ID has the expected format
        $this->assertEquals('aseg1iseg2dseg3', $generatedId);
    }
}
