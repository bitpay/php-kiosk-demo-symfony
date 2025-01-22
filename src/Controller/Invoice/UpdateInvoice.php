<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Controller\Invoice;

use App\Exception\MissingEntity;
use App\Exception\SignatureVerificationFailed;
use App\Service\Invoice\Update\UpdateInvoiceUsingBitPayIpn;
use App\Service\Shared\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateInvoice
{
    private UpdateInvoiceUsingBitPayIpn $updateInvoice;
    private Logger $logger;

    public function __construct(UpdateInvoiceUsingBitPayIpn $updateInvoice, Logger $logger)
    {
        $this->updateInvoice = $updateInvoice;
        $this->logger = $logger;
    }

    #[Route('/invoices/{uuid}', name: 'update_invoice', methods: [Request::METHOD_POST])]
    public function execute(Request $request, string $uuid): Response
    {
        $this->logger->info('IPN_RECEIVED', 'Received IPN', $request->request->all());

        $content = $request->toArray();

        try {
            $this->updateInvoice->byUuid($uuid, $content, $request->headers->all());
        } catch (MissingEntity $e) {
            $this->logError($e);
            return new Response(null, Response::HTTP_NOT_FOUND);
        } catch (SignatureVerificationFailed $e) {
            $this->logError($e);
            return new Response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            $this->logError($e);
            return new Response('Unable to process update', Response::HTTP_BAD_REQUEST);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param MissingEntity|\Exception $e
     */
    private function logError(MissingEntity|\Exception $e): void
    {
        $this->logger->error('IPN_VALIDATE_FAIL', 'Failed to validate IPN', [
            'errorMessage' => $e->getMessage(),
            'stackTrace' => $e->getTraceAsString()
        ]);
    }
}
