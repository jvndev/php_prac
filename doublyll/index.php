<!DOCTYPE html>

<?php

require  __DIR__ . '/data/doublyll.php';

session_start();

if (!isset($_SESSION['DLL'])) {
    $_SESSION['DLL'] = DoublyLL::make();
}

define('DLL', $_SESSION['DLL']);
define('CONN', new DB());

class DB
{
    private const CONNPROPS = [
        'host' => 'localhost',
        'user' => 'jaco',
        'pass' => 'password',
        'db'   => 'jaco',
    ];
    private readonly mysqli $conn;

    public function __construct()
    {
        $this->conn = mysqli_connect(...array_values(self::CONNPROPS));
    }

    private function _keyResults(array $res): array
    {
        return array_reduce($res, function (array $p, array $c) {
            $p[$c['id']] = array_filter(
                $c,
                fn($v, string $k) => $k != 'id',
                ARRAY_FILTER_USE_BOTH
            );

            return $p;
        }, []);
    }

    public function getNumbers()
    {
        return $this->_keyResults(
            $this->conn
                ->query('select id, phpsessid, nr from numbers')
                ->fetch_all(MYSQLI_ASSOC)
        );
    }
}

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
        <form method="get" action=".">
            <input name='txtGetTest'>
            <button name="btnGetTest">GET</button>
        </form>
        <form method="post" action=".">
            <input name='nr'>
            <button name="btnPostTest">POST</button>
        </form>
    </body>

    </html>

<?php
}

function api()
{
    echo $_POST['nr'];
}

if (count($_POST)) {
    api();
} elseif (count($_GET)) {
    var_dump(CONN->getNumbers());
} else {
    html();
}

?>