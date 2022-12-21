<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountBalanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_zero_balance_with_no_operations()
    {
        $account = Account::factory()->create();
        $account->calculateBalance();

        $this->assertEquals(0,$account->getBalance());
    }

    public function test_positive_balance()
    {
        $account = Account::factory()->create();
        $gain = OperationType::factory()->create(['expense' => '0']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $gain,
            'sum' => 10]);
        $account->calculateBalance();

        $this->assertCount(1, $account->financialOperations);
        $this->assertFalse($account->hasNegativeBalance());
        $this->assertEquals(10,$account->getBalance());
    }

    public function test_negative_balance()
    {
        $account = Account::factory()->create();
        $expense = OperationType::factory()->create(['expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 10]);
        $account->calculateBalance();

        $this->assertCount(1, $account->financialOperations);
        $this->assertTrue($account->hasNegativeBalance());
        $this->assertEquals(-10,$account->getBalance());
    }

    public function test_balance_with_multiple_operations()
    {
        $account = Account::factory()->create();
        $gain = OperationType::factory()->create(['expense' => '0']);
        $expense = OperationType::factory()->create(['expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $gain,
            'sum' => 10]);
        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 10]);
        $account->calculateBalance();

        $this->assertCount(2, $account->financialOperations);
        $this->assertFalse($account->hasNegativeBalance());
        $this->assertEquals(0,$account->getBalance());
    }

    public function test_balance_with_multiple_operations_2()
    {
        $account = Account::factory()->create();
        $gain = OperationType::factory()->create(['expense' => '0']);
        $expense = OperationType::factory()->create(['expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $gain,
            'sum' => 500]);
        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 250.50]);
        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 385.95]);
        $account->calculateBalance();

        $this->assertCount(3, $account->financialOperations);
        $this->assertTrue($account->hasNegativeBalance());
        $this->assertEquals(-136.45,$account->getBalance());
    }

}