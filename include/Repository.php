<?php

declare(strict_types=1);

class Repository
{
    /**
     * @var integer Number of seconds to send messages
     */
    private $timeoutSeconds;
    
    /**
     * @var string path to file with XML data
     */
    private $xmlFile;
    
    /**
     * @var Message[] List of all messages
     */
    private $messages = [];
    
    /**
     * Default XML header
     * @var string 
     */
    private $defaultXmlHead = '<?xml version="1.0" encoding="UTF-8"?><messages></messages>';
    
    /**
     * @param string $xmlFile
     * @param integer $timeoutSeconds
     */
    public function __construct(string $xmlFile, int $timeoutSeconds = 10)
    {
        $this->timeoutSeconds = $timeoutSeconds;
        $this->xmlFile = $xmlFile;
        $this->checkFile();
        $this->fromFile();
    }

    /**
     * Get one message by id
     * @param integer $id Message ID
     * @return Message|null
     */
    public function one(int $id): ?Message
    {
        return $this->messages[$id] ?? null;
    }
    
    /**
     * Проверяет, есть ли сообщение с таким ID
     * @param int $id
     * @return bool
     */
    public function exist(int $id): bool
    {
        return isset($this->messages[$id]);
    }
    
    /**
     * @return Message[]
     */
    public function all()
    {
        return $this->getAllRecursive(array_values($this->messages));
    }
    
    /**
     * @param Message[] $allMessages
     * @param type $parent
     */
    private function getAllRecursive($allMessages, $parent = null)
    {
        $list = [];
        foreach($allMessages as $message) {
            if($message->getParent() === $parent) {
                $message->setChildrens($this->getAllRecursive($allMessages, $message->getId()));
                $list[] = $message;
            }
        }
        return $list;
    }
    
    /**
     * Delete a message
     * @param type $id
     * @return $this
     */
    public function delete(int $id): Repository
    {
        unset($this->messages[$id]);
        return $this;
    }
    
    /**
     * Checks whether it is possible to send a new message,
     * and returns the number of seconds through which you can send a new message.
     * If 0 is returned, then the message can be sent
     * @return int
     */
    public function nextMessageTimeout(): int
    {
        $messages = $this->messages;
        if(!empty($messages)) {
            /* @var $lastMessage Message */
            $lastMessage = end($messages);
            $now = (new DateTime('now'))->getTimestamp();
            $end  = $lastMessage->getCreated()->getTimestamp();
            $allowTimeStamp = $end + $this->timeoutSeconds;
            if($allowTimeStamp > $now) {
                return $allowTimeStamp - $now;
            }
        }
        return 0;
    }
    
    /**
     * Create a new message
     * @param string $text Message text
     * @param int|null $parent ID of parent message, to be answered to
     * @return $this
     */
    public function add(string $text, int $parent = null): Repository
    {
        $nowDate = new DateTime('now'); 
        $message = new Message();
        $message->setText($text);
        $message->setCreated($nowDate);
        $message->setUpdated($nowDate);
        $message->setParent($parent);
        $message->generateId();
        $this->messages[$message->getId()] = $message;
        return $this;
    }
    
    /**
     * Create a new message
     * @param string $text Message text
     * @param int $id ID of parent message, to be answered to
     * @return $this
     */
    public function update(string $text, int $id): Repository
    {
        $message = $this->one($id);
        $message->setText($text);
        $message->setUpdated(new DateTime('now'));
        $this->messages[$message->getId()] = $message;
        return $this;
    }
    
    /**
     * Save all messages to storage
     * @return bool
     */
    public function save(): bool
    {
        $xml = new SimpleXMLElement($this->defaultXmlHead);
        $this->messagesToXml($xml);
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        if($dom->save($this->xmlFile)) {
            $this->fromFile();
            return true;
        }
        return false;
    }
    
    /**
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function messagesToXml(SimpleXMLElement $xml): void
    {
        foreach($this->messages as $message) {
            $this->formatXmlChild($xml, $message);
        }
    }
    
    /**
     * @param SimpleXMLElement $xml
     * @param Message $message
     * @return void
     */
    private function formatXmlChild(SimpleXMLElement $xml, Message $message): void
    {
        $element = $xml->addChild('message');
        $element->addChild('id', (string)$message->getId());
        $element->addChild('text', $message->getText());
        $element->addChild('parent', (string)$message->getParent());
        $element->addChild('created', $message->getCreated()->format(DateTime::W3C));
        $element->addChild('updated', $message->getUpdated()->format(DateTime::W3C));
    }

    /**
     * Fill messages from storage
     * @return \Repository
     */
    private function fromFile(): Repository
    {
        $content = file_get_contents($this->xmlFile);
        if (!empty($content)) {
            $xml = simplexml_load_string($content);
            foreach($xml->message as $messageData) {
                $message = new Message;
                $message->setId((int)$messageData->id);
                $message->setText((string)$messageData->text);
                $message->setParent((int)$messageData->parent ?: null);
                $message->setCreated(new DateTime((string)$messageData->created));
                $message->setUpdated(new DateTime((string)$messageData->updated));
                $this->messages[$message->getId()] = $message;
            }
        }
        return $this;
    }
    
    /**
     * Checks a XML file exist and writable
     * @throws Exception
     */
    private function checkFile(): void
    {
        if(!file_exists($this->xmlFile)) {
            file_put_contents($this->xmlFile, '');
        }
    }
}
