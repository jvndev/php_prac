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
        ($this->conn = mysqli_connect(...array_values(self::CONNPROPS)))
            || die("Couldn't connect");
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

function getPostAction(array $post): string
{
    return array_reduce(
        $post,
        fn(string $p, string $e)
        => preg_match('/^action-\w+$/', $e, $matches) !== 0
            ? $matches[0]
            : $p,
        ''
    );
}

function html()
{
?>
    <!DOCTYPE html>
    <html>

    <head>
        <script>
            window.addEventListener('load', () => {
                const postBtns_OnClick = (event) => {
                    const action = event.target.name;
                    const fetched = fetch('.', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; encoding=utf-8;',
                        },
                        body: new URLSearchParams({
                            action: action,
                            phpsessid: '<?= session_id() ?>',
                            nr: document.getElementById('txtNr').value,
                        }),
                    });

                    document.getElementById('btnNr').setAttribute('disabled', '');

                    switch (action) {
                        case 'action-nr':
                            fetched.then(async response => {
                                const json = await response.json();
                                const txtNr = document.getElementById('txtNr');

                                txtNr.value = Number.parseInt(txtNr.value) + 1;
                                document.getElementById('btnNr').removeAttribute('disabled');
                            });
                            break;
                        default:
                            console.error(`Unknown action ${action}`);
                    }
                }

                document.querySelectorAll("button[name^='action']")
                    .forEach(e => e.addEventListener('click', postBtns_OnClick));
            });
        </script>
    </head>

    <body>
        <div>
            <input id='txtNr' value="1">
            <button id='btnNr' name='action-nr'>Send</button>
        </div>
        <div>
            <button id='btnFlush' name='action-flush'>Flush to DB</button>
        </div>
    </body>

    </html>

<?php
}

function api(string $action)
{
    header('Content-Type: application/json; charset=utf-8;');
    $phpsessid = $_POST['phpsessid'];
    $nr = $_POST['nr'];

    echo json_encode(['action' => $action ? $action : '??']);
}

if (count($_POST)) {
    $action = getPostAction($_POST);

    api($action);
} else {
    html();
}

?>