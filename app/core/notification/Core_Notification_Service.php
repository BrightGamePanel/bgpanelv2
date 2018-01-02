<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 02/01/2018
 * Time: 13:39
 */

final class Core_Notification_Service
{
    /**
     * Singleton instance
     *
     * @var Core_Notification_Service
     */
    private static $service = null;

    /**
     * Stores notifications as Array
     *
     * @var array
     */
    private $notifications = array();

    /**
     * Core_Notification_Service constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return Core_Notification_Service
     */
    public static function getService()
    {
        if (empty(self::$service) ||
            !is_object(self::$service) ||
            !is_a(self::$service, 'Core_Notification_Service')) {
            self::$service = new Core_Notification_Service();
        }
        return self::$service;
    }

    public function sendNotification($message, $description, $severity = 'warning') {

        $event = array(
            'id' => uniqid('event_'),
            'message' => $message,
            'description' => $description,
            'severity' => $severity
        );

        $this->notifications[] = $event;
    }

    public function getNotifications() {
        return $this->notifications;
    }

    public function readNotification() {

        // TODO : must be implemented
    }
}