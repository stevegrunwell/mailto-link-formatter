<?php
/**
 * Tests the MailTo class.
 *
 * @package SteveGrunwell\MailToLinkFormatter
 */

use PHPUnit\Framework\TestCase;
use SteveGrunwell\MailToLinkFormatter\MailTo;

class MailToTest extends TestCase
{
    public function testSettingSingleRecipient()
    {
        $this->assertEquals(
            'mailto:test@example.com',
            (new MailTo('test@example.com'))->getLink()
        );
    }

    public function testSettingMultipleRecipients()
    {
        $this->assertEquals(
            'mailto:test@example.com,anothertest@example.com',
            (new MailTo([
                'test@example.com',
                'anothertest@example.com',
            ]))->getLink()
        );
    }

    public function testSettingMultipleRecipientsViaString()
    {
        $this->assertEquals(
            'mailto:test@example.com,anothertest@example.com',
            (new MailTo('test@example.com, anothertest@example.com'))->getLink()
        );
    }

    /**
     * @testWith ["subject", "My Subject", "?subject=My+Subject"]
     *           ["cc", "address@example.com", "?cc=address%40example.com"]
     */
    public function testSettingHeaders($header, $value, $expected)
    {
        $this->assertEquals(
            'mailto:test@example.com' . $expected,
            (new MailTo('test@example.com', [
                $header => $value,
            ]))->getLink()
        );
    }

    /**
     * @testWith ["CC", "cc=value"]
     *           ["Cc", "cc=value"]
     *           ["cc", "cc=value"]
     *           ["BCC", "bcc=value"]
     *           ["Bcc", "bcc=value"]
     *           ["bcc", "bcc=value"]
     */
    public function testHeaderNamesAreMadeLowercase($header, $expected)
    {
        $this->assertEquals(
            'mailto:test@example.com?' . $expected,
            (new MailTo('test@example.com', [
                $header => 'value',
            ]))->getLink()
        );
    }

    public function testSettingMultipleHeaderValuesForSameKey()
    {
        $this->assertEquals(
            'mailto:test@example.com?bcc=first%40example.com%2Csecond%40example.com',
            (new MailTo('test@example.com', [
                'bcc' => [
                    'first@example.com',
                    'second@example.com',
                ],
            ]))->getLink()
        );
    }

    public function testSettingMultipleHeaderValuesForSameKeyViaString()
    {
        $this->assertEquals(
            'mailto:test@example.com?bcc=first%40example.com%2Csecond%40example.com',
            (new MailTo('test@example.com', [
                'bcc' => 'first@example.com, second@example.com',
            ]))->getLink()
        );
    }

    public function testSettingMultipleHeaders()
    {
        $this->assertEquals(
            'mailto:test@example.com?subject=Subject&cc=cc%40example.com&bcc=bcc%40example.com',
            (new MailTo('test@example.com', [
                'subject' => 'Subject',
                'cc'      => 'cc@example.com',
                'bcc'     => 'bcc@example.com',
            ]))->getLink()
        );
    }

    /**
     * @testWith ["test@example.com", "test@example.com"]
     *           ["  test@example.com  ", "test@example.com"]
     *           ["test()@example.com", "test@example.com"]
     */
    public function testSanitizeEmail($email, $expected)
    {
        $this->assertEquals($expected, MailTo::sanitizeEmail($email));
    }
}
