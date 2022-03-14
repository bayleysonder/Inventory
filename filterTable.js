function filterTable() {

    var input, filter, table, tr, td, i, t;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
        var filtered = false;
        var tds = tr[i].getElementsByTagName("td");
        for (t = 0; t < tds.length; t++) {
            var td = tds[t];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    filtered = true;
                }
            }
        }
        if (filtered === true) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}