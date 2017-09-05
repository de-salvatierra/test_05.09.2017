<div class="media" style="margin-bottom: 10px" id="message-{id}-block">
    <div class="media-body">
        <small class="text-muted">
            Написано: {created} <a href="#" data-message-edit="{id}">Редактировать</a>
        </small>
        <p class="no-bottom-margin">{text}</p>
        <a href="#comment-answer-{id}-block" data-toggle="collapse" aria-expanded="false" aria-controls="comment-answer-{id}-block">Ответить</a>
        <div id="comment-answer-{id}-block" class="collapse">
            <form action="" method="post">
                <input type="hidden" name="answerTo" value="{id}">
                <div class="form-group">
                    <textarea name="message" class="form-control"></textarea>
                </div>
                <div class="clearfix">
                    <button type="submit" class="btn btn-default btn-xs pull-left">Написать</button>
                    <a href="#" data-comment-answer-cancel="{id}" class="pull-right comment-reply-link">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</div>