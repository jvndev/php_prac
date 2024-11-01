<?php

require  __DIR__ . '/data/doublyll.php';

session_start();

if (!isset($_SESSION['DLL'])) {
    $_SESSION['DLL'] = DoublyLL::make();
}

define('DLL', $_SESSION['DLL']);
define('DB', new DB());

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
        ($this->conn = mysqli_connect(...array_values(self::CONNPROPS))) || die("Couldn't connect");
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

    public function insertNumber(string $phpsessid, int $nr): string
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $stmt = $this->conn->prepare('
            insert into numbers
            (phpsessid, nr)
            value
            (?, ?)
        ');

        return $stmt && $stmt->execute([$phpsessid, $nr])
            ? 'success'
            : $this->conn->error;
    }
}

function html()
{
?>
    <!DOCTYPE html>
    <html>

    <head>
        <script>
            window.addEventListener('load', () => {
                document.getElementById('btnNr').addEventListener('click', (event) => {
                    document.getElementById('btnNr').setAttribute('disabled', '');

                    fetch('.', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; encoding=utf-8;',
                        },
                        body: new URLSearchParams({
                            phpsessid: '<?= session_id() ?>',
                            nr: document.getElementById('txtNr').value,
                        }),
                    }).then(async response => {
                        const json = await response.json();
                        let txtNr = document.getElementById('txtNr');

                        txtNr.value = Number.parseInt(txtNr.value) + 1;
                        document.getElementById('btnNr').removeAttribute('disabled');
                    });
                });
            });
        </script>
    </head>

    <body>
        <input id='txtNr' value="1">
        <button id='btnNr'>Send</button>
    </body>

    </html>

<?php
}

function api()
{
    header('Content-Type: application/json; charset=utf-8;');
    $phpsessid = $_POST['phpsessid'];
    $nr = $_POST['nr'];

    echo json_encode(['result' => DB->insertNumber($phpsessid, $nr)]);
}

if (count($_POST)) {
    api();
} else {
    html();
}

?>