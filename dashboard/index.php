<?php

define('TITLE', "Home");
include '../assets/layouts/header.php';
check_verified();




$table="users";
$search_field="email";
$search_text="supa@hot.com";
$return_field='verified_at';
//$array=$ol->get_arrayPDO($table,$search_field,$search_text,$return_field);
$array=$ol->get_arrayPDO($table,$search_field,$search_text);
var_dump($array);
?>


<main role="main" class="container">

    <div class="row">
        <div class="col-sm-3">

            <?php include('../assets/layouts/profile-card.php'); ?>

        </div>
        <div class="col-sm-9">

            <div class="d-flex align-items-center p-3 mt-5 mb-3 text-white-50 bg-black rounded box-shadow">
                <img class="mr-3" src="../assets/images/logonotextwhite.png" alt="" width="48" height="48">
                <div class="lh-100">
                    <h6 class="mb-0 text-white lh-100">Admin Dashboard</h6>
                    <small>[Development in Progress]</small>
                </div>
            </div>
                    <?php $ol->displat_useronline(); ?>




        </div>
    </div>
</main>




    <?php

    include '../assets/layouts/footer.php'

    ?>
