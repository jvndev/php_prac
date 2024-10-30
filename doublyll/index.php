<!DOCTYPE html>

<?php

require  __DIR__ . '/data/doublyll.php';

session_start();

if (!isset($_SESSION['DLL'])) {
    $_SESSION['DLL'] = DoublyLL::make();
}

define('DLL', $_SESSION['DLL']);

function html()
{
?>
    <html>

    <head>
        <script>
            window.addEventListener('load', () => {
                document.getElementById('btnNr').addEventListener('click', () => {
                    fetch('.', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; encoding=utf-8;',
                        },
                        body: new URLSearchParams({
                            nr: document.getElementById('txtNr').value,
                        }),
                    });
                });
            });
        </script>
    </head>

    <body>
        <input id='txtNr'>
        <button id='btnNr'>Send</button>
    </body>

    </html>

<?php
}

function api() {
    echo $_POST['nr'];
}

if (count($_POST)) {
    api();
} else {
    html();
}
