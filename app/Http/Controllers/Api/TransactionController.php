<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function balance(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'balance' => $user->balance->amount,
            'transactions' => $user->transactions()
                ->with('user')
                ->where('status', TransactionStatus::COMPLETED)
                ->latest()
                ->limit(5)
                ->get()
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $transactions = auth()->user()
            ->transactions()
            ->when($request->filled('description'), function ($query) use ($request) {
                $query->where('description', 'like', '%'.$request->input('description').'%');
            })
            ->orderBy($request->input('sort', 'created_at'), $request->input('order', 'desc'))
            ->paginate($request->input('per_page', 10));

        return response()->json($transactions);
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        try {
            $this->transactionService->process(
                user: auth()->user(),
                amount: $request->validated('amount'),
                type: TransactionType::from($request->validated('type')),
                description: $request->validated('description')
            );

            return response()->json([
                'message' => 'Transaction processed successfully'
            ], 201);

        } catch (InsufficientFundsException $e) {
            return response()->json([
                'error'             => $e->getMessage(),
                'current_balance'   => $e->getCurrentBalance(),
                'required_amount'   => $e->getRequestedAmount()
            ], 422);
        }
    }
}
