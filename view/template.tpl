<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Гостевушка</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            .comments-tree {
                border-left: dotted 1px #d8d8d8;
                background: #fff;
                padding-left: 27px;
                margin-left: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="#">Гостевушка</a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbars"
                    aria-controls="navbars"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </nav>
            <div id="content">
                {content}
            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
        <script
            src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"
            integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"
            crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                $(document).on('submit', 'form', function() {
                    var action,
                        updateId = $('#updateMessage').val();
                    if(updateId) {
                        action = 'update';
                    } else {
                        action = 'new';
                    }
                    $.post("", $(this).serialize() + "&action=" + action).done(function(response, status, xhr) {
                        if(typeof(response) === 'object') {
                            if(response.error) {
                                alert(response.error);
                            }
                        } else {
                            $('#content').html(response);
                            if(updateId) {
                                $('body,html').animate({
                                    scrollTop:  $('#message-'+updateId+'-block').offset().top
                                }, 500);
                            }
                        }
                    });
                    return false;
                });
                $(document).on('click', 'a[data-message-edit]', function(e) {
                    e.preventDefault();
                    var messageId = $(this).data('message-edit');
                    $.post("", {
                        action: "get", message: messageId
                    }).done(function(response) {
                        if(typeof(response) === 'object') {
                            if(response.error) {
                                alert(response.error);
                            }
                            if(response.message) {
                                $('#updateMessage').val(response.message.id);
                                $('#newMessageText').val(response.message.text);
                                $('body,html').animate({
                                    scrollTop:  $('#new-message-block').offset().top
                                }, 500);
                            }
                        }
                        
                    });
                });
                $(document).on('click', '.comment-reply-link', function(e) {
                    e.preventDefault();
                    var parentBlock = $(this).closest('div.collapse'),
                        textarea = parentBlock.find('textarea');
                    parentBlock.collapse('hide');
                    textarea.val('');
                });
            });
        </script>
    </body>
</html>