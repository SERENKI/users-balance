<?php

namespace App\Console\Commands;

use App\Enums\TransactionType;
use App\Jobs\ProcessTransactionJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcessTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:process
                            {login : Логин пользователя}
                            {amount : Сумма операции}
                            {type : Тип операции (credit/debit)}
                            {description : Описание операции}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание финансовой операции';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $validator = Validator::make($this->arguments(), [
            'login'         => ['required', 'exists:users,login'],
            'amount'        => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'type'          => ['required', Rule::enum(TransactionType::class)],
            'description'   => ['required', 'string', 'max:255']
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        try {
            ProcessTransactionJob::dispatch(
                $this->argument('login'),
                (float)$this->argument('amount'),
                TransactionType::from($this->argument('type')),
                $this->argument('description')
            );

            $this->info('Транзакция успешно поставлена в очередь!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Ошибка: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
