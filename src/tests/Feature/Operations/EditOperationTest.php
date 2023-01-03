<?php

namespace Tests\Feature\Operations;

use App\Http\Controllers\FinancialOperations\GeneralOperationController;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditOperationTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user, $account, $type, $lendingType;
    private array $headers;
    private GeneralOperationController $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(['email' => 'a@b.c']);
        $this->account = Account::factory()->create(['user_id' => $this->user]);
        $this->type = OperationType::factory()->create(['name' => 'type', 'lending' => false]);
        $this->lendingType = OperationType::factory()->create(['name' => 'lending', 'lending' => true]);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->controller = new GeneralOperationController;

    }

    public function test_update_operation(){

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'title' => 'original',
                'sum' => 100,
                'subject' => 'original'
            ]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operation/$operation->id", ['id' => $operation->id, 'sum' => 100.5, 'subject' => 'new']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('financial_operations', [
            'id' => $operation->id,
            'title' => 'original',
            'sum' => 100.5,
            'subject' => 'new'
        ]);
    }

    public function test_update_nonexisting_operation(){

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put('/operation/99999', ['sum' => 100.5, 'subject' => 'new']);

        $response->assertStatus(404);
    }

    public function test_update_type_to_lending_creates_lending_record(){

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $this->assertDatabaseMissing('lendings', ['id' => $operation->id]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operation/$operation->id", [
                'operation_type_id' => $this->lendingType->id,
                'expected_date_of_return' => '2023-01-01'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('lendings', [
            'id' => $operation->id,
            'expected_date_of_return' => '2023-01-01'
        ]);
    }

    public function test_update_type_from_lending_deletes_lending_record(){

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->lendingType]);
        Lending::factory()->create(['id' => $operation]);

        $this->assertDatabaseHas('lendings', ['id' => $operation->id]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operation/$operation->id", ['operation_type_id' => $this->type->id]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('lendings', ['id' => $operation->id]);
    }

    public function test_update_operation_creates_file(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->type, 'attachment' => null]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operation/$operation->id", ['attachment' => $file]);

        $response->assertStatus(200);
        $operation->refresh();
        $path = $operation->attachment;
        Storage::disk('local')->assertExists($path);

        Storage::fake('local');

    }

    public function test_update_operation_replaces_file(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');
        $oldPath = $this->controller->saveAttachment($this->user->id, $file);

        Storage::assertExists($oldPath);

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->type, 'attachment' => $oldPath]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operation/$operation->id", ['attachment' => $file]);

        $response->assertStatus(200);
        $operation->refresh();
        $newPath = $operation->attachment;
        Storage::disk('local')->assertExists($newPath);
        Storage::disk('local')->assertMissing($oldPath);

        Storage::fake('local');

    }

}