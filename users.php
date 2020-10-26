<?php
    ob_start();
    require_once 'header.php';

    use App\appUsers;
    $users = new appUsers();

    if($_SERVER['REQUEST_METHOD']=='GET') {
        $data = $users->getAllUsers();
    }
?>
<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-12 text-center">
            <ul>
            <?php
                foreach ($data as $user) {
                    echo "<li>".$user["username"]."</li>";
                }
            ?>
            </ul>
        </div>
    </div>
</div>

<?php
    require_once './footer.php';
?>
