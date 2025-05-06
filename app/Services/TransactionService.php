<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{

    public function process(
        User $user,
        float $amount,
        TransactionType $type,
        string $description
    ): void {
        try {
            DB::transaction(function () use ($user, $amount, $type, $description) {
                $balance = $user->balance()->lockForUpdate()->firstOrFail();

                $this->validateTransaction($balance->amount, $amount, $type);

                $newAmount = $this->calculateNewBalance($balance->amount, $amount, $type);
                $balance->update(['amount' => $newAmount]);

                $this->createTransactionRecord(
                    $user,
                    $amount,
                    $type,
                    TransactionStatus::COMPLETED,
                    $description
                );
            });

        } catch (InsufficientFundsException $e) {
            $this->createTransactionRecord(
                $user,
                $amount,
                $type,
                TransactionStatus::FAILED,
                $description
            );
            throw $e;

        } catch (\Throwable $e) {
            Log::error("Transaction failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function validateTransaction(
        float $currentBalance,
        float $amount,
        TransactionType $type
    ): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Сумма должна быть больше нуля');
        }

        if ($type === TransactionType::DEBIT && $currentBalance < $amount) {
            throw new InsufficientFundsException($currentBalance, $amount);
        }
    }

    private function calculateNewBalance(
        float $currentBalance,
        float $amount,
        TransactionType $type
    ): float {
        return match($type) {
            TransactionType::CREDIT => $currentBalance + $amount,
            TransactionType::DEBIT  => $currentBalance - $amount
        };
    }

    private function createTransactionRecord(
        User $user,
        float $amount,
        TransactionType $type,
        TransactionStatus $status,
        string $description
    ): void {
        $user->transactions()->create([
            'amount'        => $amount,
            'type'          => $type,
            'status'        => $status,
            'description'   => $description,
            'processed_at'  => now()
        ]);
    }
}
