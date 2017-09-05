<?php

include __DIR__ . '/include/Message.php';
include __DIR__ . '/include/Repository.php';
include __DIR__ . '/include/CommentsView.php';
include __DIR__ . '/include/Request.php';
include __DIR__ . '/include/Controller.php';

$repository = new Repository(__DIR__ . '/db.xml');
$request = new Request();
$controller = new Controller($repository, $request);
$controller->process();
$commentsView = new CommentsView($repository->all());

$view = file_get_contents(__DIR__ . '/view/view.tpl');
$content = str_replace('{comments}', $commentsView->view(), $view);
if (!$request->isAjax()) {
    $template = file_get_contents(__DIR__ . '/view/template.tpl');
    $content = str_replace('{content}', $content, $template);
}
echo $content;
