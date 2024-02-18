<?php

namespace App\Common;

use InvalidArgumentException;

class EmailConfig
{
    private static string $notificationSenderAddress;
    private static string $notificationSenderName;
    private static string $notificationRecipientAddress;

    public static function set(array $config): void
    {
        self::$notificationSenderAddress = $config['notification_sender_address'] ?? throw new InvalidArgumentException('Notification sender email address is missing from email config!');
        if (!filter_var(self::$notificationSenderAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Notification sender email address is not valid!');
        }
        self::$notificationSenderName = $config['notification_sender_name'] ?? throw new InvalidArgumentException('Notification sender name is missing from email config!');
        self::$notificationRecipientAddress = $config['notification_recipient_address'] ?? throw new InvalidArgumentException('Notification recipient email address is missing from email config!');
        if (!filter_var(self::$notificationRecipientAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Notification recipient email address is not valid!');
        }
    }

    public static function getNotificationSenderAddress(): string
    {
        return self::$notificationSenderAddress;
    }

    public static function getNotificationSenderName(): string
    {
        return self::$notificationSenderName;
    }

    public static function getNotificationRecipientAddress(): string
    {
        return self::$notificationRecipientAddress;
    }
}
