<?php

declare(strict_types=1);

namespace Imdhemy\Purchases\Products;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Imdhemy\GooglePlay\DeveloperNotifications\DeveloperNotification;
use Imdhemy\GooglePlay\DeveloperNotifications\OneTimePurchaseNotification;
use Imdhemy\GooglePlay\Products\ProductPurchase;
use Imdhemy\Purchases\Contracts\ProductContract;
use Imdhemy\Purchases\Exceptions\InvalidNotificationTypeException;
use Imdhemy\Purchases\Facades\Product;
use Imdhemy\Purchases\ValueObjects\Time;

class GoogleProduct implements ProductContract
{
    protected ProductPurchase $product;

    protected string $itemId;

    protected string $token;

    /**
     * GoogleProduct constructor.
     */
    public function __construct(ProductPurchase $product, string $itemId, string $token)
    {
        $this->product = $product;
        $this->itemId = $itemId;
        $this->token = $token;
    }

    /**
     * @throws GuzzleException
     */
    public static function createFromDeveloperNotification(
        DeveloperNotification $rtdNotification,
        ?ClientInterface $client = null
    ): self {
        $notification = $rtdNotification->getPayload();

        // Make sure the notification is a Product notification
        if (!$notification instanceof OneTimePurchaseNotification) {
            throw InvalidNotificationTypeException::create(OneTimePurchaseNotification::class, get_class($notification));
        }

        $packageName = $rtdNotification->getPackageName();

        $productPurchase = Product::googlePlay($client)
            ->packageName($packageName)
            ->id($notification->getSku())
            ->token($notification->getPurchaseToken())
            ->get();

        return new self(
            $productPurchase,
            $notification->getSku(),
            $notification->getPurchaseToken()
        );
    }

    public function getPurchaseState(): int
    {
        return $this->product->getPurchaseState();
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function getProvider(): string
    {
        return 'google_play';
    }

    public function getUniqueIdentifier(): string
    {
        return $this->token;
    }

    public function getProviderRepresentation(): ProductPurchase
    {
        return $this->product;
    }
}
