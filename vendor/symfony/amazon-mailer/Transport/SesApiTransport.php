<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Bridge\Amazon\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractApiTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trigger_deprecation('symfony/amazon-mailer', '5.1', 'The "%s" class is deprecated, use "%s" instead. The Amazon transport now requires "AsyncAws". Run "composer require async-aws/ses".', SesApiTransport::class, SesApiAsyncAwsTransport::class);

/**
 * @author Kevin Verschaeve
 */
class SesApiTransport extends AbstractApiTransport
{
    private const HOST = 'email.%region%.amazonaws.com';

    private $accessKey;
    private $secretKey;
    private $region;

    /**
     * @param string|null $region Amazon SES region
     */
    public function __construct(string $accessKey, string $secretKey, ?string $region = null, ?HttpClientInterface $client = null, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region = $region ?: 'eu-west-1';

        parent::__construct($client, $dispatcher, $logger);
    }

    public function __toString(): string
    {
        return sprintf('ses+api://%s@%s', $this->accessKey, $this->getEndpoint());
    }

    protected function doSendApi(SentMessage $sentMessage, Email $email, Envelope $envelope): ResponseInterface
    {
        $date = gmdate('D, d M Y H:i:s e');
        $auth = sprintf('AWS3-HTTPS AWSAccessKeyId=%s,Algorithm=HmacSHA256,Signature=%s', $this->accessKey, $this->getSignature($date));

        $response = $this->client->request('POST', 'https://'.$this->getEndpoint(), [
            'headers' => [
                'X-Amzn-Authorization' => $auth,
                'Date' => $date,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => $payload = $this->getPayload($email, $envelope),
        ]);

        try {
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
        } catch (TransportExceptionInterface $e) {
            throw new HttpTransportException('Could not reach the remote Amazon server.', $response, 0, $e);
        }

        $result = new \SimpleXMLElement($content);
        if (200 !== $statusCode) {
            throw new HttpTransportException('Unable to send an email: '.$result->Error->Message.sprintf(' (code %d).', $result->Error->Code), $response);
        }

        $property = $payload['Action'].'Result';

        $sentMessage->setMessageId($result->{$property}->MessageId);

        return $response;
    }

    private function getEndpoint(): ?string
    {
        return ($this->host ?: str_replace('%region%', $this->region, self::HOST)).($this->port ? ':'.$this->port : '');
    }

    private function getSignature(string $string): string
    {
        return base64_encode(hash_hmac('sha256', $string, $this->secretKey, true));
    }

    private function getPayload(Email $email, Envelope $envelope): array
    {
        if ($email->getAttachments()) {
            $payload = [
                'Action' => 'SendRawEmail',
                'RawMessage.Data' => base64_encode($email->toString()),
            ];

            if ($header = $email->getHeaders()->get('X-SES-CONFIGURATION-SET')) {
                $payload['ConfigurationSetName'] = $header->getBodyAsString();
            }

            if ($header = $email->getHeaders()->get('X-SES-SOURCE-ARN')) {
                $payload['FromEmailAddressIdentityArn'] = $header->getBodyAsString();
            }

            return $payload;
        }

        $payload = [
            'Action' => 'SendEmail',
            'Destination.ToAddresses.member' => $this->stringifyAddresses($this->getRecipients($email, $envelope)),
            'Message.Subject.Data' => $email->getSubject(),
            'Source' => $envelope->getSender()->toString(),
        ];

        if ($emails = $email->getCc()) {
            $payload['Destination.CcAddresses.member'] = $this->stringifyAddresses($emails);
        }
        if ($emails = $email->getBcc()) {
            $payload['Destination.BccAddresses.member'] = $this->stringifyAddresses($emails);
        }
        if ($email->getTextBody()) {
            $payload['Message.Body.Text.Data'] = $email->getTextBody();
        }
        if ($email->getHtmlBody()) {
            $payload['Message.Body.Html.Data'] = $email->getHtmlBody();
        }
        if ($email->getReplyTo()) {
            $payload['ReplyToAddresses.member'] = $this->stringifyAddresses($email->getReplyTo());
        }
        if ($header = $email->getHeaders()->get('X-SES-CONFIGURATION-SET')) {
            $payload['ConfigurationSetName'] = $header->getBodyAsString();
        }
        if ($header = $email->getHeaders()->get('X-SES-SOURCE-ARN')) {
            $payload['FromEmailAddressIdentityArn'] = $header->getBodyAsString();
        }

        return $payload;
    }

    protected function stringifyAddresses(array $addresses): array
    {
        return array_map(function (Address $a) {
            // AWS does not support UTF-8 address
            if (preg_match('~[\x00-\x08\x10-\x19\x7F-\xFF\r\n]~', $name = $a->getName())) {
                return sprintf('=?UTF-8?B?%s?= <%s>',
                    base64_encode($name),
                    $a->getEncodedAddress()
                );
            }

            return $a->toString();
        }, $addresses);
    }
}
