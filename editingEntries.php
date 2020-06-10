<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit products</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/main.css">

    <script src="js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'textarea', branding:false, menubar: false, elementpath: false});</script>
</head>
<body>

<?php
    include ("dbconnection.php");

    $languagesStmt = "SELECT * FROM languages";
    $queryResult = $mysqli->query($languagesStmt);

    $products_stmt =  "SELECT products.products_price,
                              products.products_reference, 
                              products.products_id
                       FROM   products
                       WHERE  `products_id` = '". $_GET["id"] ."'";

    $priceresult = $mysqli->query($products_stmt);
    $pricearray = $priceresult->fetch_array();
    ?>

    <h1 class="p-4">Ændring af produkt</h1>
    <div class="container">
        <form action="index.php" method="post">
            <div class="pr-3">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="exampleFormControlInput1">Product reference</label>
                    <input name="newref" type="text" class="form-control col-sm-10" id="exampleFormControlInput1" value="<?= $ref = (isset($pricearray["products_reference"]) ? $pricearray["products_reference"] : ""); ?>">
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="exampleFormControlInput1">Price</label>
                    <input name="newprice" type="text" class="form-control col-sm-10" id="exampleFormControlInput1" value="<?= $price = (isset($pricearray["products_price"]) ? $pricearray["products_price"] : ""); ?>">
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2">
                <?php
                //Fills the fields if there is any available data in DB
                if($queryResult->num_rows > 0){
                    while($editbox = $queryResult->fetch_assoc()){
                        $product_description_stmt =  "SELECT  products_description.products_description_name, 
                                                              products_description.products_description_short_description,
                                                              products_description.products_description_description
                                                      FROM    products_description                                       
                                                      WHERE   `languages_id` = '". $editbox["languages_id"] ."'
                                                      AND     `products_id` = '". $_GET["id"] ."'";

                        $result = $mysqli->query($product_description_stmt);
                        $row = $result->fetch_array();?>
                        <div class="col-lg">
                            <label for="Form"><?= $editbox["languages_name"]?></label>
                            <div class="border p-3 mb-5">
                                <div class="form-group">
                                    <label for="FormControlInput1">Product name</label>
                                    <input name="newProductName<?= $editbox["languages_id"]?>" type="text" class="form-control" id="FormControlInput1" value="<?= $price = (isset($row["products_description_name"]) ? $row["products_description_name"] : ""); ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="FormControlTextarea">Small description</label>
                                    <textarea name="newShortDescription<?= $editbox["languages_id"]?>" class="form-control" id="FormControlTextarea1" rows="5"><?= $price = (isset($row["products_description_short_description"]) ? $row["products_description_short_description"] : ""); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="FormControlTextarea">Large description</label>
                                    <textarea name="newDescription<?= $editbox["languages_id"]?>" class="form-control" id="FormControlTextarea2" rows="15"><?= $price = (isset($row["products_description_description"]) ? $row["products_description_description"] : ""); ?></textarea>
                                </div>
                            </div>
                        </div><?php
                    }
                }
            ?>
            </div>
            <div class="row justify-content-center mb-5">
                <button name="product-id" type="submit" class="btn btn-sm btn-outline-secondary" value="<?= $_GET["id"] ?>">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>