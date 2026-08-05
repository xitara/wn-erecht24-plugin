<?php

namespace Xitara\ERecht24\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Exception;
use Flash;
use Xitara\ERecht24\Classes\StatusRepository;

class LegalTextStatus extends ReportWidgetBase
{
    protected $defaultAlias = 'erecht24LegalTextStatus';

    public function render()
    {
        try {
            $this->loadData();
        } catch (Exception $exception) {
            $this->vars['error'] = $exception->getMessage();
        }

        return $this->makePartial('widget');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title' => 'backend::lang.dashboard.widget_title_label',
                'default' => 'xitara.erecht24::lang.dashboard.widget_title',
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ],
        ];
    }

    public function onLoadWarningsForm()
    {
        $this->vars['warnings'] = StatusRepository::warnings();
        $this->vars['canAcknowledge'] = StatusRepository::hasAcknowledgeableWarnings();

        return $this->makePartial('warnings_form');
    }

    public function onAcknowledgeWarnings()
    {
        StatusRepository::acknowledgeChanges();
        Flash::success(trans('xitara.erecht24::lang.dashboard.acknowledged'));

        return ['#' . $this->alias => $this->render()];
    }

    protected function loadData() : void
    {
        $this->vars['rows'] = StatusRepository::rows();
    }
}
