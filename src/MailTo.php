<?php
/**
 * Formatter for "mailto:" links.
 *
 * @package SteveGrunwell\MailToLinkFormatter
 * @author  Steve Grunwell
 */

namespace SteveGrunwell\MailToLinkFormatter;

class MailTo
{
    /**
     * The primary message recipient(s).
     *
     * @var array
     */
    protected $to = [];

    /**
     * Additional headers to attach to the message.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The message body.
     *
     * @var string
     */
    protected $body;

    /**
     * Create a new MailTo instance.
     *
     * @param string|array $recipient One or more message recipients.
     * @param array        $headers   Optional. Headers (Subject Cc, Bcc, etc.) to include with the
     *                                message. Default is empty.
     * @param string       $body      Optional. The message body. Default is empty.
     */
    public function __construct( $to, array $headers = [], string $body = '' )
    {
        $this->setRecipients($to);
        $this->setHeaders($headers);
        $this->setBody($body);
    }

    /**
     * Set the message recipient(s).
     *
     * @param string|array $to One or more message recipients.
     */
    public function setRecipients($recipients)
    {
        if (is_string($recipients)) {
            $recipients = explode(',', $recipients);
        }

        $this->recipients = array_map(__CLASS__ . '::sanitizeEmail', $recipients);
    }

    /**
     * Retrieve the message recipient(s).
     *
     * @return array The message recipient(s).
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Set the headers array.
     *
     * @param array $headers The headers to set.
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }
    }

    /**
     * Add a new header to the headers array.
     *
     * @param string $key   The header name.
     * @param mixed  $value The header value.
     */
    public function setHeader(string $key, $value)
    {
        $key = strtolower($key);

        if (is_string($value)) {
            $value = array_map('trim', explode(',', $value));
        }

        $this->headers[$key] = $value;
    }

    /**
     * Retrieve all of the headers for the link.
     *
     * @return array All headers for the link
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set the message body.
     *
     * @param string $body The message body.
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * Get the message body.
     *
     * @return string The message body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Construct a mailto: link from the provided attributes.
     *
     * @return string A valid mailto: link.
     */
    public function getLink(): string
    {
        $headers = $this->getHeaders();

        // Flatten multi-dimensional arrays.
        array_walk($headers, function (&$value, $header) {
            $value = implode(',', (array) $value);
        });

        $url = sprintf(
            'mailto:%s?%s',
            implode(',', $this->getRecipients()),
            http_build_query($headers)
        );

        // If the link ends in a question mark, strip it off.
        if ('?' === substr($url, -1, 1)) {
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    /**
     * Sanitize an email address.
     *
     * @param string $email The email address to sanitize.
     *
     * @return string The sanitized email address.
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}
