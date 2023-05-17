function showDep1() {
    document.getElementById("dep1").style.display = "block";
    document.getElementById("dep2").style.display = "none";
    document.getElementById("dep3").style.display = "none";
}

function showDep2() {
    // Esconder todos os tickets
    var tickets = document.getElementsByClassName("ticket");
    for (var i = 0; i < tickets.length; i++) {
        tickets[i].style.display = "none";
    }

    // Exibir apenas os tickets do departamento Sales
    var salesTickets = document.getElementsByClassName("sales-ticket");
    for (var j = 0; j < salesTickets.length; j++) {
        salesTickets[j].style.display = "block";
    }
}

function showDep3() {
    document.getElementById("dep1").style.display = "none";
    document.getElementById("dep2").style.display = "none";
    document.getElementById("dep3").style.display = "block";
}


<?= $reply['message'] ?>