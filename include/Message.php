<?php

declare(strict_types=1);

class Message
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var string Message text 
     */
    private $text;
    
    /**
     * @var int Parent ID
     */
    private $parent;
    
    /**
     * @var DateTime Message created time
     */
    private $created;
    
    /**
     * @var DateTime Message updated time
     */
    private $updated;
    
    /**
     * @var Message[] All childrens 
     */
    private $childrens = [];

    public function setId($id): void
    {
        $this->id = $id;
    }
    public function setParent(?int $parent): void
    {
        $this->parent = $parent;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    public function setChildrens(array $childrens): void
    {
        $this->childrens = $childrens;
    }
    
    public function setText(string $text): void
    {
        $this->text = $text;
    }
    
    public function setUpdated(DateTime $updated): void
    {
        $this->updated = $updated;
    }
    
    public function getChildrens(): array
    {
        return $this->childrens;
    }
    
    public function hasChildrens(): bool
    {
        return !!$this->getChildrens();
    }
    
    /**
     * @return string
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getParent(): ?int
    {
        return $this->parent;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }
    
    public function generateId(): void
    {
        $this->id = mt_rand(111111, 999999);
    }
}
