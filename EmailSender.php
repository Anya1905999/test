<?php
class EmailSender
{
    public function __construct()
    {

    }

    public function sendEmail(string $hostname, int $port, string $username, string $password, string $to, string $from, string $subject, string $message): bool
    {

        // Устанавливаем соединение
        $handle = fsockopen('ssl://' . $hostname, $port, $errno, $errstr, 10);

        if ($handle === false) {
            return false;
        }

        stream_set_timeout($handle, 30); // Установка таймаута на 30 секунд

        if ($this->mailHello($handle) === false) {
            $this->mailDie($handle);
            return false;
        }

        if ($this->mailAuth($handle, $username, $password) === false) {
            $this->mailDie($handle);
            return false;
        }

        $this->mailFrom($handle, $from);

        $this->mailTo($handle, $to);

        $this->mailMessage($handle, $from, $to, $subject, $message);

        sleep(1);

        $this->mailDie($handle);

        return true;

    }

    // Если нужно обработать какие-то данные
    public function handler()
    {
        $hostname = 'smtp.mail.ru';
        $port = 465; // порт для SMTPS (SSL)
        $username = ''; // замените на свой адрес электронной почты
        $password = ''; // замените на свой пароль
        $to = ''; // адрес получателя
        $from = ''; // ваш адрес электронной почты
        $subject = 'Заголовок';
        $message = "Какое-то сообщение {{test1}}, {{test2}}";

        $message = strtr($message, [
            '{{test1}}' => '321',
            '{{test2}}' => '123'
        ]);

        return $this->sendEmail($hostname, $port, $username, $password, $to, $from, $subject, $message);

    }

    /**
     * @param $handle
     * @param string $from
     * @return void
     */
    public function mailFrom($handle, string $from): void
    {
        fwrite($handle, "MAIL FROM: <$from>\r\n");
//        return $this->mailIsError($handle);
    }

    /**
     * @param $handle
     * @param string $to
     * @return void
     */
    public function mailTo($handle, string $to): void
    {
        fwrite($handle, "RCPT TO: <$to>\r\n");
//        return $this->mailIsError($handle);
    }

    /**
     * @param $handle
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function mailMessage($handle, string $from, string $to, string $subject, string $message): void
    {
        fwrite($handle, "DATA\r\n");
        fwrite($handle, "From: $from\r\n");
        fwrite($handle, "To: $to\r\n");
        fwrite($handle, "Subject: $subject\r\n");
        fwrite($handle, "\r\n");
        fwrite($handle, "$message\r\n");
        fwrite($handle, ".\r\n");
//        return $this->mailIsError($handle);
    }

    /**
     * @param $handle
     * @return void
     */
    public function mailDie($handle): void
    {
        fwrite($handle, "QUIT\r\n");
        fclose($handle);
    }

    /**
     * @param $handle
     * @return bool
     */
    public function mailHello($handle): bool
    {
        fwrite($handle, "EHLO test.com\r\n");
        return $this->mailIsError($handle);

    }

    /**
     * @param $handle
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function mailAuth($handle, string $username, string $password): bool
    {
        fwrite($handle, "AUTH LOGIN\r\n");
        fwrite($handle, base64_encode($username) . "\r\n");
        fwrite($handle, base64_encode($password) . "\r\n");
        return $this->mailIsError($handle);
    }

    /**
     * @param $handle
     * @param string $reply
     * @return bool
     */
    public function mailIsError($handle): bool
    {
        return true;
//        $reply = '';
//        while ($line = fread($handle, 8192)) {
//            $reply .= $line;
//
//            if (substr($reply, 0, 1) == 2 || substr($reply, 0, 1) == 3) {
//                $reply = '';
//            } else {
//                return false;
//            }
//        }
//        return true;
    }
}
