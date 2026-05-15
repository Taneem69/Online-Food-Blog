function deleteMember(id){
    let xhttp = new XMLHttpRequest();

    xhttp.open("POST", "../controller/deleteMember.php", true);

    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function(){

        if(this.readyState == 4 && this.status == 200){

            let data =JSON.parse(this.responseText);

            if(data.status=="success"){
                location.reload();
            }
        }
    }

    xhttp.send("id="+id);
}

function deleteReview(id){
    let xhttp = new XMLHttpRequest();

    xhttp.open("POST", "../controller/deleteReview.php", true);

    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

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




function adminDeletePost(id) {
    if (!confirm("Remove this food experience post?")) return;

    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../controller/deletePost.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let data = JSON.parse(this.responseText);
            if (data.status == "success") {
                let row = document.getElementById("fepost-row-" + id);
                if (row) row.remove();
            } else {
                alert("Failed to remove post.");
            }
        }
    };

    xhttp.send("id=" + id);
}


function adminDeleteComment(id) {
    if (!confirm("Remove this comment?")) return;

    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../controller/deleteComment.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let data = JSON.parse(this.responseText);
            if (data.status == "success") {
                let row = document.getElementById("fecomment-row-" + id);
                if (row) row.remove();
            } else {
                alert("Failed to remove comment.");
            }
        }
    };

    xhttp.send("id=" + id);
}