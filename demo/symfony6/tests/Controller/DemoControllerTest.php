<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\DemoController
 */
final class DemoControllerTest extends WebTestCase
{
    public function testFormPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Phone Input Bundle');
        $this->assertSelectorTextContains('body', 'Prefix options');
        $this->assertSelectorTextContains('body', 'Prefix display modes');
        $this->assertSelectorTextContains('body', 'Flag display modes');
        $this->assertSelectorExists('#demo-framework-select');
    }

    public function testFormContainsAllFormatCombinationFields(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('select[name="demo_form[phone_concatenated_with_prefix][country_iso]"]');
        $this->assertSelectorExists('input[name="demo_form[phone_concatenated_with_prefix][national_number]"]');
        $this->assertSelectorExists('input[name="demo_form[phone_concatenated_without_prefix][national_number]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_separated_with_prefix][country_iso]"]');
        $this->assertSelectorExists('input[name="demo_form[phone_object_with_prefix][national_number]"]');
        $this->assertSelectorExists('input[name="demo_form[phone_object_without_prefix][national_number]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_with_prefix_search][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_selector_only][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_allowed_iberian][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_excluded_fr][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_without_flag][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_prefix_display_full][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_prefix_display_iso_and_prefix][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_prefix_display_flag_only][country_iso]"]');
        $this->assertSelectorExists('.nowo-phone-input__prefix-picker--flag-only');
        $this->assertSelectorExists('select[name="demo_form[phone_flag_display_emoji][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_flag_display_css_icon][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_flag_display_ux_icon][country_iso]"]');
        $this->assertSelectorExists('select[name="demo_form[phone_flag_display_none][country_iso]"]');
        $this->assertSelectorExists('body.demo-page--bootstrap5');
        $this->assertSelectorExists('.nowo-phone-input__prefix-picker--no-flag');
        $this->assertSelectorExists('.nowo-phone-input__prefix-picker--no-search');
        $this->assertSelectorExists('.nowo-phone-input__prefix-search-input');
    }

    public function testFrameworkSelectorSwitchesPageClass(): void
    {
        $client = static::createClient();
        $client->request('GET', '/?framework=tailwind2');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body.demo-page--tailwind2');
        $this->assertSame(
            'tailwind2',
            $client->getCrawler()->filter('#demo-framework-select option[selected]')->attr('value'),
        );
    }

    public function testFormIsPrefilledWithDefaultValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertInputValueSame(
            'demo_form[phone_concatenated_with_prefix][national_number]',
            '612345678',
        );
        $this->assertSame(
            'ES',
            $crawler->filter('select[name="demo_form[phone_concatenated_with_prefix][country_iso]"] option[selected]')->attr('value'),
        );
        $this->assertInputValueSame(
            'demo_form[phone_concatenated_without_prefix][national_number]',
            '612345679',
        );
        $this->assertInputValueSame(
            'demo_form[phone_separated_with_prefix][national_number]',
            '612345680',
        );
        $this->assertSame(
            'FR',
            $crawler->filter('select[name="demo_form[phone_separated_with_prefix][country_iso]"] option[selected]')->attr('value'),
        );
        $this->assertInputValueSame(
            'demo_form[phone_object_with_prefix][national_number]',
            '7911123456',
        );
    }

    public function testFormCanBeSubmittedWithAllCombinations(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Submit all formats')->form([
            'framework' => 'bootstrap5',
            'demo_form[phone_concatenated_with_prefix][country_iso]'        => 'ES',
            'demo_form[phone_concatenated_with_prefix][national_number]'    => '612345678',
            'demo_form[phone_concatenated_without_prefix][national_number]' => '612345679',
            'demo_form[phone_separated_with_prefix][country_iso]'           => 'FR',
            'demo_form[phone_separated_with_prefix][national_number]'       => '612345680',
            'demo_form[phone_separated_without_prefix][national_number]'    => '612345681',
            'demo_form[phone_object_with_prefix][country_iso]'              => 'GB',
            'demo_form[phone_object_with_prefix][national_number]'          => '7911123456',
            'demo_form[phone_object_without_prefix][national_number]'       => '612345682',
            'demo_form[phone_with_prefix_search][country_iso]'              => 'ES',
            'demo_form[phone_with_prefix_search][national_number]'          => '612345684',
            'demo_form[phone_selector_only][country_iso]'                   => 'PT',
            'demo_form[phone_selector_only][national_number]'               => '912345678',
            'demo_form[phone_allowed_iberian][country_iso]'                 => 'PT',
            'demo_form[phone_allowed_iberian][national_number]'             => '912345679',
            'demo_form[phone_excluded_fr][country_iso]'                     => 'ES',
            'demo_form[phone_excluded_fr][national_number]'                 => '612345682',
            'demo_form[phone_without_flag][country_iso]'                    => 'ES',
            'demo_form[phone_without_flag][national_number]'                => '612345683',
            'demo_form[phone_prefix_display_full][country_iso]'            => 'ES',
            'demo_form[phone_prefix_display_full][national_number]'        => '612345685',
            'demo_form[phone_prefix_display_prefix_only][country_iso]'     => 'ES',
            'demo_form[phone_prefix_display_prefix_only][national_number]' => '612345686',
            'demo_form[phone_prefix_display_flag_and_prefix][country_iso]' => 'ES',
            'demo_form[phone_prefix_display_flag_and_prefix][national_number]' => '612345687',
            'demo_form[phone_prefix_display_iso_and_prefix][country_iso]'  => 'FR',
            'demo_form[phone_prefix_display_iso_and_prefix][national_number]' => '612345688',
            'demo_form[phone_prefix_display_flag_only][country_iso]'       => 'ES',
            'demo_form[phone_prefix_display_flag_only][national_number]'   => '612345693',
            'demo_form[phone_flag_display_emoji][country_iso]'             => 'ES',
            'demo_form[phone_flag_display_emoji][national_number]'         => '612345689',
            'demo_form[phone_flag_display_css_icon][country_iso]'          => 'ES',
            'demo_form[phone_flag_display_css_icon][national_number]'      => '612345690',
            'demo_form[phone_flag_display_ux_icon][country_iso]'           => 'ES',
            'demo_form[phone_flag_display_ux_icon][national_number]'       => '612345691',
            'demo_form[phone_flag_display_none][country_iso]'              => 'ES',
            'demo_form[phone_flag_display_none][national_number]'          => '612345692',
        ]);

        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', 'Form submitted');
        $this->assertSelectorTextContains('.result-card', '+34612345678');
        $this->assertSelectorTextContains('body', 'e164=+447911123456');
        $this->assertSelectorTextContains('body', '"iso":"FR"');
        $this->assertSelectorTextContains('body', '+351912345678');
        $this->assertSelectorExists('body.demo-page--bootstrap5');
    }
}
