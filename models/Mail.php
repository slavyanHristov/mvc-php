<?php

namespace app\models;

use app\core\Model;
use app\core\TokenCache;
use app\core\Application;
use app\core\GraphHelper;
use Microsoft\Graph\Graph;

class Mail extends Model
{
    public string $subject = '';
    public string $body = '';
    public string $recipient = '';

    public function rules(): array
    {
        return [
            'subject' => [self::RULE_REQUIRED],
            'body' => [self::RULE_REQUIRED],
            'recipient' => [self::RULE_REQUIRED, self::RULE_EMAIL]
        ];
    }

    public function labels(): array
    {
        return [
            'subject' => "Subject",
            'body' => "Content",
            'recipient' => "Send To"
        ];
    }

    public function getMails()
    {

        $graph = GraphHelper::getGraph();

        $queryParams = array(
            '$select' => 'from,isRead,receivedDateTime,subject',
            '$orderby' => 'receivedDateTime DESC',
            '$top' => 25
        );

        $mailsUrl = '/me/messages?' . http_build_query($queryParams);

        $mails = $graph->createRequest('GET', $mailsUrl)
            ->setReturnType(\Microsoft\Graph\Model\Message::class)
            ->execute();

        return $mails;
    }

    public function sendMail(): bool
    {
        $graph = GraphHelper::getGraph();
        $myTxt = nl2br("$this->body");

        $sendMailBody = array(
            'message' => array(
                'subject' => $this->subject,
                'body' => array(
                    'contentType' => 'HTMl',
                    'content' => $myTxt
                ),
                'toRecipients' => array(
                    array(
                        'emailAddress' => array(
                            'address' => $this->recipient
                        )
                    )
                )
            )
        );

        $graph->createRequest('POST', '/me/sendMail')
            ->attachBody($sendMailBody)
            ->execute();

        return true;
    }
}
