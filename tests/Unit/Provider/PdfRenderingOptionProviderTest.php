<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGiftCardPlugin\Model\GiftCardConfiguration;
use Setono\SyliusGiftCardPlugin\Provider\PdfRenderingOptionsProvider;
use Setono\SyliusGiftCardPlugin\Provider\PdfRenderingOptionsProviderInterface;

final class PdfRenderingOptionProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_rendering_options(): void
    {
        $provider = new PdfRenderingOptionsProvider();
        $giftCardConfiguration = new GiftCardConfiguration();

        $renderingOptions = $provider->getRenderingOptions($giftCardConfiguration);
        $this->assertIsArray($renderingOptions);
    }

    /**
     * @test
     */
    public function it_provides_page_size_if_not_null(): void
    {
        $provider = new PdfRenderingOptionsProvider();
        $giftCardConfiguration = new GiftCardConfiguration();
        $giftCardConfiguration->setPageSize(PdfRenderingOptionsProviderInterface::PAGE_SIZE_A8);

        $renderingOptions = $provider->getRenderingOptions($giftCardConfiguration);
        $this->assertArrayHasKey('page-size', $renderingOptions);
        $this->assertEquals(PdfRenderingOptionsProviderInterface::PAGE_SIZE_A8, $renderingOptions['page-size']);
    }

    /**
     * @test
     */
    public function it_provides_orientation_if_not_null(): void
    {
        $provider = new PdfRenderingOptionsProvider();
        $giftCardConfiguration = new GiftCardConfiguration();
        $giftCardConfiguration->setOrientation('Landscape');

        $renderingOptions = $provider->getRenderingOptions($giftCardConfiguration);
        $this->assertArrayHasKey('orientation', $renderingOptions);
        $this->assertEquals('Landscape', $renderingOptions['orientation']);
    }
}
