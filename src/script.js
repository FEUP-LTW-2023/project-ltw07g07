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
var options;

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
