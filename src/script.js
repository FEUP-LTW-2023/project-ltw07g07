function showFormReply(ticketId) {
    document.getElementById("reply-" + ticketId).style.display = "block";
}

function showDep(department){
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var result = xhr.responseText;
            console.log(result);
            document.documentElement.innerHTML = result;
            
        }
    };
    xhr.open('GET', 'admin.php?function=showDepEach&dep=' + encodeURIComponent(department) , true);
    xhr.send();
}

function showFormDep() {
    document.getElementById("myForm").style.display = "block";
}
function showFormRem() {
    document.getElementById("myForm2").style.display = "block";
}

function showFormProfile() {
    document.getElementById("myForm").style.display = "block";
}

function showFormFaq(){
    document.getElementById("faq").style.display = "block";
}

function showReplyFaq(ticketId){
    document.getElementById("reply-faq-" + ticketId).style.display = "block";
}




/*
function getval(){
var xhr = new XMLHttpRequest();

xhr.onreadystatechange = function() {
  if (xhr.readyState === XMLHttpRequest.DONE) {
    if (xhr.status === 200) {
      var variableValue = xhr.responseText;
      options = variableValue;
      
    } else {
      console.error('Error: ' + xhr.status);
    }
  }
};

xhr.open('GET', 'gethashtags.php', true);
xhr.send();
}
*/




//var options = ["teste","teste2","teste3","ajuda","muitocomplicado"];

let sortedNames = options.sort();


let input = document.getElementById("input");


input.addEventListener("keyup", (e) => {
    removeElements();
    for (let i of sortedNames) {

        
        if(i.toLowerCase().startsWith(input.value.
            toLowerCase()) && input.value != ""
            ) {

                let listItem = document.createElement("li");


                listItem.classList.add("list-items");
                listItem.style.cursor = "pointer";
                listItem.setAttribute("onclick", "displayNames('"
                 + i + "')");
                 let word = "<b>" + i.substring(0, input.value.lenth)
                 + "</b>";
                //word+= i.substring(input.value.lenth);
                //console.log(word);
                listItem.innerHTML = word;
                document.querySelector(".list").appendChild(listItem);

            }
    }

});


function displayNames(value){
    input.value = value;
}

function removeElements(){

    let items = document.querySelectorAll(".list-items");
    items.forEach((item)=> {
        item.remove();
        
    });
}