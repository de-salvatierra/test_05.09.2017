<?php

declare(strict_types=1);

class CommentsView
{
    /**
     * @var array All messages
     */
    private $messages;
    
    /**
     * @var string Content from template file
     */
    private $viewContent;
    
    /**
     * @param array $messages All messages
     */
    public function __construct(array $messages)
    {
        $this->messages = $messages;
        $this->viewContent = file_get_contents(__DIR__ . '/../view/comment.tpl');
    }
    
    /**
     * Reurn formatted content
     * @return string
     */
    public function view(): string
    {
        $html = [];
        foreach($this->messages as $message) {
            $html[] = $this->render($message);
        }
        return implode(PHP_EOL, $html);
    }
    
    /**
     * Recursive render a single message with hims childrens
     * @param Message $message
     * @return string
     */
    private function render(Message $message): string
    {
        $search = ['{id}', '{text}', '{created}', '{updated}'];
        $replace = [
            intval($message->getId()),
            htmlspecialchars($message->getText(), ENT_QUOTES),
            $message->getCreated()->format('d.m.Y H:i'),
            $message->getUpdated()->format('d.m.Y H:i'),
        ];
        $html = '<div';
        if($message->getParent()) {
            $html .= ' class="comments-tree"';
        }
        $html .= '>';
        $html .= str_replace($search, $replace, $this->viewContent);
        if($message->hasChildrens()) {
            foreach($message->getChildrens() as $children) {
                $html .= $this->render($children);
            }
        }
        return "{$html}</div>";
    }
}
