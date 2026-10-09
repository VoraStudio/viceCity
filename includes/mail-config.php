<?php

declare(strict_types=1);

require_once __DIR__ . '/site.php';

$site = siteConfig();

return [
    'recipient' => $site['mail_recipient'],
    'sender' => $site['mail_sender'],
    'subject_prefix' => $site['mail_subject_prefix'],
];
