newCommentBlock.onsubmit = async function(e){
    e.preventDefault();

    addCommentButton.disabled = true;
    addCommentButton.innerHTML = "<img src='img/ajax-loader.gif'>";

    var url = '/addComment';

    var formData = new FormData(newCommentBlock);
    formData.append('fileId', fileId.value);
    formData.append('parentId', 'NULL');

    var response = await fetch(url, {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        if (await response.text() == 1) {
            updateComments();
            createPopup("Комментарий отправлен");            
        } else {
            createPopup("Нельзя отправить пустой комментарий");
        }

        addCommentButton.disabled = false;
        addCommentButton.innerHTML = "Отправить";
    } else {
        alert("error");
    }
}

function createPopup(text) {
    var windowHeight = document.documentElement.clientHeight;
    var windowWidth = document.documentElement.clientWidth;

    var commentPopup = document.createElement('div');
    commentPopup.classList.add("commentPopup");
    commentPopup.id = "commentPopup";
    commentPopup.innerHTML = text;

    commentPopup.style.top = windowHeight - 100 + "px";
    commentPopup.style.left = windowWidth - 220 + "px";

    var body = document.getElementsByTagName('body')[0];
    body.appendChild(commentPopup);

    setTimeout(closeCommentPopup, 4000);    
}

function closeCommentPopup() {
    var body = document.getElementsByTagName('body')[0];
    body.removeChild(document.getElementById('commentPopup'));
}

async function updateComments(){
    var url = '/getCommentsList';

    var formData = new FormData();
    formData.append('fileId', fileId.value);

    var response = await fetch(url, {
        method: 'POST',
        body: formData
    });

    var commentsList = await response.json();

    var commentsBlock = document.createElement('div');
    commentsBlock.classList.add("commentsBlock");
    commentsBlock.id = "commentsBlock";

    for (var i = 0; i < commentsList.length; i++) {
        var comment = document.createElement('div');
        comment.classList.add("comment");

        var date = document.createElement('h5');
        date.innerHTML = commentsList[i].date;

        var p = document.createElement('p');
        p.innerHTML = commentsList[i].text;

        // var a = document.createElement("a");
        // a.innerHTML = "Ответить";
        // a.name = "replyButton";
        // a.setAttribute("onclick", "printReplyToComment(this)");

        comment.appendChild(date);
        comment.appendChild(p);
        // comment.appendChild(a);
        commentsBlock.appendChild(comment);
    }

    newCommentsText.value = "";

    var body = document.getElementsByTagName('body')[0];
    body.removeChild(document.getElementById('commentsBlock'));
    body.insertBefore(commentsBlock, document.getElementById('newCommentBlock'));
}

// function printReplyToComment(comment) {
//     var searchElement = comment.parentNode.getElementsByTagName('form').length;

//     if (searchElement === 0) {
//         var replyTemplate = templateReplyComment.innerHTML;
//         var newCommentBlock = document.createElement('div');
//         newCommentBlock.classList.add('replyToCommentBlock');
//         newCommentBlock.innerHTML = replyTemplate;

//         var replyButton = comment.parentNode.querySelector('a');
//         replyButton.innerHTML = "Закрыть";
//         comment.parentNode.appendChild(newCommentBlock);
//     } else {
//         var form = comment.parentNode.getElementsByTagName('form')[0];
//         comment.parentNode.removeChild(form);

//         var replyButton = comment.parentNode.querySelector('a');
//         replyButton.innerHTML = "Ответить";
//     }   
// }

// async function replyComment(e) {
//     e.disabled = true;
//     e.innerHTML = "<img src='img/ajax-loader.gif'>";

//     var replyBlock = e.parentNode.parentNode;
//     var comment = replyBlock.querySelector("div input[name='comment'").value;
//     var parentId = replyBlock.querySelector("input[name='commentId'").value;

//     var url = '/addComment';

//     var formData = new FormData();

//     formData.append('comment', comment);
//     formData.append('fileId', fileId.value);
//     formData.append('parentId', parentId);

//     var response = await fetch(url, {
//         method: 'POST',
//         body: formData
//     });

//     if (response.ok) {
//         updateComments();

//         var windowHeight = document.documentElement.clientHeight;
//         var windowWidth = document.documentElement.clientWidth;

//         var commentPopup = document.createElement('div');
//         commentPopup.classList.add("commentPopup");
//         commentPopup.id = "commentPopup";
//         commentPopup.innerHTML = "Комментарий отправлен";

//         commentPopup.style.top = windowHeight - 100 + "px";
//         commentPopup.style.left = windowWidth - 220 + "px";

//         var body = document.getElementsByTagName('body')[0];
//         body.appendChild(commentPopup);

//         setTimeout(closeCommentPopup, 2000);

//         replyBlock.removeChild(replyBlock.querySelector('.replyToCommentBlock'));
//     } else {
//         alert("error");
//     }  
// }