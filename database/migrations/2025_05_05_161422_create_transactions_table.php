<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('type', [
                TransactionType::CREDIT->value,
                TransactionType::DEBIT->value
            ]);
            $table->string('description');
            $table->timestamp('processed_at')->useCurrent();
            $table->enum('status', [
                TransactionStatus::PENDING->value,
                TransactionStatus::COMPLETED->value,
                TransactionStatus::FAILED->value
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
