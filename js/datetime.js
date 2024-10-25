const url = 'http://localhost/php_prac/datetime.php';
const request = new Request(url, {
    method: 'GET',
    responseType: 'json',
    credentials: 'omit',
});

function getDate() {
    fetch(request).then(response => {
        if (!response.ok)
            throw new Error('Response not OK');
        response.json().then(json => {
            document.getElementById('divDateTime').innerHTML = json.current_time;
        });
    }).catch(err => {
        console.error(err);
    });
}

setInterval(getDate, 1000);