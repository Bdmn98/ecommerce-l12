<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $service)
    {
    }


    public function store(Request $request)
    {
        $order = $this->service->createFromCart($request->user(), $request->input('discount_code'));
        return $this->successfulResponse($order, __('Order created'), 201);
    }


    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with(['products', 'payments'])->paginate(10);
        return $this->jsonResponseWithPagination($orders, $orders->total());
    }


    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update($request->validated());
        return $this->successfulResponse($order, __('Order status updated'));
    }
}
