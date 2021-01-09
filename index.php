<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Personalo valdymo sistema</title>
</head>

<body>
    <header class="header">
        <div class="header__btn header__btn--left-radius">
            <a href="?path=projektai" class="header__btn-name">Projektai</a>
        </div>
        <div class="header__btn">
            <a href="?path=darbuotojai" class="header__btn-name">Darbuotojai</a>
        </div>
    </header>
    <main class="main">

        <?php

        $server_name = 'localhost';
        $username = 'root';
        $password = 'mysql';

        $db_name = 'personalo_valdymo_sistema';
        $conn = mysqli_connect($server_name, $username, $password, $db_name);

        if (!$conn) {
            die('Connection failed: ' . mysqli_connect_error());
        } else {
            //magic starts here
            $title = 'DARBUOTOJAI';
            $sql = 'SELECT darbuotojai.id, concat_ws(" ", vardas, pavarde) AS vardas, projekto_pavadinimas FROM darbuotojai
            LEFT JOIN projektai ON projektai.id = darbuotojai.projekto_id';
        }

        //delete magic
        if (isset($_GET['delete'])) {
            if ($_GET['path'] == 'projektai') {
                //TODO:: if employees left throw error/ warning
                $sql = "DELETE FROM projektai WHERE ID = ?";
            } else {
                $sql = "DELETE FROM darbuotojai WHERE ID = ?";
            }

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $_GET['delete']);

            run_sql($conn, $stmt);
        }

        //update magic
        if (isset($_POST['update'])) {
            if ($_GET['path'] == 'projektai') {
                if ($_POST['projektas'] == "") {
                    print("Įveskite projekto pavadinimą");
                } else {
                    $sql = "UPDATE projektai SET
                        projekto_pavadinimas = ?
                        WHERE id = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('si', $_POST['projektas'], $_GET['edit']);

                    run_sql($conn, $stmt);
                }
            } else {
                if ($_POST['vardas'] == "" || $_POST['pavarde'] == "") {
                    print("Užpildykite visus laukus");
                } else {
                    $sql = "UPDATE darbuotojai SET
                    vardas = ?,
                    pavarde = ?,
                    projekto_id = ?
                    WHERE id = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ssii', $_POST['vardas'], $_POST['pavarde'], $id, $_GET['edit']);
                    $id = get_project_id($conn, $_POST['projects']);

                    run_sql($conn, $stmt);
                }
            }
        }



        //insert logic
        if (isset($_POST['insert'])) {
            if ($_GET['path'] == 'projektai') {
                if ($_POST['projektas'] == "") {
                    print("Įveskite projekto pavadinimą");
                } else {
                    $insert_sql = "INSERT INTO projektai VALUES (?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->bind_param('is', $id, $_POST['projektas']);
                    $nd = null;

                    run_sql($conn, $stmt);
                }
            } else {
                if ($_POST['vardas'] == "" || $_POST['pavarde'] == "") {
                    print("Užpildykite visus laukus");
                } else {
                    $insert_sql = "INSERT INTO darbuotojai VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->bind_param('isss', $id, $_POST['vardas'], $_POST['pavarde'], $project);
                    $id = null;
                    $project = null;

                    run_sql($conn, $stmt);
                }
            }
        }

        //on initiate
        if ($_GET['path'] == 'darbuotojai') {
            $title = 'DARBUOTOJAI';
            $path = "path={$_GET['path']}";
            $sql = 'SELECT darbuotojai.id, concat_ws(" ", vardas, pavarde) AS vardas, projekto_pavadinimas FROM darbuotojai
            LEFT JOIN projektai ON projektai.id = darbuotojai.projekto_id';
        } else if ($_GET['path'] == 'projektai') {
            $title = 'PROJEKTAI';
            $path = "path={$_GET['path']}";
            $sql = 'SELECT projektai.id, projekto_pavadinimas, group_concat(CONCAT_WS(" ", vardas, pavarde) SEPARATOR "; " ) AS vardas FROM projektai
            LEFT JOIN darbuotojai ON projektai.id = darbuotojai.projekto_id
            GROUP BY projektai.id';
        } else {
            $path = "";
        }

        print("<a href='?{$path}&insert=true' class='add'>+</a>");
        print("<h2 class='main__title'>{$title}</h2>");

        //update form
        if (isset($_GET['edit'])) {
            if ($_GET['path'] == 'projektai') {
                $res = mysqli_query($conn, "SELECT projekto_pavadinimas AS projektas FROM projektai WHERE id = " . $_GET['edit']);
            } else {
                $res = mysqli_query($conn, "SELECT vardas, pavarde FROM darbuotojai WHERE id = " . $_GET['edit']);
                $project_res = mysqli_query($conn, "SELECT projekto_pavadinimas AS projektas FROM projektai");
            }
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
        ?>
                    <form action="" method="post" class="form">
                        <?php if ($_GET['path'] == 'projektai') { ?>
                            <input class="form__input" type="text" name="projektas" value="<?php echo $row['projektas'] ?>">
                        <?php } else { ?>
                            <input class="form__input" type="text" name="vardas" value="<?php echo $row['vardas'] ?>">
                            <input class="form__input" type="text" name="pavarde" value="<?php echo $row['pavarde'] ?>">
                    <?php }
                    }
                }
                if ($project_res and mysqli_num_rows($project_res) > 0) { ?>
                    <select class="form__input" name="projects" id="projects">
                        <?php
                        while ($row = mysqli_fetch_assoc($project_res)) {
                        ?>
                            <option><?php echo $row['projektas'] ?></option>
                        <?php } ?>
                    </select>

                <?php } ?>
                <input class="form__btn" type="submit" value="Save" name="update">
                    </form>
                <?php }

            //insert form
            if (isset($_GET['insert']) and $_GET['insert']) { ?>
                    <form action="" method="post" class="form">
                        <?php if ($_GET['path'] == 'projektai') { ?>
                            <input class="form__input" type="text" name="projektas" placeholder="Projekto pavadinimas">
                        <?php } else { ?>
                            <input class="form__input" type="text" name="vardas" placeholder="Vardas">
                            <input class="form__input" type="text" name="pavarde" placeholder="Pavarde">
                        <?php } ?>
                        <input class="form__btn" type="submit" value="Insert" name="insert">
                    </form>
                    <?php
                }


                print_table($conn, $sql, $path);

                //main tables
                function print_table($conn, $sql, $path)
                {
                    $res = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($res) > 0) {
                    ?>
                        <div class="table">
                            <div class="table__row table__row--head">
                                <div class="table__col-id table__col--head">ID</div>
                                <?php $_GET['path'] == 'projektai' ?
                                    print("<div class='table__col table__col--head'>PROJEKTAS</div>
                            <div class='table__col-text table__col--head'>DARBUOTOJAI</div>
                            ")
                                    :
                                    print("<div class='table__col table__col--head'>DARBUOTOJAS</div>
                            <div class='table__col-text table__col--head'>PROJEKTAS</div>
                            ")
                                ?>
                                <div class="table__col-controls table__col--head">VEIKSMAI</div>
                            </div>
                            <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                                <div class='table__row'>
                                    <div class='table__col-id'><?php echo $row['id'] ?></div>
                                    <?php $_GET['path'] == 'projektai' ?
                                        print("<div class='table__col'>{$row['projekto_pavadinimas']}</div>
                                <div class='table__col-text'>{$row['vardas']}</div>
                                ")
                                        :
                                        print("<div class='table__col'>{$row['vardas']}</div>
                                <div class='table__col-text'>{$row['projekto_pavadinimas']}</div>
                                ");

                                    print("<div class='table__col-controls'>
                            <a href='?{$path}&edit={$row['id']}' class='table__col-controls-link'>EDIT</a>
                            <a href='?{$path}&delete={$row['id']}' 
                            class='table__col-controls-link table__col-controls-link--del'>DELETE</a>
                            </div>");
                                    ?>
                                </div>
                    <?php
                            }
                            print('</div>');
                        } else {
                            print('<p>Nėra duomenų</p>');
                        }
                    }

                    function get_project_id($conn, $project)
                    {
                        $res = mysqli_query($conn, "SELECT id FROM projektai WHERE projekto_pavadinimas = '{$project}'");
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $id = $row['id'];
                            }
                            return $id;
                        }
                    }

                    function run_sql($conn, $stmt)
                    {
                        $stmt->execute();
                        $stmt->close();
                        mysqli_close($conn);
                        header("Location: " . strtok($_SERVER['REQUEST_URI'], '&'));
                        die();
                    }

                    ?>

    </main>
    <footer class="footer">
        <p class="footer__text">Footeris</p>
    </footer>
</body>
<script src=""></script>

</html>