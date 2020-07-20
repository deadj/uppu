changeReplyBlock();
setInterval(updateComments, 30000);
setInterval(reloadPage, 10000);


function reloadPage(){
    var uploadStatus = document.getElementById('uploadStatus').value;

    if (uploadStatus != "done") {
        window.location.reload();
    }
}

function changeReplyBlock(){
    var replyBlocks = document.querySelectorAll('.replyBlock');

    for (var i = 0; i < replyBlocks.length; i++) {
        var replyButton = document.createElement('a');
        replyButton.name = "replyButton";
        replyButton.setAttribute("onclick", "printReplyToComment(this)");
        replyButton.innerHTML = "Ответить";

        replyBlocks[i].parentNode.querySelector('label').replaceWith(replyButton);
    }
}

newCommentBlock.onsubmit = async function(e){
    e.preventDefault();

    addCommentButton.disabled = true;
    addCommentButton.innerHTML = "<img src='/img/ajax-loader.gif'>";

    var formData = new FormData(newCommentBlock);
    formData.delete('notjs');

    addComment(formData);
}

async function replyComment(e) {
    e.disabled = true;
    e.innerHTML = "<img src='/img/ajax-loader.gif'>";

    var replyBlock = e.parentNode.parentNode;
    var comment = replyBlock.querySelector("div .replyCommentField").value;
    var parentId = replyBlock.querySelector("input[name='commentId']").value;

    var url = '/addComment';

    var formData = new FormData();

    formData.append('comment', comment);
    formData.append('fileId', fileId.value);
    formData.append('parentId', parentId);

    addComment(formData);
}

async function addComment(formData){
    var url = '/addComment';

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

    if (commentsList.length != 0) {
        var commentsBlock = document.createElement('div');
        commentsBlock.classList.add("commentsBlock");
        commentsBlock.id = "commentsBlock";

        createCommentsTree(commentsList, commentsBlock);

        newCommentsText.value = "";
        
        var body = document.getElementsByTagName('body')[0];
        body.removeChild(document.getElementById('commentsBlock'));
        body.insertBefore(commentsBlock, document.getElementById('newCommentBlock'));
    }
}

function createCommentsTree(comments, commentsBlock){
    for (var id in comments) {
        template = document.getElementById('templateComment').innerHTML;
        template = template.replace('[[id]]', comments[id].id);
        template = template.replace('[[date]]', comments[id].date);
        
        var text = comments[id].text;
        text = text.replace(/\n/g, '<br>');
        template = template.replace('[[text]]', text);

        var comment = document.createElement('div');
        comment.classList.add("comment");
        comment.innerHTML = template;

        var a = document.createElement("a");
        a.innerHTML = "Ответить";
        a.name = "replyButton";
        a.setAttribute("onclick", "printReplyToComment(this)");  
        comment.appendChild(a);

        commentsBlock.appendChild(comment);
        
        if (comments[id].children.length != 0) {
            createCommentsTree(comments[id].children, comment);
        } 
    }
}

function printReplyToComment(comment) {
    if (comment.innerHTML === "Закрыть") {
        replyToCommentBlock.parentNode.querySelector('a').innerHTML = "Ответить";
        replyToCommentBlock.parentNode.removeChild(replyToCommentBlock);        
    } else if (comment.innerHTML === "Ответить") {
        var searchBlock = document.querySelector('.replyToCommentBlock');

        if (searchBlock) {
            searchBlock.parentNode.querySelector('a').innerHTML = "Ответить";
            searchBlock.parentNode.removeChild(searchBlock);  
            createReplyBlock(comment);
        } else {
            createReplyBlock(comment);
        }          
    } 
}

function createReplyBlock(comment){
    var replyTemplate = templateReplyComment.innerHTML;
    var newCommentBlock = document.createElement('div');
    newCommentBlock.classList.add('replyToCommentBlock');
    newCommentBlock.id = "replyToCommentBlock";
    newCommentBlock.innerHTML = replyTemplate;

    var replyButton = comment.parentNode.querySelector('a').innerHTML = "Закрыть";

    comment.parentNode.querySelector('a').after(newCommentBlock);     
}

function closeReply(el) {
    var label = el.parentNode;

    label.querySelector('.replyComment').style.display = "none";
    label.querySelector('.closeReplyButton').style.display = "none";
    label.querySelector('.openReplyButton').style.display = "block";
}

function openReply(el) {
    var label = el.parentNode;

    label.querySelector('.replyComment').style.display = "block";
    label.querySelector('.closeReplyButton').style.display = "block";
    label.querySelector('.openReplyButton').style.display = "none";
}