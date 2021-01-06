<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Personalo valdymo sistema</title>
</head>

<body>
    <header class="header">
        <div class="header_btn">
            <a href="?path=projektai" class="header_btn-name">Projektai</a>
        </div>
        <div class="header_btn">
            <a href="?path=darbuotojai" class="header_btn-name">Darbuotojai</a>
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
            $sql = 'SELECT projektai.id AS id, projekto_pavadinimas,  CONCAT_WS(" ", vardas, pavarde) AS vardas FROM projektai
            LEFT JOIN darbuotojai ON projektai.id = darbuotojai.projekto_id';
        }

        if ($_GET['path'] == 'darbuotojai') {
            print('Darbuotoju lentele cia:');
            $sql = 'SELECT darbuotojai.id, concat_ws(" ", vardas, pavarde) AS vardas, projekto_pavadinimas FROM darbuotojai
            LEFT JOIN projektai ON projektai.id = darbuotojai.projekto_id';
        } else if ($_GET['path'] == 'projektai') {
            print('Projektu lentele cia:');
            $sql = 'SELECT projektai.id, projekto_pavadinimas, group_concat(vardas) AS vardas FROM projektai
            LEFT JOIN darbuotojai ON projektai.id = darbuotojai.projekto_id
            GROUP BY projektai.id';
        }

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
                print('<div style="display:flex; flex-direction:column; width:400px">');
                print('<div style="display:flex">
                <div style="flex:1">Id:</div>
                <div style="flex:2">Projektas:</div>
                <div style="flex:2">Vardas:</div>
            </div>');
                while ($row = mysqli_fetch_assoc($res)) {
                    print("
                <div style='display:flex'>
                    <div style='flex:1'>{$row['id']}</div>
                    <div style='flex:2'>{$row['projekto_pavadinimas']}</div>
                    <div style='flex:2'>{$row['vardas']}</div>
                </div>
            ");
                }
                print('</div>');
            }
        }

        ?>

    </main>
    <footer class="footer">Footeris</footer>
</body>

</html>