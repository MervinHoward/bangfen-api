<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private MenuRepository $menuRepository;
    private OrderRepository $orderRepository;

    public function __construct(
        MenuRepository $menuRepository,
        OrderRepository $orderRepository
    ) {
        $this->menuRepository = $menuRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getPendingOrder()
    {
        return $this->orderRepository->getPendingOrder();
    }

    public function getOrderById(int $id)
    {
        return $this->orderRepository->getById($id);
    }

    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $total_price = 0;
            $order = $this->orderRepository->create([
                'date' => $data['date'],
                'order_type' => $data['order_type'],
                'total_price' => 0,
                'status' => 'pending'
            ]);

            foreach ($data['items'] as $item) {
                $menu = $this->menuRepository->findById($item['menu_id']);
                $subtotal = $menu->price * $item['quantity'];
                $total_price += $subtotal;
                $this->orderRepository->addItem([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ]);
            }
            return $this->orderRepository->update(
                $order->id,
                ['total_price' => $total_price]
            );
        });
    }

    public function addItem(int $id, array $items)
    {
        return DB::transaction(function () use ($id, $items) {
            $order = $this->orderRepository->getById($id);
            foreach ($items as $item) {
                $menu = $this->menuRepository->findById($item['menu_id']);
                $existingItem = $order->items->where('menu_id', $menu->id)->first();
                $subtotal = $menu->price * $item['quantity'];
                if ($existingItem) {
                    $newQty = $existingItem->quantity + $item['quantity'];
                    $this->orderRepository->updateItem($existingItem->id, [
                        'quantity' => $newQty,
                        'subtotal' => $newQty * $menu->price
                    ]);
                } else {
                    $this->orderRepository->addItem([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'price' => $menu->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal
                    ]);
                }
                $updatedOrder = $this->orderRepository->getById($id);
                $newTotal = $updatedOrder->items->sum('subtotal');
                return $this->orderRepository->update($id, [
                    'total_price' => $newTotal
                ]);
            }
        });
    }

    public function createPayment(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $order = $this->orderRepository->getById($id);
            if ($order->status == "paid") {
                throw new Exception("Pesanan ini sudah dibayar");
            }
            $order->payment()->create([
                'payment_method' => $data['payment_method'],
                'amount_bill' => $order->total_price,
                'amount_paid' => $data['amount_paid'],
                'change_amount' => $data['amount_paid'] - $order->total_price
            ]);
            $this->orderRepository->update($id, ['status' => 'paid']);
            return $order->load(['items.menu', 'payment']);
        });
    }

    public function cancelOrder(int $id) {
        $order = $this->orderRepository->update($id, ['status' => 'cancelled']);
        return $order;
    }
}
