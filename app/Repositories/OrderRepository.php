<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;

class OrderRepository
{
    public function getPendingOrder()
    {
        return Order::where('status', 'pending')
            ->with(['items.menu'])
            ->latest('date')
            ->get();
    }

    public function getById(int $id)
    {
        $order = Order::with(['items.menu', 'payment'])
            ->findOrFail($id);
        return $order;
    }

    public function create(array $data) {
        $order = Order::create($data);
        return $order;
    }

    public function update(int $id, array $data) {
        $order = Order::findOrFail($id);
        $order->update($data);
        return $order;
    }

    public function addItem(array $data) {
        $item = OrderItem::create($data);
        return $item;
    }

    public function updateItem(int $id, array $data) {
        $item = OrderItem::findOrFail($id);
        $item->update($data);
        return $item;
    }
}
