<?php namespace Xitara\ERecht24\Components;

use Cms\Classes\ComponentBase;
use Xitara\ERecht24\Models\Text;

class Output extends ComponentBase
{
    /**
     * Gets the details for the component
     */
    public function componentDetails()
    {
        return [
            'name'        => 'eRecht 24 Ausgabe',
            'description' => 'Gibt einen einzelnen Rechtstext aus. Art des Dokumentes und Sprache müssen ausgewählt werden.'
        ];
    }

    /**
     * Returns the properties provided by the component
     */
    public function defineProperties()
    {
        return [
            'name' => [
                'title'             => 'Text für Ausgabe',
                // 'description'       => 'FooBar',
                'type'              => 'dropdown',
                'default' => 'imprint',
                'options' => [
                    'imprint' => 'Impressum',
                    'privacyPolicy' => 'Datenschutz',
                    'privacyPolicySocialMedia' => 'Datenschutz Social-Media',
                ],
            ],
            'lang' => [
                'title'             => 'Sprache',
                // 'description'       => 'FooBar',
                'type'              => 'dropdown',
                'default' => 'de',
                'options' => [
                    'de' => 'deutsch',
                    'en' => 'englisch',
                ],
            ],
        ];
    }

    public function onRender() {
        $text = Text::select('text')
            ->where('name', $this->property('name'))
            ->where('lang', $this->property('lang'))
            ->where('is_active', 1)
            ->first();

        if ($text === null) {
            return null;
        }

        // \Log::debug($this->property('name'));
        // \Log::debug($this->property('lang'));
        // \Log::debug($text);

        $this->page['text'] = $text->text;
    }
}
