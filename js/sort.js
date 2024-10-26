//new Promise(r => setTimeout(r, 3000)).then(() => console.log('done'));

window.addEventListener('load', () => {});

function btnAdd_Click(url, id) {
    const txtNr = document.getElementById('txtNr');

    fetch(url, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: new URLSearchParams({
            id: id,
            txtNr: txtNr.value,
        }),
    })
    .then(response => response.text())
    .then(responseText => {
        if (responseText == 'success') {
            listNumbers(url, id);
        } else {
            console.error('Number insert failure');
        }
    });
}

function listNumbers(url, id) {

}