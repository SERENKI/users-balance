<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InsufficientFundsException extends Exception
{
    protected $currentBalance;
    protected $requestedAmount;

    public function __construct(float $currentBalance, float $requestedAmount)
    {
        $this->currentBalance = $currentBalance;
        $this->requestedAmount = $requestedAmount;

        $message = "Недостаточно средств. Текущий баланс: {$this->formattedBalance()}, "
                 . "Требуется: {$this->formattedRequestedAmount()}";

        parent::__construct($message, 422);
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage(),
                'current_balance' => $this->currentBalance,
                'requested_amount' => $this->requestedAmount
            ], $this->getCode());
        }

        return redirect()->back()
            ->withInput()
            ->withErrors(['balance' => $this->getMessage()]);
    }

    private function formattedBalance(): string
    {
        return number_format($this->currentBalance, 2);
    }

    private function formattedRequestedAmount(): string
    {
        return number_format($this->requestedAmount, 2);
    }

    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }

    public function getRequestedAmount(): float
    {
        return $this->requestedAmount;
    }
}
