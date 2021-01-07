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

        //TODO:: instructions for db initiation:: WATCH 
        //create connection
        // $conn = mysqli_connect($server_name, $username, $password);
        // if (!$conn) (die("Connection failed: " . mysqli_connect_error()));
        // 
        //create database
        // $sql = 'CREATE DATABASE personalo_valdymo_sistema';
        // if (mysqli_query($conn, $sql)) {
        //     echo "Database created successfully";
        // } else {
        //     echo "Error creating database: " . mysqli_error($conn);
        // }

        $db_name = 'personalo_valdymo_sistema';
        $conn = mysqli_connect($server_name, $username, $password, $db_name);
        if (!$conn) {
            die('Connection failed: ' . mysqli_connect_error());
        } else {
            //magic starts here
            $title = 'DARBUOTOJAI';
            $sql = 'SELECT projektai.id AS id, projekto_pavadinimas,  CONCAT_WS(" ", vardas, pavarde) AS vardas FROM projektai
            LEFT JOIN darbuotojai ON projektai.id = darbuotojai.projekto_id';
        }

        if ($_GET['path'] == 'darbuotojai') {
            $title = 'DARBUOTOJAI';
            $sql = 'SELECT darbuotojai.id, concat_ws(" ", vardas, pavarde) AS vardas, projekto_pavadinimas FROM darbuotojai
            LEFT JOIN projektai ON projektai.id = darbuotojai.projekto_id';
        } else if ($_GET['path'] == 'projektai') {
            $title = 'PROJEKTAI';
            $sql = 'SELECT projektai.id, projekto_pavadinimas, group_concat(vardas) AS vardas FROM projektai
            LEFT JOIN darbuotojai ON projektai.id = darbuotojai.projekto_id
            GROUP BY projektai.id';
        }

        print("<h2 class='main__title'>{$title}</h2>");
        print_table($conn, $sql);


        // //create tables
        // $sql = 'CREATE TABLE projektai (
        //     id int(6) auto_increment primary key,
        //     projekto_pavadinimas varchar(30)
        // )';
        // if (mysqli_query($conn, $sql)) echo "Table projektai created successfully";

        // $sql = 'CREATE TABLE darbuotojai (
        //     id int(6) auto_increment primary key,
        //     vardas varchar(30),
        //     pavarde varchar(30),
        //     projekto_id int,
        //     FOREIGN KEY (projekto_id) REFERENCES projektai(id) 	
        // )';
        // if (mysqli_query($conn, $sql)) echo "Table darbuotojai created successfully";

        // //isert values
        // $sql = "INSERT INTO projektai VALUES
        //     (1, 'X'),
        //     (2, 'Y'),
        //     (3, 'Z')";
        // if (mysqli_query($conn, $sql)) echo "Values were successfully included into projektai";

        // $sql = "INSERT INTO darbuotojai VALUES
        // (1, 'A', 'Aaa', 1),
        // (2, 'B', 'Bbb', 1),
        // (3, 'C', 'Ccc', 1),
        // (4, 'D', 'Ddd', 1),
        // (5, 'E', 'Eee', 1),
        // (6, 'F', 'Fff', 2),
        // (7, 'G', 'Ggg', 2),
        // (8, 'H', 'Hhh', 2),
        // (9, 'I', 'Iii', 2),
        // (10, 'J', 'Jjj', 3),
        // (11, 'K', 'Kkk', 3)";
        // if (mysqli_query($conn, $sql)) echo "Values were successfully included into darbuotojai";


        //projektai ir darbuotojai
        function print_table($conn, $sql)
        {
            $res = mysqli_query($conn, $sql);
            if (mysqli_num_rows($res) > 0) {
                print('<div class="table">');
                print('<div class="table__row table__row--head">
                <div class="table__col-id table__col-id--head">ID</div>
                <div class="table__col-text table__col-text--head">PROJEKTAS</div>
                <div class="table__col-text table__col-text--head">VARDAS</div>
            </div>');
                while ($row = mysqli_fetch_assoc($res)) {
                    print("
                <div class='table__row'>
                    <div class='table__col-id'>{$row['id']}</div>
                    <div class='table__col-text'>{$row['projekto_pavadinimas']}</div>
                    <div class='table__col-text'>{$row['vardas']}</div>
                </div>
            ");
                }
                print('</div>');
            }
        }

        ?>

    </main>
    <footer class="footer">
        <p class="footer__text">Footeris</p>
    </footer>
</body>

</html>