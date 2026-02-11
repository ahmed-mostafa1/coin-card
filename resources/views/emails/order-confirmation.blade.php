@extends('emails.layouts.app')

@section('content')
  <x-emails.layouts.app :subject="'Your Order #' . $order->id . ' is Confirmed'" :title="'Thanks for your order!'" :introLines="[
      'Hi ' . $order->user->name . ',',
      'We\'ve received your order and are getting it ready. We will notify you again once it has been processed.',
  ]" :actionText="'View Your Order'" :actionUrl="route('account.orders.show', $order)" :outroLines="['Thank you for your business.']">

    {{-- Custom Content Slot --}}
    <h2 style="margin: 24px 0 16px; font-size: 18px; font-weight: 700; color: #111827;">Order Summary</h2>
    <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
      <tr>
        <td style="padding: 12px 0; border-bottom: 1px solid #E5E7EB;">
          <p style="margin: 0; font-size: 14px; color: #6B7280;">Order ID:</p>
          <p style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: #111827;">#{{ $order->id }}</p>
        </td>
      </tr>
      <tr>
        <td style="padding: 12px 0; border-bottom: 1px solid #E5E7EB;">
          <p style="margin: 0; font-size: 14px; color: #6B7280;">Service:</p>
          <p style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: #111827;">{{ $order->service->name }}</p>
        </td>
      </tr>
      <tr>
        <td style="padding: 12px 0;">
          <p style="margin: 0; font-size: 14px; color: #6B7280;">Total Price:</p>
          <p style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: #111827;">${{ number_format($order->price_at_purchase, 2) }}</p>
        </td>
      </tr>
    </table>

  </x-emails.layouts.app>
@endsection

{{--
This example shows how to inject custom content (like an order summary table) into the main slot.
For this to work, you'd pass the order object to the view and use it.
The Mailable would look something like:
return $this->view('emails.examples.order-confirmation', ['order' => $this->order]);
--}}