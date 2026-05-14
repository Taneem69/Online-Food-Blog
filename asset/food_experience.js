function addComment(postId){
    let comment =document.getElementById('comment'+postId).value;
    if(comment.trim()==""){
        alert("Comment Required");
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../controller/addComment.php",true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 &&
        this.status == 200){
            let data =JSON.parse(this.responseText);
            if(data.status=="success"){
                location.reload();
            }
            else{
                alert(data.message);
            }
        }
    }
    xhttp.send("post_id="+postId+"&comment="+encodeURIComponent(comment));
}

function deleteComment(id){
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST","../controller/deleteComment.php",true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            let data =
            JSON.parse(this.responseText);
            if(data.status=="success"){
                location.reload();
            }
        }
    }
    xhttp.send("id="+id);
}



function deletePost(id){
    let xhttp = new XMLHttpRequest();

    xhttp.open("POST", "../controller/deletePost.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function(){

        if(this.readyState == 4 && this.status == 200){

            let data = JSON.parse(this.responseText);

            if(data.status=="success"){
                location.reload();
            }
        }
    }

    xhttp.send("id="+id);
}