<?php
/**
 * Tests the MailTo class.
 *
 * @package SteveGrunwell\MailToLinkFormatter
 */

namespace SteveGrunwell\MailToLinkFormatter\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use SteveGrunwell\MailToLinkFormatter\MailTo;

class MailToTest extends TestCase
{
    public function testSetRecipients()
    {
        $mailto = new MailTo;
        $mailto->setRecipients('test@example.com');

        $this->assertEquals([
            'test@example.com',
        ], $this->getProperty($mailto, 'recipients'));
    }

    public function testSetRecipientsWithAnArray()
    {
        $mailto = new MailTo;
        $mailto->setRecipients([
            'foo@example.com',
            'bar@example.com',
        ]);

        $this->assertEquals([
            'foo@example.com',
            'bar@example.com',
        ], $this->getProperty($mailto, 'recipients'));
    }

    public function testSetRecipientsWithCommaSeparatedString()
    {
        $mailto = new MailTo;
        $mailto->setRecipients('foo@example.com, bar@example.com');

        $this->assertEquals([
            'foo@example.com',
            'bar@example.com',
        ], $this->getProperty($mailto, 'recipients'));
    }

    public function testSetRecipientsSanitizesEmail()
    {
        $mailto = new MailTo;
        $mailto->setRecipients(' test @example.com ');

        $this->assertEquals([
            'test@example.com',
        ], $this->getProperty($mailto, 'recipients'));
    }

    public function testGetRecipients()
    {
        $recipients = [
            'foo-' . uniqid() . '@example.com',
            'bar-' . uniqid() . '@example.com',
        ];

        $mailto = new MailTo;
        $mailto->setRecipients($recipients);

        $this->assertSame($recipients, $this->getProperty($mailto, 'recipients'));
    }

    public function testSetHeaders()
    {
        $mailto = new MailTo;
        $mailto->setHeaders([
            'cc'  => 'foo@example.com',
            'bcc' => 'bar@example.com',
        ]);

        $this->assertEquals([
            'cc'  => [
                'foo@example.com',
            ],
            'bcc' => [
                'bar@example.com',
            ],
        ], $this->getProperty($mailto, 'headers'));
    }

    public function testSetHeader()
    {
        $mailto = new MailTo;
        $mailto->setHeader('subject', 'My Subject');

        $this->assertEquals([
            'subject' => 'My Subject',
        ], $this->getProperty($mailto, 'headers'));
    }

    /**
     * A message can only have one subject.
     */
    public function testSetHeaderDoesNotSplitSubjects()
    {
        $mailto = new MailTo;
        $mailto->setHeader('subject', 'My subject, now with commas');

        $this->assertEquals([
            'subject' => 'My subject, now with commas',
        ], $this->getProperty($mailto, 'headers'));
    }

    public function testSetHeaderOverwritesValue()
    {
        $mailto = new MailTo;
        $mailto->setHeader('subject', 'My Subject');
        $mailto->setHeader('subject', 'My new subject');

        $this->assertEquals([
            'subject' => 'My new subject',
        ], $this->getProperty($mailto, 'headers'));
    }

    public function testSetHeaderLowercasesKeys()
    {
        $mailto = new MailTo;
        $mailto->setHeader('Header', 'Value');

        $this->assertEquals([
            'header' => [
                'Value',
            ],
        ], $this->getProperty($mailto, 'headers'));
    }

    public function testSetHeaderWithMultipleValues()
    {
        $mailto = new MailTo;
        $mailto->setHeader('cc', [
            'foo@example.com',
            'bar@example.com',
        ]);

        $this->assertEquals([
            'cc' => [
                'foo@example.com',
                'bar@example.com',
            ],
        ], $this->getProperty($mailto, 'headers'));
    }

    public function testSetHeaderWithCommaSeparatedString()
    {
        $mailto = new MailTo;
        $mailto->setHeader('cc', 'foo@example.com, bar@example.com');

        $this->assertEquals([
            'cc' => [
                'foo@example.com',
                'bar@example.com',
            ],
        ], $this->getProperty($mailto, 'headers'));
    }

    public function testGetHeaders()
    {
        $headers = [
            'subject' => 'Test subject',
            'header'  => [
                'some header',
            ],
        ];

        $mailto = new MailTo;
        $mailto->setHeaders($headers);

        $this->assertSame($headers, $this->getProperty($mailto, 'headers'));
    }

    public function testGetHeader()
    {
        $value = uniqid();
        $mailto = new MailTo;
        $mailto->setHeader('some-header', $value);

        $this->assertEquals([
            $value,
        ], $mailto->getHeader('some-header'));
    }

    public function testGetHeaderWithMissingValue()
    {
        $value = uniqid();
        $mailto = new MailTo;

        $this->assertEquals($value, $mailto->getHeader('some-header', $value));
    }

    public function testSetBody()
    {
        $mailto = new MailTo;
        $mailto->setBody('This is the message body.');

        $this->assertEquals('This is the message body.', $this->getProperty($mailto, 'body'));
    }

    public function testSetBodyTrimsText()
    {
        $mailto = new MailTo;
        $mailto->setBody('  This is the message body.' . PHP_EOL);

        $this->assertEquals('This is the message body.', $this->getProperty($mailto, 'body'));
    }

    public function testGetBody()
    {
        $mailto = new MailTo;
        $mailto->setBody('This is the message body');

        $this->assertEquals('This is the message body', $mailto->getBody());
    }

    public function testGetLinkWithSingleRecipient()
    {
        $mailto = new MailTo('test@example.com');

        $this->assertEquals('mailto:test@example.com', $mailto->getLink());
    }

    public function testGetLinkWithMultipleRecipients()
    {
        $mailto = new MailTo([
            'foo@example.com',
            'bar@example.com',
        ]);
        $this->assertEquals('mailto:foo@example.com,bar@example.com', $mailto->getLink());
    }

    public function testGetLinkWithHeaders()
    {
        $mailTo = new MailTo('test@example.com', [
            'subject' => 'My Subject',
            'cc'      => 'foo@example.com',
        ]);

        $this->assertEquals(
            'mailto:test@example.com?subject=My%20Subject&cc=foo%40example.com',
            $mailTo->getLink()
        );
    }

    public function testGetLinkWithBody()
    {
        $body = <<<EOT
Bacon ipsum dolor amet drumstick kevin biltong pork belly capicola leberkas ham. Porchetta beef short ribs filet mignon, spare ribs hamburger beef ribs cupim biltong shankle.

Spare ribs beef porchetta, pastrami flank bresaola shoulder shank. Bacon salami pancetta jowl beef, shank shoulder boudin sausage swine landjaeger. Burgdoggen pancetta tri-tip shoulder pork belly beef ribs landjaeger shank cow beef cupim sausage salami leberkas.

Fatback biltong shank burgdoggen pork loin pig pork chop ground round chicken pastrami.
EOT;
        $mailto = new MailTo('test@example.com');
        $mailto->setBody($body);

        $this->assertEquals(
            'mailto:test@example.com?body=' . rawurlencode($body),
            $mailto->getLink()
        );
    }

    public function testGetLinkWithMultipleValuesForHeader()
    {
        $mailTo = new MailTo('test@example.com', [
            'cc' => [
                'foo@example.com',
                'bar@example.com',
            ],
        ]);

        $this->assertEquals(
            'mailto:test@example.com?cc=foo%40example.com%2Cbar%40example.com',
            $mailTo->getLink()
        );
    }

    /**
     * @testWith ["test@example.com", "test@example.com"]
     *           ["  test@example\\.com  ", "test@example.com"]
     *           ["test()@example.com", "test@example.com"]
     */
    public function testSanitizeEmail($email, $expected)
    {
        $this->assertEquals($expected, MailTo::sanitizeEmail($email));
    }

    /**
     * Retrieve the value of a protected property via reflection.
     *
     * @param MailTo $instance The MailTo instance to inspect.
     * @param string $property The property name to inspect.
     *
     * @return mixed The value of the $property property.
     */
    protected function getProperty(MailTo $instance, string $property)
    {
        $prop = new ReflectionProperty($instance, $property);
        $prop->setAccessible(true);

        return $prop->getValue($instance);
    }
}
