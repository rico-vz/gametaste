<?php

function displayTemplate($template, $data)
{
    global $twig;
    echo $twig->display($template, $data);
}

function error($errorNumber, $errorMessage)
{
    http_response_code($errorNumber);
    displayTemplate('error.html', compact('errorNumber', 'errorMessage'));
    die();
}

function redirect($url)
{
    header("Location: $url");
    die();
}