<?php

namespace Potsky\LaravelLocalizationHelpers\Factory;

class MessageBag implements MessageBagInterface
{
    const LINE = 'line';

    const INFO = 'info';

    const COMMENT = 'comment';

    const QUESTION = 'question';

    const ERROR = 'error';

    private array $bag = [];

    /**
     * Tell whether or not this bag has pending messages.
     */
    public function hasMessages(): bool
    {
        return count($this->bag) > 1;
    }

    /**
     * Get all messages as an array.
     *
     * Use getMessageType and getMessage to parse a message
     */
    public function getMessages(): array
    {
        return $this->bag;
    }

    /**
     * Clean the bag by removing all messages.
     */
    public function deleteMessages(): void
    {
        $this->bag = [];
    }

    /**
     * Get the message type of a message get by getMessages.
     *
     * @return mixed
     */
    public function getMessageType(array $message)
    {
        return $message[0];
    }

    /**
     * Get the message text.
     *
     * @return mixed
     */
    public function getMessage(array $message)
    {
        return $message[1];
    }

    /**
     * Add a simple message.
     *
     * @param  string  $s  the message to display
     */
    public function writeLine($s): void
    {
        $message = $this->cleanMessage($s);

        if (! in_array($message, [null, '', '0'], true)) {
            $this->bag[] = [self::LINE, $message];
        }
    }

    /**
     * Add an info message.
     *
     * @param  string  $s  the message to display
     */
    public function writeInfo($s): void
    {
        $message = $this->cleanMessage($s);

        if (! in_array($message, [null, '', '0'], true)) {
            $this->bag[] = [self::INFO, $message];
        }
    }

    /**
     * Add a comment message.
     *
     * @param  string  $s  the message to display
     */
    public function writeComment($s): void
    {
        $message = $this->cleanMessage($s);

        if (! in_array($message, [null, '', '0'], true)) {
            $this->bag[] = [self::COMMENT, $message];
        }
    }

    /**
     * Add a question message.
     *
     * @param  string  $s  the message to display
     */
    public function writeQuestion($s): void
    {
        $message = $this->cleanMessage($s);

        if (! in_array($message, [null, '', '0'], true)) {
            $this->bag[] = [self::QUESTION, $message];
        }
    }

    /**
     * Add an error message.
     *
     * @param  string  $s  the message to display
     */
    public function writeError($s): void
    {
        $message = $this->cleanMessage($s);

        if (! in_array($message, [null, '', '0'], true)) {
            $this->bag[] = [self::ERROR, $message];
        }
    }

    /**
     * Trim and remove all XML tags.
     *
     * @param  string  $m  the message to clean
     */
    protected function cleanMessage($m): ?string
    {
        return preg_replace('@<[A-Za-z0-9/]*>@', '', trim($m));
    }
}
