<?php namespace Xitara\ERecht24\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Texts Backend Controller
 */
class Texts extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Xitara.ERecht24', 'erecht24', 'texts');
    }
}
