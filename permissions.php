<?php
    require_once './header.php';

    // объект $right(права) подтягиваются из header.php
    $rights->setCurrentFileName(basename(__FILE__));
    echo $rights->checkPermission();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Управление доступом</h1>
        </div>
        <?php
        // функция для вывода всех подкаталогов и файлов
        function tmlFile($dir, $level = 0)
        {
            global $loader;
            global $m; //mustache
            global $allAccess; // все типы прав - read write denied
            global $allGroups; // все группы - admin user viewer

            global $rights; // класс прав

            $pages = scandir($dir, 0);
            $pages = array_diff($pages, array('.', '..'));

            /*            echo "<pre>";
                        var_dump($pages);
                        echo "</pre>";
                        die();*/

            foreach ($pages as $k => $v) {

                // отрисовываем файлы
                if (!is_dir($dir . $v)) {
                    if (preg_match('/.*?\.php$/i', $v)) {

                        $page_id = $rights->getPageIdByName($pages[$k]);
                        $sp = $rights->getObjectPermission($page_id);

                        $tpl_data['object_id']   = $page_id;
                        $tpl_data['object_name'] = $pages[$k];
                        $tpl_data['groups']      = $allGroups;
                        $tpl_data['accesses']    = $allAccess;
                        $tpl_data['permissions'] = $sp;

                        $tpl = $loader->load('permissions');
                        echo $m->render($tpl, $tpl_data);

                        //обнуляем массив данных для шаблона
                        unset($tpl_data);
                    }
                }
            }
        }
        $allAccess = $rights->getAllAccess(); // все типы прав - read write denied
        $allGroups = $rights->getAllGroups(); // все группы пользователей

        $folder = $_SERVER['DOCUMENT_ROOT'].'/'; // определяем каталог для вывода всех файлов
        tmlFile($folder); // запускаем выполнение функции создания дерева страниц с правами
        ?>

    </div>
</div>
<?php
    include 'footer.php';
?>
<script src='js/permissions.js'></script>
