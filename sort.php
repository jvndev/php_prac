<?php
require __DIR__ . "/data/sort.php";

class DB
{
    private readonly PDO $conn;

    public function __construct()
    {
        $this->conn = new PDO('mysql:host=localhost;dbname=jaco;', 'jaco', 'password', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
    }

    function getNewSessionID(): int
    {
        $query = 'select coalesce(max(phpsessid), 0) + 1 newid from numbers';

        return $this->conn->query($query)->fetchColumn();
    }

    function checkSessionID(string $id): bool
    {
        if (!ctype_digit($id)) return false;

        $query = 'select count(nr) cnt from numbers where phpsessid = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'id' => intval($id),
        ]);

        return $stmt->fetchColumn() > 0;
    }

    function insertNr(int $id, int $nr): bool
    {
        $stmt = $this->conn->prepare("
            insert into numbers
            (phpsessid, nr)
            values
            (:phpsessid, :nr)
        ");
        return $stmt->execute([
            'phpsessid' => $id,
            'nr' => $nr,
        ]);
    }
}

function serverURL()
{
    $reqUri = preg_replace("/(?<=\/)[^\/]*$/", "", $_SERVER['REQUEST_URI']);

    return sprintf(
        "%s://%s:%s%s",
        $_SERVER['REQUEST_SCHEME'],
        $_SERVER['SERVER_NAME'],
        $_SERVER['SERVER_PORT'],
        $reqUri
    );
}

function isAjaxCall()
{
    return isset($_POST['txtNr']);
}
?>

<?php
$db = new DB();
if (isAjaxCall()) {
    $id = $_POST['id'];
    $txtNr = $_POST['txtNr'];

    if ($db->insertNr($id, $txtNr)) {
        echo "success";
    } else {
        echo "failure";
    }
} else {
    session_start();

    if (isset($_GET['phpsessid']) && $db->checkSessionID($_GET['phpsessid']))
        $phpsessid = intval($_GET['phpsessid']);
    else
        $phpsessid = $db->getNewSessionID();

    setcookie("PHPSESSID", $phpsessid, 0, "/");
?>

    <!DOCTYPE html>
    <html>

    <head>
        <script src="<?= serverURL() ?>js/sort.js"></script>
    </head>

    <body>
        <div>
            <input name="txtNr" id='txtNr'>
        </div>
        <div>
            <button id='btnAdd' onclick="btnAdd_Click('<?= serverURL() ?>sort.php', <?= $phpsessid ?>)">Add</button>
        </div>
    </body>

    </html>

<?php
}
