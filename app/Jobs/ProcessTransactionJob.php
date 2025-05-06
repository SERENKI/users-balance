<?php

namespace App\Jobs;

use App\Enums\TransactionType;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTransactionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $login,
        private float $amount,
        private TransactionType $type,
        private string $description
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TransactionService $service): void
    {
        $user = User::where('login', $this->login)->firstOrFail();
        $service->process($user, $this->amount, $this->type, $this->description);
    }
}
