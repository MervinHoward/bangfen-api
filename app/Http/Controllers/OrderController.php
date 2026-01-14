<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderService->getPendingOrder();
        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());
        return response()->json(new OrderResource($order), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        Log::info($id);
        try {
            $order = $this->orderService->getOrderById($id);
            Log::info(json_encode($order));
            return response()->json(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => "Pesanan tidak ditemukan."
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            $data = $request->validate([
                'items' => 'required|array',
                'items.menu_id' => 'required|exists:menus,id',
                'items.quantity' => 'required|integer|min:1',
            ]);
            $order = $this->orderService->addItem($id, $data);
            return response()->json(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => "Pesanan tidak ditemukan."
            ], 404);
        }
    }

    /**
     * Create payment for specified order
     */
    public function pay(Request $request, int $id) {
        $data = $request->validate([
            'payment_method' => 'required|in:cash,qris,transfer',
            'amount_paid' => 'required|numeric|min:0'
        ]);
        try {
            try {
                $order = $this->orderService->createPayment($id, $data);
                return response()->json([
                    'message' => 'Pembayaran berhasil.',
                    'data' => new OrderResource($order)
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }
    }
}
