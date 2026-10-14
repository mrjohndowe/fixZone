<?php
namespace App\Billing;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

class StripePaymentGateway implements PaymentGatewayContract
{
    private $token = null;

    public function __construct($stripeToken)
    {
        $this->token = $stripeToken;
    }

    public function payDue($order)
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
        $customer = Customer::create([
            'name' => $order->name,
            'email' => $order->email,
            'source' => $this->token,
        ]);

        $charge = Charge::create([
            'customer' => $customer->id,
            'amount' => 100 * $order->dueAmount(),
            'currency' => config('services.stripe.currency'),
            'description' => 'Due Payment for repair order: ' . $order->tracking, // Add your desired description here
        ]);

        return [
            'success' => $charge->captured,
            'payment_status' => $charge->captured,
            'payment_info' => $order->payment_info . ' <br/>  Customer : ' . $charge->customer . ' | ID : ' . $customer->id,
            'pre_paid' => $order->pre_paid + $order->dueAmount(),
        ];
    }

    public function charge($validated)
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'source' => $this->token,
        ]);
        $charge = Charge::create([
            'customer' => $customer->id,
            'amount' => 100 * $validated['total_charges'],
            'currency' => config('services.stripe.currency'),
            'description' => 'Payment for repair order: ' . $validated['tracking'], // Add your desired description here
        ]);
        $validated['pre_paid'] = $validated['total_charges'];
        $validated['payment_status'] = $charge->captured;
        $validated['payment_info'] = 'Customer : ' . $charge->customer . ' | ID : ' . $customer->id;
        return [
            'success' => $charge->captured,
            'data' => $validated,
        ];
    }
}
