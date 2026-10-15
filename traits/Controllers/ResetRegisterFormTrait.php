<?php
namespace Traits\Controllers;

trait ResetRegisterFormTrait
{
    public function resetForm()
    {
        $currentUrl = trim($_SERVER['REQUEST_URI'], '/');
        // die($currentUrl); // ?  DEBUG
        $validPages = ['register'];

        // * IF MIDWAY STOPPED FILLING IN REGISTER FORM AND DIFFERENT LINK WAS OPENED. THEN RESET ALL REGISTER FORM DATA
        if (
            $_SERVER['REQUEST_METHOD'] === 'GET' &&
            (isset($_SESSION['form-step']) || isset($_SESSION['form-steps'])) &&
            !in_array($currentUrl, $validPages)
        ) {
            if ($currentUrl !== 'register') {
                unset(
                    $_SESSION['form-data'],
                    $_SESSION['errors'],
                    $_SESSION['form-step'],
                    $_SESSION['form-steps']
                );
            }
        }
    }
}
