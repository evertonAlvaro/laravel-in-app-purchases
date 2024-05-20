<?php

declare(strict_types=1);

namespace Imdhemy\Purchases\Contracts;

use Imdhemy\AppStore\Receipts\ReceiptResponse;
use Imdhemy\AppStore\ServerNotifications\V2DecodedPayload;
use Imdhemy\GooglePlay\Products\ProductPurchase;

/**
 * Interface ProductContract.
 */
interface ProductContract
{
    // List of providers
    public const PROVIDER_APP_STORE = 'app_store';
    public const PROVIDER_GOOGLE_PLAY = 'google_play';

    public function getPurchaseState(): int;

    public function getItemId(): string;

    public function getProvider(): string;

    public function getUniqueIdentifier(): string;

    /**
     * @return mixed|ProductPurchase|ReceiptResponse|V2DecodedPayload
     */
    public function getProviderRepresentation();
}
