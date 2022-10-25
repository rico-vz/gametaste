<?php

use RedBeanPHP\R as R;

require '../public/api/igdb/class.igdb.php';

class BaseController
{
    public function getBeanById($typeOfBean, $queryStringKey)
    {
        $bean = R::findOne($typeOfBean, 'id=?', [$queryStringKey]);
        return $bean;
    }

    public function authorizeUser()
    {
        if (!isset($_SESSION['loggedInUser'])) {
            header('Location: /user/login?returnTo=' . $_SERVER['REQUEST_URI']);
            die();
        }
    }
}