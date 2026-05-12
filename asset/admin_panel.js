function deleteMember(id){
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST","../controller/deleteMember.php",true
    );
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            let data=JSON.parse(this.responseText);
            if(data.status=="success"){
                location.reload();
            }
        }
    }
    xhttp.send("id="+id);
}

function deleteReview(id){
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST","../controller/deleteReview.php",true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
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