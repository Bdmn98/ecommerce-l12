<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{

    public function store(Order $order)
    {

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'status' => PaymentStatusEnum::SUCCESS->value,
            'meta' => ['provider' => 'mock', 'txn_id' => uniqid('txn_')],
        ]);


        $order->update([
            'status' => OrderStatusEnum::PAID->value,
        ]);

        return $this->successfulResponse($payment, __('Payment processed'), 201);
    }

    /**
     * Ödeme detayını getir
     */
    public function show(Payment $payment)
    {


        return $this->successfulResponse($payment);
    }
}
