<?php

declare(strict_types=1);

class Controller
{
    /**
     * @var Repository 
     */
    private $repository;
    
    /**
     * @var Request 
     */
    private $request;
    
    /**
     * @param Repository $repository
     * @param Request $request
     */
    public function __construct(Repository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * Action for create a new comment
     * @return string
     */
    protected function newAction(): ?string
    {
        $nextMessageTimeout = $this->repository->nextMessageTimeout();
        if($nextMessageTimeout > 0) {
            return $this->sendJson(['error' => "Следующее сообщение можно написать только через {$nextMessageTimeout} сек."]);
        }
        $message = $this->request->post('message');
        $answerTo = intval($this->request->post('answerTo'));
        $this->validateText($message);
        if($answerTo) {
            $this->validateMessage($answerTo);
        }
        $this->repository->add($message, $answerTo);
        if(!$this->repository->save()) {
            return $this->sendJson(['error' => "Не удалось сохранить в файл"]);
        }
        return null;
    }

    /**
     * Action for update comment message
     * @return string
     */
    public function updateAction(): ?string
    {
        $message = $this->request->post('message');
        $updateMessage = intval($this->request->post('updateMessage'));
        $this->validateText($message);
        $this->validateMessage($updateMessage);
        if(!$this->repository->update($message, $updateMessage)->save()) {
            return $this->sendJson(['error' => "Не удалось сохранить в файл"]);
        }
        return null;
    }
    
    /**
     * Validate message by id
     * @param int $id
     */
    protected function validateMessage(int $id): void
    {
        if(!$this->repository->exist($id)) {
            exit($this->sendJson(['error' => "Сообщение ID {$id} не найдено"]));
        }
    }
    
    /**
     * Validate message text
     * @param string $message
     * @return void
     */
    protected function validateText(string $message):void
    {
        if(!$message) {
            exit($this->sendJson(['error' => 'Введите сообщение']));
        }
        if(mb_strlen($message) < 5) {
            exit($this->sendJson(['error' => 'Минимум 5 символов']));
        }
    }
    
    /**
     * Action for get one message
     * @return string JSON object with text and id of selected message
     */
    protected function getAction(): string
    {
        $messageId = $this->request->post('message');
        if(!$messageId) {
            return $this->sendJson(['error' => 'Не передан ID сообщения']);
        }
        $messageId = intval($messageId);
        if(!$this->repository->exist($messageId)) {
            return $this->sendJson(['error' => 'Сообщение не найдено']);
        }
        $message = $this->repository->one($messageId);
        return $this->sendJson([
            'message' => [
                'text' => htmlspecialchars($message->getText(), ENT_QUOTES),
                'id' => intval($message->getId())
            ]
        ]);
    }
    
    /**
     * Format action method name and request this method
     * @return type
     */
    public function process(): void
    {
        $action = $this->request->post('action');
        if($action) {
            $action  .= 'Action';
            if(!method_exists($this, $action)) {
                http_response_code(404);
                throw new Exception('Page not found');
            }
            $result = call_user_func([$this, $action]);
            if($result) {
                exit($result);
            }
        }
    }
    
    /**
     * Format JSON response
     * @param mixed $data
     * @return string
     */
    protected function sendJson($data): string
    {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}
