<?php declare(strict_types=1);

namespace App\Event\Listener;

use Payum\Core\Bridge\Symfony\Event\ExecuteEvent;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use App\Entity\Payment;

class UpdatePaymentStatus
{
    public function updateStatus(ExecuteEvent $event)
    {
        $request = $event->getContext()->getRequest();

        if ($request instanceof GetStatusInterface && $request instanceof Generic) {
            $payment = $request->getFirstModel();
            if ($request->isCaptured() && $payment instanceof Payment) {
                // payment is completed and succeeded
                // do whatever you want
            }
        }
    }
}
