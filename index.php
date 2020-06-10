<!doctype html>
<html class="no-js" lang="da">

<head>
    <meta charset="utf-8">
    <title>Product page</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
<?php
    include ("dbconnection.php");
    $language_id = 1; // Language set to Danish

    //Querying for number of languages
    $languagesStmt = "SELECT languages_id FROM languages";
    $queryResult = $mysqli->query($languagesStmt);

    function ValidateInputVal5($conn, $sql, $val1, $val2, $val3, $val4, $val5, $val_type){
        if ($insertstmt = $conn->prepare($sql)) {
            /* bind parameters for markers */
            $insertstmt->bind_param($val_type, $val1, $val2, $val3, $val4, $val5);

            /* execute query */
            $insertstmt->execute();

            /* close statement */
            $insertstmt->close();
        } else {
            echo "ERROR: Could not prepare query: $sql. " . mysqli_error($conn);
        }
    }
    function ValidateInputVal3($conn, $sql, $val1, $val2, $val3, $val_type){
        if ($insertstmt = $conn->prepare($sql)) {
            /* bind parameters for markers */
            $insertstmt->bind_param($val_type, $val1, $val2, $val3);

            /* execute query */
            $insertstmt->execute();

            /* close statement */
            $insertstmt->close();
        } else {
            echo "ERROR: Could not prepare query: $sql. " . mysqli_error($conn);
        }
    }

    // Showing product overview
    $products_stmt = "SELECT products.products_id,
                             products.products_price,
                             products.products_reference,
                             products_description.products_description_name,
                             products_description.products_description_short_description
                          FROM products_description 
                          INNER JOIN products ON products.products_id=products_description.products_id 
                          WHERE `languages_id` = '" . $language_id ."'";
    $result = $mysqli->query($products_stmt);

    //Inserting new product into DB
    if(isset($_POST["product-id"])) {
        if ($result->num_rows < $_POST["product-id"]) {
            for ($i = 1; $i <= $queryResult->num_rows; $i++) {
                $anothersql = "INSERT INTO products_description (products_id, languages_id, products_description_name, products_description_short_description, products_description_description) VALUES (?, ?, ?, ?, ?)";
                $anothersql2 ="INSERT INTO products (products_id, products_price, products_reference) VALUES (?, ?, ? )";

                ValidateInputVal5($mysqli, $anothersql, $_POST["product-id"], $i, $_POST["newProductName" . $i], $_POST["newShortDescription" . $i], $_POST["newDescription" . $i], "iisss");
                ValidateInputVal3($mysqli, $anothersql2, $_POST["product-id"], $_POST["newprice"], $_POST["newref"], "iis");
            }
        }
    }

    //Checks if any product is going to be deleted, and if so, does it.
    if (isset($_POST["delete"])){
        $deleteSQL = "DELETE products, products_description 
                      FROM products INNER JOIN  products_description 
                      WHERE products.`products_id` = products_description.`products_id` 
                      AND products_description.`products_id` = '". $_POST["delete"] ."'";
        if (!$mysqli->query($deleteSQL)) {
            echo "Error deleting: " . $mysqli->error;
        }
        $mysqli->close();
    }

    //Check to see of any change to any product info has happened, and if so, updates the db with it.
    if(!empty($_POST)){
        //Loops through languages to update each one individually
        for ($i = 1; $i <= $queryResult->num_rows; $i++) {
            if(isset($_POST["newprice"]) || isset($_POST["newref"]) || isset($_POST["newProductName". $i]) || isset($_POST["newShortDescription". $i]) || isset($_POST["newDescription". $i])) {
                $updateSQL = "UPDATE products_description, products 
                        SET products.products_reference = ?,
                            products.products_price = ?,
                            products_description.products_description_name = ?,
                            products_description.products_description_short_description = ?,
                            products_description.products_description_description = ? 
                        WHERE languages_id = '" . $i . "' 
                        AND products.products_id = '". $_POST["product-id"] ."' 
                        AND products_description.products_id = '". $_POST["product-id"] ."'";

                ValidateInputVal5($mysqli,$updateSQL,$_POST["newref"],$_POST["newprice"],$_POST["newProductName" . $i],$_POST["newShortDescription" . $i],$_POST["newDescription" . $i],"sisss");
            }
        }
    }?>
    <h1 class="p-4"><?=$language = ($language_id == 2 ? "Product page" : "Produkt oversigt")?></h1>
    <div class="container">
        <form action="editingEntries.php" method="get">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col"><?=$language = ($language_id == 2 ? "Product" : "Produkt")?> reference</th>
                    <th scope="col"><?=$language = ($language_id == 2 ? "Product" : "Produkt")?></th>
                    <th scope="col"><?=$language = ($language_id == 2 ? "Price" : "Pris")?></th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody><?php
                    //Displays products in a table
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {?>
                            <tr>
                                <td><?= $row["products_reference"] ?></td>
                                <td><?= $row["products_description_name"] ?></td>
                                <td><?= $currency = ($language_id == 2 ? "$ " . $row["products_price"] : $row["products_price"]. " kr.")?></td>
                                <td class="text-right">
                                    <button name="id" type="submit" class="btn btn-sm btn-outline-secondary" value="<?= $row["products_id"]?>">
                                        <?=$language = ($language_id == 2 ? "Edit" : "Redigér")?>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-toggle="modal"
                                            data-target="#lastChanceModal">
                                    <?php echo $language = ($language_id == 2 ? "Delete" : "Slet"); $deleteThis = $row["products_id"];?>
                                    </button>
                                </td>
                            </tr><?php
                        }
                    }
                    $mysqli->close();
                ?>
                </tbody>
            </table>
            <div class="justify-content-end row">
                <button name="id" class="btn btn-secondary mb-3" type="submit" value="<?= $val = $result->num_rows + 1 ?>"><?=$language = ($language_id == 2 ? "Add product" : "Tilføj produkt")?></button>
            </div>
        </form>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="modal fade" id="lastChanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?=$language = ($language_id == 2 ? "Last chance!" : "Sidste chance!")?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?=$language = ($language_id == 2 ? "Sure you wish to delete the product?" : "Sikker på du vil slette produktet?")?>
                    </div>
                    <div class="modal-footer">
                        <button name="delete" type="submit" class="btn btn-danger" value="<?= $deleteThis ?>"><?=$language = ($language_id == 2 ? "Delete product" : "Slet produkt")?></button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$language = ($language_id == 2 ? "Close" : "Annullér")?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>

