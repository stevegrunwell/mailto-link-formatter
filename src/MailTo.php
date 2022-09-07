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
    protected $recipients = [];

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
     * @param string|array $recipients Optional. One or more message recipients. Default is empty.
     * @param array        $headers    Optional. Headers (Subject Cc, Bcc, etc.) to include with the
     *                                 message. Default is empty.
     * @param string       $body       Optional. The message body. Default is empty.
     */
    public function __construct($recipients = '', array $headers = [], string $body = '')
    {
        $this->setRecipients($recipients);
        $this->setHeaders($headers);
        $this->setBody($body);
    }

    /**
     * Magic setter for methods.
     *
     * If a set{$property}() method exists, it will be called. Otherwise, the property will be
     * treated as a new header.
     *
     * @param string $property The property being set.
     * @param mixed  $value    The value being assigned to the property or header.
     */
    public function __set($property, $value)
    {
        $method = 'set' . ucwords($property);

        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->setHeader($property, $value);
        }
    }

    /**
     * Magic getter for methods.
     *
     * @param string $property The property being accessed.
     *
     * @return mixed Either the value of the property (if a get{$property}() method exists) or the
     *               value of $this->getHeader($property), with a default of an empty string.
     */
    public function __get($property)
    {
        $method = 'get' . ucwords($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return $this->getHeader($property, '');
        }
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

        if (is_string($value) && 'subject' !== $key) {
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
     * Retrieve a single header.
     *
     * @param string $header  The header name to look for.
     * @param mixed  $default Optional. The default value to return if the header is not set.
     *                        Default value is null.
     *
     * @return mixed Data stored for the given header, or $default if the header has not been set.
     */
    public function getHeader(string $header, $default = null)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : $default;
    }

    /**
     * Set the message body.
     *
     * @param string $body The message body.
     */
    public function setBody(string $body)
    {
        $this->body = trim($body);
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
        $parameters = $this->getHeaders();
        $body = $this->getBody();

        // Flatten multi-dimensional arrays.
        array_walk($parameters, function (&$value, $header) {
            $value = implode(',', (array) $value);
        });

        // Append the body, if we have one.
        if ($body) {
            $parameters['body'] = $body;
        }

        $url = sprintf(
            'mailto:%s?%s',
            implode(',', $this->getRecipients()),
            http_build_query($parameters, "", '&', PHP_QUERY_RFC3986)
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
